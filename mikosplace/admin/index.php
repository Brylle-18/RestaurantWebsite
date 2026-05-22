<?php
    // admin/index.php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/auth.php';
    requireAdmin();
    $adminName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
    $csrfToken = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Miko's Place</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/mikosplace/assets/admin-styles.css">
</head>
<body>
<div class="shell">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="logo-panel">
      <img src="../assets/mikosplace.jpg" alt="Miko's Place" class="brand-logo"
           onerror="this.style.display='none'">
      <p class="eyebrow">Seafoods, Grill &amp; Catering</p>
      <h1>Miko's Place</h1>
      <p>Admin Dashboard</p>
    </div>

    <nav id="nav">
      <button class="nav-item active" onclick="show('dashboard',this)"><span class="icon">📊</span> Overview</button>
      <button class="nav-item" onclick="show('bookings',this)"><span class="icon">📋</span> Bookings</button>
      <button class="nav-item" onclick="show('menu',this)"><span class="icon">🍽</span> Menu</button>
      <button class="nav-item" onclick="show('venues',this)"><span class="icon">🏛</span> Venues</button>
      <button class="nav-item" onclick="show('staff',this)"><span class="icon">👥</span> Team</button>
      <button class="nav-item" onclick="show('reports',this)"><span class="icon">📈</span> Reports</button>
      <button class="nav-item" onclick="show('financials',this)"><span class="icon">💰</span> Financials</button>
    </nav>

    <div class="user-info">
      <div class="user-avatar"><?php echo substr($adminName, 0, 2) ?></div>
      <div>
        <p class="user-name"><?php echo $adminName ?></p>
        <p class="user-role">Operations Admin</p>
      </div>
      <a href="logout.php" class="logout-btn">Sign out</a>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main">
    <header class="header">
      <div>
        <p class="header-tag">Miko's Place Operations</p>
        <h2 id="page-title">Overview</h2>
      </div>
      <div class="header-actions">
        <a href="../customer/index.php" target="_blank" class="btn btn-ghost btn-sm">Customer View →</a>
      </div>
    </header>

    <!--  DASHBOARD  -->
    <section class="section active" id="sec-dashboard">
      <div class="hero">
        <div class="hero-copy">
          <p class="eyebrow-white">Bamboo-inspired hospitality</p>
          <h3>Welcome back, <?php echo $adminName ?>!</h3>
          <p>Here's what's happening at Miko's Place today.</p>
        </div>
        <div class="hero-highlight">
          <span>Target Revenue</span>
          <strong>₱100,000</strong>
          <p>Monthly Goal</p>
        </div>
      </div>

      <div class="stats-grid" id="stats-grid">
        <!-- Stats populated by JS -->
      </div>

      <div class="two-col">
        <div class="card">
          <div class="section-header">
            <h3 style="font-size:18px;">Revenue Overview</h3>
            <span class="badge info">Last 7 Days</span>
          </div>
          <div id="revenue-chart" class="chart-container">
            <!-- Chart populated by JS -->
          </div>
        </div>
        <div class="card">
          <div class="section-header">
            <h3 style="font-size:18px;">Recent Activity</h3>
          </div>
          <div class="activity-list" id="recent-activity">
            <!-- Activity populated by JS -->
          </div>
        </div>
        <div class="card full-width">
          <div class="section-header">
            <h3 style="font-size:18px;">Recent Bookings</h3>
            <button class="btn btn-ghost btn-sm" onclick="show('bookings', document.querySelector('[onclick*=\"bookings\"]'))">View All</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Ticket</th><th>Customer</th><th>Service</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
              <tbody id="recent-bookings"><tr class="loading-row"><td colspan="6"><span class="spinner"></span> Loading...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!--  BOOKINGS  -->
    <section class="section" id="sec-bookings">
      <div class="section-header">
        <h3>Bookings Management</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-booking-add')">+ New Booking</button>
      </div>
      
      <div class="filter-chips">
        <div class="filter-chip active" onclick="setBookingFilter('', this)">All Bookings</div>
        <div class="filter-chip" onclick="setBookingFilter('pending', this)">Pending Review</div>
        <div class="filter-chip" onclick="setBookingFilter('confirmed', this)">Confirmed</div>
        <div class="filter-chip" onclick="setBookingFilter('today', this)">Today's Events</div>
      </div>

      <div class="search-bar">
        <input class="search-input" id="booking-q" placeholder="Search by ticket, name or email..." oninput="currentBookingPage=1; loadBookings()">
        <select class="search-select" id="booking-status" onchange="currentBookingPage=1; loadBookings()">
          <option value="">Status: All</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <div class="card">
        <div class="table-wrap">
          <table>
            <thead><tr><th>Ticket</th><th>Customer</th><th>Service</th><th>Event Schedule</th><th>Pax</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="bookings-tbody"><tr class="loading-row"><td colspan="8"><span class="spinner"></span></td></tr></tbody>
          </table>
        </div>
        <div class="pagination-controls" id="pagination-controls" style="display:none; text-align:center; padding:20px; gap:10px; justify-content:center; align-items:center;">
          <button class="btn btn-ghost btn-sm" id="prev-btn" onclick="prevPage()">← Previous</button>
          <span id="page-info" style="min-width:100px; font-weight:500;"></span>
          <button class="btn btn-ghost btn-sm" id="next-btn" onclick="nextPage()">Next →</button>
        </div>
      </div>
    </section>

    <!--  FINANCIALS  -->
    <section class="section" id="sec-financials">
      <div class="section-header">
        <h3>Financials & Rates</h3>
      </div>
      <div class="two-col">
        <div class="card full-width">
          <h4 style="margin-bottom:16px;font-family:'Playfair Display',serif">Net Revenue Snapshot</h4>
          <div id="financial-summary-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px">
            <p style="color:var(--muted)"><span class="spinner"></span> Loading financial summary...</p>
          </div>
        </div>
        <div class="card">
          <h4 style="margin-bottom:16px;font-family:'Playfair Display',serif">Venue Rates</h4>
          <div id="financial-venues">
            <!-- Venue rates populated by JS -->
          </div>
        </div>
        <div class="card">
          <h4 style="margin-bottom:16px;font-family:'Playfair Display',serif">Top Menu Prices</h4>
          <div id="financial-menu">
            <!-- Menu prices populated by JS -->
          </div>
        </div>
        <div class="card">
          <h4 style="margin-bottom:16px;font-family:'Playfair Display',serif">Expense Breakdown</h4>
          <div id="financial-expense-pie"></div>
        </div>
        <div class="card">
          <h4 style="margin-bottom:16px;font-family:'Playfair Display',serif">Staff Cost Estimate</h4>
          <div id="financial-staff-costs"></div>
        </div>
        <div class="card full-width">
          <h4 style="margin-bottom:16px;font-family:'Playfair Display',serif">Revenue Insights</h4>
          <div id="monthly-trend-chart" class="chart-container" style="height:240px">
            <!-- Monthly trend chart populated by JS -->
          </div>
        </div>
      </div>
    </section>

    <!--  MENU  -->
    <section class="section" id="sec-menu">
      <div class="section-header">
        <h3>Menu Items</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-menu-add')">+ Add Dish</button>
      </div>
      <div class="search-bar">
        <input class="search-input" id="menu-q" placeholder="Search dishes..." oninput="loadMenu()">
        <select class="search-select" id="menu-cat" onchange="loadMenu()">
          <option value="">All Categories</option>
          <option value="restaurant">Restaurant</option>
          <option value="catering">Catering</option>
          <option value="cafe">Cafe</option>
          <option value="pastry">Pastry</option>
        </select>
      </div>
      <div class="card">
        <div class="table-wrap">
          <table>
            <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Description</th><th>Available</th><th>Actions</th></tr></thead>
            <tbody id="menu-tbody"><tr class="loading-row"><td colspan="7"><span class="spinner"></span></td></tr></tbody>
          </table>
        </div>
      </div>
    </section>

    <!--  VENUES  -->
    <section class="section" id="sec-venues">
      <div class="section-header"><h3>Venue Packages</h3></div>
      <div class="venues-grid" id="venues-grid">
        <div style="color:var(--muted);padding:20px"><span class="spinner"></span> Loading...</div>
      </div>
    </section>

    <!--  STAFF  -->
    <section class="section" id="sec-staff">
      <div class="section-header">
        <h3>Team Coverage</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-staff-add')">+ Add Staff</button>
      </div>
      <div class="search-bar">
        <input class="search-input" id="staff-q" placeholder="Search by name or role..." oninput="loadStaff()">
      </div>
      <div class="card">
        <div class="table-wrap">
          <table>
            <thead><tr><th>Name</th><th>Role</th><th>Assignment</th><th>Shift</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="staff-tbody"><tr class="loading-row"><td colspan="6"><span class="spinner"></span></td></tr></tbody>
          </table>
        </div>
      </div>
    </section>

    <!--  REPORTS  -->
    <section class="section" id="sec-reports">
      <div class="section-header"><h3>Reports &amp; Insights</h3></div>
      
      <div class="filter-chips">
        <div class="filter-chip active" onclick="loadReports()">Overview</div>
        <div class="filter-chip" onclick="loadSalesReport()">Sales Report with Discounts</div>
      </div>

      <div class="reports-grid" id="reports-grid">
        <div class="card"><p style="color:var(--muted)"><span class="spinner"></span> Loading...</p></div>
      </div>
    </section>
  </main>
</div>

<!--  MODALS  -->
<!-- Add Booking Modal -->
<div class="modal-backdrop" id="modal-booking-add">
  <div class="modal">
    <h3>New Booking</h3><p class="sub">Create a customer reservation or order</p>
    <div class="form-grid">
      <div class="form-row">
        <div class="field"><label>Customer Name</label><input id="b-name" placeholder="Full name"></div>
        <div class="field"><label>Phone</label><input id="b-phone" placeholder="09XXXXXXXXX"></div>
      </div>
      <div class="field"><label>Email</label><input id="b-email" type="email" placeholder="email@example.com"></div>
      <div class="form-row">
        <div class="field"><label>Service Type</label>
          <select id="b-service">
            <option value="restaurant">Restaurant</option>
            <option value="catering">Catering</option>
            <option value="cafe">Cafe</option>
            <option value="venue">Venue</option>
          </select>
        </div>
        <div class="field"><label>Pax</label><input id="b-pax" type="number" min="1" value="2"></div>
      </div>
      <div class="form-row">
        <div class="field"><label>Event Date</label><input id="b-date" type="date"></div>
        <div class="field"><label>Event Time</label><input id="b-time" type="time"></div>
        <div class="field"><label>Amount (₱)</label><input id="b-amount" type="number" min="0" step="0.01" placeholder="0.00"></div>
      </div>
      <div class="field"><label>Notes / Details</label><textarea id="b-notes" placeholder="Special requests, menu preferences..."></textarea></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modal-booking-add')">Cancel</button>
      <button class="btn btn-green btn-sm" onclick="addBooking()">Save Booking</button>
    </div>
  </div>
</div>

<!-- Add Menu Modal -->
<div class="modal-backdrop" id="modal-menu-add">
  <div class="modal">
    <h3>Add Menu Item</h3><p class="sub">Add a new dish or beverage</p>
    <div class="form-grid">
      <div class="field"><label>Dish Name</label><input id="m-name" placeholder="e.g. Buttered Chicken"></div>
      <div class="form-row">
        <div class="field"><label>Category</label>
          <select id="m-cat">
            <option value="restaurant">Restaurant</option>
            <option value="catering">Catering</option>
            <option value="cafe">Cafe</option>
            <option value="pastry">Pastry</option>
          </select>
        </div>
        <div class="field"><label>Price (₱)</label><input id="m-price" type="number" min="0" step="0.01" placeholder="0.00"></div>
      </div>
      <div class="field"><label>Description</label><textarea id="m-desc" placeholder="Short description..."></textarea></div>
      <div class="field"><label>Image Filename</label><input id="m-image" placeholder="e.g. adobo.jpg (saved in assets/dishes/)"></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modal-menu-add')">Cancel</button>
      <button class="btn btn-green btn-sm" onclick="addMenuItem()">Add Item</button>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="modal-booking-review">
  <div class="modal modal-wide">
    <h3>Review & Finalize Booking</h3>
    <p class="sub">Review the customer's selections below. You must set a <strong>Final Price</strong> before confirming.</p>
    
    <div class="review-shell">
      <div class="review-summary" id="booking-review-summary"></div>
      
      <div class="review-grid">
        <div class="card-lite">
          <p class="mini-label">🍽 Menu Selections</p>
          <div id="booking-review-items" class="review-list"></div>
        </div>
        <div class="card-lite">
          <p class="mini-label">✨ Event Add-ons</p>
          <div id="booking-review-addons" class="review-list"></div>
        </div>
      </div>

      <div class="card" style="background:var(--surface-soft); border: 1px dashed var(--green);">
        <h4 style="font-size:14px; margin-bottom:12px; color:var(--green-dk)">Administrative Actions</h4>
        <div class="form-grid">
          <input id="review-booking-id" type="hidden">
          <div class="form-row">
            <div class="field">
              <label>Booking Status</label>
              <select id="review-status">
                <option value="pending">⏳ Pending (Awaiting Review)</option>
                <option value="confirmed">✅ Confirmed (Paid/Reserved)</option>
                <option value="in_progress">🔄 In Progress (Ongoing)</option>
                <option value="completed">🏁 Completed (Done)</option>
                <option value="cancelled">❌ Cancelled</option>
              </select>
              <small style="color:var(--muted); font-size:11px; margin-top:4px; display:block;">Change status to track the booking lifecycle.</small>
            </div>
            <div class="field">
              <label>Final Price (₱)</label>
              <input id="review-amount" type="number" min="0" step="0.01" placeholder="0.00" onchange="calculateDiscountedPrice()">
              <small style="color:var(--muted); font-size:11px; margin-top:4px; display:block;">Enter total amount including all items & fees.</small>
            </div>
            <div class="field">
              <label>Discount (%)</label>
              <input id="review-discount" type="number" min="0" max="100" step="0.01" placeholder="0.00" value="0" onchange="calculateDiscountedPrice()">
              <small style="color:var(--muted); font-size:11px; margin-top:4px; display:block;">Enter discount percentage (10%, 20%, etc.)</small>
            </div>
            <div class="field">
              <label>Calculated Final Amount (₱)</label>
              <input id="review-final-amount" type="number" min="0" step="0.01" readonly style="background:var(--bg);">
              <small style="color:var(--green); font-size:11px; margin-top:4px; display:block; font-weight:600;">Auto-calculated after discount</small>
            </div>
          </div>
          <div class="form-row">
            <div class="field">
              <label>Pricing Notes (Customer sees this)</label>
              <textarea id="review-pricing-notes" placeholder="e.g. Discount applied for early booking..."></textarea>
            </div>
            <div class="field">
              <label>Internal Staff Notes (Private)</label>
              <textarea id="review-notes" placeholder="e.g. Customer prefers window seating..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modal-booking-review')">Close</button>
      <button class="btn btn-green btn-sm" onclick="saveBookingReview()">Save Changes</button>
    </div>
  </div>
</div>

<!-- Add Staff Modal -->
<div class="modal-backdrop" id="modal-staff-add">
  <div class="modal">
    <h3>Add Team Member</h3><p class="sub">Register a new staff record</p>
    <div class="form-grid">
      <div class="form-row">
        <div class="field"><label>Full Name</label><input id="s-name" placeholder="Full name"></div>
        <div class="field"><label>Role</label><input id="s-role" placeholder="e.g. Head Chef"></div>
      </div>
      <div class="field"><label>Assignment</label><input id="s-assign" placeholder="e.g. Main Kitchen"></div>
      <div class="form-row">
        <div class="field"><label>Shift Start</label><input id="s-start" type="time" value="08:00"></div>
        <div class="field"><label>Shift End</label><input id="s-end" type="time" value="17:00"></div>
      </div>
      <div class="field"><label>Status</label>
        <select id="s-status">
          <option value="on_duty">On Duty</option>
          <option value="off_duty">Off Duty</option>
          <option value="prepping">Prepping</option>
          <option value="on_leave">On Leave</option>
        </select>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modal-staff-add')">Cancel</button>
      <button class="btn btn-green btn-sm" onclick="addStaff()">Save</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
const API = 'api.php';
const CSRF_TOKEN = <?php echo json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
let currentBookingReview = null;

//  Section navigation 
function show(id, btn) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('sec-' + id).classList.add('active');
  if (btn) btn.classList.add('active');
  const titles = {dashboard:'Overview',bookings:'Bookings Management',menu:'Menu Items',venues:'Venue Packages',staff:'Team Coverage',reports:'Detailed Reports',financials:'Financials & Rates'};
  document.getElementById('page-title').textContent = titles[id] || id;
  if (id === 'dashboard') loadDashboard();
  if (id === 'bookings') loadBookings();
  if (id === 'menu') loadMenu();
  if (id === 'venues') loadVenues();
  if (id === 'staff') loadStaff();
  if (id === 'reports') loadReports();
  if (id === 'financials') loadFinancials();
}

// Chart rendering helper
function renderChart(containerId, data, maxVal) {
  const container = document.getElementById(containerId);
  if (!data || !data.length) {
    container.innerHTML = '<p style="color:var(--muted);margin:auto">No trend data available yet.</p>';
    return;
  }
  const max = maxVal || Math.max(...data.map(d => parseFloat(d.total))) || 1;
  container.innerHTML = data.map(d => {
    const height = (parseFloat(d.total) / max) * 100;
    const label = d.date ? new Date(d.date).toLocaleDateString('en-PH', {weekday:'short'}) : d.month;
    return `
      <div class="chart-bar-wrapper">
        <div class="chart-bar" style="height:${height}%" data-value="${fmt(d.total)}"></div>
        <span class="chart-label">${label}</span>
      </div>`;
  }).join('');
}

//  API helpers 
async function api(params, method = 'GET') {
  try {
    const payload = new URLSearchParams(params);
    if (method !== 'GET') {
      payload.set('csrf_token', CSRF_TOKEN);
    }
    const opts = method === 'GET'
      ? { method: 'GET' }
      : { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: payload };
    const url = method === 'GET' ? API + '?' + new URLSearchParams(params) : API;
    const r = await fetch(url, opts);
    return await r.json();
  } catch(e) { toast('Network error', true); return null; }
}

//  Toast  //
function toast(msg, err = false) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.className = 'toast show ' + (err ? 'err' : 'ok');
  setTimeout(() => t.className = 'toast', 3000);
}

//  Modal helpers  //
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(m => m.addEventListener('click', e => { if(e.target === m) m.classList.remove('open'); }));

//  Status badge helper  //
function badge(status) {
  const map = {
    pending:'info', confirmed:'success', in_progress:'info',
    completed:'success', cancelled:'warning', on_duty:'success',
    off_duty:'grey', prepping:'info', on_leave:'warning'
  };
  const label = status.replace('_',' ').replace(/\b\w/g, c=>c.toUpperCase());
  return `<span class="badge ${map[status]||'grey'}">${label}</span>`;
}
function fmt(n) { return '₱' + parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2}); }
function pct(n) { return `${parseFloat(n || 0).toFixed(1)}%`; }
function esc(s) {
  if (s === null || s === undefined) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function formatBookingSchedule(dateValue, timeValue, dateStyle = 'medium') {
  if (!dateValue && !timeValue) {
    return '-';
  }

  const parts = [];

  if (dateValue) {
    const [year, month, day] = String(dateValue).split('-').map(Number);
    if (year && month && day) {
      const localDate = new Date(year, month - 1, day);
      parts.push(localDate.toLocaleDateString('en-PH', { dateStyle }));
    } else {
      parts.push(String(dateValue));
    }
  }

  if (timeValue) {
    const localTime = new Date(`2000-01-01T${String(timeValue).slice(0, 8)}`);
    if (!Number.isNaN(localTime.getTime())) {
      parts.push(localTime.toLocaleTimeString('en-PH', { timeStyle: 'short' }));
    } else {
      parts.push(String(timeValue));
    }
  }

  return parts.join(' at ');
}

function renderPieChart(containerId, items, centerPrimary, centerSecondary) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const total = (items || []).reduce((sum, item) => sum + Number(item.value || 0), 0);
  if (!items || !items.length || total <= 0) {
    container.innerHTML = '<p style="color:var(--muted);font-size:13px">No chart data available yet.</p>';
    return;
  }

  let current = 0;
  const gradient = items.map((item) => {
    const value = Number(item.value || 0);
    const start = (current / total) * 360;
    current += value;
    const end = (current / total) * 360;
    return `${item.color} ${start}deg ${end}deg`;
  }).join(', ');

  container.innerHTML = `
    <div style="display:grid;grid-template-columns:minmax(180px,220px) 1fr;gap:18px;align-items:center">
      <div style="width:210px;height:210px;margin:0 auto;border-radius:50%;background:conic-gradient(${gradient});position:relative;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08)">
        <div style="position:absolute;inset:28px;border-radius:50%;background:var(--panel);display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:18px">
          <strong style="font-size:24px;line-height:1.1">${esc(centerPrimary)}</strong>
          <span style="font-size:12px;color:var(--muted);margin-top:6px">${esc(centerSecondary)}</span>
        </div>
      </div>
      <div style="display:grid;gap:10px">
        ${items.map((item) => `
          <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:10px 12px;background:var(--surface-soft);border:1px solid var(--border);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
              <span style="width:12px;height:12px;border-radius:999px;background:${item.color};display:inline-block"></span>
              <span style="font-weight:600">${esc(item.label)}</span>
            </div>
            <div style="text-align:right">
              <strong>${fmt(item.value)}</strong>
              <div style="font-size:11px;color:var(--muted)">${pct((Number(item.value || 0) / total) * 100)}</div>
            </div>
          </div>`).join('')}
      </div>
    </div>`;
}

//  DASHBOARD 
async function loadDashboard() {
  const d = await api({action:'stats'});
  if (!d) return;
  const g = document.getElementById('stats-grid');
  g.innerHTML = `
    <div class="stat-card"><div class="stat-icon">📅</div><div><p class="stat-label">Weekly Bookings</p><p class="stat-value">${d.weekly_bookings}</p><p class="stat-sub">Restaurant + Catering</p></div></div>
    <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Pending</p><p class="stat-value">${d.pending}</p><p class="stat-sub">Need confirmation</p></div></div>
    <div class="stat-card"><div class="stat-icon">💰</div><div><p class="stat-label">Monthly Revenue</p><p class="stat-value">${fmt(d.monthly_revenue)}</p><p class="stat-sub">After approved discounts</p></div></div>
    <div class="stat-card"><div class="stat-icon">🍽</div><div><p class="stat-label">Active Dishes</p><p class="stat-value">${d.active_dishes}</p><p class="stat-sub">On the menu</p></div></div>`;

  const r = await api({action:'reports'});
  if (!r) return;
  
  // Recent Bookings Table
  const tb = document.getElementById('recent-bookings');
  tb.innerHTML = r.recent.length ? r.recent.map(b => `
    <tr>
      <td><strong>${esc(b.ticket_no)}</strong></td>
      <td>${esc(b.customer_name)}</td>
      <td style="text-transform:capitalize">${esc(b.service_type)}</td>
      <td>${fmt(b.total_amount)}</td>
      <td>${badge(b.status)}</td>
      <td>${new Date(b.created_at).toLocaleDateString('en-PH')}</td>
    </tr>`).join('') : `<tr class="loading-row"><td colspan="6" style="color:var(--muted)">No bookings yet</td></tr>`;

  // Revenue Chart
  renderChart('revenue-chart', r.daily_revenue);

  // Recent Activity Feed (Simulated based on recent bookings and staff)
  const activity = document.getElementById('recent-activity');
  const items = [];
  r.recent.forEach(b => {
    items.push({
      icon: b.status === 'pending' ? '🔔' : '✅',
      text: `<strong>${esc(b.customer_name)}</strong> ${b.status === 'pending' ? 'requested a new' : 'confirmed their'} ${esc(b.service_type)} booking.`,
      time: b.created_at
    });
  });
  
  activity.innerHTML = items.length ? items.slice(0, 4).map(item => `
    <div class="activity-item">
      <div class="activity-icon">${item.icon}</div>
      <div class="activity-content">
        <p>${item.text}</p>
        <small>${new Date(item.time).toLocaleString('en-PH', {timeStyle:'short', dateStyle:'medium'})}</small>
      </div>
    </div>`).join('') : '<p style="color:var(--muted);text-align:center;padding:20px;">No recent activity</p>';
}

//  BOOKINGS  
let bookingFilter = '';
let currentBookingPage = 1;
function setBookingFilter(f, el) {
  bookingFilter = f;
  currentBookingPage = 1;
  document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  loadBookings();
}

async function loadBookings() {
  const q = document.getElementById('booking-q').value;
  let s = document.getElementById('booking-status').value;
  if (bookingFilter && bookingFilter !== 'today') s = bookingFilter;
  
  const params = {action:'bookings', q, status:s, page: currentBookingPage};
  if (bookingFilter === 'today') params.today = 1;

  const d = await api(params);
  const tb = document.getElementById('bookings-tbody');
  if (!d) { tb.innerHTML = `<tr class="loading-row"><td colspan="8">Error loading</td></tr>`; return; }
  
  tb.innerHTML = d.bookings.length ? d.bookings.map(b => `
    <tr>
      <td><span class="ticket-badge">${esc(b.ticket_no)}</span></td>
      <td><strong>${esc(b.customer_name)}</strong><br><small style="color:var(--muted)">${esc(b.customer_phone||'')}</small></td>
      <td style="text-transform:capitalize">${esc(b.service_type)}${b.venue_name ? `<br><small style="color:var(--muted)">${esc(b.venue_name)}</small>` : ''}</td>
      <td>${formatBookingSchedule(b.event_date, b.event_time, 'medium')}</td>
      <td>${b.pax} pax</td>
      <td>${Number(b.total_amount) > 0 ? `<span class="price-tag">${fmt(b.total_amount)}</span>` : '<em style="color:var(--muted)">Awaiting quote</em>'}</td>
      <td>${badge(b.status)}</td>
      <td>
        <div class="action-group">
          <button class="btn btn-ghost btn-sm" onclick="openBookingReview(${b.id})">Review</button>
          <button class="btn btn-danger btn-sm" onclick="deleteBooking(${b.id})">×</button>
        </div>
      </td>
    </tr>`).join('')
    : `<tr class="loading-row"><td colspan="8" style="color:var(--muted)">No bookings found</td></tr>`;
  
  // Update pagination controls
  const paginationDiv = document.getElementById('pagination-controls');
  if (d.total_pages > 1) {
    paginationDiv.style.display = 'flex';
    document.getElementById('page-info').textContent = `Page ${d.page} of ${d.total_pages}`;
    document.getElementById('prev-btn').disabled = d.page === 1;
    document.getElementById('next-btn').disabled = d.page === d.total_pages;
  } else {
    paginationDiv.style.display = 'none';
  }
}

function prevPage() {
  if (currentBookingPage > 1) {
    currentBookingPage--;
    loadBookings();
  }
}

function nextPage() {
  currentBookingPage++;
  loadBookings();
}

//  FINANCIALS  
async function loadFinancials() {
  const venues = await api({action:'venues'});
  const menu = await api({action:'menu', q:''});
  const reports = await api({action:'reports'});

  if (venues) {
    document.getElementById('financial-venues').innerHTML = venues.venues.map(v => `
      <div class="pricing-row">
        <div>
          <strong>${esc(v.name)}</strong>
          <p>${esc(v.type)} · ${v.capacity} pax</p>
        </div>
        <span class="price-tag">${fmt(v.rate)}</span>
      </div>`).join('');
  }

  if (menu) {
    document.getElementById('financial-menu').innerHTML = menu.items.slice(0, 6).map(m => `
      <div class="pricing-row">
        <div>
          <strong>${esc(m.name)}</strong>
          <p>${esc(m.category)}</p>
        </div>
        <span class="price-tag">${m.price > 0 ? fmt(m.price) : 'Custom'}</span>
      </div>`).join('');
  }

  if (reports) {
    const summary = reports.financial_summary || {};
    document.getElementById('financial-summary-grid').innerHTML = `
      <div style="padding:16px;background:var(--surface-soft);border-radius:14px;border-left:4px solid #168a24">
        <p style="font-size:11px;text-transform:uppercase;color:var(--muted)">Gross Revenue</p>
        <p style="font-size:24px;font-weight:800">${fmt(summary.gross_revenue || 0)}</p>
      </div>
      <div style="padding:16px;background:var(--surface-soft);border-radius:14px;border-left:4px solid #d97706">
        <p style="font-size:11px;text-transform:uppercase;color:var(--muted)">Recipe / Food Cost</p>
        <p style="font-size:24px;font-weight:800">${fmt(summary.food_cost || 0)}</p>
      </div>
      <div style="padding:16px;background:var(--surface-soft);border-radius:14px;border-left:4px solid #2563eb">
        <p style="font-size:11px;text-transform:uppercase;color:var(--muted)">Staff Labor</p>
        <p style="font-size:24px;font-weight:800">${fmt(summary.staff_labor_cost || 0)}</p>
      </div>
      <div style="padding:16px;background:linear-gradient(135deg,rgba(22,138,36,0.12),rgba(22,138,36,0.04));border-radius:14px;border-left:4px solid #168a24">
        <p style="font-size:11px;text-transform:uppercase;color:var(--muted)">Estimated Net Profit</p>
        <p style="font-size:24px;font-weight:800;color:var(--green)">${fmt(summary.net_profit || 0)}</p>
        <p style="font-size:11px;color:var(--muted);margin-top:4px">Margin: ${pct(summary.profit_margin_percent || 0)}</p>
      </div>`;

    renderPieChart(
      'financial-expense-pie',
      reports.charts?.expense_breakdown || [],
      fmt(summary.gross_revenue || 0),
      'Gross revenue split'
    );

    const staffRows = (reports.staff_costs || []).slice(0, 5);
    document.getElementById('financial-staff-costs').innerHTML = staffRows.length
      ? `
        <div style="display:grid;gap:10px">
          ${staffRows.map((staff) => `
            <div style="padding:12px;background:var(--surface-soft);border:1px solid var(--border);border-radius:12px">
              <div style="display:flex;justify-content:space-between;gap:12px">
                <div>
                  <strong>${esc(staff.name)}</strong>
                  <p style="font-size:12px;color:var(--muted)">${esc(staff.role)} · ${esc(staff.status.replace('_', ' '))}</p>
                </div>
                <strong>${fmt(staff.estimated_period_cost)}</strong>
              </div>
            </div>`).join('')}
          <p style="font-size:12px;color:var(--muted)">Daily staff-cost estimate: ${fmt(summary.avg_daily_staff_cost || 0)}. Calculated from role-based daily rates, current shift hours, and active staff status.</p>
        </div>`
      : '<p style="color:var(--muted);font-size:13px">No staff records available.</p>';

    renderChart('monthly-trend-chart', reports.monthly_trends);
  }
}

function renderReviewList(targetId, items, type) {
  const target = document.getElementById(targetId);
  if (!items.length) {
    target.innerHTML = '<p style="color:var(--muted);font-size:13px">No selections attached to this booking.</p>';
    return;
  }
  target.innerHTML = items.map((item) => `
    <div class="review-line">
      <div>
        <strong>${type === 'menu' ? esc(item.dish) : esc(item.addon_name)}</strong>
        <p>${item.quantity} x ${Number(item.unit_price) > 0 ? fmt(item.unit_price) : 'Custom quote'}</p>
      </div>
      <span>${Number(item.unit_price) > 0 ? fmt(item.unit_price * item.quantity) : 'Quoted later'}</span>
    </div>`).join('');
}

async function openBookingReview(id) {
  const d = await api({action:'booking_get', id});
  if (!d?.ok) {
    toast(d?.error || 'Could not load booking', true);
    return;
  }

  currentBookingReview = d;
  const booking = d.booking;
  let detailSummary = '';
  try {
    const parsedDetails = booking.details ? JSON.parse(booking.details) : null;
    if (parsedDetails) {
      detailSummary = [
        parsedDetails.custom_quote_required ? 'Includes items that still need a custom quote.' : '',
        parsedDetails.menu_item_count ? `${parsedDetails.menu_item_count} menu item selection(s)` : '',
        parsedDetails.addon_count ? `${parsedDetails.addon_count} add-on selection(s)` : '',
      ].filter(Boolean).join(' ');
    }
  } catch (error) {
    detailSummary = '';
  }
  document.getElementById('review-booking-id').value = booking.id;
  document.getElementById('review-status').value = booking.status;
  document.getElementById('review-amount').value = Number(booking.total_amount || 0) > 0 ? booking.total_amount : '';
  document.getElementById('review-discount').value = Number(booking.discount_percent || 0);
  document.getElementById('review-pricing-notes').value = booking.pricing_notes || '';
  document.getElementById('review-notes').value = booking.notes || '';
  calculateDiscountedPrice();
  document.getElementById('booking-review-summary').innerHTML = `
    <div class="review-summary-grid">
      <div class="review-stat"><span>Ticket</span><strong>${esc(booking.ticket_no)}</strong></div>
      <div class="review-stat"><span>Customer</span><strong>${esc(booking.customer_name)}</strong><small>${esc(booking.customer_phone || 'No phone provided')}</small></div>
      <div class="review-stat"><span>Service</span><strong style="text-transform:capitalize">${esc(booking.service_type)}</strong><small>${esc(booking.venue_name || 'Standard booking flow')}</small></div>
      <div class="review-stat"><span>Event Schedule</span><strong>${formatBookingSchedule(booking.event_date, booking.event_time, 'long')}</strong><small>${booking.pax} pax</small></div>
    </div>
    ${detailSummary ? `<div class="review-note">${esc(detailSummary)}</div>` : ''}
  `;
  renderReviewList('booking-review-items', d.items || [], 'menu');
  renderReviewList('booking-review-addons', d.addons || [], 'addon');
  openModal('modal-booking-review');
}

function calculateDiscountedPrice() {
  const amount = parseFloat(document.getElementById('review-amount').value) || 0;
  const discount = parseFloat(document.getElementById('review-discount').value) || 0;
  const finalAmount = amount * (1 - (discount / 100));
  document.getElementById('review-final-amount').value = finalAmount.toFixed(2);
}

async function saveBookingReview() {
  const payload = {
    action: 'booking_update',
    id: document.getElementById('review-booking-id').value,
    status: document.getElementById('review-status').value,
    total_amount: document.getElementById('review-amount').value,
    discount_percent: document.getElementById('review-discount').value,
    pricing_notes: document.getElementById('review-pricing-notes').value,
    notes: document.getElementById('review-notes').value,
  };
  const d = await api(payload, 'POST');
  if (d?.ok) {
    toast('Booking review saved ✓');
    closeModal('modal-booking-review');
    loadBookings();
    loadDashboard();
  } else {
    toast(d?.error || 'Failed to save booking', true);
  }
}
async function deleteBooking(id) {
  if (!confirm('Delete this booking?')) return;
  const d = await api({action:'booking_delete', id}, 'POST');
  if (d?.ok) { toast('Booking deleted'); loadBookings(); } else toast('Failed', true);
}
async function addBooking() {
  const payload = {
    action:'booking_add',
    customer_name: document.getElementById('b-name').value,
    customer_phone: document.getElementById('b-phone').value,
    customer_email: document.getElementById('b-email').value,
    service_type: document.getElementById('b-service').value,
    pax: document.getElementById('b-pax').value,
    event_date: document.getElementById('b-date').value,
    event_time: document.getElementById('b-time').value,
    total_amount: document.getElementById('b-amount').value,
    notes: document.getElementById('b-notes').value,
  };
  const d = await api(payload, 'POST');
  if (d?.ok) {
    toast('Booking created ✓');
    document.getElementById('b-name').value = '';
    document.getElementById('b-phone').value = '';
    document.getElementById('b-email').value = '';
    document.getElementById('b-service').value = 'restaurant';
    document.getElementById('b-pax').value = '2';
    document.getElementById('b-date').value = '';
    document.getElementById('b-time').value = '';
    document.getElementById('b-amount').value = '';
    document.getElementById('b-notes').value = '';
    closeModal('modal-booking-add');
    loadBookings();
  }
  else toast(d?.error || 'Failed', true);
}

//  MENU  //
async function loadMenu() {
  const q = document.getElementById('menu-q').value;
  const c = document.getElementById('menu-cat').value;
  const d = await api({action:'menu', q, category:c});
  const tb = document.getElementById('menu-tbody');
  if (!d) return;
  tb.innerHTML = d.items.length ? d.items.map(m => `
    <tr>
      <td>
        <img src="../assets/dishes/${esc(m.image_path || 'default-dish.jpg')}" 
             style="width:50px;height:50px;object-fit:cover;border-radius:8px;background:#eee"
             onerror="this.src='../assets/mikosplace.jpg'">
      </td>
      <td><strong>${esc(m.name)}</strong></td>
      <td style="text-transform:capitalize"><span class="badge ${m.category==='restaurant'?'success':m.category==='cafe'?'info':'grey'}">${esc(m.category)}</span></td>
      <td>${m.price > 0 ? fmt(m.price) : '<em style="color:var(--muted)">Custom</em>'}</td>
      <td style="color:var(--muted);font-size:13px">${esc(m.description||'-')}</td>
      <td>${m.is_available ? '<span class="badge success">Yes</span>' : '<span class="badge warning">No</span>'}</td>
      <td>
        <button class="btn btn-ghost btn-sm" style="border-radius:10px;font-size:12px;padding:6px 10px;margin-right:4px"
          onclick="toggleMenuAvail(${m.id},${m.is_available})">Toggle</button>
        <button class="btn btn-danger btn-sm" style="border-radius:10px;font-size:12px;padding:6px 10px"
          onclick="deleteMenuItem(${m.id})">Delete</button>
      </td>
    </tr>`).join('')
    : `<tr class="loading-row"><td colspan="7" style="color:var(--muted)">No items found</td></tr>`;
}

async function toggleMenuAvail(id, cur) {
  const d = await api({action:'menu_update', id, is_available: cur?0:1, name:'_', price:0}, 'POST');
  if (d?.ok) { toast('Updated ✓'); loadMenu(); } else toast('Failed', true);
}
async function deleteMenuItem(id) {
  if (!confirm('Remove this item?')) return;
  const d = await api({action:'menu_delete', id}, 'POST');
  if (d?.ok) { toast('Deleted'); loadMenu(); } else toast('Failed', true);
}
async function addMenuItem() {
  const d = await api({
    action:'menu_add',
    name: document.getElementById('m-name').value,
    category: document.getElementById('m-cat').value,
    price: document.getElementById('m-price').value,
    description: document.getElementById('m-desc').value,
    image_path: document.getElementById('m-image').value,
  }, 'POST');
  if (d?.ok) { toast('Item added ✓'); closeModal('modal-menu-add'); loadMenu(); } else toast(d?.error||'Failed', true);
}

//  VENUES  //
async function loadVenues() {
  const d = await api({action:'venues'});
  const g = document.getElementById('venues-grid');
  if (!d) return;
  g.innerHTML = d.venues.map(v => `
    <div class="venue-card ${v.is_available?'':'unavailable'}">
      <h4>${esc(v.name)}</h4>
      <p class="venue-type">${esc(v.type)} venue</p>
      <p class="venue-rate">${fmt(v.rate)}</p>
      <p class="venue-cap">Capacity: ${v.capacity} pax</p>
      <p style="color:var(--muted);font-size:13px;margin-bottom:14px">${esc(v.description||'')}</p>
      <div class="venue-actions">
        <button class="btn btn-sm ${v.is_available?'btn-ghost':'btn-green'}" onclick="toggleVenue(${v.id},${v.is_available})">
          ${v.is_available ? 'Mark Unavailable' : 'Mark Available'}
        </button>
      </div>
    </div>`).join('');
}
async function toggleVenue(id, cur) {
  const d = await api({action:'venue_toggle', id, is_available: cur?0:1}, 'POST');
  if (d?.ok) { toast('Venue updated ✓'); loadVenues(); } else toast('Failed', true);
}

//  STAFF  //
async function loadStaff() {
  const q = document.getElementById('staff-q').value;
  const d = await api({action:'staff', q});
  const tb = document.getElementById('staff-tbody');
  if (!d) return;
  tb.innerHTML = d.staff.length ? d.staff.map(s => `
    <tr>
      <td><strong>${esc(s.name)}</strong></td>
      <td>${esc(s.role)}</td>
      <td style="color:var(--muted);font-size:13px">${esc(s.assignment||'-')}</td>
      <td style="font-size:13px">${esc(s.shift_start||'')} - ${esc(s.shift_end||'')}</td>
      <td>${badge(s.status)}</td>
      <td>
        <select class="search-select" style="padding:6px 10px;font-size:12px;border-radius:10px;margin-right:4px" onchange="updateStaffStatus(${s.id},this.value)">
          ${['on_duty','off_duty','prepping','on_leave'].map(st=>`<option value="${st}" ${s.status===st?'selected':''}>${esc(st.replace('_',' '))}</option>`).join('')}
        </select>
        <button class="btn btn-danger btn-sm" style="border-radius:10px;font-size:12px;padding:6px 10px" onclick="deleteStaff(${s.id})">Remove</button>
      </td>
    </tr>`).join('')
    : `<tr class="loading-row"><td colspan="6" style="color:var(--muted)">No staff found</td></tr>`;
}
async function updateStaffStatus(id, status) {
  const d = await api({action:'staff_status', id, status}, 'POST');
  if (d?.ok) toast('Status updated ✓'); else toast('Failed', true);
}
async function deleteStaff(id) {
  if (!confirm('Remove this staff member?')) return;
  const d = await api({action:'staff_delete', id}, 'POST');
  if (d?.ok) { toast('Removed'); loadStaff(); } else toast('Failed', true);
}
async function addStaff() {
  const d = await api({
    action:'staff_add',
    name: document.getElementById('s-name').value,
    role: document.getElementById('s-role').value,
    assignment: document.getElementById('s-assign').value,
    shift_start: document.getElementById('s-start').value,
    shift_end: document.getElementById('s-end').value,
    status: document.getElementById('s-status').value,
  }, 'POST');
  if (d?.ok) { toast('Staff added ✓'); closeModal('modal-staff-add'); loadStaff(); } else toast(d?.error||'Failed', true);
}

//  REPORTS  //
async function loadReports() {
  const d = await api({action:'reports'});
  if (!d) return;
  const g = document.getElementById('reports-grid');
  const summary = d.financial_summary || {};
  const byService = d.by_service.map(r => `
    <div class="report-stat">
      <p class="lbl" style="text-transform:capitalize">${esc(r.service_type)}</p>
      <p class="val">${r.cnt} bookings &nbsp;·&nbsp; ${fmt(r.total)}</p>
    </div>`).join('');
  const topDishes = d.top_dishes.length
    ? `<ol style="padding-left:18px;color:var(--text)">${d.top_dishes.map(x=>`<li style="margin-bottom:8px">${esc(x.name)} <span style="color:var(--muted);font-size:12px">(${x.qty} orders)</span></li>`).join('')}</ol>`
    : '<p style="color:var(--muted);font-size:13px">No dish data yet</p>';
  const statusCounts = d.status_counts.map(s => `
    <div class="report-stat"><p class="lbl" style="text-transform:capitalize">${esc(s.status.replace('_',' '))}</p><p class="val">${s.cnt}</p></div>`).join('');

  g.innerHTML = `
    <div class="card full-width">
      <h4 style="font-family:'Playfair Display',serif;font-size:18px;margin-bottom:14px">Revenue vs Costs</h4>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:18px">
        <div class="report-stat"><p class="lbl">Gross Revenue</p><p class="val">${fmt(summary.gross_revenue || 0)}</p></div>
        <div class="report-stat"><p class="lbl">Food Cost</p><p class="val">${fmt(summary.food_cost || 0)}</p></div>
        <div class="report-stat"><p class="lbl">Staff Labor</p><p class="val">${fmt(summary.staff_labor_cost || 0)}</p></div>
        <div class="report-stat"><p class="lbl">Net Profit</p><p class="val" style="color:var(--green)">${fmt(summary.net_profit || 0)}</p></div>
      </div>
      <div id="reports-expense-pie"></div>
      <p style="font-size:12px;color:var(--muted);margin-top:14px">${esc(summary.cost_model?.labor || '')} ${esc(summary.cost_model?.food || '')}</p>
    </div>
    <div class="card">
      <h4 style="font-family:'Playfair Display',serif;font-size:18px;margin-bottom:14px">By Service</h4>
      ${byService || '<p style="color:var(--muted);font-size:13px">No data</p>'}
      <div id="service-revenue-pie" style="margin-top:18px"></div>
    </div>
    <div class="card">
      <h4 style="font-family:'Playfair Display',serif;font-size:18px;margin-bottom:14px">Top Dishes</h4>
      ${topDishes}
    </div>
    <div class="card">
      <h4 style="font-family:'Playfair Display',serif;font-size:18px;margin-bottom:14px">By Status</h4>
      ${statusCounts || '<p style="color:var(--muted);font-size:13px">No data</p>'}
      <div style="margin-top:16px;padding:14px;background:var(--surface-soft);border-radius:14px;border:1px solid var(--border)">
        <p style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">This Month Revenue</p>
        <p style="font-size:26px;font-weight:800;color:var(--red)">${fmt(d.revenue_month)}</p>
      </div>
    </div>`;

  renderPieChart('reports-expense-pie', d.charts?.expense_breakdown || [], fmt(summary.net_profit || 0), 'Estimated net profit');
  renderPieChart('service-revenue-pie', d.charts?.service_revenue || [], fmt(d.revenue_month || 0), 'Revenue by service');
}

async function loadSalesReport() {
  const startDate = new Date();
  startDate.setDate(1);
  const endDate = new Date();
  
  const d = await api({
    action: 'sales_report',
    start_date: startDate.toISOString().split('T')[0],
    end_date: endDate.toISOString().split('T')[0]
  });
  
  if (!d) {
    toast('Failed to load sales report', true);
    return;
  }

  const g = document.getElementById('reports-grid');
  const summary = d.summary || {};
  
  const bookingsTable = d.bookings.length
    ? `<table style="width:100%;font-size:13px">
        <thead>
          <tr style="border-bottom:2px solid var(--border)">
            <th style="padding:8px;text-align:left">Ticket</th>
            <th style="padding:8px;text-align:left">Customer</th>
            <th style="padding:8px;text-align:right">Original Price</th>
            <th style="padding:8px;text-align:center">Discount</th>
            <th style="padding:8px;text-align:right">Final Price</th>
          </tr>
        </thead>
        <tbody>
          ${d.bookings.map(b => {
            const discountAmount = (parseFloat(b.total_amount) * parseFloat(b.discount_percent) / 100).toFixed(2);
            return `
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px"><strong>${esc(b.ticket_no)}</strong></td>
              <td style="padding:8px">${esc(b.customer_name)}</td>
              <td style="padding:8px;text-align:right">${fmt(b.total_amount)}</td>
              <td style="padding:8px;text-align:center">${parseFloat(b.discount_percent).toFixed(1)}% <small style="color:var(--muted)">(${fmt(discountAmount)})</small></td>
              <td style="padding:8px;text-align:right;color:var(--green);font-weight:600">${fmt(b.final_amount)}</td>
            </tr>`;
          }).join('')}
        </tbody>
      </table>`
    : '<p style="color:var(--muted);font-size:13px">No completed bookings found for this period</p>';

  g.innerHTML = `
    <div class="card full-width">
      <h4 style="font-family:'Playfair Display',serif;font-size:18px;margin-bottom:16px">Sales Report Summary (${startDate.toLocaleDateString('en-PH')} - ${endDate.toLocaleDateString('en-PH')})</h4>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px">
        <div style="padding:16px;background:var(--surface-soft);border-radius:10px;border-left:4px solid var(--green)">
          <p style="font-size:12px;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Total Bookings</p>
          <p style="font-size:24px;font-weight:800">${summary.total_bookings || 0}</p>
        </div>
        <div style="padding:16px;background:var(--surface-soft);border-radius:10px;border-left:4px solid var(--blue);border-left-color:#168a24">
          <p style="font-size:12px;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Total Revenue (Before Discount)</p>
          <p style="font-size:24px;font-weight:800">${fmt(summary.total_revenue || 0)}</p>
        </div>
        <div style="padding:16px;background:var(--surface-soft);border-radius:10px;border-left:4px solid var(--red)">
          <p style="font-size:12px;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Total Discounts Given</p>
          <p style="font-size:24px;font-weight:800">${fmt(summary.total_discount || 0)}</p>
          <p style="font-size:11px;color:var(--muted);margin-top:4px">Avg: ${parseFloat(summary.average_discount_percent || 0).toFixed(1)}%</p>
        </div>
        <div style="padding:16px;background:var(--surface-soft);border-radius:10px;border-left:4px solid var(--green);background:linear-gradient(135deg,rgba(22,138,36,0.1),rgba(22,138,36,0.05))">
          <p style="font-size:12px;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Final Revenue (After Discount)</p>
          <p style="font-size:24px;font-weight:800;color:var(--green)">${fmt(summary.final_revenue || 0)}</p>
        </div>
        <div style="padding:16px;background:var(--surface-soft);border-radius:10px;border-left:4px solid #d97706">
          <p style="font-size:12px;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Estimated Food Cost</p>
          <p style="font-size:24px;font-weight:800">${fmt(summary.food_cost || 0)}</p>
        </div>
        <div style="padding:16px;background:var(--surface-soft);border-radius:10px;border-left:4px solid #2563eb">
          <p style="font-size:12px;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Estimated Staff Labor</p>
          <p style="font-size:24px;font-weight:800">${fmt(summary.staff_labor_cost || 0)}</p>
        </div>
        <div style="padding:16px;background:linear-gradient(135deg,rgba(22,138,36,0.12),rgba(22,138,36,0.04));border-radius:10px;border-left:4px solid #168a24">
          <p style="font-size:12px;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Estimated Net Profit</p>
          <p style="font-size:24px;font-weight:800;color:var(--green)">${fmt(summary.net_profit || 0)}</p>
          <p style="font-size:11px;color:var(--muted);margin-top:4px">Margin: ${pct(summary.profit_margin_percent || 0)}</p>
        </div>
      </div>
    </div>
    <div class="card full-width">
      <h4 style="font-family:'Playfair Display',serif;font-size:16px;margin-bottom:12px">Revenue Breakdown</h4>
      <div id="sales-report-pie"></div>
      <p style="font-size:12px;color:var(--muted);margin-top:14px">${esc(d.assumptions?.labor || '')} ${esc(d.assumptions?.food || '')}</p>
    </div>
    <div class="card full-width">
      <h4 style="font-family:'Playfair Display',serif;font-size:16px;margin-bottom:12px">Booking Details</h4>
      <div style="overflow-x:auto">
        ${bookingsTable}
      </div>
    </div>`;

  renderPieChart('sales-report-pie', d.charts?.revenue_breakdown || [], fmt(summary.final_revenue || 0), 'After-discount revenue');
}

//  INIT  //
loadDashboard();
</script>
</body>
</html>
