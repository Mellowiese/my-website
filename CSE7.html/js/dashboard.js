/**
 * MANMADE Dashboard — dashboard.js
 */

'use strict';

// ══════════════════════════════════════════
// UTILITIES
// ══════════════════════════════════════════

async function apiCall(action, payload = null) {
  try {
    const res = await fetch(`/CSE7.html/php/api.php?action=${action}`, {
      method:  payload ? 'POST' : 'GET',
      headers: { 'Content-Type': 'application/json' },
      body:    payload ? JSON.stringify(payload) : null,
    });
    const data = await res.json();
    if (data.error) { toast(`⚠️ ${data.error}`); return null; }
    return data;
  } catch (err) {
    toast('⚠️ Connection error. Check your server.');
    return null;
  }
}

function toast(msg, duration = 2800) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), duration);
}

function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }

// ══════════════════════════════════════════
// INIT
// ══════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {

  const pages         = document.querySelectorAll('.page-section');
  const navItems      = document.querySelectorAll('.nav-item[data-page]');
  const sidebar       = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const notifBtn      = document.getElementById('notif-btn');
  const notifPanel    = document.getElementById('notif-panel');

  // ── NAVIGATION ──

  function navigateTo(pageId) {
    pages.forEach(p => p.classList.remove('active'));
    navItems.forEach(n => n.classList.remove('active'));

    const target = document.getElementById(`page-${pageId}`);
    if (!target) return;
    target.classList.add('active');

    const navItem = document.querySelector(`.nav-item[data-page="${pageId}"]`);
    if (navItem) navItem.classList.add('active');

    loadPageData(pageId);

    if (window.innerWidth <= 768) sidebar.classList.remove('mobile-open');
  }

  navItems.forEach(item => {
    item.addEventListener('click', e => { e.preventDefault(); navigateTo(item.dataset.page); });
  });

  document.querySelectorAll('.see-all-link[data-page]').forEach(link => {
    link.addEventListener('click', e => { e.preventDefault(); navigateTo(link.dataset.page); });
  });

  document.querySelectorAll('.action-btn[data-page]').forEach(btn => {
    btn.addEventListener('click', () => navigateTo(btn.dataset.page));
  });

  // ── SIDEBAR TOGGLE ──

  sidebarToggle.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
      sidebar.classList.toggle('mobile-open');
    } else {
      sidebar.classList.toggle('collapsed');
      document.querySelector('.main-wrap').style.marginLeft =
        sidebar.classList.contains('collapsed') ? '0' : 'var(--sidebar-w)';
    }
  });

  document.addEventListener('click', e => {
    if (window.innerWidth <= 768 &&
        sidebar.classList.contains('mobile-open') &&
        !sidebar.contains(e.target) &&
        e.target !== sidebarToggle) {
      sidebar.classList.remove('mobile-open');
    }
  });

  // ── NOTIFICATION PANEL ──

  notifBtn.addEventListener('click', e => {
    e.stopPropagation();
    notifPanel.classList.toggle('open');
  });

  document.addEventListener('click', e => {
    if (!notifPanel.contains(e.target) && e.target !== notifBtn) {
      notifPanel.classList.remove('open');
    }
  });

  // ── MODALS ──

  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.dataset.close));
  });

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(overlay.id); });
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
      notifPanel.classList.remove('open');
    }
  });

  // ── PRODUCT CRUD ──

  let editingProductId = null;

  document.getElementById('open-add-product')?.addEventListener('click', () => {
    editingProductId = null;
    document.getElementById('modal-product-title').textContent = 'Add Menu Item';
    ['p-id','p-name','p-cost','p-desc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('p-cat').value = 'Beverage';
    document.getElementById('p-id').disabled = false;
    openModal('modal-product');
  });

  function openEditProduct(card) {
    editingProductId = card.dataset.id || null;
    document.getElementById('modal-product-title').textContent = 'Edit Menu Item';
    document.getElementById('p-id').value   = card.dataset.id || '';
    document.getElementById('p-name').value = card.querySelector('.product-name').textContent;
    document.getElementById('p-cost').value = parseFloat(card.querySelector('.product-price').textContent.replace('₱',''));
    document.getElementById('p-desc').value = '';
    document.getElementById('p-cat').value  = card.dataset.category || 'Beverage';
    document.getElementById('p-id').disabled = true;
    openModal('modal-product');
  }

  document.getElementById('save-product-btn')?.addEventListener('click', async () => {
    const id   = document.getElementById('p-id').value.trim();
    const name = document.getElementById('p-name').value.trim();
    const cost = parseFloat(document.getElementById('p-cost').value);
    const desc = document.getElementById('p-desc').value.trim();
    const cat  = document.getElementById('p-cat').value;

    if (!id || !name || !cost || cost <= 0) {
      toast('⚠️ Please fill in all fields with a valid price.');
      return;
    }

    await apiCall(editingProductId ? 'update_product' : 'add_product', { id, name, cost, desc, cat });
    toast(`✅ "${name}" ${editingProductId ? 'updated' : 'added'} successfully.`);
    closeModal('modal-product');
  });

  document.getElementById('product-grid')?.addEventListener('click', e => {
    const card = e.target.closest('.product-card');
    if (!card) return;
    if (e.target.closest('.edit-btn')) openEditProduct(card);
    if (e.target.closest('.delete-btn')) {
      const name = card.querySelector('.product-name').textContent;
      if (confirm(`Delete "${name}" from the menu?`)) {
        apiCall('delete_product', { id: card.dataset.id });
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';
        setTimeout(() => card.remove(), 300);
        toast(`🗑️ "${name}" removed.`);
      }
    }
  });

  // ── MENU FILTERS & SEARCH ──

  document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.filter-bar').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      document.querySelectorAll('.product-card').forEach(card => {
        card.classList.toggle('hidden', filter !== 'all' && card.dataset.category !== filter);
      });
    });
  });

  document.getElementById('menu-search')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      card.classList.toggle('hidden', !card.querySelector('.product-name').textContent.toLowerCase().includes(q));
    });
  });

  // ── ORDER ACTIONS ──

  document.getElementById('open-add-order')?.addEventListener('click', () => openModal('modal-order'));

  document.getElementById('orders-tbody')?.addEventListener('click', e => {
    const row = e.target.closest('tr');
    if (!row) return;
    const orderId = row.querySelector('.order-id-cell')?.textContent?.replace('#','').trim();

    if (e.target.closest('.pay-btn')) {
      document.getElementById('pay-order-id').textContent = `#${orderId}`;
      document.getElementById('pay-amount').value = '';
      document.getElementById('confirm-payment-btn').dataset.orderId = orderId;
      openModal('modal-payment');
    }

    if (e.target.closest('.refund-btn')) {
      document.getElementById('refund-order-id').textContent = `#${orderId}`;
      openModal('modal-refund');
    }
  });

  document.getElementById('confirm-payment-btn')?.addEventListener('click', async function () {
    const orderId = this.dataset.orderId;
    const method  = document.getElementById('pay-method').value;
    const amount  = parseFloat(document.getElementById('pay-amount').value);
    if (!amount || amount <= 0) { toast('⚠️ Enter a valid payment amount.'); return; }
    await apiCall('process_payment', { orderId, method, amount });
    updateOrderBadge(orderId, 'Paid');
    closeModal('modal-payment');
    toast(`✅ Order #${orderId} marked as Paid via ${method}.`);
  });

  function updateOrderBadge(orderId, status) {
    document.querySelectorAll('#orders-tbody tr').forEach(row => {
      const cell = row.querySelector('.order-id-cell');
      if (!cell || cell.textContent.replace('#','').trim() !== orderId) return;
      const badge = row.querySelector('.status-badge');
      if (badge) {
        badge.className = `status-badge ${status === 'Paid' ? 'done' : status === 'Unpaid' ? 'new' : 'refunded'}`;
        badge.textContent = status;
      }
      if (status === 'Paid') row.querySelector('.pay-btn')?.remove();
    });
  }

  // ── ORDER TABLE FILTERS ──

  document.querySelectorAll('.filter-btn[data-order-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.filter-bar').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.orderFilter;
      document.querySelectorAll('#orders-tbody tr').forEach(row => {
        const badge = row.querySelector('.status-badge');
        row.style.display = (!badge || filter === 'all' || badge.textContent.trim() === filter) ? '' : 'none';
      });
    });
  });

  // ── CHART ANIMATIONS ──

  function animateChartBars(section) {
    section.querySelectorAll('.chart-bar, .hour-bar').forEach((bar, i) => {
      const h = bar.style.getPropertyValue('--h') || bar.style.height || '0%';
      bar.style.height = '0%';
      setTimeout(() => { bar.style.height = h; }, 100 + i * 50);
    });
    section.querySelectorAll('.mini-prog-fill, .pay-bar div, .pending-bar-fill').forEach((el, i) => {
      const w = el.style.width;
      el.style.width = '0%';
      setTimeout(() => { el.style.width = w; }, 200 + i * 60);
    });
  }

  // ── LOAD PAGE DATA ──

  function loadPageData(pageId) {
    if (pageId === 'overview' || pageId === 'sales') {
      animateChartBars(document.getElementById(`page-${pageId}`));
    }
    switch(pageId) {
      case 'overview':  loadOverview();   break;
      case 'orders':    loadOrders();     break;
      case 'menu':      loadMenu();       break;
      case 'customers': loadCustomers();  break;
      case 'refunds':   loadRefunds();    break;
      case 'reports':   loadReports();    break;
      case 'sales':     loadSales();      break;
    }
  }

  // ── OVERVIEW ──
  async function loadOverview() {
    const data = await apiCall('overview_stats');
    if (!data) return;

    // Stats
    document.querySelector('#stat-revenue .stat-value').textContent  = '₱' + data.revenue.toLocaleString();
    document.querySelector('#stat-orders .stat-value').textContent   = data.orders_today;
    document.querySelector('#stat-avg .stat-value').textContent      = '₱' + data.avg_value;
    document.querySelector('#stat-pending .stat-value').textContent  = data.pending;
    updatePendingBadge(data.pending);

    // Live orders
    const container = document.querySelector('.live-orders-list');
    if (container && data.live_orders.length) {
      container.innerHTML = data.live_orders.map(o => `
        <div class="order-item">
          <div class="order-num">#${o.Order_ID}</div>
          <div class="order-items-txt">${o.Item_Name ?? '—'}</div>
          <div class="order-right">
            <div class="order-price">₱${parseFloat(o.Order_TotalAmount).toLocaleString()}</div>
            <span class="status-badge ${statusClass(o.Payment_Status)}">${o.Payment_Status}</span>
          </div>
        </div>
      `).join('');
    }
  }

  // ── ORDERS ──
  async function loadOrders() {
    const data = await apiCall('get_orders');
    if (!data) return;
    const tbody = document.getElementById('orders-tbody');
    if (!tbody) return;
    tbody.innerHTML = data.orders.map(o => `
      <tr>
        <td class="order-id-cell">#${o.Order_ID}</td>
        <td>${o.Customer_ID}</td>
        <td>${o.Item_Name ?? '—'}</td>
        <td>₱${parseFloat(o.Order_TotalAmount).toFixed(2)}</td>
        <td>${o.Order_Date}</td>
        <td><span class="status-badge ${statusClass(o.Payment_Status)}">${o.Payment_Status}</span></td>
        <td class="actions-cell">
          ${o.Payment_Status === 'Unpaid' ? `<button class="icon-action pay-btn" title="Mark Paid"><i class="bi bi-check-circle"></i></button>` : ''}
          ${o.Payment_Status !== 'Refunded' ? `<button class="icon-action refund-btn" title="Refund"><i class="bi bi-arrow-counterclockwise"></i></button>` : ''}
        </td>
      </tr>
    `).join('');
  }

  // ── MENU ──
  async function loadMenu() {
    const data = await apiCall('get_products');
    if (!data) return;
    const grid = document.getElementById('product-grid');
    if (!grid) return;
    grid.innerHTML = data.items.map(item => `
      <div class="product-card" data-id="${item.Item_ID}" data-category="${item.Item_Category}">
        <div class="product-emoji">${categoryEmoji(item.Item_Category)}</div>
        <div class="product-body">
          <div class="product-name">${item.Item_Name}</div>
          <div class="product-cat">${item.Item_Category}</div>
          <div class="product-price">₱${parseFloat(item.Item_Cost).toFixed(2)}</div>
          <div class="product-stock ${item.Item_Stock <= 5 ? 'low' : ''}">
            Status: <strong>${item.Item_Status}</strong>
          </div>
        </div>
        <div class="product-actions">
          <button class="icon-action edit-btn" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="icon-action delete-btn" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </div>
    `).join('');
  }

  // ── CUSTOMERS ──
  async function loadCustomers() {
    const data = await apiCall('get_customers');
    if (!data) return;
    const tbody = document.querySelector('#page-customers tbody');
    if (!tbody) return;
    tbody.innerHTML = data.customers.map(c => `
      <tr>
        <td>${c.Customer_ID}</td>
        <td>${c.Customer_Name}</td>
        <td>${c.Customer_Email}</td>
        <td>${c.total_orders}</td>
        <td>₱${parseFloat(c.total_spent).toFixed(2)}</td>
        <td class="actions-cell"><button class="icon-action"><i class="bi bi-eye"></i></button></td>
      </tr>
    `).join('');
  }

  // ── REFUNDS ──
  async function loadRefunds() {
    const data = await apiCall('get_refunds');
    if (!data) return;
    const tbody = document.querySelector('#page-refunds tbody');
    if (!tbody) return;
    tbody.innerHTML = data.refunds.map(r => `
      <tr>
        <td>${r.Refund_ID}</td>
        <td>${r.Customer_ID}</td>
        <td>#${r.Order_ID}</td>
        <td>₱${parseFloat(r.Refund_Amount).toFixed(2)}</td>
        <td>${r.Refund_Date}</td>
        <td>${r.Refund_Reason}</td>
      </tr>
    `).join('');
  }

  // ── REPORTS ──
  async function loadReports() {
    const data = await apiCall('get_reports');
    if (!data) return;
    const tbody = document.querySelector('#page-reports .orders-table tbody');
    if (!tbody) return;
    tbody.innerHTML = data.reports.map(r => `
      <tr>
        <td>${r.Report_ID}</td>
        <td>${r.Report_Type}</td>
        <td>${r.Report_Date}</td>
        <td>${r.Report_Content}</td>
      </tr>
    `).join('');
  }

  // ── SALES ──
  async function loadSales() {
    const data = await apiCall('get_sales');
    if (!data) return;
    // Payment methods
    const list = document.querySelector('.pay-method-list');
    if (list && data.methods.length) {
      const total = data.methods.reduce((s, m) => s + parseFloat(m.total), 0);
      list.innerHTML = data.methods.map(m => {
        const pct = total > 0 ? Math.round((m.total / total) * 100) : 0;
        return `
          <div class="pay-method-item">
            <div class="pay-icon ${m.Payment_Method.toLowerCase()}"><i class="bi bi-cash"></i></div>
            <div class="pay-info"><span>${m.Payment_Method}</span>
              <div class="pay-bar"><div style="width:${pct}%;background:var(--brown)"></div></div>
            </div>
            <div class="pay-pct">${pct}%</div>
          </div>`;
      }).join('');
    }
  }

  // ── HELPERS ──
  function statusClass(status) {
    const map = { 'Unpaid': 'new', 'Paid': 'done', 'Refunded': 'refunded', 'Pending': 'prep' };
    return map[status] || 'new';
  }

  function categoryEmoji(cat) {
    const map = { 'Beverage': '☕', 'Pastry': '🥐', 'Food': '🍽️' };
    return map[cat] || '🍴';
  }

  // ── TOPBAR CLOCK ──

  function updateTopbar() {
    const now   = new Date();
    const hour  = now.getHours();
    const greet = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';
    const emoji = hour < 12 ? '☀️' : hour < 17 ? '🌤️' : '🌙';
    const dateStr = now.toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    const titleEl = document.getElementById('topbar-title');
    const staffName = titleEl ? titleEl.textContent.replace(/^(Good \w+), (.+?) [☀️🌤️🌙].*$/, '$2') : 'Staff';
    if (titleEl) titleEl.textContent = `${greet}, ${staffName} ${emoji}`;
    if (subEl)   subEl.textContent   = `${dateStr} — Brew & Co. is open`;
  }

  updateTopbar();
  setInterval(updateTopbar, 60_000);

  // ── INITIAL LOAD ──
  animateChartBars(document.getElementById('page-overview'));
  loadOverview();

});