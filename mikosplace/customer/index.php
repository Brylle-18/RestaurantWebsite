<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Miko's Place - Seafoods, Grill and Catering</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/mikosplace/assets/customer-styles.css">
</head>
<body>

<header class="navbar">
  <a href="#home" class="nav-brand">
    <img
      src="/mikosplace/assets/mikosplace.jpg"
      alt="Miko's Place logo"
      class="nav-logo"
      onerror="this.style.background='linear-gradient(135deg,#168a24,#0a5616)'"
    >
    <div>
      <div class="nav-brand-name">Miko's Place</div>
    </div>
  </a>
  <button class="nav-toggle" type="button" aria-label="Toggle navigation" onclick="document.getElementById('main-nav').classList.toggle('open')">&#9776;</button>
  <nav id="main-nav">
    <a href="#home" class="nav-link active">Home</a>
    <a href="#menu-section" class="nav-link">Menu</a>
    <a href="#venues" class="nav-link">Venues</a>
    <a href="#inquiry" class="nav-link">Book Now</a>
    <a href="#track" class="nav-link">Track Booking</a>
  </nav>
  <a href="#inquiry" class="btn-nav">Make a Reservation</a>
</header>

<section id="home">
  <div class="hero-blob b1"></div>
  <div class="hero-blob b2"></div>
  <div class="hero-content">
    <div class="hero-tag">Seafood, Grill, and Catering Services</div>
    <h1>Miko's Place</h1>
    <p>From fresh seafood dining and artisan pastries to full catering and event venue packages, everything is here for your next gathering.</p>
    <div class="hero-actions">
      <a href="#menu-section" class="btn-hero btn-hero-primary">Explore Our Menu</a>
      <a href="#inquiry" class="btn-hero btn-hero-outline">Reserve a Venue &rarr;</a>
    </div>
  </div>
  <div class="hero-float">
    <div class="float-card">
      <p class="fc-label">Est. Price Range</p>
      <p class="fc-val">&#8369;249-&#8369;599</p>
      <p class="fc-sub">Restaurant dishes per serve</p>
    </div>
    <div class="float-card red">
      <p class="fc-label">Event Packages</p>
      <p class="fc-val">3 Venues</p>
      <p class="fc-sub">Pool, Banquet, and Function</p>
    </div>
  </div>
</section>

<section id="services">
  <div style="text-align:center;margin-bottom:36px">
    <p class="section-label">What We Offer</p>
    <h2 style="font-family:'Playfair Display',serif;font-size:clamp(26px,3.5vw,38px);color:#fff;line-height:1.2">Everything you need for a perfect occasion</h2>
  </div>
  <div class="services-grid">
    <div class="service-tile"><span class="icon">Restaurant</span><h4>Restaurant</h4><p>Fresh seafood, grilled dishes, and Filipino favorites</p></div>
    <div class="service-tile"><span class="icon">Catering</span><h4>Catering</h4><p>Custom menus for events of all sizes</p></div>
    <div class="service-tile"><span class="icon">Cafe</span><h4>Cafe</h4><p>Brewed coffee, drinks, and pastries</p></div>
    <div class="service-tile"><span class="icon">Pastries</span><h4>Pastries</h4><p>Fresh-baked daily selections</p></div>
    <div class="service-tile"><span class="icon">Pool</span><h4>Pool and Pavilion</h4><p>Outdoor pool with covered pavilion for 50 guests</p></div>
    <div class="service-tile"><span class="icon">Banquet</span><h4>Banquet Hall</h4><p>Elegant indoor venue for up to 40 guests</p></div>
    <div class="service-tile"><span class="icon">Events</span><h4>Function Hall</h4><p>Premium event space with 50-person capacity</p></div>
  </div>
</section>

<section class="section" id="menu-section">
  <p class="section-label">Our Menu</p>
  <h2 class="section-title">Crafted with love,<br>served with pride</h2>
  <p class="section-sub">Browse our menu, from signature seafood dishes to light cafe fare.</p>

  <div class="menu-filters">
    <input class="menu-search" id="menu-search" placeholder="Search dishes..." oninput="loadMenu()">
    <button class="filter-btn active" type="button" onclick="setMenuFilter('', this)">All</button>
    <button class="filter-btn" type="button" onclick="setMenuFilter('restaurant', this)">Restaurant</button>
    <button class="filter-btn" type="button" onclick="setMenuFilter('catering', this)">Catering</button>
    <button class="filter-btn" type="button" onclick="setMenuFilter('cafe', this)">Cafe</button>
    <button class="filter-btn" type="button" onclick="setMenuFilter('pastry', this)">Pastry</button>
  </div>

  <div class="menu-grid" id="menu-grid">
    <div class="loading-state"><span class="spinner"></span> Loading menu...</div>
  </div>
</section>

<section class="section" id="venues">
  <p class="section-label">Event Venues</p>
  <h2 class="section-title">Host your special moments here</h2>
  <p class="section-sub">Choose from our indoor, outdoor, and premium event spaces, each with dedicated staff support.</p>

  <div class="venues-grid" id="venues-grid">
    <div class="loading-state"><span class="spinner"></span> Loading venues...</div>
  </div>
</section>

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
        <div class="field">
          <label>Service Type *</label>
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
          <option value="">Loading venues...</option>
        </select>
      </div>
      <div class="field"><label>Preferred Date</label><input id="inq-date" type="date"></div>
      <div class="field"><label>Special Requests / Notes</label><textarea id="inq-notes" placeholder="Menu preferences, dietary restrictions, occasion details..."></textarea></div>
    </div>
    <button class="btn-submit" type="button" onclick="submitInquiry()">Submit Reservation Request</button>
    <div class="success-msg" id="inq-success"></div>
  </div>
</section>

<section class="section" id="track">
  <p class="section-label">Track Your Booking</p>
  <h2 class="section-title">Check your reservation status</h2>
  <p class="section-sub">Enter your ticket number (for example, #MP-301) to see the current status of your booking.</p>

  <div class="track-box">
    <div class="track-input-row">
      <input class="track-input" id="track-input" placeholder="#MP-301" onkeydown="if(event.key==='Enter')trackBooking()">
      <button class="btn-track" type="button" onclick="trackBooking()">Track &rarr;</button>
    </div>
    <div class="track-err" id="track-err">Booking not found. Please check your ticket number.</div>
    <div class="booking-result" id="booking-result"></div>
  </div>
</section>

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
        <li><a href="#menu-section">Cafe and Pastries</a></li>
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
    <span>&copy; <?= date('Y') ?> Miko's Place. All rights reserved.</span>
    <span>Powered by <a href="#">Franco Miguel's Place</a></span>
  </div>
</footer>

<script>
const API = 'api.php';
let menuFilter = '';

const sections = ['home', 'menu-section', 'venues', 'inquiry', 'track'];
window.addEventListener('scroll', () => {
  let currentSection = '';
  sections.forEach((id) => {
    const element = document.getElementById(id);
    if (element && window.scrollY >= element.offsetTop - 100) {
      currentSection = id;
    }
  });

  document.querySelectorAll('.nav-link').forEach((link) => {
    const href = link.getAttribute('href').replace('#', '');
    link.classList.toggle('active', href === currentSection);
  });
});

async function api(params, method = 'GET') {
  const url = method === 'GET' ? `${API}?${new URLSearchParams(params)}` : API;
  const options = method === 'GET' ? {} : {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(params),
  };
  const response = await fetch(url, options);
  return response.json();
}

async function loadMenu() {
  const query = document.getElementById('menu-search').value;
  const grid = document.getElementById('menu-grid');
  grid.innerHTML = '<div class="loading-state"><span class="spinner"></span> Loading...</div>';

  const data = await api({ action: 'menu', q: query, category: menuFilter });
  if (!data || !data.ok) {
    grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:40px">Could not load menu.</p>';
    return;
  }

  grid.innerHTML = data.items.length
    ? data.items.map((item) => `
      <div class="dish-card">
        <p class="dish-cat">${item.category}</p>
        <h3 class="dish-name">${item.name}</h3>
        <p class="dish-desc">${item.description || 'A delicious offering from our kitchen'}</p>
        ${Number(item.price) > 0
          ? `<p class="dish-price">&#8369;${parseFloat(item.price).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</p>`
          : '<p class="dish-price custom">Custom quote</p>'}
      </div>`).join('')
    : '<p style="color:var(--muted);text-align:center;grid-column:1/-1;padding:40px">No items found for this category.</p>';
}

function setMenuFilter(category, button) {
  menuFilter = category;
  document.querySelectorAll('.filter-btn').forEach((item) => item.classList.remove('active'));
  button.classList.add('active');
  loadMenu();
}

async function loadVenues() {
  const grid = document.getElementById('venues-grid');
  const data = await api({ action: 'venues' });
  if (!data || !data.ok) {
    grid.innerHTML = '<p style="color:var(--muted)">Could not load venues.</p>';
    return;
  }

  grid.innerHTML = data.venues.map((venue) => `
    <div class="venue-card">
      <div class="venue-header">
        <h3>${venue.name}</h3>
        <p class="venue-type-tag">${venue.type} venue</p>
      </div>
      <div class="venue-body">
        <p class="venue-rate">&#8369;${parseFloat(venue.rate).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</p>
        <div class="venue-detail"><strong>Capacity</strong><span>${venue.capacity} pax</span></div>
        <div class="venue-detail"><strong>Type</strong><span style="text-transform:capitalize">${venue.type}</span></div>
        <div class="venue-detail"><strong>Description</strong><span>${venue.description || 'Perfect for your occasion'}</span></div>
        <span class="venue-avail ${venue.is_available ? 'yes' : 'no'}">${venue.is_available ? '&#10003; Available' : '&#10005; Booked'}</span>
        <button class="btn-book" type="button" onclick='prefillVenueBooking(${venue.id})' ${venue.is_available ? '' : 'disabled'}>
          ${venue.is_available ? 'Book This Venue' : 'Currently Unavailable'}
        </button>
      </div>
    </div>`).join('');

  const select = document.getElementById('inq-venue-id');
  select.innerHTML = data.venues.map((venue) => `<option value="${venue.id}">${venue.name} (&#8369;${parseFloat(venue.rate).toLocaleString('en-PH')})</option>`).join('');
}

function prefillVenueBooking(id) {
  document.getElementById('inq-service').value = 'venue';
  onServiceChange();
  document.getElementById('inq-venue-id').value = id;
  document.getElementById('inquiry').scrollIntoView({ behavior: 'smooth' });
}

function onServiceChange() {
  const service = document.getElementById('inq-service').value;
  document.getElementById('venue-select-field').style.display = service === 'venue' ? 'block' : 'none';
}

async function submitInquiry() {
  const errorElement = document.getElementById('inq-error');
  const successElement = document.getElementById('inq-success');
  errorElement.style.display = 'none';
  successElement.style.display = 'none';

  const name = document.getElementById('inq-name').value.trim();
  const phone = document.getElementById('inq-phone').value.trim();
  if (!name || !phone) {
    errorElement.textContent = 'Please fill in your name and phone number.';
    errorElement.style.display = 'block';
    return;
  }

  const service = document.getElementById('inq-service').value;
  const data = await api({
    action: 'inquire',
    customer_name: name,
    customer_phone: phone,
    customer_email: document.getElementById('inq-email').value,
    service_type: service,
    pax: document.getElementById('inq-pax').value,
    event_date: document.getElementById('inq-date').value,
    venue_id: service === 'venue' ? document.getElementById('inq-venue-id').value : '',
    notes: document.getElementById('inq-notes').value,
  }, 'POST');

  if (data?.ok) {
    successElement.innerHTML = `<strong>Booking submitted.</strong> Your ticket number is <strong>${data.ticket_no}</strong>. Use it to track your booking.`;
    successElement.style.display = 'block';
    document.getElementById('inq-name').value = '';
    document.getElementById('inq-phone').value = '';
    document.getElementById('inq-email').value = '';
    document.getElementById('inq-notes').value = '';
  } else {
    errorElement.textContent = data?.error || 'Something went wrong. Please try again.';
    errorElement.style.display = 'block';
  }
}

async function trackBooking() {
  const ticket = document.getElementById('track-input').value.trim();
  const result = document.getElementById('booking-result');
  const error = document.getElementById('track-err');
  result.style.display = 'none';
  error.style.display = 'none';
  if (!ticket) {
    return;
  }

  const data = await api({ action: 'track', ticket });
  if (!data?.ok) {
    error.style.display = 'block';
    return;
  }

  const booking = data.booking;
  const formatAmount = (amount) => `&#8369;${parseFloat(amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
  result.innerHTML = `
    <div class="result-row"><span class="lbl">Ticket</span><strong>${booking.ticket_no}</strong></div>
    <div class="result-row"><span class="lbl">Name</span><span>${booking.customer_name}</span></div>
    <div class="result-row"><span class="lbl">Service</span><span style="text-transform:capitalize">${booking.service_type}</span></div>
    <div class="result-row"><span class="lbl">Event Date</span><span>${booking.event_date ? new Date(booking.event_date).toLocaleDateString('en-PH', { dateStyle: 'long' }) : 'Not set'}</span></div>
    <div class="result-row"><span class="lbl">Guests</span><span>${booking.pax} pax</span></div>
    <div class="result-row"><span class="lbl">Amount</span><span>${Number(booking.total_amount) > 0 ? formatAmount(booking.total_amount) : 'To be quoted'}</span></div>
    <div class="result-row"><span class="lbl">Status</span><span class="badge ${booking.status}">${booking.status.replace('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase())}</span></div>
    ${booking.notes ? `<div class="result-row"><span class="lbl">Notes</span><span style="font-size:13px;color:var(--muted)">${booking.notes}</span></div>` : ''}`;
  result.style.display = 'block';
}

loadMenu();
loadVenues();

// Scroll overlay effect for green components
window.addEventListener('scroll', () => {
  const greenSections = document.querySelectorAll('#services, #inquiry, .btn-hero-primary, .filter-btn.active, .btn-submit, .btn-track, .btn-book');
  let showOverlay = false;
  
  greenSections.forEach(section => {
    const rect = section.getBoundingClientRect();
    // Check if section is visible in viewport
    if (rect.top < window.innerHeight && rect.bottom > 0) {
      showOverlay = true;
    }
  });
  
  document.body.classList.toggle('scroll-overlay', showOverlay);
});
</script>
</body>
</html>
