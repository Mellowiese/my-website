<?php
// ── Session Guard ──
session_start();
if (!isset($_SESSION['staff'])) {
    header('Location: /CSE7.html/html/login.html');
    exit;
}
$staff = $_SESSION['staff'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MANMADE — Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<!-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <img src="../images/logo.png" alt="Logo" class="brand-logo">
    <h5 class="brand-name"><span class="text-MAN">MAN</span><span class="text-MADE">MADE</span></h5>
    <small class="brand-sub">Admin Portal</small>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-group-label">Main</div>
    <a class="nav-item active" data-page="overview" href="#">
      <i class="bi bi-grid-1x2-fill"></i>
      <span>Overview</span>
    </a>
    <a class="nav-item" data-page="sales" href="#">
      <i class="bi bi-bar-chart-line-fill"></i>
      <span>Sales</span>
    </a>
    <a class="nav-item" data-page="menu" href="#">
      <i class="bi bi-cup-hot-fill"></i>
      <span>Menu</span>
    </a>
    <a class="nav-item" data-page="orders" href="#">
      <i class="bi bi-receipt"></i>
      <span>Orders</span>
      <span class="nav-badge" id="pending-badge">7</span>
    </a>

    <div class="nav-group-label">Manage</div>
    <a class="nav-item" data-page="customers" href="#">
      <i class="bi bi-people-fill"></i>
      <span>Customers</span>
    </a>
    <a class="nav-item" data-page="inventory" href="#">
      <i class="bi bi-box-seam-fill"></i>
      <span>Inventory</span>
    </a>
    <a class="nav-item" data-page="refunds" href="#">
      <i class="bi bi-arrow-counterclockwise"></i>
      <span>Refunds</span>
    </a>
    <a class="nav-item" data-page="reports" href="#">
      <i class="bi bi-file-earmark-bar-graph-fill"></i>
      <span>Reports</span>
    </a>

    <div class="nav-group-label">Settings</div>
    <a class="nav-item" data-page="settings" href="#">
      <i class="bi bi-sliders"></i>
      <span>Preferences</span>
    </a>
  </nav>

  <div class="sidebar-user">
    <div class="user-avatar"><?php echo strtoupper(substr($staff['name'], 0, 2)); ?></div>
    <div class="user-meta">
      <p><?php echo htmlspecialchars($staff['name']); ?></p>
      <small><?php echo htmlspecialchars($staff['position']); ?></small>
    </div>
    <a href="../php/logout.php" class="logout-btn" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</aside>

<!-- ══════════════════════════════════════════
     MAIN CONTENT WRAPPER
══════════════════════════════════════════ -->
<div class="main-wrap">

  <!-- TOPBAR -->
  <header class="topbar">
    <div class="topbar-left">
      <button class="sidebar-toggle" id="sidebar-toggle"><i class="bi bi-list"></i></button>
      <div>
        <h2 id="topbar-title">Good morning, <?php echo htmlspecialchars($staff['name']); ?> ☀️</h2>
        <p id="topbar-sub">Friday, Feb 27, 2026 — Brew &amp; Co. is open</p>
      </div>
    </div>
    <div class="topbar-right">
      <div class="open-badge">● Open Now</div>
      <button class="icon-btn" id="notif-btn">
        <i class="bi bi-bell"></i>
        <span class="badge-dot"></span>
      </button>
    </div>
  </header>

  <!-- NOTIFICATION PANEL -->
  <div class="notif-panel" id="notif-panel">
    <div class="notif-header">Notifications <span class="notif-count">3 new</span></div>
    <div class="notif-item">
      <i class="bi bi-exclamation-circle-fill" style="color:#e67e22;"></i>
      <div><strong>Low Stock:</strong> Oat Milk is running low (3 left)</div>
    </div>
    <div class="notif-item">
      <i class="bi bi-arrow-counterclockwise" style="color:#e74c3c;"></i>
      <div><strong>Refund Request:</strong> Order #0138 — Wrong Item</div>
    </div>
    <div class="notif-item">
      <i class="bi bi-receipt-cutoff" style="color:#27ae60;"></i>
      <div><strong>New Order:</strong> #0143 — Iced Americano × 2</div>
    </div>
  </div>

  <!-- ══ PAGES ══ -->

  <!-- ─── OVERVIEW ─── -->
  <section class="page-section active" id="page-overview">
    <div class="page-heading">
      <h1>Today's Overview</h1>
      <p>Here's how the cafe is doing right now — updated live.</p>
    </div>

    <div class="bento-grid">

      <div class="bento-card col-3 stat-card" id="stat-revenue" data-animate>
        <div class="stat-icon" style="background:#f5ede3;color:#7b4f2e;">
          <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="stat-label">Today's Revenue</div>
        <div class="stat-value">₱8,420</div>
        <div class="stat-change up"><i class="bi bi-arrow-up-short"></i> +18% vs yesterday</div>
        <div class="mini-bars">
          <div class="bar" style="height:45%"></div>
          <div class="bar" style="height:60%"></div>
          <div class="bar" style="height:50%"></div>
          <div class="bar" style="height:75%"></div>
          <div class="bar" style="height:65%"></div>
          <div class="bar" style="height:85%"></div>
          <div class="bar active-bar" style="height:100%"></div>
        </div>
        <div class="card-decor">₱</div>
      </div>

      <div class="bento-card col-3 stat-card" id="stat-orders" data-animate>
        <div class="stat-icon" style="background:#f5ede3;color:#7b4f2e;">
          <i class="bi bi-receipt-cutoff"></i>
        </div>
        <div class="stat-label">Orders Today</div>
        <div class="stat-value">142</div>
        <div class="stat-change up"><i class="bi bi-arrow-up-short"></i> +9 vs yesterday</div>
        <div class="mini-bars">
          <div class="bar" style="height:55%"></div>
          <div class="bar" style="height:70%"></div>
          <div class="bar" style="height:40%"></div>
          <div class="bar" style="height:85%"></div>
          <div class="bar" style="height:60%"></div>
          <div class="bar" style="height:75%"></div>
          <div class="bar active-bar" style="height:90%"></div>
        </div>
        <div class="card-decor">🧾</div>
      </div>

      <div class="bento-card col-3 stat-card" id="stat-avg" data-animate>
        <div class="stat-icon" style="background:#eaf4e8;color:#2d7a3a;">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="stat-label">Avg. Order Value</div>
        <div class="stat-value">₱59</div>
        <div class="stat-change up"><i class="bi bi-arrow-up-short"></i> up from ₱52 last week</div>
        <div class="mini-bars green">
          <div class="bar" style="height:60%"></div>
          <div class="bar" style="height:55%"></div>
          <div class="bar" style="height:65%"></div>
          <div class="bar" style="height:70%"></div>
          <div class="bar" style="height:75%"></div>
          <div class="bar" style="height:80%"></div>
          <div class="bar active-bar" style="height:100%"></div>
        </div>
        <div class="card-decor">📈</div>
      </div>

      <div class="bento-card col-3 stat-card" id="stat-pending" data-animate>
        <div class="stat-icon" style="background:#fef3e2;color:#c07a00;">
          <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="stat-label">Pending Orders</div>
        <div class="stat-value" style="color:#c07a00;">7</div>
        <div class="stat-change" style="color:#c07a00;"><i class="bi bi-clock"></i> 3 in prep · 4 new</div>
        <div class="pending-bar-wrap">
          <div class="pending-bar-fill" style="width:43%"></div>
        </div>
        <div style="font-size:0.65rem;font-family:'DM Mono',monospace;color:var(--muted);margin-top:6px;">3 of 7 being prepared</div>
        <div class="card-decor">⏳</div>
      </div>

      <!-- Weekly Revenue Chart -->
      <div class="bento-card col-8" data-animate>
        <div class="card-title">
          Weekly Revenue
          <span class="card-tag">Feb 21 – 27</span>
        </div>
        <div class="chart-bars">
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:52%"></div><span>Fri</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:68%"></div><span>Sat</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:44%"></div><span>Sun</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:79%"></div><span>Mon</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:61%"></div><span>Tue</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:88%"></div><span>Wed</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar today" style="--h:100%"></div><span class="today-label">Today</span></div>
        </div>
        <div class="summary-row">
          <div class="summary-chip"><div class="val">₱51,840</div><div class="lbl">This Week</div></div>
          <div class="summary-chip"><div class="val">₱44,100</div><div class="lbl">Last Week</div></div>
          <div class="summary-chip"><div class="val" style="color:var(--green);">+17.5%</div><div class="lbl">Growth</div></div>
          <div class="summary-chip"><div class="val">₱7,406</div><div class="lbl">Daily Avg</div></div>
        </div>
      </div>

      <!-- Live Orders -->
      <div class="bento-card col-4" data-animate>
        <div class="card-title">
          Live Orders
          <span class="card-tag">right now</span>
        </div>
        <div class="live-orders-list">
          <div style="color:var(--muted);font-size:0.8rem;padding:12px 0;">Loading orders...</div>
        </div>
        <a href="#" class="see-all-link" data-page="orders">See all orders →</a>
      </div>

      <!-- Top Menu Items -->
      <div class="bento-card col-6" data-animate>
        <div class="card-title">Top Menu Items <span class="card-tag">today</span></div>
        <div class="menu-item-row">
          <div class="menu-emoji">☕</div>
          <div class="menu-info">
            <div class="menu-name">Caramel Latte</div>
            <div class="menu-cat">Hot Drinks</div>
            <div class="mini-prog"><div class="mini-prog-fill" style="width:92%"></div></div>
          </div>
          <div class="menu-stats"><div class="menu-price">₱95</div><div class="sold-count">38 sold</div></div>
        </div>
        <div class="menu-item-row">
          <div class="menu-emoji">🍵</div>
          <div class="menu-info">
            <div class="menu-name">Matcha Latte</div>
            <div class="menu-cat">Hot Drinks</div>
            <div class="mini-prog"><div class="mini-prog-fill" style="width:75%"></div></div>
          </div>
          <div class="menu-stats"><div class="menu-price">₱100</div><div class="sold-count">31 sold</div></div>
        </div>
        <div class="menu-item-row">
          <div class="menu-emoji">🥐</div>
          <div class="menu-info">
            <div class="menu-name">Butter Croissant</div>
            <div class="menu-cat">Pastries</div>
            <div class="mini-prog"><div class="mini-prog-fill" style="width:60%;background:var(--brown-mid)"></div></div>
          </div>
          <div class="menu-stats"><div class="menu-price">₱75</div><div class="sold-count">25 sold</div></div>
        </div>
        <div class="menu-item-row">
          <div class="menu-emoji">🧋</div>
          <div class="menu-info">
            <div class="menu-name">Iced Spanish Latte</div>
            <div class="menu-cat">Cold Drinks</div>
            <div class="mini-prog"><div class="mini-prog-fill" style="width:48%;background:var(--brown-mid)"></div></div>
          </div>
          <div class="menu-stats"><div class="menu-price">₱110</div><div class="sold-count">20 sold</div></div>
        </div>
        <div class="menu-item-row">
          <div class="menu-emoji">🍰</div>
          <div class="menu-info">
            <div class="menu-name">Cheesecake Slice</div>
            <div class="menu-cat">Pastries</div>
            <div class="mini-prog"><div class="mini-prog-fill" style="width:38%;opacity:0.6"></div></div>
          </div>
          <div class="menu-stats"><div class="menu-price">₱120</div><div class="sold-count">16 sold</div></div>
        </div>
      </div>

      <!-- Busiest Hours -->
      <div class="bento-card col-6" data-animate>
        <div class="card-title">Busiest Hours <span class="card-tag">today</span></div>
        <div class="hour-chart">
          <div class="hour-bar-wrap"><div class="hour-bar" style="--h:20%"></div><span>7am</span></div>
          <div class="hour-bar-wrap"><div class="hour-bar" style="--h:35%"></div><span>8am</span></div>
          <div class="hour-bar-wrap"><div class="hour-bar peak" style="--h:90%"></div><span class="peak-label">9am</span></div>
          <div class="hour-bar-wrap"><div class="hour-bar" style="--h:65%"></div><span>10am</span></div>
          <div class="hour-bar-wrap"><div class="hour-bar" style="--h:45%"></div><span>11am</span></div>
          <div class="hour-bar-wrap"><div class="hour-bar peak" style="--h:100%"></div><span class="peak-label">12pm</span></div>
          <div class="hour-bar-wrap"><div class="hour-bar" style="--h:75%"></div><span>1pm</span></div>
          <div class="hour-bar-wrap"><div class="hour-bar" style="--h:30%"></div><span>2pm</span></div>
        </div>
        <div class="peak-chip">
          <div>
            <div style="font-size:0.7rem;font-family:'DM Mono',monospace;color:var(--muted);">Peak hours today</div>
            <div style="font-family:'Playfair Display',serif;font-weight:700;font-size:1rem;color:var(--brown-dark);margin-top:2px;">9:00 AM &amp; 12:00 PM</div>
          </div>
          <i class="bi bi-fire" style="font-size:1.6rem;color:var(--brown-light);"></i>
        </div>
      </div>

    </div>
  </section>

  <!-- ─── SALES ─── -->
  <section class="page-section" id="page-sales">
    <div class="page-heading">
      <h1>Sales</h1>
      <p>Revenue breakdown, payment methods, and growth trends.</p>
    </div>
    <div class="bento-grid">
      <div class="bento-card col-12" data-animate>
        <div class="card-title">Revenue Over Time <span class="card-tag">last 30 days</span></div>
        <div class="chart-bars tall">
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:40%"></div><span>Jan 29</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:55%"></div><span>Jan 30</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:62%"></div><span>Jan 31</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:38%"></div><span>Feb 1</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:71%"></div><span>Feb 2</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:83%"></div><span>Feb 3</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:76%"></div><span>Feb 4</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:59%"></div><span>Feb 5</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:65%"></div><span>Feb 6</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:48%"></div><span>Feb 7</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:70%"></div><span>Feb 8</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:90%"></div><span>Feb 9</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar" style="--h:85%"></div><span>Feb 10</span></div>
          <div class="chart-bar-wrap"><div class="chart-bar today" style="--h:100%"></div><span class="today-label">Today</span></div>
        </div>
        <div class="summary-row">
          <div class="summary-chip"><div class="val">₱51,840</div><div class="lbl">This Month</div></div>
          <div class="summary-chip"><div class="val" style="color:var(--green);">+17.5%</div><div class="lbl">vs Last Month</div></div>
          <div class="summary-chip"><div class="val">GCash</div><div class="lbl">Top Method</div></div>
          <div class="summary-chip"><div class="val">₱8,420</div><div class="lbl">Best Day</div></div>
        </div>
      </div>

      <div class="bento-card col-4" data-animate>
        <div class="card-title">Payment Methods</div>
        <div class="pay-method-list">
          <div class="pay-method-item">
            <div class="pay-icon gcash"><i class="bi bi-phone"></i></div>
            <div class="pay-info"><span>GCash</span><div class="pay-bar"><div style="width:65%;background:var(--brown)"></div></div></div>
            <div class="pay-pct">65%</div>
          </div>
          <div class="pay-method-item">
            <div class="pay-icon cash"><i class="bi bi-cash"></i></div>
            <div class="pay-info"><span>Cash</span><div class="pay-bar"><div style="width:28%;background:var(--brown-mid)"></div></div></div>
            <div class="pay-pct">28%</div>
          </div>
          <div class="pay-method-item">
            <div class="pay-icon card"><i class="bi bi-credit-card"></i></div>
            <div class="pay-info"><span>Card</span><div class="pay-bar"><div style="width:7%;background:var(--brown-light)"></div></div></div>
            <div class="pay-pct">7%</div>
          </div>
        </div>
      </div>

      <div class="bento-card col-4" data-animate>
        <div class="card-title">Refund Summary</div>
        <div class="refund-summary">
          <div class="refund-stat"><span class="refund-num">3</span><span class="refund-lbl">Refunds This Week</span></div>
          <div class="refund-stat"><span class="refund-num">₱360</span><span class="refund-lbl">Total Refunded</span></div>
          <div class="refund-stat"><span class="refund-num">Wrong Item</span><span class="refund-lbl">Top Reason</span></div>
        </div>
        <button class="action-btn" data-page="refunds">View Refunds →</button>
      </div>

      <div class="bento-card col-4" data-animate>
        <div class="card-title">Generate Report</div>
        <p style="font-size:0.8rem;color:var(--muted);margin-bottom:16px;">Export a sales report for any date range.</p>
        <div class="form-group">
          <label>Report Type</label>
          <select class="form-select-custom">
            <option>Daily Sales</option>
            <option>Weekly Summary</option>
            <option>Monthly Report</option>
          </select>
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="date" class="form-input-custom" value="2026-02-27">
        </div>
        <button class="action-btn primary">Generate Report</button>
      </div>
    </div>
  </section>

  <!-- ─── MENU ─── -->
  <section class="page-section" id="page-menu">
    <div class="page-heading">
      <h1>Menu</h1>
      <p>Add, edit, or remove items from the cafe menu.</p>
      <button class="action-btn primary" id="open-add-product">+ Add Item</button>
    </div>

    <div class="filter-bar">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="Beverage">Beverages</button>
      <button class="filter-btn" data-filter="Pastry">Pastries</button>
      <button class="filter-btn" data-filter="Food">Food</button>
      <input type="text" class="search-input" id="menu-search" placeholder="Search items...">
    </div>

    <div class="product-grid" id="product-grid">
      <!-- Rendered by JS -->
      <div class="product-card" data-category="Beverage">
        <div class="product-emoji">☕</div>
        <div class="product-body">
          <div class="product-name">Caramel Latte</div>
          <div class="product-cat">Beverage</div>
          <div class="product-price">₱95.00</div>
          <div class="product-stock">Stock: <strong>24</strong></div>
        </div>
        <div class="product-actions">
          <button class="icon-action edit-btn" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="icon-action delete-btn" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </div>
      <div class="product-card" data-category="Beverage">
        <div class="product-emoji">🍵</div>
        <div class="product-body">
          <div class="product-name">Matcha Latte</div>
          <div class="product-cat">Beverage</div>
          <div class="product-price">₱100.00</div>
          <div class="product-stock">Stock: <strong>18</strong></div>
        </div>
        <div class="product-actions">
          <button class="icon-action edit-btn" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="icon-action delete-btn" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </div>
      <div class="product-card" data-category="Pastry">
        <div class="product-emoji">🥐</div>
        <div class="product-body">
          <div class="product-name">Butter Croissant</div>
          <div class="product-cat">Pastry</div>
          <div class="product-price">₱75.00</div>
          <div class="product-stock low">Stock: <strong>3</strong> ⚠️</div>
        </div>
        <div class="product-actions">
          <button class="icon-action edit-btn" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="icon-action delete-btn" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </div>
      <div class="product-card" data-category="Beverage">
        <div class="product-emoji">🧋</div>
        <div class="product-body">
          <div class="product-name">Iced Spanish Latte</div>
          <div class="product-cat">Beverage</div>
          <div class="product-price">₱110.00</div>
          <div class="product-stock">Stock: <strong>15</strong></div>
        </div>
        <div class="product-actions">
          <button class="icon-action edit-btn" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="icon-action delete-btn" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </div>
      <div class="product-card" data-category="Pastry">
        <div class="product-emoji">🍰</div>
        <div class="product-body">
          <div class="product-name">Cheesecake Slice</div>
          <div class="product-cat">Pastry</div>
          <div class="product-price">₱120.00</div>
          <div class="product-stock">Stock: <strong>8</strong></div>
        </div>
        <div class="product-actions">
          <button class="icon-action edit-btn" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="icon-action delete-btn" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── ORDERS ─── -->
  <section class="page-section" id="page-orders">
    <div class="page-heading">
      <h1>Orders</h1>
      <p>Track, fulfill, and manage all orders.</p>
      <button class="action-btn primary" id="open-add-order">+ New Order</button>
    </div>

    <div class="filter-bar">
      <button class="filter-btn active" data-order-filter="all">All</button>
      <button class="filter-btn" data-order-filter="Unpaid">Unpaid</button>
      <button class="filter-btn" data-order-filter="Paid">Paid</button>
      <button class="filter-btn" data-order-filter="Refunded">Refunded</button>
    </div>

    <div class="orders-table-wrap">
      <table class="orders-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Item</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="orders-tbody">
          <tr>
            <td class="order-id-cell">#M006</td>
            <td>CM055</td>
            <td>Iced Americano</td>
            <td>₱135.00</td>
            <td>2026-02-27</td>
            <td><span class="status-badge new">Unpaid</span></td>
            <td class="actions-cell">
              <button class="icon-action pay-btn" title="Mark Paid"><i class="bi bi-check-circle"></i></button>
              <button class="icon-action refund-btn" title="Refund"><i class="bi bi-arrow-counterclockwise"></i></button>
            </td>
          </tr>
          <tr>
            <td class="order-id-cell">#M005</td>
            <td>CM032</td>
            <td>Caramel Latte</td>
            <td>₱95.00</td>
            <td>2026-02-27</td>
            <td><span class="status-badge done">Paid</span></td>
            <td class="actions-cell">
              <button class="icon-action refund-btn" title="Refund"><i class="bi bi-arrow-counterclockwise"></i></button>
            </td>
          </tr>
          <tr>
            <td class="order-id-cell">#M004</td>
            <td>CM019</td>
            <td>Matcha Latte</td>
            <td>₱100.00</td>
            <td>2026-02-26</td>
            <td><span class="status-badge refunded">Refunded</span></td>
            <td class="actions-cell"><span style="color:var(--muted);font-size:0.75rem;">—</span></td>
          </tr>
          <tr>
            <td class="order-id-cell">#M003</td>
            <td>CM055</td>
            <td>Butter Croissant</td>
            <td>₱75.00</td>
            <td>2026-02-26</td>
            <td><span class="status-badge done">Paid</span></td>
            <td class="actions-cell">
              <button class="icon-action refund-btn" title="Refund"><i class="bi bi-arrow-counterclockwise"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <!-- ─── CUSTOMERS ─── -->
  <section class="page-section" id="page-customers">
    <div class="page-heading">
      <h1>Customers</h1>
      <p>View customer profiles and order history.</p>
    </div>
    <div class="bento-grid">
      <div class="bento-card col-12" data-animate>
        <div class="card-title">Customer List</div>
        <div class="orders-table-wrap">
          <table class="orders-table">
            <thead>
              <tr><th>Customer ID</th><th>Name</th><th>Email</th><th>Total Orders</th><th>Total Spent</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr><td>CM055</td><td>Ana Reyes</td><td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="c1a0afa081a4aca0a8adefa2aeac">[email&#160;protected]</a></td><td>12</td><td>₱1,540</td><td class="actions-cell"><button class="icon-action"><i class="bi bi-eye"></i></button></td></tr>
              <tr><td>CM032</td><td>Ben Santos</td><td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6d0f08032d08000c0401430e0200">[email&#160;protected]</a></td><td>7</td><td>₱890</td><td class="actions-cell"><button class="icon-action"><i class="bi bi-eye"></i></button></td></tr>
              <tr><td>CM019</td><td>Carla Lim</td><td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="cdaeacbfa1ac8da8a0aca4a1e3aea2a0">[email&#160;protected]</a></td><td>4</td><td>₱420</td><td class="actions-cell"><button class="icon-action"><i class="bi bi-eye"></i></button></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── INVENTORY ─── -->
  <section class="page-section" id="page-inventory">
    <div class="page-heading">
      <h1>Inventory</h1>
      <p>Monitor stock levels and get low-stock alerts.</p>
    </div>
    <div class="bento-grid">
      <div class="bento-card col-12" data-animate>
        <div class="card-title">Stock Levels</div>
        <div class="orders-table-wrap">
          <table class="orders-table">
            <thead>
              <tr><th>Item ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr><td>IM001</td><td>Caramel Latte</td><td>Beverage</td><td>₱95</td><td>24</td><td><span class="status-badge done">OK</span></td></tr>
              <tr><td>IM002</td><td>Matcha Latte</td><td>Beverage</td><td>₱100</td><td>18</td><td><span class="status-badge done">OK</span></td></tr>
              <tr><td>IM003</td><td>Butter Croissant</td><td>Pastry</td><td>₱75</td><td>3</td><td><span class="status-badge new" style="background:#fef3e2;color:#c07a00;">Low</span></td></tr>
              <tr><td>IM004</td><td>Iced Spanish Latte</td><td>Beverage</td><td>₱110</td><td>15</td><td><span class="status-badge done">OK</span></td></tr>
              <tr><td>IM005</td><td>Cheesecake Slice</td><td>Pastry</td><td>₱120</td><td>8</td><td><span class="status-badge done">OK</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── REFUNDS ─── -->
  <section class="page-section" id="page-refunds">
    <div class="page-heading">
      <h1>Refunds</h1>
      <p>All processed refund records.</p>
    </div>
    <div class="bento-grid">
      <div class="bento-card col-12" data-animate>
        <div class="orders-table-wrap">
          <table class="orders-table">
            <thead>
              <tr><th>Refund ID</th><th>Customer</th><th>Order</th><th>Amount</th><th>Date</th><th>Reason</th></tr>
            </thead>
            <tbody>
              <tr><td>MR10</td><td>CM055</td><td>#M001</td><td>₱120.00</td><td>2025-07-04</td><td>Wrong Item</td></tr>
              <tr><td>MR09</td><td>CM019</td><td>#M004</td><td>₱100.00</td><td>2026-02-26</td><td>Wrong Item</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── REPORTS ─── -->
  <section class="page-section" id="page-reports">
    <div class="page-heading">
      <h1>Reports</h1>
      <p>Generate and view sales reports and logs.</p>
    </div>
    <div class="bento-grid">
      <div class="bento-card col-6" data-animate>
        <div class="card-title">Generate New Report</div>
        <div class="form-group"><label>Report ID</label><input type="text" class="form-input-custom" placeholder="e.g. MR059"></div>
        <div class="form-group"><label>Type</label>
          <select class="form-select-custom"><option>Sales</option><option>Inventory</option><option>Refunds</option></select>
        </div>
        <div class="form-group"><label>Date</label><input type="date" class="form-input-custom" value="2026-02-27"></div>
        <div class="form-group"><label>Content / Notes</label><input type="text" class="form-input-custom" placeholder="Daily summary..."></div>
        <button class="action-btn primary">Generate Report</button>
      </div>
      <div class="bento-card col-6" data-animate>
        <div class="card-title">Report Log</div>
        <div class="orders-table-wrap">
          <table class="orders-table">
            <thead><tr><th>Report ID</th><th>Type</th><th>Date</th><th>Log</th></tr></thead>
            <tbody>
              <tr><td>MR058</td><td>Sales</td><td>2025-08-27</td><td>New report added: Sales</td></tr>
              <tr><td>MR057</td><td>Inventory</td><td>2025-08-20</td><td>New report added: Inventory</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── SETTINGS ─── -->
  <section class="page-section" id="page-settings">
    <div class="page-heading">
      <h1>Preferences</h1>
      <p>Manage your account and cafe settings.</p>
    </div>
    <div class="bento-grid">
      <div class="bento-card col-6" data-animate>
        <div class="card-title">Cafe Info</div>
        <div class="form-group"><label>Cafe Name</label><input type="text" class="form-input-custom" value="MANMADE Brew &amp; Co."></div>
        <div class="form-group"><label>Owner Name</label><input type="text" class="form-input-custom" value="Sofia Cruz"></div>
        <div class="form-group"><label>Email</label><input type="email" class="form-input-custom" value="sofia@manmade.ph"></div>
        <button class="action-btn primary">Save Changes</button>
      </div>
      <div class="bento-card col-6" data-animate>
        <div class="card-title">Change Password</div>
        <div class="form-group"><label>Current Password</label><input type="password" class="form-input-custom" placeholder="••••••••"></div>
        <div class="form-group"><label>New Password</label><input type="password" class="form-input-custom" placeholder="••••••••"></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" class="form-input-custom" placeholder="••••••••"></div>
        <button class="action-btn primary">Update Password</button>
      </div>
    </div>
  </section>

</div><!-- end main-wrap -->

<!-- ══════════════════════════════════════════
     MODALS
══════════════════════════════════════════ -->

<!-- Add/Edit Product Modal -->
<div class="modal-overlay" id="modal-product">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modal-product-title">Add Menu Item</h3>
      <button class="modal-close" data-close="modal-product"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="form-group"><label>Item ID</label><input type="text" class="form-input-custom" id="p-id" placeholder="e.g. IM006"></div>
    <div class="form-group"><label>Item Name</label><input type="text" class="form-input-custom" id="p-name" placeholder="e.g. Matcha Latte"></div>
    <div class="form-group"><label>Price (₱)</label><input type="number" class="form-input-custom" id="p-cost" placeholder="0.00" min="0.01" step="0.01"></div>
    <div class="form-group"><label>Description</label><input type="text" class="form-input-custom" id="p-desc" placeholder="Short description"></div>
    <div class="form-group"><label>Category</label>
      <select class="form-select-custom" id="p-cat">
        <option>Beverage</option><option>Pastry</option><option>Food</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="action-btn" data-close="modal-product">Cancel</button>
      <button class="action-btn primary" id="save-product-btn">Save Item</button>
    </div>
  </div>
</div>

<!-- New Order Modal -->
<div class="modal-overlay" id="modal-order">
  <div class="modal-box">
    <div class="modal-header">
      <h3>New Order</h3>
      <button class="modal-close" data-close="modal-order"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="form-group"><label>Order ID</label><input type="text" class="form-input-custom" placeholder="e.g. M007"></div>
    <div class="form-group"><label>Customer ID</label><input type="text" class="form-input-custom" placeholder="e.g. CM055"></div>
    <div class="form-group"><label>Item ID</label><input type="text" class="form-input-custom" placeholder="e.g. IM001"></div>
    <div class="form-group"><label>Discount (optional)</label><input type="text" class="form-input-custom" placeholder="e.g. 10%"></div>
    <div class="form-group"><label>Total (₱)</label><input type="number" class="form-input-custom" placeholder="0.00"></div>
    <div class="modal-footer">
      <button class="action-btn" data-close="modal-order">Cancel</button>
      <button class="action-btn primary">Create Order</button>
    </div>
  </div>
</div>

<!-- Process Payment Modal -->
<div class="modal-overlay" id="modal-payment">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Process Payment</h3>
      <button class="modal-close" data-close="modal-payment"><i class="bi bi-x-lg"></i></button>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);margin-bottom:16px;">Marking order <strong id="pay-order-id"></strong> as Paid.</p>
    <div class="form-group"><label>Payment Method</label>
      <select class="form-select-custom" id="pay-method">
        <option>GCash</option><option>Cash</option><option>Card</option>
      </select>
    </div>
    <div class="form-group"><label>Amount (₱)</label><input type="number" class="form-input-custom" id="pay-amount" placeholder="0.00"></div>
    <div class="modal-footer">
      <button class="action-btn" data-close="modal-payment">Cancel</button>
      <button class="action-btn primary" id="confirm-payment-btn">Confirm Payment</button>
    </div>
  </div>
</div>

<!-- Refund Modal -->
<div class="modal-overlay" id="modal-refund">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Process Refund</h3>
      <button class="modal-close" data-close="modal-refund"><i class="bi bi-x-lg"></i></button>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);margin-bottom:16px;">Refunding order <strong id="refund-order-id"></strong>.</p>
    <div class="form-group"><label>Refund ID</label><input type="text" class="form-input-custom" placeholder="e.g. MR011"></div>
    <div class="form-group"><label>Refund Amount (₱)</label><input type="number" class="form-input-custom" placeholder="0.00"></div>
    <div class="form-group"><label>Reason</label>
      <select class="form-select-custom">
        <option>Wrong Item</option><option>Quality Issue</option><option>Duplicate Order</option><option>Other</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="action-btn" data-close="modal-refund">Cancel</button>
      <button class="action-btn danger">Process Refund</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script src="../js/dashboard.js"></script>
</body>
</html>