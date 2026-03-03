<?php
require_once 'db.php';

// ============================================================
// FUNCTIONS  (mirrors SQL scalar functions)
// ============================================================

/**
 * fn_product_stock — Returns current stock level for a given item.
 *
 * @param  string $itemId  e.g. 'IM001'
 * @return int|null  Stock count, or null if item not found.
 */
function fn_product_stock(string $itemId): ?int {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("SELECT Item_Stock FROM item WHERE Item_ID = :id");
    $stmt->execute([':id' => $itemId]);
    $row  = $stmt->fetch();
    return $row ? (int) $row['Item_Stock'] : null;
}

/**
 * fn_order_total — Returns the total amount for a given order.
 *
 * @param  string $orderId  e.g. 'M001'
 * @return float|null  Order total, or null if not found.
 */
function fn_order_total(string $orderId): ?float {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("SELECT Order_TotalAmount FROM orders WHERE Order_ID = :id");
    $stmt->execute([':id' => $orderId]);
    $row  = $stmt->fetch();
    return $row ? (float) $row['Order_TotalAmount'] : null;
}

/**
 * fn_daily_sales — Returns the sum of all paid payments for a given date.
 *
 * @param  string $date  Format 'YYYY-MM-DD'
 * @return float  Total sales (0.00 if none).
 */
function fn_daily_sales(string $date): float {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(p.Payment_Amount), 0) AS total
        FROM   payment p
        JOIN   orders  o ON p.Order_ID = o.Order_ID
        WHERE  o.Order_Date      = :date
          AND  p.Payment_Status  = 'Paid'
    ");
    $stmt->execute([':date' => $date]);
    $row = $stmt->fetch();
    return (float) $row['total'];
}