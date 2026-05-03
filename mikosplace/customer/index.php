<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Miko's Place — Seafoods, Grill & Catering</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
<style>
/* ─── RESET & VARIABLES ─────────────────────────────── */
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --green:#168a24;--green-dk:#0a5616;--green-lt:rgba(22,138,36,.10);
  --red:#b61217;  --red-dk:#7e0e11;  --red-lt:rgba(182,18,23,.09);
  --bg:#f4fbe9;   --surface:#fff;    --surface-soft:#f7fbf2;
  --text:#12311a; --muted:#58705e;   --border:rgba(18,98,33,.13);
  --shadow:0 18px 40px rgba(12,55,19,.12);
}
html{scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);line-height:1.6;
  background:radial-gradient(circle at 10% 15%,rgba(129,214,123,.20) 0,transparent 30%),
             radial-gradient(circle at 90% 85%,rgba(182,18,23,.09) 0,transparent 25%),
             linear-gradient(145deg,#f4fbe9,#eef7e5 55%,#fcfef7);}
::-webkit-scrollbar{width:8px}
::-webkit-scrollbar-thumb{background:rgba(18,98,33,.22);border-radius:999px}

/* ─── NAVBAR ─────────────────────────────────────────── */
.navbar{
  position:sticky;top:0;z-index:80;
  background:rgba(255,255,255,.90);backdrop-filter:blur(12px);
  border-bottom:1px solid var(--border);
  padding:14px 40px;display:flex;justify-content:space-between;align-items:center;gap:20px;
}
.nav-brand{display:flex;align-items:center;gap:12px;text-decoration:none;}
.nav-logo{width:44px;height:44px;border-radius:14px;object-fit:cover;
  background:linear-gradient(135deg,var(--green),var(--green-dk));display:block;}
.nav-brand-name{font-family:'Playfair Display',serif;font-size:20px;color:var(--text);}
.nav-brand-sub{font-size:11px;color:var(--muted);letter-spacing:.06em;}
nav{display:flex;gap:4px;align-items:center;}
.nav-link{
  color:var(--muted);text-decoration:none;padding:8px 14px;border-radius:999px;
  font-size:14px;font-weight:500;transition:.2s;
}
.nav-link:hover{background:var(--green-lt);color:var(--green-dk);}
.nav-link.active{background:var(--green-lt);color:var(--green-dk);font-weight:700;}
.btn-nav{
  background:linear-gradient(135deg,var(--red),#d22327);color:#fff;
  padding:10px 18px;border-radius:999px;font-family:'DM Sans',sans-serif;
  font-size:14px;font-weight:700;border:none;cursor:pointer;
  box-shadow:0 6px 16px rgba(182,18,23,.22);transition:.2s;text-decoration:none;
}
.btn-nav:hover{transform:translateY(-2px);box-shadow:0 10px 22px rgba(182,18,23,.28);}
.nav-toggle{display:none;background:none;border:none;cursor:pointer;font-size:22px;color:var(--text);}

/* ─── HERO ───────────────────────────────────────────── */
#home{
  min-height:92vh;display:flex;align-items:center;
  padding:60px 40px;position:relative;overflow:hidden;
}
.hero-content{max-width:640px;position:relative;z-index:2;}
.hero-tag{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(22,138,36,.10);color:var(--green-dk);
  padding:8px 16px;border-radius:999px;font-size:12px;font-weight:700;
  letter-spacing:.1em;text-transform:uppercase;margin-bottom:22px;
}
.hero-content h1{
  font-family:'Playfair Display',serif;font-size:clamp(44px,6vw,72px);
  line-height:1.05;margin-bottom:20px;
}
.hero-content h1 em{font-style:italic;color:var(--green);}
.hero-content p{font-size:17px;color:var(--muted);max-width:500px;margin-bottom:30px;}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;}
.btn-hero{
  padding:16px 28px;border-radius:999px;font-family:'DM Sans',sans-serif;
  font-size:16px;font-weight:700;border:none;cursor:pointer;transition:.25s;text-decoration:none;
}
.btn-hero-primary{
  background:linear-gradient(135deg,var(--green),var(--green-dk));color:#fff;
  box-shadow:0 12px 28px rgba(10,86,22,.25);
}
.btn-hero-primary:hover{transform:translateY(-3px);box-shadow:0 18px 36px rgba(10,86,22,.30);}
.btn-hero-outline{
  background:transparent;color:var(--text);
  border:2px solid var(--border);
}
.btn-hero-outline:hover{border-color:var(--green);color:var(--green);}

/* Hero floating card */
.hero-float{
  position:absolute;right:60px;top:50%;transform:translateY(-50%);
  display:grid;gap:14px;z-index:2;
}
.float-card{
  background:rgba(255,255,255,.92);border-radius:22px;padding:18px 22px;
  box-shadow:var(--shadow);border:1px solid rgba(255,255,255,.8);
  backdrop-filter:blur(10px);min-width:190px;
}
.float-card .fc-label{font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700;}
.float-card .fc-val{font-family:'Playfair Display',serif;font-size:26px;color:var(--green-dk);}
.float-card .fc-sub{font-size:12px;color:var(--muted);}
.float-card.red .fc-val{color:var(--red);}
/* Hero BG blur circles */
.hero-blob{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:1;}
.hero-blob.b1{width:500px;height:500px;background:rgba(22,138,36,.12);top:-100px;right:-50px;}
.hero-blob.b2{width:300px;height:300px;background:rgba(182,18,23,.08);bottom:0;right:200px;}

/* ─── SECTION COMMONS ────────────────────────────────── */
.section{padding:80px 40px;}
.section-label{display:inline-block;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--red);margin-bottom:10px;}
.section-title{font-family:'Playfair Display',serif;font-size:clamp(30px,4vw,44px);line-height:1.1;margin-bottom:16px;}
.section-sub{color:var(--muted);font-size:16px;max-width:560px;margin-bottom:44px;}

/* ─── SERVICES STRIP ─────────────────────────────────── */
#services{background:linear-gradient(135deg,rgba(10,86,22,.97),rgba(22,138,36,.90));color:#fff;padding:60px 40px;}
.services-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:18px;}
.service-tile{
  background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.15);
  border-radius:22px;padding:24px 20px;text-align:center;
  transition:.25s;cursor:default;
}
.service-tile:hover{background:rgba(255,255,255,.18);transform:translateY(-4px);}
.service-tile .icon{font-size:32px;display:block;margin-bottom:12px;}
.service-tile h4{font-size:16px;font-weight:700;margin-bottom:6px;}
.service-tile p{font-size:12px;color:rgba(255,255,255,.72);}

/* ─── MENU ───────────────────────────────────────────── */
.menu-filters{display:flex;gap:10px;margin-bottom:28px;flex-wrap:wrap;}
.filter-btn{
  padding:9px 18px;border-radius:999px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
  border:2px solid var(--border);background:var(--surface);color:var(--muted);cursor:pointer;transition:.2s;
}
.filter-btn.active,.filter-btn:hover{background:var(--green-dk);color:#fff;border-color:var(--green-dk);}
.menu-search{
  flex:1;min-width:200px;padding:10px 16px;border:2px solid var(--border);border-radius:14px;
  font-family:'DM Sans',sans-serif;font-size:14px;background:#fafff7;color:var(--text);outline:none;transition:.2s;
}
.menu-search:focus{border-color:var(--green);box-shadow:0 0 0 3px var(--green-lt);}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px;}
.dish-card{
  background:var(--surface);border-radius:22px;padding:22px;
  box-shadow:var(--shadow);border:1px solid rgba(255,255,255,.8);transition:.25s;
}
.dish-card:hover{transform:translateY(-4px);}
.dish-cat{font-size:10px;text-transform:uppercase;letter-spacing:.1em;font-weight:700;color:var(--muted);margin-bottom:8px;}
.dish-name{font-family:'Playfair Display',serif;font-size:19px;margin-bottom:6px;}
.dish-desc{font-size:13px;color:var(--muted);margin-bottom:14px;min-height:38px;}
.dish-price{color:var(--red);font-size:20px;font-weight:800;}
.dish-price.custom{color:var(--muted);font-size:14px;font-style:italic;}

/* ─── VENUES ─────────────────────────────────────────── */
#venues{background:var(--surface-soft);}
.venues-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:22px;}
.venue-card{
  background:var(--surface);border-radius:26px;overflow:hidden;
  box-shadow:var(--shadow);border:1px solid rgba(255,255,255,.8);transition:.25s;
}
.venue-card:hover{transform:translateY(-5px);box-shadow:0 30px 60px rgba(12,55,19,.15);}
.venue-header{
  padding:28px 26px;
  background:linear-gradient(135deg,rgba(10,86,22,.96),rgba(22,138,36,.88));color:#fff;
}
.venue-header h3{font-family:'Playfair Display',serif;font-size:24px;margin-bottom:4px;}
.venue-type-tag{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.7);font-weight:700;}
.venue-body{padding:22px 26px;}
.venue-rate{color:var(--red);font-size:26px;font-weight:800;margin-bottom:10px;}
.venue-detail{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:14px;}
.venue-detail:last-of-type{border-bottom:none;}
.venue-detail span{color:var(--muted);font-size:13px;}
.venue-avail{
  display:inline-block;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;margin-top:12px;margin-bottom:14px;
}
.venue-avail.yes{background:var(--green-lt);color:var(--green-dk);}
.venue-avail.no{background:var(--red-lt);color:var(--red-dk);}
.btn-book{
  display:block;width:100%;padding:13px;border-radius:14px;
  background:linear-gradient(135deg,var(--green),var(--green-dk));
  color:#fff;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
  border:none;cursor:pointer;transition:.2s;
}
.btn-book:hover{filter:brightness(1.05);}
.btn-book:disabled{background:#ccc;cursor:default;}

/* ─── INQUIRY FORM ───────────────────────────────────── */
#inquiry{background:linear-gradient(135deg,rgba(10,86,22,.96),rgba(22,138,36,.90));color:#fff;}
#inquiry .section-label{color:#ffd9d9;}
.form-card{
  background:rgba(255,255,255,.97);border-radius:28px;
  padding:36px;box-shadow:0 40px 80px rgba(5,24,10,.20);max-width:680px;
}
.form-grid{display:grid;gap:16px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.field label{display:block;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;}
.field input,.field select,.field textarea{
  width:100%;padding:13px 15px;border:2px solid var(--border);border-radius:13px;
  font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);background:#fafff7;
  outline:none;transition:.2s;
}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--green);box-shadow:0 0 0 3px var(--green-lt);}
.field textarea{min-height:90px;resize:vertical;}
.btn-submit{
  width:100%;padding:16px;border:none;border-radius:16px;cursor:pointer;
  background:linear-gradient(135deg,var(--green),var(--green-dk));color:#fff;
  font-family:'DM Sans',sans-serif;font-size:16px;font-weight:700;
  box-shadow:0 12px 28px rgba(10,86,22,.28);transition:.25s;margin-top:4px;
}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(10,86,22,.32);}
.success-msg{
  background:var(--green-lt);border:2px solid var(--green);
  color:var(--green-dk);border-radius:16px;padding:16px 20px;
  font-weight:700;font-size:15px;margin-top:16px;display:none;
}
.error-msg{
  background:var(--red-lt);border:2px solid var(--red);
  color:var(--red-dk);border-radius:16px;padding:14px 18px;
  font-weight:600;font-size:14px;margin-bottom:14px;display:none;
}

/* ─── TRACKING ───────────────────────────────────────── */
#track{background:var(--surface-soft);}
.track-box{max-width:560px;}
.track-input-row{display:flex;gap:10px;margin-bottom:20px;}
.track-input{
  flex:1;padding:14px 16px;border:2px solid var(--border);border-radius:14px;
  font-family:'DM Sans',sans-serif;font-size:15px;background:#fafff7;color:var(--text);outline:none;
  transition:.2s;
}
.track-input:focus{border-color:var(--green);box-shadow:0 0 0 3px var(--green-lt);}
.btn-track{
  padding:14px 22px;border-radius:14px;background:linear-gradient(135deg,var(--green),var(--green-dk));
  color:#fff;font-family:'DM Sans',sans-serif;font-size:15px;font-weight:700;
  border:none;cursor:pointer;transition:.2s;white-space:nowrap;
}
.btn-track:hover{transform:translateY(-2px);}
.booking-result{
  background:var(--surface);border-radius:22px;padding:24px;
  box-shadow:var(--shadow);border:1px solid rgba(255,255,255,.8);display:none;
}
.result-row{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);font-size:14px;}
.result-row:last-child{border-bottom:none;}
.result-row .lbl{color:var(--muted);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;}
.badge{display:inline-block;padding:5px 12px;border-radius:999px;font-size:12px;font-weight:700;}
.badge.pending{background:rgba(59,130,246,.1);color:#1d4ed8;}
.badge.confirmed{background:var(--green-lt);color:var(--green-dk);}
.badge.in_progress{background:rgba(234,179,8,.1);color:#854d0e;}
.badge.completed{background:var(--green-lt);color:var(--green-dk);}
.badge.cancelled{background:var(--red-lt);color:var(--red-dk);}
.track-err{color:var(--red);font-size:14px;font-weight:600;padding:12px 0;display:none;}

/* ─── FOOTER ─────────────────────────────────────────── */
footer{
  background:linear-gradient(180deg,rgba(5,54,15,.98),rgba(3,38,10,.99));
  color:rgba(255,255,255,.8);padding:50px 40px 30px;
}
.footer-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr;gap:40px;margin-bottom:36px;}
.footer-brand h3{font-family:'Playfair Display',serif;font-size:26px;color:#fff;margin-bottom:8px;}
.footer-brand p{font-size:13px;line-height:1.7;}
.footer-col h4{font-size:13px;text-transform:uppercase;letter-spacing:.1em;font-weight:700;color:rgba(255,255,255,.5);margin-bottom:14px;}
.footer-col ul{list-style:none;}
.footer-col ul li{margin-bottom:9px;font-size:14px;}
.footer-col ul li a{color:rgba(255,255,255,.75);text-decoration:none;transition:.2s;}
.footer-col ul li a:hover{color:#fff;}
.footer-bottom{border-top:1px solid rgba(255,255,255,.1);padding-top:22px;display:flex;justify-content:space-between;align-items:center;font-size:13px;}
.footer-bottom a{color:rgba(255,255,255,.55);text-decoration:none;transition:.2s;}
.footer-bottom a:hover{color:#fff;}

/* ─── SPINNER ────────────────────────────────────────── */
.spinner{display:inline-block;width:16px;height:16px;border:3px solid var(--border);border-top-color:var(--green);border-radius:50%;animation:spin .7s linear infinite;vertical-align:middle;margin-right:6px;}
@keyframes spin{to{transform:rotate(360deg)}}
.loading-state{text-align:center;padding:40px;color:var(--muted);}

/* ─── MOBILE NAV ─────────────────────────────────────── */
@media(max-width:900px){
  .hero-float{display:none;}
  .footer-grid{grid-template-columns:1fr;}
  .form-row{grid-template-columns:1fr;}
}
@media(max-width:720px){
  .navbar{padding:12px 20px;}
  nav{display:none;position:absolute;top:70px;left:0;right:0;flex-direction:column;background:rgba(255,255,255,.97);padding:16px 20px;border-bottom:1px solid var(--border);gap:4px;}
  nav.open{display:flex;}
  .nav-toggle{display:block;}
  #home,.section,#services,#inquiry,footer{padding-left:20px;padding-right:20px;}
  #home{min-height:auto;padding-top:50px;padding-bottom:50px;}
}
@media(max-width:480px){
  .track-input-row{flex-direction:column;}
  .hero-actions{flex-direction:column;}
  .btn-hero{text-align:center;}
}
</style>
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
  <a href="#home" class="nav-brand">
    <img src="/mikosplace/assets/mikosplace.jpg" alt="Logo" class="nav-logo"
         onerror="this.style.background='linear-gradient(135deg,#168a24,#0a5616)'">
    <div>
      <div class="nav-brand-name">Miko's Place</div>
      <div class="nav-brand-sub">Seafoods · Grill · Catering</div>
    </div>
  </a>
  <button class="nav-toggle" onclick="document.querySelector('nav').classList.toggle('open')">☰</button>
  <nav id="main-nav">
    <a href="#home" class="nav-link active">Home</a>
    <a href="#menu-section" class="nav-link">Menu</a>
    <a href="#venues" class="nav-link">Venues</a>
    <a href="#inquiry" class="nav-link">Book Now</a>
    <a href="#track" class="nav-link">Track Booking</a>
  </nav>
  <a href="#inquiry" class="btn-nav">Make a Reservation</a>
</header>

<!-- HERO -->
<section id="home">
  <div class="hero-blob b1"></div>
  <div class="hero-blob b2"></div>
  <div class="hero-content">
    <div class="hero-tag">🍽 Bamboo-Inspired Filipino Hospitality</div>
    <h1>Savor the taste of <em>home</em>, elevated.</h1>
    <p>From fresh seafood dining and artisan pastries to full catering and event venue packages — all in one beloved place.</p>
    <div class="hero-actions">
      <a href="#menu-section" class="btn-hero btn-hero-primary">Explore Our Menu</a>
      <a href="#inquiry" class="btn-hero btn-hero-outline">Reserve a Venue →</a>
    </div>
  </div>
  <div class="hero-float">
    <div class="float-card">
      <p class="fc-label">Est. Price Range</p>
      <p class="fc-val">₱249–₱599</p>
      <p class="fc-sub">Restaurant dishes per serve</p>
    </div>
    <div class="float-card red">
      <p class="fc-label">Event Packages</p>
      <p class="fc-val">3 Venues</p>
      <p class="fc-sub">Pool · Banquet · Function</p>
    </div>
  </div>
</section>

<!-- SERVICES STRIP -->
<section id="services">
  <div style="text-align:center;margin-bottom:36px">
    <p class="section-label">What We Offer</p>
    <h2 style="font-family:'Playfair Display',serif;font-size:clamp(26px,3.5vw,38px);color:#fff;line-height:1.2">Everything you need for a perfect occasion</h2>
  </div>
  <div class="services-grid">
    <div class="service-tile"><span class="icon">🦐</span><h4>Restaurant</h4><p>Fresh seafood, grilled dishes & Filipino favorites</p></div>
    <div class="service-tile"><span class="icon">🍱</span><h4>Catering</h4><p>Custom menus for events of all sizes</p></div>
    <div class="service-tile"><span class="icon">☕</span><h4>Cafe</h4><p>Brewed coffee, drinks & pastries</p></div>
    <div class="service-tile"><span class="icon">🎂</span><h4>Pastries</h4><p>Fresh-baked daily selections</p></div>
    <div class="service-tile"><span class="icon">🏊</span><h4>Pool & Pavilion</h4><p>Outdoor pool with covered pavilion, 50 pax</p></div>
    <div class="service-tile"><span class="icon">🏛</span><h4>Banquet Hall</h4><p>Elegant indoor venue, up to 40 pax</p></div>
    <div class="service-tile"><span class="icon">✨</span><h4>Function Hall</h4><p>Premium event space, 50 pax capacity</p></div>
  </div>
</section>

<!-- MENU SECTION -->
<section class="section" id="menu-section">
  <p class="section-label">Our Menu</p>
  <h2 class="section-title">Crafted with love,<br>served with pride</h2>
  <p class="section-sub">Browse our menu — from signature seafood dishes to light cafe fare.</p>

  <div class="menu-filters">
    <input class="menu-search" id="menu-search" placeholder="Search dishes…" oninput="loadMenu()">
    <button class="filter-btn active" onclick="setMenuFilter('',this)">All</button>
    <button class="filter-btn" onclick="setMenuFilter('restaurant',this)">🦐 Restaurant</button>
    <button class="filter-btn" onclick="setMenuFilter('catering',this)">🍱 Catering</button>
    <button class="filter-btn" onclick="setMenuFilter('cafe',this)">☕ Cafe</button>
    <button class="filter-btn" onclick="setMenuFilter('pastry',this)">🎂 Pastry</button>
  </div>

  <div class="menu-grid" id="menu-grid">
    <div class="loading-state"><span class="spinner"></span> Loading menu…</div>
  </div>
</section>

<!-- VENUES SECTION -->
<section class="section" id="venues">
  <p class="section-label">Event Venues</p>
  <h2 class="section-title">Host your special moments here</h2>
  <p class="section-sub">Choose from our indoor, outdoor, and premium event spaces, each with dedicated staff support.</p>

  <div class="venues-grid" id="venues-grid">
    <div class="loading-state"><span class="spinner"></span> Loading venues…</div>
  </div>
</section>

<!-- INQUIRY / BOOKING FORM -->
<section class="section" id="inquiry">
  <p class="section-label">Book Now</p>
  <h2 class="section-title" style="color:#fff;">Reserve your experience</h2>
  <p class="section-sub" style="color:rgba(255,255,255,.78)">Fill in the form and our team will confirm your booking shortly.</p>

  <div class="form-card">
    <div class="error-msg" id="inq-error"></div>
    <div class="form-grid">
      <div class="form-row">
        <div class="field"><label>Full Name *</label><input id="inq-name" placeholder="Your full name"></div>
        <div class="field"><label>Phone Number *</label><input id="inq-phone" placeholder="09XXXXXXXXX"></div>
      </div>
      <div class="field"><label>Email Address</label><input id="inq-email" type="email" placeholder="email@example.com (optional)"></div>
      <div class="form-row">
        <div class="field"><label>Service Type *</label>
          <select id="inq-service" onchange="onServiceChange()">
            <option value="restaurant">Restaurant Dining</option>
            <option value="catering">Catering Service</option>
            <option value="cafe">Cafe / Pastries</option>
            <option value="venue">Venue Rental</option>
          </select>
        </div>
        <div class="field"><label>Number of Guests *</label><input id="inq-pax" type="number" min="1" value="2" placeholder="e.g. 10"></div>
      </div>
      <div class="field" id="venue-select-field" style="display:none">
        <label>Select Venue</label>
        <select id="inq-venue-id">
          <option value="">Loading venues…</option>
        </select>
      </div>
      <div class="field"><label>Preferred Date</label><input id="inq-date" type="date"></div>
      <div class="field"><label>Special Requests / Notes</label><textarea id="inq-notes" placeholder="Menu preferences, dietary restrictions, occasion details…"></textarea></div>
    </div>
    <button class="btn-submit" onclick="submitInquiry()">Submit Reservation Request</button>
    <div class="success-msg" id="inq-success"></div>
  </div>
</section>

<!-- BOOKING TRACKER -->
<section class="section" id="track">
  <p class="section-label">Track Your Booking</p>
  <h2 class="section-title">Check your reservation status</h2>
  <p class="section-sub">Enter your ticket number (e.g. #MP-301) to see the current status of your booking.</p>

  <div class="track-box">
    <div class="track-input-row">
      <input class="track-input" id="track-input" placeholder="#MP-301" onkeydown="if(event.key==='Enter')trackBooking()">
      <button class="btn-track" onclick="trackBooking()">Track →</button>
    </div>
    <div class="track-err" id="track-err">Booking not found. Please check your ticket number.</div>
    <div class="booking-result" id="booking-result"></div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <h3>Miko's Place</h3>
      <p>Seafoods, Grill and Catering Services.<br>Your trusted venue for dining, events, and memorable occasions.</p>
    </div>
    <div class="footer-col">
      <h4>Services</h4>
      <ul>
        <li><a href="#menu-section">Restaurant Menu</a></li>
        <li><a href="#menu-section">Catering</a></li>
        <li><a href="#menu-section">Cafe & Pastries</a></li>
        <li><a href="#venues">Event Venues</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#inquiry">Make a Reservation</a></li>
        <li><a href="#track">Track Booking</a></li>
        <li><a href="/mikosplace/admin/login.php">Admin Login</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= date('Y') ?> Miko's Place. All rights reserved.</span>
    <span>Powered by <a href="#">Franco Miguel's Place</a></span>
  </div>
</footer>

<script>
const API = 'api.php';
let menuFilter = '';

// ── Smooth nav active state ──────────────────────────────────
const sections = ['home','menu-section','venues','inquiry','track'];
window.addEventListener('scroll', () => {
  let cur = '';
  sections.forEach(id => {
    const el = document.getElementById(id);
    if (el && window.scrollY >= el.offsetTop - 100) cur = id;
  });
  document.querySelectorAll('.nav-link').forEach(l => {
    const href = l.getAttribute('href').replace('#','');
    l.classList.toggle('active', href === cur);
  });
});

// ── API helper ───────────────────────────────────────────────
async function api(params, method = 'GET') {
  const url = method === 'GET' ? API + '?' + new URLSearchParams(params) : API;
  const opts = method === 'GET' ? {} : {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams(params)
  };
  const r = await fetch(url, opts);
  return r.json();
}

// ── Menu ─────────────────────────────────────────────────────
async function loadMenu() {
  const q = document.getElementById('menu-search').value;
  const g = document.getElementById('menu-grid');
  g.innerHTML = '<div class="loading-state"><span class="spinner"></span> Loading…</div>';
  const d = await api({action:'menu', q, category:menuFilter});
  if (!d || !d.ok) { g.innerHTML='<p style="color:var(--muted);text-align:center;padding:40px">Could not load menu.</p>'; return; }
  g.innerHTML = d.items.length ? d.items.map(m => `
    <div class="dish-card">
      <p class="dish-cat">${m.category}</p>
      <h3 class="dish-name">${m.name}</h3>
      <p class="dish-desc">${m.description||'A delicious offering from our kitchen'}</p>
      ${m.price > 0 ? `<p class="dish-price">₱${parseFloat(m.price).toLocaleString('en-PH',{minimumFractionDigits:2})}</p>` : '<p class="dish-price custom">Custom quote</p>'}
    </div>`).join('')
    : '<p style="color:var(--muted);text-align:center;grid-column:1/-1;padding:40px">No items found for this category.</p>';
}

function setMenuFilter(cat, btn) {
  menuFilter = cat;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadMenu();
}

// ── Venues ────────────────────────────────────────────────────
async function loadVenues() {
  const g = document.getElementById('venues-grid');
  const d = await api({action:'venues'});
  if (!d || !d.ok) { g.innerHTML='<p style="color:var(--muted)">Could not load venues.</p>'; return; }
  g.innerHTML = d.venues.map(v => `
    <div class="venue-card">
      <div class="venue-header">
        <h3>${v.name}</h3>
        <p class="venue-type-tag">${v.type} venue</p>
      </div>
      <div class="venue-body">
        <p class="venue-rate">₱${parseFloat(v.rate).toLocaleString('en-PH',{minimumFractionDigits:2})}</p>
        <div class="venue-detail"><strong>Capacity</strong><span>${v.capacity} pax</span></div>
        <div class="venue-detail"><strong>Type</strong><span style="text-transform:capitalize">${v.type}</span></div>
        <div class="venue-detail"><strong>Description</strong><span>${v.description||'Perfect for your occasion'}</span></div>
        <span class="venue-avail ${v.is_available?'yes':'no'}">${v.is_available?'✓ Available':'✕ Booked'}</span>
        <button class="btn-book" onclick="prefillVenueBooking(${v.id},'${v.name.replace(/'/,"\\'")}')" ${v.is_available?'':'disabled'}>
          ${v.is_available ? 'Book This Venue' : 'Currently Unavailable'}
        </button>
      </div>
    </div>`).join('');

  // Also populate venue dropdown in form
  const sel = document.getElementById('inq-venue-id');
  sel.innerHTML = d.venues.map(v=>`<option value="${v.id}">${v.name} (₱${parseFloat(v.rate).toLocaleString('en-PH')})</option>`).join('');
}

function prefillVenueBooking(id, name) {
  document.getElementById('inq-service').value = 'venue';
  onServiceChange();
  document.getElementById('inq-venue-id').value = id;
  document.getElementById('inquiry').scrollIntoView({behavior:'smooth'});
}

function onServiceChange() {
  const v = document.getElementById('inq-service').value;
  document.getElementById('venue-select-field').style.display = v === 'venue' ? 'block' : 'none';
}

// ── Inquiry Submission ────────────────────────────────────────
async function submitInquiry() {
  const errEl = document.getElementById('inq-error');
  const sucEl = document.getElementById('inq-success');
  errEl.style.display = 'none'; sucEl.style.display = 'none';

  const name  = document.getElementById('inq-name').value.trim();
  const phone = document.getElementById('inq-phone').value.trim();
  if (!name || !phone) { errEl.textContent = 'Please fill in your name and phone number.'; errEl.style.display='block'; return; }

  const service = document.getElementById('inq-service').value;
  const d = await api({
    action:'inquire',
    customer_name: name,
    customer_phone: phone,
    customer_email: document.getElementById('inq-email').value,
    service_type: service,
    pax: document.getElementById('inq-pax').value,
    event_date: document.getElementById('inq-date').value,
    venue_id: service === 'venue' ? document.getElementById('inq-venue-id').value : '',
    notes: document.getElementById('inq-notes').value,
  }, 'POST');

  if (d?.ok) {
    sucEl.innerHTML = `✅ <strong>Booking submitted!</strong> Your ticket number is <strong>${d.ticket_no}</strong>. Screenshot this and use it to track your booking.`;
    sucEl.style.display = 'block';
    document.getElementById('inq-name').value = '';
    document.getElementById('inq-phone').value = '';
    document.getElementById('inq-email').value = '';
    document.getElementById('inq-notes').value = '';
  } else {
    errEl.textContent = d?.error || 'Something went wrong. Please try again.';
    errEl.style.display = 'block';
  }
}

// ── Booking Tracker ───────────────────────────────────────────
async function trackBooking() {
  const ticket = document.getElementById('track-input').value.trim();
  const res    = document.getElementById('booking-result');
  const err    = document.getElementById('track-err');
  res.style.display = 'none'; err.style.display = 'none';
  if (!ticket) return;

  const d = await api({action:'track', ticket});
  if (!d?.ok) { err.style.display = 'block'; return; }
  const b = d.booking;
  const fmt = n => '₱' + parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2});
  res.innerHTML = `
    <div class="result-row"><span class="lbl">Ticket</span><strong>${b.ticket_no}</strong></div>
    <div class="result-row"><span class="lbl">Name</span><span>${b.customer_name}</span></div>
    <div class="result-row"><span class="lbl">Service</span><span style="text-transform:capitalize">${b.service_type}</span></div>
    <div class="result-row"><span class="lbl">Event Date</span><span>${b.event_date ? new Date(b.event_date).toLocaleDateString('en-PH',{dateStyle:'long'}) : '—'}</span></div>
    <div class="result-row"><span class="lbl">Guests</span><span>${b.pax} pax</span></div>
    <div class="result-row"><span class="lbl">Amount</span><span>${b.total_amount > 0 ? fmt(b.total_amount) : 'To be quoted'}</span></div>
    <div class="result-row"><span class="lbl">Status</span><span class="badge ${b.status}">${b.status.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase())}</span></div>
    ${b.notes ? `<div class="result-row"><span class="lbl">Notes</span><span style="font-size:13px;color:var(--muted)">${b.notes}</span></div>` : ''}`;
  res.style.display = 'block';
}

// ── Init ──────────────────────────────────────────────────────
loadMenu();
loadVenues();
</script>
</body>
</html>
