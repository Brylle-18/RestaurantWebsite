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
      <p class="fc-val">&#8369;140-&#8369;950</p>
      <p class="fc-sub">Shareable menu rates for 4 pax</p>
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
  <h2 class="section-title">Filipino favorites,<br>good for 4 pax</h2>
  <p class="section-sub">A clean group menu with hearty Filipino classics, realistic 4-person serving prices, and ready image slots you can fill in later.</p>

  <div class="menu-table-wrap">
    <div class="menu-table-card">
      <div class="menu-table-head">
        <h3>Main Entrees</h3>
        <p><strong>All prices below are for 4 people.</strong></p>
      </div>
      <table class="menu-table">
        <thead>
          <tr>
            <th>Dish</th>
            <th>Description</th>
            <th>Price (PHP)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Pork Adobo</strong></td>
            <td>Slow-braised pork in soy sauce, vinegar, garlic, and bay leaves with a rich, savory glaze.</td>
            <td><strong>&#8369;620.00</strong></td>
          </tr>
          <tr>
            <td><strong>Sinigang na Baboy</strong></td>
            <td>Tender pork simmered in a bright tamarind broth with kangkong, radish, okra, and tomatoes.</td>
            <td><strong>&#8369;760.00</strong></td>
          </tr>
          <tr>
            <td><strong>Kare-Kare</strong></td>
            <td>Peanut-stewed beef and vegetables served with bagoong for a deep, comforting Filipino classic.</td>
            <td><strong>&#8369;880.00</strong></td>
          </tr>
          <tr>
            <td><strong>Crispy Pata</strong></td>
            <td>Golden pork knuckle with crackling skin, served with a tangy soy-vinegar dipping sauce.</td>
            <td><strong>&#8369;950.00</strong></td>
          </tr>
        </tbody>
      </table>
      <div class="menu-image-placeholders">
        <img src="" alt="Pork Adobo">
        <img src="" alt="Sinigang na Baboy">
        <img src="" alt="Kare-Kare">
        <img src="" alt="Crispy Pata">
      </div>
    </div>

    <div class="menu-table-card">
      <div class="menu-table-head">
        <h3>Sides / Noodles</h3>
        <p><strong>Made for sharing at the table.</strong></p>
      </div>
      <table class="menu-table">
        <thead>
          <tr>
            <th>Dish</th>
            <th>Description</th>
            <th>Price (PHP)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Pancit Canton</strong></td>
            <td>Wok-tossed egg noodles with vegetables, chicken, and a savory stir-fried finish.</td>
            <td><strong>&#8369;520.00</strong></td>
          </tr>
          <tr>
            <td><strong>Lumpiang Shanghai</strong></td>
            <td>Crisp bite-sized pork spring rolls served with sweet chili sauce, ideal for group sharing.</td>
            <td><strong>&#8369;420.00</strong></td>
          </tr>
          <tr>
            <td><strong>Garlic Rice Platter</strong></td>
            <td>Fragrant fried rice tossed with roasted garlic, a perfect partner for every entree.</td>
            <td><strong>&#8369;180.00</strong></td>
          </tr>
          <tr>
            <td><strong>Steamed Rice Bucket</strong></td>
            <td>Fluffy steamed rice portioned for four, ready to round out the feast.</td>
            <td><strong>&#8369;140.00</strong></td>
          </tr>
        </tbody>
      </table>
      <div class="menu-image-placeholders">
        <img src="" alt="Pancit Canton">
        <img src="" alt="Lumpiang Shanghai">
        <img src="" alt="Garlic Rice Platter">
        <img src="" alt="Steamed Rice Bucket">
      </div>
    </div>

    <div class="menu-table-card">
      <div class="menu-table-head">
        <h3>Desserts / Drinks</h3>
        <p><strong>Sweet finishes and refreshing pitchers for 4 pax.</strong></p>
      </div>
      <table class="menu-table">
        <thead>
          <tr>
            <th>Dish</th>
            <th>Description</th>
            <th>Price (PHP)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Leche Flan</strong></td>
            <td>Silky caramel custard with a smooth, creamy texture that is easy to pass around the table.</td>
            <td><strong>&#8369;220.00</strong></td>
          </tr>
          <tr>
            <td><strong>Buko Pandan</strong></td>
            <td>Chilled young coconut and pandan jelly dessert in a lightly sweet cream blend.</td>
            <td><strong>&#8369;280.00</strong></td>
          </tr>
          <tr>
            <td><strong>Iced Tea Pitcher</strong></td>
            <td>Refreshing house-brewed iced tea served in a pitcher for the whole group.</td>
            <td><strong>&#8369;180.00</strong></td>
          </tr>
          <tr>
            <td><strong>Calamansi Juice Pitcher</strong></td>
            <td>Bright and citrusy local lime cooler with a clean, refreshing finish.</td>
            <td><strong>&#8369;220.00</strong></td>
          </tr>
        </tbody>
      </table>
      <div class="menu-image-placeholders">
        <img src="" alt="Leche Flan">
        <img src="" alt="Buko Pandan">
        <img src="" alt="Iced Tea Pitcher">
        <img src="" alt="Calamansi Juice Pitcher">
      </div>
    </div>
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
      <div class="booking-helper">
        <strong>Build your booking details</strong>
        <span>Choose menu items, event extras, or venue add-ons so our team can prepare a more accurate quote before confirming.</span>
      </div>
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
        <select id="inq-venue-id" onchange="renderServiceBuilder()">
          <option value="">Loading venues...</option>
        </select>
      </div>
      <div class="service-builder" id="service-builder">
        <div class="service-builder-head">
          <div>
            <p class="builder-label">Booking Details</p>
            <h3 id="service-builder-title">Choose your dining items</h3>
          </div>
          <p class="builder-sub" id="service-builder-sub">Pick the dishes and extras you want included in your request.</p>
        </div>
        <div class="service-selection-grid">
          <div class="field">
            <label id="menu-choice-label">Menu Choices</label>
            <div class="menu-choice-grid" id="menu-choice-grid">
              <div class="selection-empty">Loading available options...</div>
            </div>
          </div>
          <div class="field">
            <label id="addon-choice-label">Add-ons</label>
            <div class="addon-choice-grid" id="addon-choice-grid">
              <div class="selection-empty">Loading add-ons...</div>
            </div>
          </div>
        </div>
        <div class="price-preview" id="price-preview"></div>
      </div>
      <div class="field"><label>Preferred Date *</label><input id="inq-date" type="date"></div>
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
let allMenuItems = [];
let availableVenues = [];
const bookingState = {
  selectedItems: new Map(),
  selectedAddons: new Map(),
};
const SERVICE_CONFIG = {
  restaurant: {
    title: 'Choose your restaurant dishes',
    subtitle: 'Add the dishes you want reserved for your table and include optional celebration extras.',
    menuLabel: 'Restaurant Menu',
    addonLabel: 'Dining Add-ons',
    categories: ['restaurant'],
    addons: [
      { code: 'restaurant-cake', name: 'Birthday cake setup', type: 'celebration', unit_price: 1800 },
      { code: 'restaurant-decor', name: 'Table styling and decor', type: 'setup', unit_price: 1200 },
      { code: 'restaurant-drinks', name: 'Bottomless iced tea station', type: 'beverage', unit_price: 950 },
    ],
  },
  catering: {
    title: 'Build your catering request',
    subtitle: 'Select tray-based dishes first, then add service support so the team can prepare a full event quotation.',
    menuLabel: 'Catering Menu',
    addonLabel: 'Catering Add-ons',
    categories: ['catering'],
    addons: [
      { code: 'catering-buffet', name: 'Buffet table setup', type: 'setup', unit_price: 3500 },
      { code: 'catering-staff', name: 'On-site servers', type: 'staff', unit_price: 2500 },
      { code: 'catering-drinks', name: 'Beverage station', type: 'beverage', unit_price: 1800 },
    ],
  },
  cafe: {
    title: 'Pick your cafe favorites',
    subtitle: 'Choose coffee, pastries, or dessert items for your reservation, then add a few extras for celebrations.',
    menuLabel: 'Cafe and Pastry Choices',
    addonLabel: 'Cafe Add-ons',
    categories: ['cafe', 'pastry'],
    addons: [
      { code: 'cafe-platter', name: 'Dessert platter add-on', type: 'dessert', unit_price: 1200 },
      { code: 'cafe-carafe', name: 'Coffee carafe refill', type: 'beverage', unit_price: 650 },
      { code: 'cafe-setup', name: 'Mini celebration setup', type: 'setup', unit_price: 900 },
    ],
  },
  venue: {
    title: 'Configure your venue package',
    subtitle: 'Choose your venue first, then add the event support items you want included in the initial quote.',
    menuLabel: 'Venue Package',
    addonLabel: 'Venue Add-ons',
    categories: [],
    addons: [
      { code: 'venue-sound', name: 'Sound system rental', type: 'equipment', unit_price: 3000 },
      { code: 'venue-projector', name: 'Projector and screen', type: 'equipment', unit_price: 2000 },
      { code: 'venue-styling', name: 'Basic event styling', type: 'setup', unit_price: 7500 },
      { code: 'venue-catering-support', name: 'Catering coordination support', type: 'service', unit_price: 5000 },
    ],
  },
};

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

function formatCurrency(amount) {
  return `&#8369;${parseFloat(amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
}

function getCurrentServiceConfig() {
  const service = document.getElementById('inq-service').value;
  return SERVICE_CONFIG[service];
}

function clearBookingSelections() {
  bookingState.selectedItems.clear();
  bookingState.selectedAddons.clear();
}

async function loadBookingCatalog() {
  const data = await api({ action: 'menu' });
  if (data?.ok) {
    allMenuItems = data.items;
  }
  renderServiceBuilder();
}

function getFilteredServiceItems(service) {
  const config = SERVICE_CONFIG[service];
  return allMenuItems.filter((item) => config.categories.includes(item.category));
}

function updateMenuSelection(itemId, quantityValue) {
  const quantity = Number(quantityValue);
  if (quantity > 0) {
    bookingState.selectedItems.set(itemId, quantity);
  } else {
    bookingState.selectedItems.delete(itemId);
  }
  renderPricePreview();
}

function toggleAddonSelection(code, checked) {
  const addon = getCurrentServiceConfig().addons.find((item) => item.code === code);
  if (!addon) {
    return;
  }
  if (checked) {
    bookingState.selectedAddons.set(code, { ...addon, quantity: 1 });
  } else {
    bookingState.selectedAddons.delete(code);
  }
  renderServiceBuilder();
}

function updateAddonQuantity(code, quantityValue) {
  const quantity = Number(quantityValue);
  if (quantity <= 0) {
    bookingState.selectedAddons.delete(code);
  } else if (bookingState.selectedAddons.has(code)) {
    bookingState.selectedAddons.set(code, {
      ...bookingState.selectedAddons.get(code),
      quantity,
    });
  }
  renderPricePreview();
}

function getPriceEstimate() {
  const service = document.getElementById('inq-service').value;
  const selectedVenueId = document.getElementById('inq-venue-id').value;
  const items = getFilteredServiceItems(service);
  let subtotal = 0;
  let hasCustomQuote = false;
  const lines = [];

  if (service === 'venue' && selectedVenueId) {
    const venue = availableVenues.find((item) => String(item.id) === String(selectedVenueId));
    if (venue) {
      subtotal += Number(venue.rate || 0);
      lines.push({ label: venue.name, detail: 'Venue rate', amount: Number(venue.rate || 0) });
    }
  }

  bookingState.selectedItems.forEach((quantity, itemId) => {
    const item = items.find((entry) => Number(entry.id) === Number(itemId));
    if (!item) {
      return;
    }
    const price = Number(item.price || 0);
    if (price <= 0) {
      hasCustomQuote = true;
      lines.push({ label: item.name, detail: `${quantity} selected`, amount: null });
      return;
    }
    const amount = price * quantity;
    subtotal += amount;
    lines.push({ label: item.name, detail: `${quantity} x ${formatCurrency(price)}`, amount });
  });

  bookingState.selectedAddons.forEach((addon) => {
    const amount = Number(addon.unit_price || 0) * addon.quantity;
    subtotal += amount;
    lines.push({ label: addon.name, detail: `${addon.quantity} x ${formatCurrency(addon.unit_price)}`, amount });
  });

  return { subtotal, hasCustomQuote, lines };
}

function renderPricePreview() {
  const preview = document.getElementById('price-preview');
  const { subtotal, hasCustomQuote, lines } = getPriceEstimate();
  const hasSelections = lines.length > 0;

  preview.innerHTML = `
    <div>
      <p class="price-preview-label">Estimated Pricing</p>
      <strong>${hasSelections ? formatCurrency(subtotal) : 'No selections yet'}</strong>
      <p class="price-preview-note">${hasCustomQuote ? 'Some selected items need a custom quote. Final price will be confirmed by the admin team.' : 'This estimate is based on your selected items and add-ons.'}</p>
    </div>
    <div class="price-line-list">
      ${hasSelections
        ? lines.map((line) => `
          <div class="price-line">
            <div>
              <span>${line.label}</span>
              <small>${line.detail}</small>
            </div>
            <strong>${line.amount === null ? 'Quoted later' : formatCurrency(line.amount)}</strong>
          </div>`).join('')
        : '<div class="selection-empty compact">Select menu items or add-ons to build your request.</div>'}
    </div>`;
}

function renderServiceBuilder() {
  const service = document.getElementById('inq-service').value;
  const config = SERVICE_CONFIG[service];
  const menuGrid = document.getElementById('menu-choice-grid');
  const addonGrid = document.getElementById('addon-choice-grid');

  document.getElementById('service-builder-title').textContent = config.title;
  document.getElementById('service-builder-sub').textContent = config.subtitle;
  document.getElementById('menu-choice-label').textContent = config.menuLabel;
  document.getElementById('addon-choice-label').textContent = config.addonLabel;

  const serviceItems = getFilteredServiceItems(service);
  if (service === 'venue') {
    const venue = availableVenues.find((item) => String(item.id) === document.getElementById('inq-venue-id').value);
    menuGrid.innerHTML = `
      <div class="booking-option static-option">
        <div>
          <strong>${venue ? venue.name : 'Choose a venue above'}</strong>
          <p>${venue ? `${venue.capacity} pax · ${venue.type} venue` : 'Select a venue to start building your event package.'}</p>
        </div>
        <span class="option-price">${venue ? formatCurrency(venue.rate) : 'Required'}</span>
      </div>`;
  } else if (!serviceItems.length) {
    menuGrid.innerHTML = '<div class="selection-empty">No menu items available for this service yet.</div>';
  } else {
    menuGrid.innerHTML = serviceItems.map((item) => `
      <div class="booking-option">
        <div class="booking-option-copy">
          <strong>${item.name}</strong>
          <p>${item.description || 'Prepared fresh for your reservation'}</p>
        </div>
        <div class="booking-option-meta">
          <span class="option-price">${Number(item.price) > 0 ? formatCurrency(item.price) : 'Custom quote'}</span>
          <input
            type="number"
            min="0"
            value="${bookingState.selectedItems.get(Number(item.id)) || 0}"
            class="option-qty"
            aria-label="Quantity for ${item.name}"
            onchange="updateMenuSelection(${item.id}, this.value)"
          >
        </div>
      </div>`).join('');
  }

  if (!config.addons.length) {
    addonGrid.innerHTML = '<div class="selection-empty">No add-ons configured for this service yet.</div>';
  } else {
    addonGrid.innerHTML = config.addons.map((addon) => {
      const activeAddon = bookingState.selectedAddons.get(addon.code);
      return `
        <div class="booking-addon ${activeAddon ? 'active' : ''}">
          <label class="addon-check">
            <input type="checkbox" ${activeAddon ? 'checked' : ''} onchange="toggleAddonSelection('${addon.code}', this.checked)">
            <span>
              <strong>${addon.name}</strong>
              <small>${formatCurrency(addon.unit_price)}</small>
            </span>
          </label>
          <input
            type="number"
            min="1"
            value="${activeAddon?.quantity || 1}"
            class="option-qty addon-qty"
            ${activeAddon ? '' : 'disabled'}
            aria-label="Quantity for ${addon.name}"
            onchange="updateAddonQuantity('${addon.code}', this.value)"
          >
        </div>`;
    }).join('');
  }

  renderPricePreview();
}

async function loadMenu() {
  const grid = document.getElementById('menu-grid');
  const search = document.getElementById('menu-search');
  if (!grid || !search) {
    return;
  }
  const query = search.value;
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
  if (button) {
    button.classList.add('active');
  }
  loadMenu();
}

async function loadVenues() {
  const grid = document.getElementById('venues-grid');
  const data = await api({ action: 'venues' });
  if (!data || !data.ok) {
    grid.innerHTML = '<p style="color:var(--muted)">Could not load venues.</p>';
    return;
  }

  availableVenues = data.venues;

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
  select.innerHTML = `<option value="">Choose a venue</option>${data.venues.map((venue) => `<option value="${venue.id}">${venue.name} (&#8369;${parseFloat(venue.rate).toLocaleString('en-PH')})</option>`).join('')}`;
  renderServiceBuilder();
}

function prefillVenueBooking(id) {
  document.getElementById('inq-service').value = 'venue';
  onServiceChange();
  document.getElementById('inq-venue-id').value = id;
  renderServiceBuilder();
  document.getElementById('inquiry').scrollIntoView({ behavior: 'smooth' });
}

function onServiceChange() {
  const service = document.getElementById('inq-service').value;
  document.getElementById('venue-select-field').style.display = service === 'venue' ? 'block' : 'none';
  clearBookingSelections();
  renderServiceBuilder();
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
  const selectedItems = Array.from(bookingState.selectedItems.entries()).map(([menu_item_id, quantity]) => ({ menu_item_id, quantity }));
  const selectedAddons = Array.from(bookingState.selectedAddons.values()).map((addon) => ({
    code: addon.code,
    name: addon.name,
    type: addon.type,
    quantity: addon.quantity,
    unit_price: addon.unit_price,
  }));

  if (service !== 'venue' && selectedItems.length === 0) {
    errorElement.textContent = 'Please choose at least one menu item for this booking.';
    errorElement.style.display = 'block';
    return;
  }
  if (service === 'venue' && !document.getElementById('inq-venue-id').value) {
    errorElement.textContent = 'Please select a venue before submitting.';
    errorElement.style.display = 'block';
    return;
  }

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
    selected_items: JSON.stringify(selectedItems),
    selected_addons: JSON.stringify(selectedAddons),
  }, 'POST');

  if (data?.ok) {
    successElement.innerHTML = `<strong>Booking submitted.</strong> Your ticket number is <strong>${data.ticket_no}</strong>. ${data.custom_quote_required ? 'Some selected items will be quoted by the team.' : `Estimated amount: <strong>${formatCurrency(data.estimated_amount)}</strong>.`} Use your ticket to track updates.`;
    successElement.style.display = 'block';
    document.getElementById('inq-name').value = '';
    document.getElementById('inq-phone').value = '';
    document.getElementById('inq-email').value = '';
    document.getElementById('inq-pax').value = '2';
    document.getElementById('inq-date').value = '';
    document.getElementById('inq-notes').value = '';
    document.getElementById('inq-service').value = 'restaurant';
    document.getElementById('inq-venue-id').value = '';
    clearBookingSelections();
    onServiceChange();
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
  const items = data.items || [];
  const addons = data.addons || [];
  result.innerHTML = `
    <div class="result-row"><span class="lbl">Ticket</span><strong>${booking.ticket_no}</strong></div>
    <div class="result-row"><span class="lbl">Name</span><span>${booking.customer_name}</span></div>
    <div class="result-row"><span class="lbl">Service</span><span style="text-transform:capitalize">${booking.service_type}</span></div>
    <div class="result-row"><span class="lbl">Event Date</span><span>${booking.event_date ? new Date(booking.event_date).toLocaleDateString('en-PH', { dateStyle: 'long' }) : 'Not set'}</span></div>
    <div class="result-row"><span class="lbl">Guests</span><span>${booking.pax} pax</span></div>
    <div class="result-row"><span class="lbl">Amount</span><span>${Number(booking.total_amount) > 0 ? formatCurrency(booking.total_amount) : 'To be quoted'}</span></div>
    <div class="result-row"><span class="lbl">Status</span><span class="badge ${booking.status}">${booking.status.replace('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase())}</span></div>
    ${booking.pricing_notes ? `<div class="result-row"><span class="lbl">Pricing Notes</span><span style="font-size:13px;color:var(--muted)">${booking.pricing_notes}</span></div>` : ''}
    ${items.length ? `<div class="result-block"><span class="lbl">Selected Items</span><div class="result-stack">${items.map((item) => `<div class="result-chip">${item.name} x${item.quantity}${Number(item.unit_price) > 0 ? ` · ${formatCurrency(item.unit_price)}` : ' · Custom quote'}</div>`).join('')}</div></div>` : ''}
    ${addons.length ? `<div class="result-block"><span class="lbl">Add-ons</span><div class="result-stack">${addons.map((addon) => `<div class="result-chip">${addon.addon_name} x${addon.quantity} · ${formatCurrency(addon.unit_price)}</div>`).join('')}</div></div>` : ''}
    ${booking.notes ? `<div class="result-row"><span class="lbl">Notes</span><span style="font-size:13px;color:var(--muted)">${booking.notes}</span></div>` : ''}`;
  result.style.display = 'block';
}

loadMenu();
loadBookingCatalog();
loadVenues();
onServiceChange();

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
