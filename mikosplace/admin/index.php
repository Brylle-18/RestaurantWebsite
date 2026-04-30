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
<title>Admin Dashboard — Miko's Place</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
<style>
/* ─── RESET & VARS ─────────────────────────────────────── */
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --green:#168a24;--green-dk:#0a5616;--green-lt:rgba(22,138,36,.10);
  --red:#b61217;  --red-dk:#7e0e11;  --red-lt:rgba(182,18,23,.10);
  --bg:#f4fbe9;   --surface:#fff;    --surface-soft:#f7fbf2;
  --text:#12311a; --muted:#58705e;   --border:rgba(18,98,33,.13);
  --shadow:0 18px 40px rgba(12,55,19,.12);
  --sidebar:320px;
}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);line-height:1.6;
  background:radial-gradient(circle at top left,rgba(129,214,123,.22) 0,transparent 30%),
             radial-gradient(circle at bottom right,rgba(182,18,23,.10) 0,transparent 24%),
             linear-gradient(135deg,#f4fbe9,#eef7e5 50%,#fcfef7);}
::-webkit-scrollbar{width:8px}
::-webkit-scrollbar-thumb{background:rgba(18,98,33,.22);border-radius:999px}

/* ─── LAYOUT ───────────────────────────────────────────── */
.shell{display:flex;min-height:100vh;}

/* ─── SIDEBAR ──────────────────────────────────────────── */
.sidebar{
  width:var(--sidebar);flex-shrink:0;
  background:linear-gradient(180deg,rgba(5,54,15,.97),rgba(13,94,28,.92));
  padding:24px 18px;display:flex;flex-direction:column;gap:22px;
  box-shadow:12px 0 30px rgba(7,45,15,.18);position:sticky;top:0;height:100vh;overflow-y:auto;
}
.logo-panel{
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);
  border-radius:22px;padding:18px;
}
.brand-logo{
  width:100%;height:140px;object-fit:cover;border-radius:14px;
  border:3px solid rgba(255,255,255,.3);margin-bottom:14px;
  background:linear-gradient(135deg,#0a5616,#168a24); /* fallback if no img */
  display:flex;align-items:center;justify-content:center;
  color:rgba(255,255,255,.5);font-size:12px;
}
.eyebrow{color:#ffd9d9;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;}
.logo-panel h1{font-family:'Playfair Display',serif;font-size:26px;color:#fff;line-height:1.1;margin:4px 0 2px;}
.logo-panel p{color:rgba(255,255,255,.7);font-size:12px;}
nav{display:flex;flex-direction:column;gap:6px;flex:1;}
.nav-item{
  display:flex;align-items:center;gap:10px;
  color:rgba(255,255,255,.8);text-decoration:none;
  padding:12px 14px;border-radius:14px;font-weight:500;
  border:1px solid transparent;transition:.2s;cursor:pointer;background:none;
  font-family:'DM Sans',sans-serif;font-size:14px;text-align:left;width:100%;
}
.nav-item .icon{font-size:18px;width:22px;text-align:center;}
.nav-item:hover,.nav-item.active{
  background:rgba(255,255,255,.14);color:#fff;
  border-color:rgba(255,255,255,.14);transform:translateX(3px);
}
.user-info{
  display:flex;align-items:center;gap:10px;
  padding:14px;border-radius:16px;background:rgba(255,255,255,.08);
}
.user-avatar{
  width:44px;height:44px;border-radius:50%;
  background:linear-gradient(135deg,#fff,#d8ffd8);
  color:var(--green-dk);font-weight:800;display:grid;place-items:center;font-size:14px;flex-shrink:0;
}
.user-name{font-weight:700;color:#fff;font-size:14px;}
.user-role{font-size:11px;color:rgba(255,255,255,.65);}
.logout-btn{
  margin-left:auto;background:rgba(255,255,255,.12);border:none;
  color:rgba(255,255,255,.7);padding:6px 10px;border-radius:8px;cursor:pointer;font-size:11px;
  transition:.2s;font-family:'DM Sans',sans-serif;
}
.logout-btn:hover{background:rgba(182,18,23,.4);color:#fff;}

/* ─── MAIN ─────────────────────────────────────────────── */
.main{flex:1;display:flex;flex-direction:column;min-width:0;}
.header{
  display:flex;justify-content:space-between;align-items:center;gap:20px;
  padding:20px 28px;background:rgba(255,255,255,.85);
  border-bottom:1px solid var(--border);backdrop-filter:blur(10px);
  position:sticky;top:0;z-index:50;
}
.header-tag{color:var(--red);font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;}
.header h2{font-family:'Playfair Display',serif;font-size:28px;line-height:1.1;}
.header-actions{display:flex;gap:8px;}
.btn,.btn-sm{border:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:700;transition:.2s;border-radius:999px;}
.btn{padding:10px 20px;font-size:14px;}
.btn-sm{padding:8px 14px;font-size:13px;}
.btn-primary{background:linear-gradient(135deg,var(--red),#d22327);color:#fff;box-shadow:0 8px 20px rgba(182,18,23,.22);}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(182,18,23,.28);}
.btn-green{background:linear-gradient(135deg,var(--green),var(--green-dk));color:#fff;box-shadow:0 8px 20px rgba(10,86,22,.20);}
.btn-green:hover{transform:translateY(-2px);}
.btn-ghost{background:var(--surface);color:var(--text);border:1px solid var(--border);box-shadow:0 4px 12px rgba(12,55,19,.06);}
.btn-ghost:hover{border-color:var(--green);color:var(--green);}
.btn-danger{background:rgba(182,18,23,.1);color:var(--red);border:1px solid rgba(182,18,23,.2);}
.btn-danger:hover{background:var(--red);color:#fff;}

/* ─── SECTIONS ─────────────────────────────────────────── */
.section{display:none;padding:28px;flex:1;overflow-y:auto;}
.section.active{display:block;}
.section-header{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:22px;}
.section-header h3{font-family:'Playfair Display',serif;font-size:22px;}

/* ─── SEARCH BAR ───────────────────────────────────────── */
.search-bar{
  display:flex;gap:10px;margin-bottom:22px;flex-wrap:wrap;align-items:center;
}
.search-input{
  flex:1;min-width:220px;padding:12px 16px;border:2px solid var(--border);border-radius:14px;
  font-family:'DM Sans',sans-serif;font-size:14px;background:#fafff7;color:var(--text);
  outline:none;transition:.2s;
}
.search-input:focus{border-color:var(--green);box-shadow:0 0 0 4px var(--green-lt);}
.search-select{
  padding:12px 14px;border:2px solid var(--border);border-radius:14px;
  font-family:'DM Sans',sans-serif;font-size:14px;background:#fafff7;color:var(--text);
  outline:none;cursor:pointer;
}
.search-select:focus{border-color:var(--green);}

/* ─── STATS GRID ───────────────────────────────────────── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:24px;}
.stat-card{
  background:var(--surface);border:1px solid rgba(255,255,255,.8);
  border-radius:22px;padding:20px;display:flex;gap:14px;align-items:center;
  box-shadow:var(--shadow);transition:.2s;
}
.stat-card:hover{transform:translateY(-3px);}
.stat-icon{
  min-width:58px;height:58px;border-radius:18px;display:grid;place-items:center;
  background:linear-gradient(135deg,var(--green-lt),var(--red-lt));
  font-size:24px;font-weight:800;color:var(--green-dk);
}
.stat-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);}
.stat-value{font-size:20px;font-weight:800;color:var(--green-dk);}
.stat-sub{font-size:12px;color:var(--muted);}

/* ─── HERO BANNER ──────────────────────────────────────── */
.hero{
  display:grid;grid-template-columns:2fr 1fr;gap:18px;
  padding:26px;margin-bottom:24px;border-radius:26px;color:#fff;
  background:linear-gradient(120deg,rgba(10,86,22,.96),rgba(22,138,36,.88));
  box-shadow:var(--shadow);
}
.hero-copy .eyebrow-white{color:#ffd9d9;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;}
.hero-copy h3{font-family:'Playfair Display',serif;font-size:28px;margin:6px 0 10px;}
.hero-copy p{color:rgba(255,255,255,.85);font-size:14px;}
.hero-highlight{
  align-self:center;background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.18);border-radius:22px;
  padding:20px;text-align:center;backdrop-filter:blur(8px);
}
.hero-highlight span{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#ffe5e5;margin-bottom:8px;}
.hero-highlight strong{display:block;font-size:32px;font-family:'Playfair Display',serif;}

/* ─── CARDS ────────────────────────────────────────────── */
.card{background:var(--surface);border:1px solid rgba(255,255,255,.8);border-radius:22px;padding:22px;box-shadow:var(--shadow);}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;}
.full-width{grid-column:1/-1;}

/* ─── TABLE ────────────────────────────────────────────── */
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
thead{background:rgba(22,138,36,.06);}
th{text-align:left;padding:12px 14px;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--green-dk);}
td{padding:13px 14px;border-bottom:1px solid var(--border);font-size:14px;vertical-align:middle;}
tbody tr:hover{background:rgba(22,138,36,.03);}
.badge{display:inline-block;padding:5px 12px;border-radius:999px;font-size:12px;font-weight:700;}
.badge.success{background:var(--green-lt);color:var(--green-dk);}
.badge.warning{background:var(--red-lt);color:var(--red-dk);}
.badge.info{background:rgba(59,130,246,.1);color:#1d4ed8;}
.badge.grey{background:rgba(100,100,100,.1);color:#555;}

/* ─── MODAL ────────────────────────────────────────────── */
.modal-backdrop{
  display:none;position:fixed;inset:0;background:rgba(5,24,10,.55);
  z-index:100;backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:20px;
}
.modal-backdrop.open{display:flex;}
.modal{
  background:var(--surface);border-radius:24px;padding:32px;
  width:100%;max-width:520px;max-height:90vh;overflow-y:auto;
  box-shadow:0 40px 80px rgba(5,24,10,.30);
}
.modal h3{font-family:'Playfair Display',serif;font-size:22px;margin-bottom:4px;}
.modal .sub{color:var(--muted);font-size:13px;margin-bottom:22px;}
.form-grid{display:grid;gap:16px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.field label{display:block;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;}
.field input,.field select,.field textarea{
  width:100%;padding:12px 14px;border:2px solid var(--border);border-radius:13px;
  font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);background:#fafff7;
  outline:none;transition:.2s;
}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--green);box-shadow:0 0 0 3px var(--green-lt);}
.field textarea{resize:vertical;min-height:80px;}
.modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:22px;}
.close-modal{position:absolute;} /* placeholder */

/* ─── VENUE CARDS ──────────────────────────────────────── */
.venues-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;}
.venue-card{
  border-radius:22px;padding:22px;box-shadow:var(--shadow);
  border-left:5px solid var(--green);transition:.2s;
  background:linear-gradient(180deg,rgba(241,248,234,.98),rgba(223,245,215,.94));
}
.venue-card.unavailable{
  border-left-color:var(--red);
  background:linear-gradient(180deg,rgba(255,250,250,.98),rgba(255,236,236,.96));
}
.venue-card:hover{transform:translateY(-3px);}
.venue-card h4{font-size:20px;margin-bottom:8px;}
.venue-rate{color:var(--red);font-size:20px;font-weight:800;margin-bottom:6px;}
.venue-cap{color:var(--muted);font-size:13px;margin-bottom:4px;}
.venue-type{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px;}
.venue-actions{display:flex;gap:8px;flex-wrap:wrap;}

/* ─── CHIP ─────────────────────────────────────────────── */
.chip-grid{display:flex;flex-wrap:wrap;gap:8px;}
.chip{padding:9px 14px;border-radius:999px;background:linear-gradient(135deg,var(--green-lt),var(--red-lt));color:var(--green-dk);font-weight:700;font-size:13px;}

/* ─── PRICING ROWS ─────────────────────────────────────── */
.pricing-row{display:flex;justify-content:space-between;align-items:center;padding:13px 14px;border-radius:16px;background:var(--surface-soft);border:1px solid var(--border);margin-bottom:10px;}
.pricing-row span{color:var(--red);font-weight:800;}
.pricing-row p{color:var(--muted);font-size:12px;}

/* ─── TOAST ────────────────────────────────────────────── */
.toast{
  position:fixed;bottom:28px;right:28px;z-index:200;
  padding:14px 20px;border-radius:16px;font-weight:700;font-size:14px;
  box-shadow:0 12px 32px rgba(0,0,0,.18);transform:translateY(80px);
  opacity:0;transition:.35s cubic-bezier(.34,1.56,.64,1);pointer-events:none;
}
.toast.show{transform:translateY(0);opacity:1;}
.toast.ok{background:var(--green-dk);color:#fff;}
.toast.err{background:var(--red);color:#fff;}

/* ─── REPORTS ──────────────────────────────────────────── */
.reports-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;}
.report-stat{padding:11px 0;border-bottom:1px solid var(--border);}
.report-stat:last-child{border-bottom:none;}
.report-stat .lbl{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);}
.report-stat .val{font-size:22px;font-weight:800;color:var(--red);}

/* ─── LOADING SPINNER ──────────────────────────────────── */
.spinner{display:inline-block;width:18px;height:18px;border:3px solid var(--border);border-top-color:var(--green);border-radius:50%;animation:spin .7s linear infinite;vertical-align:middle;}
@keyframes spin{to{transform:rotate(360deg)}}
.loading-row td{text-align:center;padding:32px;color:var(--muted);}

/* ─── RESPONSIVE ───────────────────────────────────────── */
@media(max-width:1160px){
  .stats-grid{grid-template-columns:repeat(2,1fr);}
  .two-col,.hero,.reports-grid{grid-template-columns:1fr;}
}
@media(max-width:860px){
  .shell{flex-direction:column;}
  .sidebar{width:100%;height:auto;position:relative;}
  nav{flex-direction:row;flex-wrap:wrap;}
  .nav-item{flex:1 1 130px;text-align:center;justify-content:center;}
  .stats-grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:560px){
  .section,.header{padding:16px;}
  .stats-grid{grid-template-columns:1fr;}
  .form-row{grid-template-columns:1fr;}
}
</style>
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
      <button class="nav-item active" onclick="show('dashboard',this)"><span class="icon">🏠</span> Overview</button>
      <button class="nav-item" onclick="show('bookings',this)"><span class="icon">📋</span> Bookings</button>
      <button class="nav-item" onclick="show('menu',this)"><span class="icon">🍽</span> Menu</button>
      <button class="nav-item" onclick="show('venues',this)"><span class="icon">🏛</span> Venues</button>
      <button class="nav-item" onclick="show('staff',this)"><span class="icon">👥</span> Team</button>
      <button class="nav-item" onclick="show('reports',this)"><span class="icon">📊</span> Reports</button>
    </nav>

    <div class="user-info">
      <div class="user-avatar"><?= substr($adminName,0,2) ?></div>
      <div>
        <p class="user-name"><?= $adminName ?></p>
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
        <a href="../customer/index.php" target="_blank" class="btn btn-ghost btn-sm">Customer View ↗</a>
      </div>
    </header>

    <!-- ── DASHBOARD ──────────────────────────────────────── -->
    <section class="section active" id="sec-dashboard">
      <div class="hero">
        <div class="hero-copy">
          <p class="eyebrow-white">Bamboo-inspired hospitality</p>
          <h3>Manage dining, catering, cafe &amp; venues in one place.</h3>
          <p>Full operations view — bookings, menu, staff, and real-time reports.</p>
        </div>
        <div class="hero-highlight">
          <span>Featured Rate</span>
          <strong>₱25,000</strong>
          <p>Function Hall · 50 pax</p>
        </div>
      </div>

      <div class="stats-grid" id="stats-grid">
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading…</p><p class="stat-value">—</p></div></div>
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading…</p><p class="stat-value">—</p></div></div>
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading…</p><p class="stat-value">—</p></div></div>
        <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Loading…</p><p class="stat-value">—</p></div></div>
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
              <tbody id="recent-bookings"><tr class="loading-row"><td colspan="6"><span class="spinner"></span> Loading…</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- ── BOOKINGS ───────────────────────────────────────── -->
    <section class="section" id="sec-bookings">
      <div class="section-header">
        <h3>Bookings</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-booking-add')">+ New Booking</button>
      </div>
      <div class="search-bar">
        <input class="search-input" id="booking-q" placeholder="Search by ticket, name or email…" oninput="loadBookings()">
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

    <!-- ── MENU ──────────────────────────────────────────── -->
    <section class="section" id="sec-menu">
      <div class="section-header">
        <h3>Menu Items</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-menu-add')">+ Add Dish</button>
      </div>
      <div class="search-bar">
        <input class="search-input" id="menu-q" placeholder="Search dishes…" oninput="loadMenu()">
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

    <!-- ── VENUES ────────────────────────────────────────── -->
    <section class="section" id="sec-venues">
      <div class="section-header"><h3>Venue Packages</h3></div>
      <div class="venues-grid" id="venues-grid">
        <div style="color:var(--muted);padding:20px"><span class="spinner"></span> Loading…</div>
      </div>
    </section>

    <!-- ── STAFF ─────────────────────────────────────────── -->
    <section class="section" id="sec-staff">
      <div class="section-header">
        <h3>Team Coverage</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-staff-add')">+ Add Staff</button>
      </div>
      <div class="search-bar">
        <input class="search-input" id="staff-q" placeholder="Search by name or role…" oninput="loadStaff()">
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

    <!-- ── REPORTS ───────────────────────────────────────── -->
    <section class="section" id="sec-reports">
      <div class="section-header"><h3>Reports &amp; Insights</h3></div>
      <div class="reports-grid" id="reports-grid">
        <div class="card"><p style="color:var(--muted)"><span class="spinner"></span> Loading…</p></div>
      </div>
    </section>
  </main>
</div>

<!-- ═══ MODALS ══════════════════════════════════════════════════ -->

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
      <div class="field"><label>Notes / Details</label><textarea id="b-notes" placeholder="Special requests, menu preferences…"></textarea></div>
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
      <div class="field"><label>Description</label><textarea id="m-desc" placeholder="Short description…"></textarea></div>
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

// ── Section navigation ──────────────────────────────────────
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

// ── API helpers ─────────────────────────────────────────────
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

// ── Toast ───────────────────────────────────────────────────
function toast(msg, err = false) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.className = 'toast show ' + (err ? 'err' : 'ok');
  setTimeout(() => t.className = 'toast', 3000);
}

// ── Modal helpers ────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(m => m.addEventListener('click', e => { if(e.target === m) m.classList.remove('open'); }));

// ── Status badge helper ──────────────────────────────────────
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

// ── DASHBOARD ───────────────────────────────────────────────
async function loadDashboard() {
  const d = await api({action:'stats'});
  if (!d) return;
  const g = document.getElementById('stats-grid');
  g.innerHTML = `
    <div class="stat-card"><div class="stat-icon">📅</div><div><p class="stat-label">Weekly Bookings</p><p class="stat-value">${d.weekly_bookings}</p><p class="stat-sub">Restaurant + Catering</p></div></div>
    <div class="stat-card"><div class="stat-icon">⏳</div><div><p class="stat-label">Pending</p><p class="stat-value">${d.pending}</p><p class="stat-sub">Need confirmation</p></div></div>
    <div class="stat-card"><div class="stat-icon">₱</div><div><p class="stat-label">Monthly Revenue</p><p class="stat-value">${fmt(d.monthly_revenue)}</p><p class="stat-sub">Completed orders</p></div></div>
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

// ── BOOKINGS ─────────────────────────────────────────────────
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
      <td>${b.event_date ? new Date(b.event_date).toLocaleDateString('en-PH') : '—'}</td>
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

// ── MENU ─────────────────────────────────────────────────────
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
      <td style="color:var(--muted);font-size:13px">${m.description||'—'}</td>
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

// ── VENUES ───────────────────────────────────────────────────
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

// ── STAFF ─────────────────────────────────────────────────────
async function loadStaff() {
  const q = document.getElementById('staff-q').value;
  const d = await api({action:'staff', q});
  const tb = document.getElementById('staff-tbody');
  if (!d) return;
  tb.innerHTML = d.staff.length ? d.staff.map(s => `
    <tr>
      <td><strong>${s.name}</strong></td>
      <td>${s.role}</td>
      <td style="color:var(--muted);font-size:13px">${s.assignment||'—'}</td>
      <td style="font-size:13px">${s.shift_start||''} – ${s.shift_end||''}</td>
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

// ── REPORTS ──────────────────────────────────────────────────
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

// ── INIT ──────────────────────────────────────────────────────
loadDashboard();
</script>
</body>
</html>
