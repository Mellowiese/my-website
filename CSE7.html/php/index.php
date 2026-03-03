<?php
// ============================================================
// MAIN ENTRY — Usage examples for all backend functions
// ============================================================

require_once 'db.php';
require_once 'functions.php';
require_once 'procedures.php';

header('Content-Type: application/json');

// Helper to send JSON response
function respond(string $label, $data): void {
    echo json_encode(['action' => $label, 'result' => $data], JSON_PRETTY_PRINT) . PHP_EOL;
}

// Helper to handle errors gracefully
function safe_call(string $label, callable $fn): void {
    try {
        $result = $fn();
        respond($label, $result ?? 'OK');
    } catch (Throwable $e) {
        respond($label . ' [ERROR]', $e->getMessage());
    }
}


// ============================================================
// SCALAR FUNCTIONS
// ============================================================

safe_call('fn_product_stock(IM001)', fn() =>
    fn_product_stock('IM001')
);

safe_call('fn_order_total(M001)', fn() =>
    fn_order_total('M001')
);

safe_call('fn_daily_sales(2025-08-26)', fn() =>
    fn_daily_sales('2025-08-26')
);


// ============================================================
// PRODUCT PROCEDURES
// ============================================================

safe_call('sp_add_product IM004', fn() =>
    sp_add_product('IM004', 'Matcha Latte', 150.00, 'Green tea with steamed milk', 'Beverage')
);

safe_call('sp_update_product IM004', fn() =>
    sp_update_product('IM004', 'Matcha Latte', 145.00, 'Premium matcha with milk', 'Beverage')
);

safe_call('sp_get_products(all)', fn() =>
    sp_get_products()
);

safe_call('sp_get_products(IM001)', fn() =>
    sp_get_products('IM001')
);

safe_call('sp_delete_product IM004', fn() =>
    sp_delete_product('IM004')
);


// ============================================================
// ORDER & PAYMENT PROCEDURES
// ============================================================

safe_call('sp_create_order M006', fn() =>
    sp_create_order('M006', 'CM055', 'IM002', '2025-08-29', null, 135.00)
);

safe_call('sp_add_order_item M006', fn() =>
    sp_add_order_item('M006', 'IM003')
);

safe_call('sp_remove_order_item M006', fn() =>
    sp_remove_order_item('M006')
);

safe_call('sp_process_payment M006', fn() =>
    sp_process_payment('M006', 'GCash', 135.00)
);

safe_call('sp_finalize_sale M006', fn() =>
    sp_finalize_sale('M006')
);


// ============================================================
// REFUND PROCEDURES
// ============================================================

safe_call('sp_process_refund MR10', fn() =>
    sp_process_refund('MR10', 'CM055', 'M001', 'IM001', 120.00, '2025-07-04', 'Wrong Item')
);


// ============================================================
// REPORT PROCEDURES
// ============================================================

safe_call('sp_add_report MR058', fn() =>
    sp_add_report('MR058', 'Sales', '2025-08-27', 'Daily Sales Check')
);


// ============================================================
// TRIGGER VALIDATION DEMOS
// ============================================================

// Trigger 4 — Block zero/negative price
safe_call('Trigger 4 — negative price (should fail)', fn() =>
    sp_add_product('IM999', 'Bad Item', -10.00, 'Test', 'Test')
);

// Trigger 5 — Block >50% price drop
safe_call('Trigger 5 — >50% price drop (should fail)', fn() =>
    sp_update_product('IM001', 'Cappuccino', 10.00, 'Hot espresso', 'Beverage')
);

// Trigger 3 — Block editing a refunded payment
safe_call('Trigger 3 — edit refunded payment (should fail after refund)', fn() =>
    sp_process_refund('MR99', 'CM055', 'M001', 'IM001', 120.00, '2025-09-01', 'Duplicate refund attempt')
);