<?php
// ============================================================
// api.php — Dashboard API Router
// All dashboard JS fetch() calls hit this file
// ============================================================

session_start();
require_once 'db.php';
require_once 'functions.php';
require_once 'procedures.php';

header('Content-Type: application/json');

// ── Auth guard — must be logged in ──
if (!isset($_SESSION['staff'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    match($action) {

        // ══════════════════════════════════════════
        // OVERVIEW STATS
        // ══════════════════════════════════════════

        'overview_stats' => (function() {
            $today = date('Y-m-d');
            $pdo   = getConnection();

            // Daily revenue
            $revenue = fn_daily_sales($today);

            // Orders today
            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM orders WHERE Order_Date = :d");
            $stmt->execute([':d' => $today]);
            $ordersToday = (int) $stmt->fetch()['cnt'];

            // Average order value today
            $stmt = $pdo->prepare("
                SELECT COALESCE(AVG(Order_TotalAmount), 0) as avg
                FROM orders WHERE Order_Date = :d AND Order_TotalAmount > 0
            ");
            $stmt->execute([':d' => $today]);
            $avgValue = round((float) $stmt->fetch()['avg'], 2);

            // Pending orders (Unpaid payments)
            $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM payment WHERE Payment_Status = 'Unpaid'");
            $pending = (int) $stmt->fetch()['cnt'];

            // Weekly revenue (last 7 days)
            $stmt = $pdo->query("
                SELECT o.Order_Date, COALESCE(SUM(p.Payment_Amount), 0) as daily_total
                FROM orders o
                LEFT JOIN payment p ON o.Order_ID = p.Order_ID AND p.Payment_Status = 'Paid'
                WHERE o.Order_Date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY o.Order_Date
                ORDER BY o.Order_Date ASC
            ");
            $weeklyData = $stmt->fetchAll();

            // Recent live orders
            $stmt = $pdo->query("
                SELECT o.Order_ID, o.Customer_ID, i.Item_Name, o.Order_TotalAmount, p.Payment_Status
                FROM orders o
                LEFT JOIN item i ON o.Item_ID = i.Item_ID
                LEFT JOIN payment p ON o.Order_ID = p.Order_ID
                ORDER BY o.Order_Date DESC
                LIMIT 5
            ");
            $liveOrders = $stmt->fetchAll();

            echo json_encode([
                'revenue'     => $revenue,
                'orders_today'=> $ordersToday,
                'avg_value'   => $avgValue,
                'pending'     => $pending,
                'weekly'      => $weeklyData,
                'live_orders' => $liveOrders,
            ]);
        })(),

        // ══════════════════════════════════════════
        // MENU / ITEMS
        // ══════════════════════════════════════════

        'get_products' => (function() {
            $items = sp_get_products();
            echo json_encode(['items' => $items]);
        })(),

        'add_product' => (function() use ($body) {
            sp_add_product(
                $body['id'],
                $body['name'],
                (float) $body['cost'],
                $body['desc']      ?? '',
                $body['category']  ?? 'Beverage'
            );
            echo json_encode(['ok' => true]);
        })(),

        'update_product' => (function() use ($body) {
            sp_update_product(
                $body['id'],
                $body['name'],
                (float) $body['cost'],
                $body['desc']     ?? '',
                $body['category'] ?? 'Beverage'
            );
            echo json_encode(['ok' => true]);
        })(),

        'delete_product' => (function() use ($body) {
            sp_delete_product($body['id']);
            echo json_encode(['ok' => true]);
        })(),

        // ══════════════════════════════════════════
        // ORDERS
        // ══════════════════════════════════════════

        'get_orders' => (function() {
            $pdo  = getConnection();
            $stmt = $pdo->query("
                SELECT o.Order_ID, o.Customer_ID, i.Item_Name, o.Item_Cost,
                       o.Order_Date, o.Order_TotalAmount, p.Payment_Status
                FROM orders o
                LEFT JOIN item    i ON o.Item_ID    = i.Item_ID
                LEFT JOIN payment p ON o.Order_ID   = p.Order_ID
                ORDER BY o.Order_Date DESC
            ");
            echo json_encode(['orders' => $stmt->fetchAll()]);
        })(),

        'create_order' => (function() use ($body) {
            sp_create_order(
                $body['order_id'],
                $body['customer_id'],
                $body['item_id'],
                $body['date'] ?? date('Y-m-d'),
                $body['discount'] ?? null,
                (float) $body['total']
            );
            echo json_encode(['ok' => true]);
        })(),

        'process_payment' => (function() use ($body) {
            sp_process_payment(
                $body['orderId'],
                $body['method'],
                (float) $body['amount']
            );
            echo json_encode(['ok' => true]);
        })(),

        // ══════════════════════════════════════════
        // REFUNDS
        // ══════════════════════════════════════════

        'get_refunds' => (function() {
            $pdo  = getConnection();
            $stmt = $pdo->query("
                SELECT r.*, i.Item_Name
                FROM refund r
                LEFT JOIN item i ON r.Item_ID = i.Item_ID
                ORDER BY r.Refund_Date DESC
            ");
            echo json_encode(['refunds' => $stmt->fetchAll()]);
        })(),

        'process_refund' => (function() use ($body) {
            sp_process_refund(
                $body['refund_id'],
                $body['customer_id'],
                $body['order_id'],
                $body['item_id'],
                (float) $body['amount'],
                $body['date']   ?? date('Y-m-d'),
                $body['reason'] ?? ''
            );
            echo json_encode(['ok' => true]);
        })(),

        // ══════════════════════════════════════════
        // CUSTOMERS
        // ══════════════════════════════════════════

        'get_customers' => (function() {
            $pdo  = getConnection();
            $stmt = $pdo->query("
                SELECT c.Customer_ID, c.Customer_Name, c.Customer_Email,
                       COUNT(o.Order_ID) as total_orders,
                       COALESCE(SUM(o.Order_TotalAmount), 0) as total_spent
                FROM customer c
                LEFT JOIN orders o ON c.Customer_ID = o.Customer_ID
                GROUP BY c.Customer_ID
                ORDER BY total_spent DESC
            ");
            echo json_encode(['customers' => $stmt->fetchAll()]);
        })(),

        // ══════════════════════════════════════════
        // INVENTORY
        // ══════════════════════════════════════════

        'get_inventory' => (function() {
            $items = sp_get_products();
            echo json_encode(['items' => $items]);
        })(),

        // ══════════════════════════════════════════
        // REPORTS
        // ══════════════════════════════════════════

        'get_reports' => (function() {
            $pdo  = getConnection();
            $stmt = $pdo->query("SELECT * FROM report ORDER BY Report_Date DESC");
            $logs = $pdo->query("SELECT * FROM report_log ORDER BY Log_Date DESC");
            echo json_encode([
                'reports' => $stmt->fetchAll(),
                'logs'    => $logs->fetchAll(),
            ]);
        })(),

        'add_report' => (function() use ($body) {
            sp_add_report(
                $body['report_id'],
                $body['type'],
                $body['date']    ?? date('Y-m-d'),
                $body['content'] ?? ''
            );
            echo json_encode(['ok' => true]);
        })(),

        // ══════════════════════════════════════════
        // SALES
        // ══════════════════════════════════════════

        'get_sales' => (function() {
            $pdo = getConnection();

            // Payment method breakdown
            $stmt = $pdo->query("
                SELECT Payment_Method, COUNT(*) as count,
                       SUM(Payment_Amount) as total
                FROM payment WHERE Payment_Status = 'Paid'
                GROUP BY Payment_Method
            ");
            $methods = $stmt->fetchAll();

            // Monthly revenue
            $stmt = $pdo->query("
                SELECT DATE_FORMAT(o.Order_Date, '%Y-%m-%d') as date,
                       COALESCE(SUM(p.Payment_Amount), 0) as total
                FROM orders o
                LEFT JOIN payment p ON o.Order_ID = p.Order_ID AND p.Payment_Status = 'Paid'
                WHERE o.Order_Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY o.Order_Date
                ORDER BY o.Order_Date ASC
            ");
            $daily = $stmt->fetchAll();

            // Refund summary
            $stmt = $pdo->query("
                SELECT COUNT(*) as count, COALESCE(SUM(Refund_Amount), 0) as total,
                       Refund_Reason as top_reason
                FROM refund
                GROUP BY Refund_Reason
                ORDER BY count DESC
                LIMIT 1
            ");
            $refunds = $stmt->fetch();

            echo json_encode([
                'methods' => $methods,
                'daily'   => $daily,
                'refunds' => $refunds,
            ]);
        })(),

        default => (function() {
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
        })(),
    };

} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['error' => $e->getMessage()]);
} catch (RuntimeException $e) {
    http_response_code(409);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}