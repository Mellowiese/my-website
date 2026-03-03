<?php
require_once 'db.php';

// ============================================================
// STORED PROCEDURES  (mirrors SQL stored procedures)
// ============================================================


// ------------------------------------------------------------
// A. PRODUCT / ITEM PROCEDURES
// ------------------------------------------------------------

/**
 * sp_add_product — Inserts a new item into the item table.
 * Mirrors Trigger 4: throws if cost <= 0.
 *
 * @throws InvalidArgumentException
 * @throws PDOException
 */
function sp_add_product(
    string $id,
    string $name,
    float  $cost,
    string $description,
    string $category
): void {
    // Mirror Trigger 4: block zero / negative price
    if ($cost <= 0) {
        throw new InvalidArgumentException('Item cost cannot be zero or negative!');
    }

    $pdo  = getConnection();
    $stmt = $pdo->prepare("
        INSERT INTO item (Item_ID, Item_Name, Item_Cost, Item_Description, Item_Category)
        VALUES (:id, :name, :cost, :desc, :category)
    ");
    $stmt->execute([
        ':id'       => $id,
        ':name'     => $name,
        ':cost'     => $cost,
        ':desc'     => $description,
        ':category' => $category,
    ]);
}

/**
 * sp_update_product — Updates an existing item record.
 * Mirrors Trigger 5: blocks price reduction of more than 50%.
 *
 * @throws InvalidArgumentException
 * @throws PDOException
 */
function sp_update_product(
    string $id,
    string $name,
    float  $cost,
    string $description,
    string $category
): void {
    $pdo = getConnection();

    // Mirror Trigger 5: fetch current price for validation
    $check = $pdo->prepare("SELECT Item_Cost FROM item WHERE Item_ID = :id");
    $check->execute([':id' => $id]);
    $row = $check->fetch();

    if ($row && $cost < ($row['Item_Cost'] * 0.50)) {
        throw new InvalidArgumentException('Price cannot be reduced by more than 50%!');
    }

    $stmt = $pdo->prepare("
        UPDATE item
        SET    Item_Name        = :name,
               Item_Cost        = :cost,
               Item_Description = :desc,
               Item_Category    = :category
        WHERE  Item_ID = :id
    ");
    $stmt->execute([
        ':id'       => $id,
        ':name'     => $name,
        ':cost'     => $cost,
        ':desc'     => $description,
        ':category' => $category,
    ]);
}

/**
 * sp_delete_product — Deletes an item by ID.
 */
function sp_delete_product(string $id): void {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("DELETE FROM item WHERE Item_ID = :id");
    $stmt->execute([':id' => $id]);
}

/**
 * sp_get_products — Returns all items, or a single item by ID.
 *
 * @param  string|null $id  Pass null to get all items.
 * @return array
 */
function sp_get_products(?string $id = null): array {
    $pdo = getConnection();

    if ($id === null) {
        return $pdo->query("SELECT * FROM item")->fetchAll();
    }

    $stmt = $pdo->prepare("SELECT * FROM item WHERE Item_ID = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll();
}


// ------------------------------------------------------------
// B. SALES & TRANSACTION PROCEDURES
// ------------------------------------------------------------

/**
 * sp_create_order — Creates a new order.
 * Mirrors Trigger 1: automatically inserts an 'Unpaid' payment record.
 *
 * @throws PDOException
 */
function sp_create_order(
    string  $orderId,
    string  $customerId,
    string  $itemId,
    string  $orderDate,
    ?string $discount,
    float   $total
): void {
    $pdo = getConnection();

    // Fetch item cost
    $stmt = $pdo->prepare("SELECT Item_Cost FROM item WHERE Item_ID = :id");
    $stmt->execute([':id' => $itemId]);
    $item = $stmt->fetch();
    $itemCost = $item ? (float) $item['Item_Cost'] : $total;

    $pdo->beginTransaction();
    try {
        // Insert order
        $ins = $pdo->prepare("
            INSERT INTO orders
                (Order_ID, Customer_ID, Item_ID, Item_Cost, Order_Date, Order_Discount, Order_TotalAmount)
            VALUES
                (:oid, :cid, :iid, :icost, :date, :disc, :total)
        ");
        $ins->execute([
            ':oid'   => $orderId,
            ':cid'   => $customerId,
            ':iid'   => $itemId,
            ':icost' => $itemCost,
            ':date'  => $orderDate,
            ':disc'  => $discount,
            ':total' => $total,
        ]);

        // Mirror Trigger 1: auto-create Unpaid payment
        _trigger_after_order_insert($pdo, $orderId, $total);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * sp_add_order_item — Updates an existing order with a new item
 *                     and adds that item's cost to the total.
 */
function sp_add_order_item(string $orderId, string $itemId): void {
    $pdo = getConnection();

    $stmt = $pdo->prepare("SELECT Item_Cost FROM item WHERE Item_ID = :id");
    $stmt->execute([':id' => $itemId]);
    $item = $stmt->fetch();

    if (!$item) {
        throw new InvalidArgumentException("Item '$itemId' not found.");
    }

    $cost = (float) $item['Item_Cost'];

    $upd = $pdo->prepare("
        UPDATE orders
        SET    Item_ID           = :iid,
               Item_Cost         = :cost,
               Order_TotalAmount = Order_TotalAmount + :cost2
        WHERE  Order_ID = :oid
    ");
    $upd->execute([
        ':iid'   => $itemId,
        ':cost'  => $cost,
        ':cost2' => $cost,
        ':oid'   => $orderId,
    ]);
}

/**
 * sp_remove_order_item — Clears the item from an order and resets the total.
 */
function sp_remove_order_item(string $orderId): void {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("
        UPDATE orders
        SET    Item_ID           = NULL,
               Item_Cost         = 0.00,
               Order_TotalAmount = 0.00
        WHERE  Order_ID = :oid
    ");
    $stmt->execute([':oid' => $orderId]);
}

/**
 * sp_process_payment — Updates a payment record with method, amount, and marks it Paid.
 */
function sp_process_payment(string $orderId, string $method, float $amount): void {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("
        UPDATE payment
        SET    Payment_Method = :method,
               Payment_Amount = :amount,
               Payment_Status = 'Paid'
        WHERE  Order_ID = :oid
    ");
    $stmt->execute([
        ':method' => $method,
        ':amount' => $amount,
        ':oid'    => $orderId,
    ]);
}

/**
 * sp_finalize_sale — Marks the payment for an order as 'Paid'.
 */
function sp_finalize_sale(string $orderId): void {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("
        UPDATE payment
        SET    Payment_Status = 'Paid'
        WHERE  Order_ID = :oid
    ");
    $stmt->execute([':oid' => $orderId]);
}


// ------------------------------------------------------------
// C. REFUND PROCEDURES
// ------------------------------------------------------------

/**
 * sp_process_refund — Inserts a refund record.
 * Mirrors Trigger 2: automatically marks associated payment as 'Refunded'.
 * Mirrors Trigger 3: blocks if payment is already Refunded.
 *
 * @throws RuntimeException
 * @throws PDOException
 */
function sp_process_refund(
    string $refundId,
    string $customerId,
    string $orderId,
    string $itemId,
    float  $amount,
    string $refundDate,
    string $reason
): void {
    $pdo = getConnection();

    // Mirror Trigger 3: block modification of already-refunded payment
    $check = $pdo->prepare("
        SELECT Payment_Status FROM payment WHERE Order_ID = :oid
    ");
    $check->execute([':oid' => $orderId]);
    $payment = $check->fetch();

    if ($payment && $payment['Payment_Status'] === 'Refunded') {
        throw new RuntimeException('Cannot modify a refunded payment!');
    }

    $pdo->beginTransaction();
    try {
        $ins = $pdo->prepare("
            INSERT INTO refund
                (Refund_ID, Customer_ID, Order_ID, Item_ID, Refund_Amount, Refund_Date, Refund_Reason)
            VALUES
                (:rid, :cid, :oid, :iid, :amt, :date, :reason)
        ");
        $ins->execute([
            ':rid'    => $refundId,
            ':cid'    => $customerId,
            ':oid'    => $orderId,
            ':iid'    => $itemId,
            ':amt'    => $amount,
            ':date'   => $refundDate,
            ':reason' => $reason,
        ]);

        // Mirror Trigger 2: auto-update payment status to 'Refunded'
        _trigger_after_refund_insert($pdo, $orderId);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}


// ------------------------------------------------------------
// D. REPORT PROCEDURES
// ------------------------------------------------------------

/**
 * sp_add_report — Inserts a new report.
 * Mirrors Trigger 6: automatically logs the new report into report_log.
 */
function sp_add_report(
    string $reportId,
    string $type,
    string $date,
    string $content
): void {
    $pdo = getConnection();

    $pdo->beginTransaction();
    try {
        $ins = $pdo->prepare("
            INSERT INTO report (Report_ID, Report_Type, Report_Date, Report_Content)
            VALUES (:rid, :type, :date, :content)
        ");
        $ins->execute([
            ':rid'     => $reportId,
            ':type'    => $type,
            ':date'    => $date,
            ':content' => $content,
        ]);

        // Mirror Trigger 6: auto-log the new report
        _trigger_after_report_insert($pdo, $reportId, $type);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}


// ============================================================
// PRIVATE TRIGGER HELPERS
// (called internally to mirror MySQL AFTER INSERT triggers)
// ============================================================

/**
 * Trigger 1 — Auto-creates an Unpaid payment after a new order.
 */
function _trigger_after_order_insert(PDO $pdo, string $orderId, float $total): void {
    $paymentId = 'PAY' . $orderId;
    $stmt = $pdo->prepare("
        INSERT INTO payment (Payment_ID, Order_ID, Payment_Method, Payment_Amount, Payment_Status)
        VALUES (:pid, :oid, 'Pending', :amt, 'Unpaid')
    ");
    $stmt->execute([
        ':pid' => $paymentId,
        ':oid' => $orderId,
        ':amt' => $total,
    ]);
}

/**
 * Trigger 2 — Marks payment as 'Refunded' after a refund is inserted.
 */
function _trigger_after_refund_insert(PDO $pdo, string $orderId): void {
    $stmt = $pdo->prepare("
        UPDATE payment SET Payment_Status = 'Refunded' WHERE Order_ID = :oid
    ");
    $stmt->execute([':oid' => $orderId]);
}

/**
 * Trigger 6 — Auto-logs into report_log after a new report is inserted.
 */
function _trigger_after_report_insert(PDO $pdo, string $reportId, string $reportType): void {
    $stmt = $pdo->prepare("
        INSERT INTO report_log (Report_ID, Log_Message, Log_Date)
        VALUES (:rid, :msg, NOW())
    ");
    $stmt->execute([
        ':rid' => $reportId,
        ':msg' => 'New report added: ' . $reportType,
    ]);
}