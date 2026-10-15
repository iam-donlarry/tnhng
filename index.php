<?php
// index.php - The Next Highlandah (TNH Season 3) Public Campaign Microsite
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();

// Fetch outlets & challenges for display
$outlets = [];
$challenges = [];
if ($pdo) {
    $outlets = $pdo->query("SELECT * FROM outlets WHERE status = 'active' ORDER BY id ASC")->fetchAll();
    $challenges = $pdo->query("SELECT * FROM challenges WHERE is_active = 1 ORDER BY points DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Primary SEO Meta Tags -->
  <title>The Next Highlandah Season 3 | William Lawson's Official Campaign</title>
  <meta name="title" content="The Next Highlandah Season 3 | William Lawson's Official Campaign">
  <meta name="description" content="TNH Season 3 is live in Nigeria. Register your official Clan Pass, conquer wheel spin challenges at participating bars, and bank 100 points to enter the Grand Finale VIP draw.">
  <meta name="keywords" content="The Next Highlandah, William Lawson's Nigeria, TNH Season 3, Scotch Whisky, Highlandah Clan Pass, wheel spin challenge, earn points, Lagos nightlife, Abuja bars, Port Harcourt lounges, Earn It">
  <meta name="author" content="William Lawson's Blended Scotch Whisky">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <link rel="canonical" href="https://tnhng.com/">

  <!-- Open Graph / Facebook / WhatsApp Meta Tags -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://tnhng.com/">
  <meta property="og:site_name" content="The Next Highlandah | William Lawson's">
  <meta property="og:title" content="The Next Highlandah Season 3 | Don't Ask For It. Earn It!">
  <meta property="og:description" content="Register your Clan ID once. Conquer wheel spin challenges at participating bars nationwide and bank points toward the 100-point Grand Finale threshold.">
  <meta property="og:image" content="https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg">
  <meta property="og:image:secure_url" content="https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg">
  <meta property="og:image:type" content="image/jpeg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="William Lawson's The Next Highlandah Season 3 Promotional Campaign">
  <meta property="og:locale" content="en_NG">

  <!-- Twitter / X Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="https://tnhng.com/">
  <meta name="twitter:title" content="The Next Highlandah Season 3 | William Lawson's">
  <meta name="twitter:description" content="Don't ask for it. Earn it! Register your Clan Pass, hit partner outlets across Nigeria, and bank 100 points for the Grand Finale draw.">
  <meta name="twitter:image" content="https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg">

  <!-- Favicon & Mobile Meta -->
  <link rel="icon" type="image/svg+xml" href="images/logo.svg">
  <link rel="apple-touch-icon" href="images/logo.svg">
  <meta name="theme-color" content="#00c853">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

  <link rel="stylesheet" href="css/style.css?v=<?= filemtime(__DIR__ . '/css/style.css') ?>">
  
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Structured Data: Organization, WebSite & Promotional Campaign Event Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "https://tnhng.com/#organization",
        "name": "William Lawson's",
        "url": "https://tnhng.com/",
        "logo": {
          "@type": "ImageObject",
          "url": "https://tnhng.com/images/logo.svg"
        },
        "sameAs": [
          "https://www.instagram.com/williamlawsons_ng",
          "https://twitter.com/williamlawsons"
        ]
      },
      {
        "@type": "WebSite",
        "@id": "https://tnhng.com/#website",
        "url": "https://tnhng.com/",
        "name": "The Next Highlandah",
        "description": "Official William Lawson's Season 3 promotional campaign microsite in Nigeria",
        "publisher": {
          "@id": "https://tnhng.com/#organization"
        },
        "inLanguage": "en-NG"
      },
      {
        "@type": "Event",
        "@id": "https://tnhng.com/#campaign-event",
        "name": "The Next Highlandah Season 3: Don't Ask For It. Earn It!",
        "description": "William Lawson's nationwide on-trade consumer promotion across Nigeria. Register for a Clan Pass, complete physical wheel spin challenges with brand ambassadors, and earn 100 points for the Grand Finale draw.",
        "startDate": "2026-10-01T18:00:00+01:00",
        "endDate": "2026-12-31T23:59:59+01:00",
        "eventStatus": "https://schema.org/EventScheduled",
        "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
        "location": {
          "@type": "Place",
          "name": "Partner Bars & Lounges Nationwide",
          "address": {
            "@type": "PostalAddress",
            "addressCountry": "NG",
            "addressRegion": "Nigeria"
          }
        },
        "image": [
          "https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg"
        ],
        "organizer": {
          "@id": "https://tnhng.com/#organization"
        },
        "offers": {
          "@type": "Offer",
          "url": "https://tnhng.com/",
          "price": "0",
          "priceCurrency": "NGN",
          "availability": "https://schema.org/InStock",
          "validFrom": "2026-10-01"
        },
        "audience": {
          "@type": "Audience",
          "audienceType": "Legal Drinking Age (18+)"
        }
      }
    ]
  }
  </script>
</head>
<body>

  <!-- ================= AGE GATE MODAL (18+ MANDATORY COMPLIANCE) ================= -->
  <div class="modal-overlay" id="age-gate-modal">
    <div class="modal-card age-gate-card">
      <div class="age-gate-poster">
        <img src="images/image.png" alt="William Lawson's - Highlandahs Only!">
      </div>
      <div class="age-gate-content">
        <img src="images/logo.svg" alt="William Lawson's" class="age-gate-logo">
        <div class="age-gate-badge">
          <i data-lucide="shield-alert" style="width: 14px; height: 14px;"></i> 18+ Legal Drinking Age Required
        </div>
        <h2 class="age-gate-title">ARE YOU BOLD ENOUGH TO ENTER?</h2>
        <p class="age-gate-copy">
          This promotional campaign is strictly reserved for persons of legal drinking age in Nigeria (18+). Please enter your year of birth to proceed into The Clan.
        </p>

        <div class="form-group age-year-group">
          <label class="form-label" for="age-gate-year">Year of Birth</label>
          <div class="age-year-input-wrap">
            <input 
              type="text" 
              id="age-gate-year" 
              class="form-control age-year-input" 
              placeholder="____" 
              maxlength="4" 
              inputmode="numeric" 
              pattern="[0-9]{4}" 
              autocomplete="bday-year"
              required
            >
          </div>
          <div class="age-year-hint">Enter your 4-digit birth year</div>
        </div>

        <button id="age-gate-confirm" class="btn btn-primary btn-lg" style="width: 100%;">
          <i data-lucide="shield-check"></i> I am 18+ — Enter The Clan
        </button>

        <p class="age-gate-disclaimer">
          William Lawson's encourages responsible drinking. No Rules. Great Scotch.
        </p>
      </div>
    </div>
  </div>

  <!-- ================= TOP CAMPAIGN BANNER ================= -->
  <div class="campaign-topbar">
    <div class="container campaign-topbar-inner">
      <div class="topbar-item">
        <span class="status-indicator"></span>
        <span><strong>TNH SEASON 3 IS LIVE</strong> • 100 POINTS TO QUALIFY FOR GRAND FINALE</span>
      </div>
      <div class="topbar-links">
        <a href="promoter" class="topbar-link"><i data-lucide="smartphone" style="width: 13px; height: 13px;"></i> Ambassador Portal</a>
        <span class="topbar-sep">|</span>
        <a href="admin" class="topbar-link"><i data-lucide="shield" style="width: 13px; height: 13px;"></i> Admin Console</a>
      </div>
    </div>
  </div>

  <!-- ================= PRIMARY NAVIGATION ================= -->
  <header class="navbar">
    <div class="container nav-container">
      <a href="./" class="brand-logo" aria-label="William Lawson's The Next Highlandah">
        <img src="images/logo.svg" alt="William Lawson's" class="brand-logo-img">
        <div class="brand-text-wrap">
          <span class="brand-title">THE NEXT HIGHLANDAH</span>
          <span class="brand-subtitle">SEASON 3 • OFFICIAL PROMOTION</span>
        </div>
      </a>

      <nav class="nav-desktop">
        <ul class="nav-links">
          <li><a href="./" class="nav-link active">Home</a></li>
          <li><a href="leaderboard" class="nav-link">Leaderboard</a></li>
          <li><a href="outlets" class="nav-link">Outlets</a></li>
        </ul>
      </nav>

      <div class="nav-actions">
        <a href="#check-points-anchor" class="btn btn-primary btn-sm">
          <i data-lucide="award"></i> Check Points
        </a>
      </div>

      <!-- Mobile Hamburger Toggle -->
      <button class="mobile-menu-btn" id="mobile-nav-toggle" aria-label="Open mobile menu">
        <i data-lucide="menu"></i>
      </button>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div class="mobile-nav-drawer" id="mobile-nav-drawer">
      <ul>
        <li><a href="./" class="nav-link mobile-link active">Home</a></li>
        <li><a href="leaderboard" class="nav-link mobile-link">Full Leaderboard</a></li>
        <li><a href="outlets" class="nav-link mobile-link">Outlet Tour & Dates</a></li>
        <li style="border-top: 1px solid var(--border-subtle); margin: 0.5rem 0; padding-top: 0.5rem;">
          <span style="font-size: 0.7rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px; padding: 0 0.5rem;">On This Page</span>
        </li>
        <li><a href="#playbook" class="nav-link mobile-link">How It Works</a></li>
        <li><a href="#brand-range" class="nav-link mobile-link">The Highlandah Range</a></li>
        <li><a href="#scorecard" class="nav-link mobile-link">Challenges & Scorecard</a></li>
        <li><a href="#vip-table" class="nav-link mobile-link">VIP Finale Table</a></li>
      </ul>
      <div class="mobile-nav-actions">
        <a href="#check-points-anchor" class="btn btn-primary mobile-link" style="width: 100%;">
          <i data-lucide="award"></i> Check My Clan Points
        </a>
        <div class="mobile-portal-grid">
          <a href="promoter" class="btn btn-outline btn-sm">
            <i data-lucide="smartphone"></i> Ambassador
          </a>
          <a href="admin" class="btn btn-outline btn-sm">
            <i data-lucide="shield"></i> Admin
          </a>
        </div>
      </div>
    </div>
  </header>

  <main>
    <!-- ================= HERO SECTION (COMMERCIAL EDITORIAL) ================= -->
    <section class="hero-section">
      <div class="container hero-container">
        <div class="hero-content">
          <div class="campaign-pill">
            <span class="status-indicator"></span>
            <span>WILLIAM LAWSON'S PROMOTIONAL CAMPAIGN</span>
          </div>

          <h1 class="hero-title">
            DON'T ASK FOR IT.<br>
            <span class="text-accent">EARN IT!</span>
          </h1>

          <p class="hero-lead">
            Register your unique Clan ID once. Walk into any participating bar or lounge across Nigeria, let our Brand Ambassador spin the wheel, conquer physical challenges, and bank locked points.
          </p>

          <div class="hero-target-banner">
            <div class="target-flag">100 PTS</div>
            <div class="target-text">
              <strong>Grand Finale Threshold</strong>
              <span>Hit 100 points across any outlets to enter the draw for the celebrity VIP table.</span>
            </div>
          </div>

          <div class="hero-cta-group">
            <a href="leaderboard" class="btn btn-primary btn-lg">
              <i data-lucide="trophy"></i> Live Clan Standings
            </a>
            <a href="outlets" class="btn btn-outline btn-lg">
              <i data-lucide="map-pin"></i> Find Outlets to Play
            </a>
          </div>

          <!-- Official Campaign KPIs -->
          <div class="hero-kpi-strip">
            <div class="kpi-item">
              <span class="kpi-number" id="stat-total-participants">2,410</span>
              <span class="kpi-label">Registered Highlandahs</span>
            </div>
            <div class="kpi-sep"></div>
            <div class="kpi-item">
              <span class="kpi-number" id="stat-total-points">18,920</span>
              <span class="kpi-label">Points Awarded</span>
            </div>
            <div class="kpi-sep"></div>
            <div class="kpi-item">
              <span class="kpi-number" id="stat-total-qualified">48</span>
              <span class="kpi-label">Finalists Qualified</span>
            </div>
          </div>
        </div>

        <!-- Hero Combo: William Lawson's Bottles & Official Clan Ticket -->
        <div class="hero-visual">
          <div class="hero-product-combo">
            <!-- Brand Bottle Lineup -->
            <div class="hero-bottles-pod">
              <img src="images/whiskey_bottles.png" alt="William Lawson's Blended Scotch Whisky & Spirit Drink" class="hero-bottles-img">
              <div class="hero-bottles-tag">
                <span>NO RULES. GREAT SCOTCH.</span>
              </div>
            </div>

            <!-- Digital Clan Ticket (Redesigned Compact Near-Square Clan Pass) -->
            <div class="hero-ticket-frame tilt-card sheen-glow" id="hero-clan-pass">
              <!-- Warrior Artwork as Card Background -->
              <div class="ticket-bg-art" aria-hidden="true"></div>

              <div class="ticket-header-strip">
                <div class="ticket-logo-mark">
                  <span>WL</span> OFFICIAL CLAN PASS
                </div>
                <span class="ticket-status-stamp">UNIVERSAL PASS</span>
              </div>

              <div class="ticket-details-block">
                <div class="ticket-id-display">
                  <span class="ticket-label">OFFICIAL CLAN ID</span>
                  <span class="pass-id">TNH-7701</span>
                </div>
                <div class="ticket-holder-info pass-info">
                  <span class="ticket-label">PARTICIPANT</span>
                  <h4>Tu**** B*****</h4>
                </div>
              </div>

              <!-- Road to Finale Progress Meter -->
              <div class="ticket-meter-box">
                <div class="meter-labels">
                  <span>ROAD TO 100 PTS FINALE</span>
                  <span class="points-val">105 / 100 PTS</span>
                </div>
                <div class="progress-track">
                  <div class="progress-fill" style="width: 100%;"></div>
                </div>
                <div class="ticket-meter-footer">
                  <span class="ticket-badge-qualified">GRAND FINALE QUALIFIED</span>
                  <span class="ticket-outlet-note">Active Clan Pass</span>
                </div>
              </div>

              <div class="ticket-notches">
                <span class="notch notch-left"></span>
                <span class="notch notch-right"></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ticker Strip -->
      <div class="campaign-ticker-bar">
        <div class="container ticker-container">
          <div class="ticker-col">
            <span class="ticker-num">01</span>
            <div>
              <strong>Single Clan Pass</strong>
              <p>Register once, play at any partner outlet nationwide</p>
            </div>
          </div>
          <div class="ticker-divider"></div>
          <div class="ticker-col">
            <span class="ticker-num">02</span>
            <div>
              <strong>Physical Bottle Spin</strong>
              <p>Challenges completed in-person with on-site ambassadors</p>
            </div>
          </div>
          <div class="ticker-divider"></div>
          <div class="ticker-col">
            <span class="ticker-num">03</span>
            <div>
              <strong>Locked Point Allocation</strong>
              <p>Fixed scorecard scoring, immediate live syncing</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION SEPARATOR (GREEN BOTTOM DISTRESSED EDGE - TOP OF SCROLLING TEXT) -->
    <div class="section-separator sep-bottom-green"></div>

    <!-- ================= KINETIC OPPOSING PARALLAX STRIP 1 ================= -->
    <div class="kinetic-text-strip" aria-hidden="true">
      <div class="kinetic-track kinetic-track-left">
        <span class="kinetic-text">NO RULES<span class="kinetic-dot">&bull;</span>GREAT SCOTCH<span class="kinetic-dot">&bull;</span>DON'T ASK FOR IT<span class="kinetic-dot">&bull;</span>EARN IT<span class="kinetic-dot">&bull;</span>NO RULES<span class="kinetic-dot">&bull;</span>GREAT SCOTCH<span class="kinetic-dot">&bull;</span>DON'T ASK FOR IT<span class="kinetic-dot">&bull;</span>EARN IT</span>
      </div>
      <div class="kinetic-track kinetic-track-right">
        <span class="kinetic-text outline-text">THE NEXT HIGHLANDAH<span class="kinetic-dot">&bull;</span>SEASON 3<span class="kinetic-dot">&bull;</span>100 POINTS TO FINALE<span class="kinetic-dot">&bull;</span>HIGHLANDAHS ONLY<span class="kinetic-dot">&bull;</span>THE NEXT HIGHLANDAH<span class="kinetic-dot">&bull;</span>SEASON 3</span>
      </div>
    </div>

    <!-- SECTION SEPARATOR (GREEN TOP DISTRESSED EDGE - BOTTOM OF SCROLLING TEXT) -->
    <div class="section-separator sep-top-green"></div>

    <!-- ================= THE ACTIVATION PLAYBOOK (STEP BY STEP) ================= -->
    <section class="section section-playbook" id="playbook">
      <div class="container">
        <div class="section-header">
          <span class="section-kicker">Campaign Architecture</span>
          <h2 class="section-heading">HOW THE PROMOTION WORKS</h2>
          <p class="section-subheading">
            No games are played online. The microsite is your official pass and broadcast tracker; the real action happens in-person at our partner bars and lounges.
          </p>
        </div>

        <div class="playbook-flow">
          <!-- Step 1 -->
          <div class="playbook-step">
            <div class="step-num-box">01</div>
            <div class="step-content">
              <h3 class="step-title">Register Once Online or On-Site</h3>
              <p class="step-desc">
                Fill out the 1PD registration form to generate your official Clan Pass and permanent Clan ID (e.g. <code>TNH-7701</code>). Take a screenshot or save it to your phone.
              </p>
              <div class="step-note">
                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> One-time 18+ registration
              </div>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="playbook-step">
            <div class="step-num-box">02</div>
            <div class="step-content">
              <h3 class="step-title">Visit Any Scheduled Outlet</h3>
              <p class="step-desc">
                Check our activation calendar and head to any participating outlet in Lagos, Abuja, or Port Harcourt. Walk up to the William Lawson's Brand Ambassador and present your Clan ID.
              </p>
              <div class="step-note">
                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Nationwide multi-outlet validity
              </div>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="playbook-step">
            <div class="step-num-box">03</div>
            <div class="step-content">
              <h3 class="step-title">The Physical Wheel Spin</h3>
              <p class="step-desc">
                Our ambassador spins the official William Lawson's wheel at the activation bar. The wheel selects your challenge category: <strong>Bold</strong>, <strong>Unconventional</strong>, or <strong>Good-Humored</strong>.
              </p>
              <div class="step-note">
                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Physical, live challenge
              </div>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="playbook-step">
            <div class="step-num-box">04</div>
            <div class="step-content">
              <h3 class="step-title">Locked Point Allocation</h3>
              <p class="step-desc">
                Upon completing your challenge, the ambassador selects the challenge on their console. Points are <strong>strictly fixed and non-editable</strong> from the official scorecard, instantly crediting your profile.
              </p>
              <div class="step-note">
                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Transparent, immutable audit trail
              </div>
            </div>
          </div>

          <!-- Step 5 -->
          <div class="playbook-step step-highlight">
            <div class="step-num-box">05</div>
            <div class="step-content">
              <h3 class="step-title">Reach 100 Points & Qualify</h3>
              <p class="step-desc">
                Accumulate 100 points across any combination of partner outlets to secure your Grand Finale qualification badge and enter the draw for the VIP celebrity creator table.
              </p>
              <div class="step-note step-note-accent">
                <i data-lucide="crown" style="width: 14px; height: 14px;"></i> Grand Finale draw seat secured
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION SEPARATOR (BLACK BOTTOM DISTRESSED EDGE) -->
    <div class="section-separator sep-bottom-black"></div>

    <!-- ================= BRAND AWARENESS: THE HIGHLANDAH RANGE & SIGNATURE SERVES ================= -->
    <section class="section section-brand-range" id="brand-range">
      <div class="container">
        <div class="section-header">
          <span class="section-kicker">Brand Awareness & Spirits Portfolio</span>
          <h2 class="section-heading">NO RULES. GREAT SCOTCH.</h2>
          <p class="section-subheading">
            William Lawson's doesn't follow traditional scotch whisky rules. Crafted with big, bold malts and unpeated scotch character, discover our signature bottles and refreshing on-premise serves.
          </p>
        </div>

        <div class="brand-showcase-grid">
          <!-- Card 1: Product Lineup -->
          <div class="brand-showcase-card tilt-card sheen-glow">
            <div class="brand-card-img-wrap">
              <img src="images/whiskey_bottles.png" alt="William Lawson's Bottle Lineup" class="brand-card-img">
            </div>
            <h3 class="brand-card-title">THE HIGHLANDAH RANGE</h3>
            <p class="brand-card-desc">
              From our flagship Blended Scotch Whisky aged in Scotland, to our smooth Vanilla Spiced spirit drink and premixed ready-to-drink Whisky & Cola cans—crafted for bold individuals who dare to stand out.
            </p>
            <div class="brand-pills-row">
              <span class="brand-mini-pill">Blended Scotch Whisky</span>
              <span class="brand-mini-pill">Vanilla Spiced Drink</span>
              <span class="brand-mini-pill">Whisky & Cola Can</span>
            </div>
          </div>

          <!-- Card 2: Signature Bar Serves -->
          <div class="brand-showcase-card tilt-card sheen-glow">
            <div class="brand-card-img-wrap">
              <img src="images/drinks.png" alt="William Lawson's Signature Chilled Serves" class="brand-card-img">
            </div>
            <h3 class="brand-card-title">SERVED BOLD ON-PREMISE</h3>
            <p class="brand-card-desc">
              Hit our partner bars and lounges during live activations to experience our signature chilled serves over ice. Taste the bold, sweet character with cola, soda, or spiced ginger.
            </p>
            <div class="brand-pills-row">
              <span class="brand-mini-pill">On The Rocks</span>
              <span class="brand-mini-pill">Highball & Cola</span>
              <span class="brand-mini-pill">Spiced & Ginger</span>
            </div>
          </div>
        </div>

        <!-- Serves Ritual Callout -->
        <div class="serves-banner">
          <div class="serves-content">
            <span class="section-kicker" style="margin-bottom: 0.25rem;">Activation Bar Experience</span>
            <h3>SPIN. PLAY. ENJOY GREAT SCOTCH.</h3>
            <p>
              Every challenge you conquer at our activation tour is paired with great whisky. Head to the bar, order your favorite William Lawson's serve, and let our ambassador spin the wheel for your next points challenge!
            </p>
            <div class="serves-badges">
              <div class="serves-badge-item">
                <i data-lucide="check-circle" style="color: var(--wl-green); width: 16px; height: 16px;"></i>
                <span>Bold, sweet, unpeated profile</span>
              </div>
              <div class="serves-badge-item">
                <i data-lucide="check-circle" style="color: var(--wl-green); width: 16px; height: 16px;"></i>
                <span>Served ice-cold at partner outlets</span>
              </div>
              <div class="serves-badge-item">
                <i data-lucide="check-circle" style="color: var(--wl-green); width: 16px; height: 16px;"></i>
                <span>Strictly 18+ Drink Responsibly</span>
              </div>
            </div>
          </div>
          <div class="serves-img-wrap">
            <img src="images/drinks.png" alt="Chilled William Lawson's Serves" class="serves-img">
          </div>
        </div>
      </div>
    </section>

    <!-- ================= CHALLENGES & OFFICIAL SCORECARD ================= 
    <section class="section section-scorecard" id="scorecard">
      <div class="container">
        <div class="section-header">
          <span class="section-kicker">Official Rules & Scorecard</span>
          <h2 class="section-heading">CHALLENGES & POINT VALUES</h2>
          <p class="section-subheading">
            Every challenge carries a predetermined, locked point allocation. Review the challenges you could be assigned during a wheel spin.
          </p>
        </div>

        <div class="scorecard-table-wrap">
          <div class="scorecard-header-row">
            <div>Challenge Name & Description</div>
            <div style="text-align: center;">Category</div>
            <div style="text-align: right;">Points Awarded</div>
          </div>

          <div class="scorecard-list">
            <?php foreach ($challenges as $ch): 
              $isBold = ($ch['category'] === 'Bold');
              $isUncov = ($ch['category'] === 'Unconventional');
            ?>
              <div class="scorecard-item">
                <div class="scorecard-info">
                  <h3 class="scorecard-title"><?= htmlspecialchars($ch['title']) ?></h3>
                  <p class="scorecard-desc"><?= htmlspecialchars($ch['description']) ?></p>
                  <span class="scorecard-outlet-tag"><i data-lucide="map-pin" style="width: 12px; height: 12px;"></i> Available at All Activation Outlets</span>
                </div>
                <div class="scorecard-category-col">
                  <span class="category-badge <?= $isBold ? 'cat-bold' : ($isUncov ? 'cat-unconventional' : 'cat-good-humored') ?>">
                    <?= htmlspecialchars($ch['category']) ?>
                  </span>
                </div>
                <div class="scorecard-points-col">
                  <span class="scorecard-points-value">+<?= htmlspecialchars($ch['points']) ?></span>
                  <span class="scorecard-points-unit">LOCKED PTS</span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section> -->

    <!-- SECTION SEPARATOR (DISTRESSED EDGE) -->
    <div class="section-separator sep-bottom-black"></div>

    <!-- ================= 1PD REGISTRATION: JOIN THE CLAN (TEMPORARILY COMMENTED OUT) =================
    <section class="section section-registration" id="join-clan">
      <div class="container registration-container">
        <div class="registration-card">
          <div class="registration-header">
            <span class="section-kicker">1PD Participant Registration</span>
            <h2 class="registration-title">FORGE YOUR CLAN PASS</h2>
            <p class="registration-subtitle">
              Register once to receive your permanent Clan ID. Present your ID at any activation outlet across Nigeria to accumulate points toward the Grand Finale.
            </p>
          </div>

          <form id="clan-register-form" class="registration-form">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Full Legal Name *</label>
                <input type="text" name="full_name" class="form-control" placeholder="e.g. Tunde Bakare" required>
              </div>

              <div class="form-group">
                <label class="form-label">Phone Number (WhatsApp) *</label>
                <input type="tel" name="phone" class="form-control" placeholder="e.g. 08012345678" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="e.g. tunde@example.com" required>
              </div>

              <div class="form-group">
                <label class="form-label">Date of Birth (Must be 18+) *</label>
                <input type="date" name="dob" class="form-control" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Primary Activation Outlet *</label>
                <select name="outlet_id" class="form-control" required>
                  <option value="">-- Select Nearest Outlet --</option>
                  <?php foreach ($outlets as $o): ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?> (<?= htmlspecialchars($o['city']) ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Instagram or TikTok Handle (Optional)</label>
                <input type="text" name="instagram" class="form-control" placeholder="e.g. @tundetitan">
              </div>
            </div>

            <div class="terms-group">
              <input type="checkbox" id="terms" required>
              <label for="terms" class="terms-label">
                I confirm that I am at least 18 years of age. I agree to the campaign terms and consent to receiving points updates and promotional notices from William Lawson's (TNH Season 3).
              </label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
              <i data-lucide="sparkles"></i> Forge My Clan Pass
            </button>
          </form>
        </div>
      </div>
    </section>
    ================= END 1PD REGISTRATION ================= -->

    <!-- ================= STANDINGS & CHECK POINTS COMMAND CENTER ================= -->
    <section class="section section-standings" id="leaderboard-hub">
      <div class="container">
        <!-- Integrated Points Checker Banner -->
        <div class="lookup-banner" id="check-points-anchor">
          <div class="lookup-banner-content">
            <span class="lookup-pill"><i data-lucide="search" style="width: 13px; height: 13px;"></i> PARTICIPANT ARCHIVE</span>
            <h3 class="lookup-heading">CHECK YOUR CLAN STANDINGS & ACTIVITY</h3>
            <p class="lookup-desc">
              Enter your Clan ID (e.g. <code>TNH-7701</code>) or registered Email Address to inspect your verified scores, road-to-100 meter, and outlet history.
            </p>
          </div>

          <form id="points-lookup-form" class="lookup-form">
            <div class="lookup-input-wrap">
              <input type="text" id="lookup-query" class="form-control lookup-input" placeholder="Enter Clan ID (TNH-XXXX) or Email Address..." required>
            </div>
            <button type="submit" class="btn btn-primary lookup-btn">
              <i data-lucide="search"></i> Check Points
            </button>
          </form>
        </div>

        <!-- Live Leaderboard -->
        <div class="leaderboard-wrapper">
          <div class="leaderboard-header">
            <div>
              <span class="section-kicker">Live National Rankings</span>
              <h2 class="section-heading" style="margin-bottom: 0.25rem;">NATIONAL CLAN LEADERBOARD</h2>
              <p class="section-subheading" style="margin: 0;">Live audit of all participants across every outlet. 100+ points earns Grand Finale qualification.</p>
            </div>

            <div class="leaderboard-filters">
              <div class="search-filter-wrap">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" id="leaderboard-search" class="form-control filter-input" placeholder="Filter by ID, Name, or Email...">
              </div>

              <div class="outlet-filter-wrap">
                <select id="leaderboard-outlet-filter" class="form-control filter-select">
                  <option value="">All Outlets Nationwide</option>
                  <?php foreach ($outlets as $o): ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="leaderboard-table-container">
            <table class="leaderboard-table">
              <thead>
                <tr>
                  <th style="width: 80px;">Rank</th>
                  <th>Clan ID</th>
                  <th>Participant</th>
                  <th>Total Points</th>
                  <th>Outlets Visited</th>
                  <th>Qualification Status</th>
                </tr>
              </thead>
              <tbody id="leaderboard-tbody">
                <tr>
                  <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-dim);">
                    Loading verified Clan standings...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="view-all-cta-box">
            <a href="leaderboard.php" class="btn btn-primary btn-lg">
              <i data-lucide="trophy"></i> View Full National Standings & Clan Podium &rarr;
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= OUTLET TOUR DATES & LIFESTYLE CAN TOWER ================= -->
    <section class="section section-outlets" id="outlets-tour">
      <div class="container">
        <div class="section-header">
          <span class="section-kicker">Activation Calendar</span>
          <h2 class="section-heading">ACTIVATION OUTLETS & TOUR DATES</h2>
          <p class="section-subheading">
            Visit our scheduled activation venues to meet our Brand Ambassadors, spin the wheel, and rack up points toward the 100-point threshold.
          </p>
        </div>

        <!-- Lifestyle KV Card: Can Tower & Highlandah Energy -->
        <div class="outlets-lifestyle-card parallax-viewport">
          <img src="images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg" alt="William Lawson's - Top Up Anyone? No Rules. Great Scotch." class="outlets-lifestyle-img parallax-img">
          <div class="outlets-lifestyle-overlay">
            <div class="lifestyle-copy">
              <h3>TOP UP ANYONE? NO RULES. GREAT SCOTCH.</h3>
              <p>Experience live Highlandah energy at partner bars and lounges across Lagos, Abuja, and Port Harcourt.</p>
            </div>
            <a href="#outlets-tour" class="btn btn-primary">
              <i data-lucide="map-pin"></i> Find Your Activation Night
            </a>
          </div>
        </div>

        <div class="outlets-grid">
          <?php foreach ($outlets as $out): ?>
            <div class="outlet-card">
              <div class="outlet-top">
                <span class="outlet-date-badge">
                  <i data-lucide="calendar" style="width: 13px; height: 13px;"></i> <?= htmlspecialchars($out['event_date']) ?>
                </span>
                <span class="outlet-status-pill">Active Tour Outlet</span>
              </div>
              <h3 class="outlet-name"><?= htmlspecialchars($out['name']) ?></h3>
              <p class="outlet-location">
                <i data-lucide="map-pin" style="width: 14px; height: 14px;"></i>
                <?= htmlspecialchars($out['address']) ?>, <?= htmlspecialchars($out['city']) ?>
              </p>
              <div class="outlet-footer">
                <span class="outlet-action-note">Wheel Spin on Site</span>
                <a href="#check-points-anchor" class="btn btn-outline btn-sm">Check Points</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="view-all-cta-box">
          <a href="outlets.php" class="btn btn-primary btn-lg">
            <i data-lucide="map-pin"></i> View Full Tour Calendar & Venue Directory &rarr;
          </a>
        </div>
      </div>
    </section>

    <!-- SECTION SEPARATOR (GREEN BOTTOM DISTRESSED EDGE - TOP OF SCROLLING TEXT) -->
    <div class="section-separator sep-bottom-green"></div>

    <!-- ================= KINETIC OPPOSING PARALLAX STRIP 2 ================= -->
    <div class="kinetic-text-strip kinetic-strip-compact" aria-hidden="true">
      <div class="kinetic-track kinetic-track-left">
        <span class="kinetic-text outline-text">DON'T ASK FOR IT<span class="kinetic-dot">&bull;</span>EARN IT<span class="kinetic-dot">&bull;</span>GRAND FINALE VIP TABLE<span class="kinetic-dot">&bull;</span>HANG OUT WITH THE CREATOR<span class="kinetic-dot">&bull;</span>DON'T ASK FOR IT<span class="kinetic-dot">&bull;</span>EARN IT</span>
      </div>
      <div class="kinetic-track kinetic-track-right">
        <span class="kinetic-text">WILLIAM LAWSON'S<span class="kinetic-dot">&bull;</span>TOUR DATES<span class="kinetic-dot">&bull;</span>LAGOS<span class="kinetic-dot">&bull;</span>ABUJA<span class="kinetic-dot">&bull;</span>PORT HARCOURT<span class="kinetic-dot">&bull;</span>WILLIAM LAWSON'S</span>
      </div>
    </div>

    <!-- SECTION SEPARATOR (GREEN TOP DISTRESSED EDGE - BOTTOM OF SCROLLING TEXT) -->
    <div class="section-separator sep-top-green"></div>

    <!-- ================= VIP CREATOR TABLE SPOTLIGHT ================= -->
    <section class="section section-vip" id="vip-table">
      <div class="container">
        <div class="vip-card sheen-glow">
          <div class="vip-content">
            <span class="vip-badge">GRAND FINALE SPOTLIGHT</span>
            <h2 class="vip-heading">
              HANGOUT WITH A HIGHLANDAH:<br>
              <span class="text-accent">SHARE A TABLE WITH THE CREATOR</span>
            </h2>
            <p class="vip-lead">
              Everyone wants to hangout with a celebrity and flex the Highlandah lifestyle. But you don't ask for it — you earn it. 
              Top contenders who cross the 100-point threshold will be entered into the final selection to share the exclusive VIP table with our celebrity creator at the Grand Finale.
            </p>
            <div class="vip-cta-row">
              <a href="outlets.php" class="btn btn-primary btn-lg">
                <i data-lucide="map-pin"></i> Find Activation Outlets
              </a>
              <a href="leaderboard.php" class="btn btn-outline btn-lg">
                <i data-lucide="trophy"></i> View Current Top Contenders
              </a>
            </div>
          </div>
          <div class="vip-poster-container">
            <img src="images/image.png" alt="William Lawson's Highlandahs Only" class="vip-poster-img">
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION SEPARATOR (BLACK TOP DISTRESSED EDGE INTO FOOTER) -->
    <div class="section-separator sep-top-black"></div>
  </main>

  <!-- ================= CORPORATE COMPLIANCE FOOTER ================= -->
  <footer class="footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-brand-col">
          <div class="footer-logo">
            <img src="images/logo.svg" alt="William Lawson's" class="footer-brand-logo-img">
            <span class="footer-brand-name">WILLIAM LAWSON'S</span>
          </div>
          <p class="footer-tagline">
            The Next Highlandah (Season 3). Bold, unconventional, good-humored. First-party data campaign and multi-outlet points management platform.
          </p>
          <div class="legal-age-warning">
            STRICTLY 18+. DRINK RESPONSIBLY. NOT FOR SALE TO PERSONS UNDER 18 YEARS OF AGE.
          </div>
        </div>

        <div class="footer-links-col">
          <h4 class="footer-col-title">Campaign Navigation</h4>
          <ul class="footer-menu">
            <li><a href="./">Campaign Homepage</a></li>
            <li><a href="leaderboard">National Leaderboard</a></li>
            <li><a href="outlets">Activation Outlets & Tour</a></li>
            <li><a href="#scorecard">Challenge Scorecard</a></li>
          </ul>
        </div>

        <div class="footer-links-col">
          <h4 class="footer-col-title">Campaign Infrastructure</h4>
          <ul class="footer-menu">
            <li><span>Official Domain: <strong>TNHNG.COM</strong></span></li>
            <li><span>First-Party Data (1PD) Collection</span></li>
            <li><span>Nationwide Multi-Outlet Points Aggregator</span></li>
            <li><span>Locked Scorecard Architecture</span></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> William Lawson's • The Next Highlandah (Season 3). All rights reserved.</p>
        <p class="footer-credits">William Lawson's Blended Scotch Whisky encourages responsible consumption.</p>
      </div>
    </div>
  </footer>

  <!-- ================= DIGITAL CLAN PASS MODAL ================= -->
  <div class="modal-overlay" id="clan-pass-modal">
    <div class="modal-card modal-pass-card">
      <button class="modal-close" aria-label="Close modal">&times;</button>
      <div class="modal-pass-header">
        <span class="modal-pass-pill">OFFICIAL CLAN PASS ACTIVATED</span>
        <h3 class="modal-pass-title">YOUR HIGHLANDAH PASS</h3>
      </div>

      <div class="digital-ticket">
        <div class="digital-qr-wrap">
          <img id="modal-pass-qr" src="" alt="Clan QR Code" class="digital-qr-img">
        </div>
        <div class="digital-id" id="modal-pass-id">TNH-0000</div>
        <div class="digital-name" id="modal-pass-name">Participant Name</div>
        <div class="digital-phone" id="modal-pass-phone">08012345678</div>
        <div class="digital-footer">
          <span class="digital-score-label">Current Verified Score:</span>
          <span class="digital-score-value" id="modal-pass-points">0 PTS</span>
        </div>
      </div>

      <p class="modal-pass-instruction">
        Take a screenshot or note down your Clan ID. Present this ID to the William Lawson's Brand Ambassador at <strong>any outlet</strong> across the country to log your points!
      </p>

      <button class="btn btn-primary btn-lg" style="width: 100%;" onclick="document.getElementById('clan-pass-modal').classList.remove('active')">
        <i data-lucide="check"></i> Save Pass & Proceed
      </button>
    </div>
  </div>

  <!-- ================= PARTICIPANT DETAILS & MULTI-OUTLET AUDIT MODAL ================= -->
  <div class="modal-overlay" id="participant-details-modal">
    <div class="modal-card modal-details-card">
      <button class="modal-close" aria-label="Close modal">&times;</button>
      
      <div class="details-top">
        <div>
          <span class="details-id-badge" id="detail-clan-id">TNH-0000</span>
          <h2 class="details-name" id="detail-name">Participant Name</h2>
        </div>
        <div class="details-rank-wrap">
          <span class="details-rank-label">NATIONAL RANK</span>
          <span class="details-rank-num" id="detail-rank">#1</span>
        </div>
      </div>

      <!-- Meter Box -->
      <div class="details-meter-box">
        <div class="meter-labels">
          <span>ROAD TO GRAND FINALE (100 PTS)</span>
          <span class="points-val" id="detail-points">0 PTS</span>
        </div>
        <div class="progress-track" style="height: 12px;">
          <div class="progress-fill" id="detail-progress" style="width: 0%;"></div>
        </div>
        <div id="detail-qual-badge" style="margin-top: 0.75rem;"></div>
      </div>

      <!-- Outlets History Audit Log -->
      <h4 class="details-history-title">
        <i data-lucide="history" style="width: 16px; height: 16px;"></i> POINTS ALLOCATED ACROSS OUTLETS
      </h4>

      <div class="details-table-wrap">
        <table class="details-table">
          <thead>
            <tr>
              <th>Date / Time</th>
              <th>Challenge</th>
              <th>Points</th>
              <th>Outlet Allocated</th>
              <th>Staff / Ambassador</th>
            </tr>
          </thead>
          <tbody id="detail-history-tbody">
            <!-- Dynamically populated via JS -->
          </tbody>
        </table>
      </div>

      <div class="details-footer-action">
        <button class="btn btn-outline" onclick="document.getElementById('participant-details-modal').classList.remove('active')">
          Close Archive
        </button>
      </div>
    </div>
  </div>

  <script src="js/highlandah-alert.js?v=<?= filemtime(__DIR__ . '/js/highlandah-alert.js') ?>"></script>
  <script src="js/main.js?v=<?= filemtime(__DIR__ . '/js/main.js') ?>"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>
