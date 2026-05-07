<?php
    // admin/index.php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/auth.php';
    requireAdmin();
    $adminName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
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
      <button class="nav-item" onclick="show('reports',this)"><span class="icon">📊</span> Reports</button>
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
        <p class="header-tag">Franco Miguel's Place</p>
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
          <h3>Manage dining, catering, cafe & venues in one place.</h3>
          <p>Full operations view - bookings, menu, staff, and real-time reports.</p>
        </div>
        <div class="hero-highlight">
          <span>Featured Rate</span>
          <strong>₱25,000</strong>
          <p>Function Hall · 50 pax</p>
        </div>
      </div>

      <div class="stats-grid" id="stats-grid">
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading...</p><p class="stat-value">-</p></div></div>
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading...</p><p class="stat-value">-</p></div></div>
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading...</p><p class="stat-value">-</p></div></div>
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading...</p><p class="stat-value">-</p></div></div>
      </div>

      <div class="two-col">
        <div class="card">
          <h3 style="font-family:'Playfair Display',serif;font-size:19px;margin-bottom:16px;">Core Services</h3>
          <div class="chip-grid">
            <span class="chip">Catering</span><span class="chip">Restaurant</span>
            <span class="chip">Cafe &amp; Pastries</span><span class="chip">Pool w/ Pavilion</span>
            <span class="chip">Function Hall</span><span class="chip">Banquet Hall</span>
          </div>
        </div>
        <div class="card">
          <h3 style="font-family:'Playfair Display',serif;font-size:19px;margin-bottom:16px;">Venue Pricing</h3>
          <div class="pricing-row"><div><strong>Pool w/ Pavilion</strong><p>50 pax outdoor</p></div><span>₱10,000</span></div>
          <div class="pricing-row"><div><strong>Banquet Hall</strong><p>40 pax indoor</p></div><span>₱20,000</span></div>
          <div class="pricing-row"><div><strong>Function Hall</strong><p>50 pax premium</p></div><span>₱25,000</span></div>
        </div>
        <div class="card full-width">
          <h3 style="font-family:'Playfair Display',serif;font-size:19px;margin-bottom:16px;">Recent Bookings</h3>
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
        <h3>Bookings</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-booking-add')">+ New Booking</button>
      </div>
      <div class="search-bar">
        <input class="search-input" id="booking-q" placeholder="Search by ticket, name or email..." oninput="loadBookings()">
        <select class="search-select" id="booking-status" onchange="loadBookings()">
          <option value="">All Statuses</option>
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
            <thead><tr><th>Ticket</th><th>Customer</th><th>Service</th><th>Event Date</th><th>Pax</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="bookings-tbody"><tr class="loading-row"><td colspan="8"><span class="spinner"></span></td></tr></tbody>
          </table>
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
            <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Description</th><th>Available</th><th>Actions</th></tr></thead>
            <tbody id="menu-tbody"><tr class="loading-row"><td colspan="6"><span class="spinner"></span></td></tr></tbody>
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
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modal-menu-add')">Cancel</button>
      <button class="btn btn-green btn-sm" onclick="addMenuItem()">Add Item</button>
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

//  Section navigation 
function show(id, btn) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('sec-' + id).classList.add('active');
  btn.classList.add('active');
  const titles = {dashboard:'Overview',bookings:'Bookings',menu:'Menu Items',venues:'Venue Packages',staff:'Team Coverage',reports:'Reports & Insights'};
  document.getElementById('page-title').textContent = titles[id] || id;
  if (id === 'bookings') loadBookings();
  if (id === 'menu') loadMenu();
  if (id === 'venues') loadVenues();
  if (id === 'staff') loadStaff();
  if (id === 'reports') loadReports();
}

//  API helpers 
async function api(params, method = 'GET') {
  try {
    const opts = method === 'GET'
      ? { method: 'GET' }
      : { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams(params) };
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

//  DASHBOARD 
async function loadDashboard() {
  const d = await api({action:'stats'});
  if (!d) return;
  const g = document.getElementById('stats-grid');
  g.innerHTML = `
    <div class="stat-card"><div class="stat-icon">📅</div><div><p class="stat-label">Weekly Bookings</p><p class="stat-value">${d.weekly_bookings}</p><p class="stat-sub">Restaurant + Catering</p></div></div>
    <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Pending</p><p class="stat-value">${d.pending}</p><p class="stat-sub">Need confirmation</p></div></div>
    <div class="stat-card"><div class="stat-icon">💰</div><div><p class="stat-label">Monthly Revenue</p><p class="stat-value">${fmt(d.monthly_revenue)}</p><p class="stat-sub">Completed orders</p></div></div>
    <div class="stat-card"><div class="stat-icon">🍽</div><div><p class="stat-label">Active Dishes</p><p class="stat-value">${d.active_dishes}</p><p class="stat-sub">On the menu</p></div></div>`;

  const r = await api({action:'reports'});
  if (!r) return;
  const tb = document.getElementById('recent-bookings');
  tb.innerHTML = r.recent.length ? r.recent.map(b => `
    <tr>
      <td><strong>${b.ticket_no}</strong></td>
      <td>${b.customer_name}</td>
      <td style="text-transform:capitalize">${b.service_type}</td>
      <td>${fmt(b.total_amount)}</td>
      <td>${badge(b.status)}</td>
      <td>${new Date(b.created_at).toLocaleDateString('en-PH')}</td>
    </tr>`).join('') : `<tr class="loading-row"><td colspan="6" style="color:var(--muted)">No bookings yet</td></tr>`;
}

//  BOOKINGS  
async function loadBookings() {
  const q = document.getElementById('booking-q').value;
  const s = document.getElementById('booking-status').value;
  const d = await api({action:'bookings', q, status:s});
  const tb = document.getElementById('bookings-tbody');
  if (!d) { tb.innerHTML = `<tr class="loading-row"><td colspan="8">Error loading</td></tr>`; return; }
  tb.innerHTML = d.bookings.length ? d.bookings.map(b => `
    <tr>
      <td><strong>${b.ticket_no}</strong></td>
      <td>${b.customer_name}<br><small style="color:var(--muted)">${b.customer_phone||''}</small></td>
      <td style="text-transform:capitalize">${b.service_type}</td>
      <td>${b.event_date ? new Date(b.event_date).toLocaleDateString('en-PH') : '-'}</td>
      <td>${b.pax}</td>
      <td>${fmt(b.total_amount)}</td>
      <td>
        <select class="search-select" style="padding:6px 10px;font-size:12px;border-radius:10px;" onchange="updateBookingStatus(${b.id},this.value)">
          ${['pending','confirmed','in_progress','completed','cancelled'].map(st=>`<option value="${st}" ${b.status===st?'selected':''}>${st.replace('_',' ')}</option>`).join('')}
        </select>
      </td>
      <td>
        <button class="btn btn-danger btn-sm" style="border-radius:10px;font-size:12px;padding:6px 10px;" onclick="deleteBooking(${b.id})">Delete</button>
      </td>
    </tr>`).join('')
    : `<tr class="loading-row"><td colspan="8" style="color:var(--muted)">No bookings found</td></tr>`;
}

async function updateBookingStatus(id, status) {
  const d = await api({action:'booking_status', id, status}, 'POST');
  if (d?.ok) toast('Status updated ✓'); else toast('Failed', true);
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
    total_amount: document.getElementById('b-amount').value,
    notes: document.getElementById('b-notes').value,
  };
  // Direct DB insert via inline PHP endpoint
  const r = await fetch(API, {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams(payload)
  });
  const d = await r.json();
  if (d?.ok) { toast('Booking created ✓'); closeModal('modal-booking-add'); loadBookings(); }
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
      <td><strong>${m.name}</strong></td>
      <td style="text-transform:capitalize"><span class="badge ${m.category==='restaurant'?'success':m.category==='cafe'?'info':'grey'}">${m.category}</span></td>
      <td>${m.price > 0 ? fmt(m.price) : '<em style="color:var(--muted)">Custom</em>'}</td>
      <td style="color:var(--muted);font-size:13px">${m.description||'-'}</td>
      <td>${m.is_available ? '<span class="badge success">Yes</span>' : '<span class="badge warning">No</span>'}</td>
      <td>
        <button class="btn btn-ghost btn-sm" style="border-radius:10px;font-size:12px;padding:6px 10px;margin-right:4px"
          onclick="toggleMenuAvail(${m.id},${m.is_available})">Toggle</button>
        <button class="btn btn-danger btn-sm" style="border-radius:10px;font-size:12px;padding:6px 10px"
          onclick="deleteMenuItem(${m.id})">Delete</button>
      </td>
    </tr>`).join('')
    : `<tr class="loading-row"><td colspan="6" style="color:var(--muted)">No items found</td></tr>`;
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
      <h4>${v.name}</h4>
      <p class="venue-type">${v.type} venue</p>
      <p class="venue-rate">${fmt(v.rate)}</p>
      <p class="venue-cap">Capacity: ${v.capacity} pax</p>
      <p style="color:var(--muted);font-size:13px;margin-bottom:14px">${v.description||''}</p>
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
      <td><strong>${s.name}</strong></td>
      <td>${s.role}</td>
      <td style="color:var(--muted);font-size:13px">${s.assignment||'-'}</td>
      <td style="font-size:13px">${s.shift_start||''} - ${s.shift_end||''}</td>
      <td>${badge(s.status)}</td>
      <td>
        <select class="search-select" style="padding:6px 10px;font-size:12px;border-radius:10px;margin-right:4px" onchange="updateStaffStatus(${s.id},this.value)">
          ${['on_duty','off_duty','prepping','on_leave'].map(st=>`<option value="${st}" ${s.status===st?'selected':''}>${st.replace('_',' ')}</option>`).join('')}
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
  const byService = d.by_service.map(r => `
    <div class="report-stat">
      <p class="lbl" style="text-transform:capitalize">${r.service_type}</p>
      <p class="val">${r.cnt} bookings &nbsp;·&nbsp; ${fmt(r.total)}</p>
    </div>`).join('');
  const topDishes = d.top_dishes.length
    ? `<ol style="padding-left:18px;color:var(--text)">${d.top_dishes.map(x=>`<li style="margin-bottom:8px">${x.name} <span style="color:var(--muted);font-size:12px">(${x.qty} orders)</span></li>`).join('')}</ol>`
    : '<p style="color:var(--muted);font-size:13px">No dish data yet</p>';
  const statusCounts = d.status_counts.map(s => `
    <div class="report-stat"><p class="lbl" style="text-transform:capitalize">${s.status.replace('_',' ')}</p><p class="val">${s.cnt}</p></div>`).join('');

  g.innerHTML = `
    <div class="card">
      <h4 style="font-family:'Playfair Display',serif;font-size:18px;margin-bottom:14px">By Service</h4>
      ${byService || '<p style="color:var(--muted);font-size:13px">No data</p>'}
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
}

//  INIT  //
loadDashboard();
</script>
</body>
</html>
