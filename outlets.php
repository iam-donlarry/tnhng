<?php
// outlets.php - The Next Highlandah (Season 3) Activation Outlets & Tour Calendar
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();

$outlets = [];
$cities = [];

if ($pdo) {
    $outlets = $pdo->query("SELECT * FROM outlets WHERE status = 'active' ORDER BY city ASC, name ASC")->fetchAll();
    foreach ($outlets as $o) {
        if (!in_array($o['city'], $cities)) {
            $cities[] = $o['city'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Primary SEO Meta Tags -->
  <title>Partner Outlets & Activation Tour Dates | The Next Highlandah</title>
  <meta name="title" content="Partner Outlets & Activation Tour Dates | The Next Highlandah">
  <meta name="description" content="Find participating bars, lounges, and clubs hosting live William Lawson's The Next Highlandah activation nights across Lagos, Abuja, and Port Harcourt. Spin the bottle and bank points!">
  <meta name="keywords" content="The Next Highlandah outlets, William Lawson's tour dates, Lagos bars, Abuja lounges, Port Harcourt clubs, scotch whisky activation, bottle spin challenges Nigeria">
  <meta name="author" content="William Lawson's Blended Scotch Whisky">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <link rel="canonical" href="https://tnhng.com/outlets">

  <!-- Open Graph / Facebook / WhatsApp Meta Tags -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://tnhng.com/outlets">
  <meta property="og:site_name" content="The Next Highlandah | William Lawson's">
  <meta property="og:title" content="Partner Outlets & Activation Tour Dates | The Next Highlandah">
  <meta property="og:description" content="Discover partner bars, lounges, and clubs across Nigeria hosting live William Lawson's activation nights. Play physical challenges and bank locked points.">
  <meta property="og:image" content="https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg">
  <meta property="og:image:secure_url" content="https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg">
  <meta property="og:image:type" content="image/jpeg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="William Lawson's The Next Highlandah Activation Outlets Nationwide">
  <meta property="og:locale" content="en_NG">

  <!-- Twitter / X Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="https://tnhng.com/outlets">
  <meta name="twitter:title" content="Partner Outlets & Tour Dates | The Next Highlandah">
  <meta name="twitter:description" content="Find partner bars and lounges hosting live William Lawson's activation nights across Lagos, Abuja, and Port Harcourt.">
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

  <!-- Structured Data: BreadcrumbList & Place / ItemList Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "https://tnhng.com/"
          },
          {
            "@type": "ListItem",
            "position": 2,
            "name": "Outlets",
            "item": "https://tnhng.com/outlets"
          }
        ]
      },
      {
        "@type": "CollectionPage",
        "@id": "https://tnhng.com/outlets#webpage",
        "url": "https://tnhng.com/outlets",
        "name": "Activation Outlets & Tour Schedule | The Next Highlandah",
        "description": "Directory of partner bars, lounges, and clubs hosting live William Lawson's activation games across Nigeria.",
        "isPartOf": {
          "@type": "WebSite",
          "name": "The Next Highlandah",
          "url": "https://tnhng.com/"
        },
        "inLanguage": "en-NG"
      }
    ]
  }
  </script>
</head>
<body>

  <!-- ================= AGE GATE MODAL (18+ COMPLIANCE) ================= -->
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

  <!-- ================= MINIMAL PRIMARY NAVIGATION ================= -->
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
          <li><a href="./" class="nav-link">Home</a></li>
          <li><a href="leaderboard" class="nav-link">Leaderboard</a></li>
          <li><a href="outlets" class="nav-link active">Outlets</a></li>
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
        <li><a href="./" class="nav-link mobile-link">Home</a></li>
        <li><a href="leaderboard" class="nav-link mobile-link">Full Leaderboard</a></li>
        <li><a href="outlets" class="nav-link mobile-link active">Outlet Tour & Dates</a></li>
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
    <!-- ================= PAGE HERO ================= -->
    <section class="page-hero">
      <div class="container">
        <div class="breadcrumb-nav">
          <a href="./" class="breadcrumb-link">Home</a>
          <span class="breadcrumb-sep">/</span>
          <span class="breadcrumb-current">Activation Outlets</span>
        </div>

        <div>
          <span class="section-kicker">Nationwide On-Premise Tour</span>
          <h1 class="page-hero-title">OUTLET TOUR & ACTIVATION DATES</h1>
          <p class="page-hero-subtitle">
            William Lawson's is taking over premier bars, lounges, and clubs across Nigeria. Visit any official activation night, show your Clan ID to the brand ambassador team, spin the bottle, conquer challenges, and rack up verified points toward the 100-point Grand Finale threshold.
          </p>
        </div>
      </div>
    </section>

    <!-- SECTION SEPARATOR (GREEN BOTTOM DISTRESSED EDGE) -->
    <div class="section-separator sep-bottom-green"></div>

    <!-- ================= FEATURED CAN TOWER LIFESTYLE BANNER ================= -->
    <section class="section" style="padding-top: 3.5rem; padding-bottom: 2rem;">
      <div class="container">
        <div class="outlets-lifestyle-card">
          <img src="images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg" alt="William Lawson's - Top Up Anyone? No Rules. Great Scotch." class="outlets-lifestyle-img">
          <div class="outlets-lifestyle-overlay">
            <div class="lifestyle-copy">
              <span class="section-kicker" style="margin-bottom: 0.25rem;">Live Bar Tour Experience</span>
              <h3>TOP UP ANYONE? NO RULES. GREAT SCOTCH.</h3>
              <p>Experience real Highlandah energy live on-site. Ice cold scotch, locked game challenges, live DJ sets, and official clan giveaways.</p>
            </div>
            <a href="leaderboard" class="btn btn-primary">
              <i data-lucide="trophy"></i> View Current Leaderboard
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= ACTIVATION OUTLETS DIRECTORY ================= -->
    <section class="section section-outlets" id="outlets-directory" style="padding-top: 2rem;">
      <div class="container">
        <div class="section-header">
          <span class="section-kicker">Partner Venue Network</span>
          <h2 class="section-heading">WHERE THE CLAN GATHERS</h2>
          <p class="section-subheading">
            Filter by city to locate activation nights near you. Visit multiple outlets to earn multi-venue badges and bonus points.
          </p>
        </div>

        <!-- City Filter Pills -->
        <div class="filter-pills-row">
          <button class="filter-pill-btn active" data-city="all">All Cities (<?= count($outlets) ?>)</button>
          <?php foreach ($cities as $city): 
            $count = count(array_filter($outlets, fn($o) => $o['city'] === $city));
          ?>
            <button class="filter-pill-btn" data-city="<?= htmlspecialchars(strtolower(str_replace(' ', '-', $city))) ?>">
              <?= htmlspecialchars($city) ?> (<?= $count ?>)
            </button>
          <?php endforeach; ?>
        </div>

        <!-- Outlets Grid -->
        <div class="outlets-grid" id="outlets-list">
          <?php foreach ($outlets as $out): 
            $citySlug = strtolower(str_replace(' ', '-', $out['city']));
          ?>
            <div class="outlet-card" data-city="<?= htmlspecialchars($citySlug) ?>">
              <div>
                <div class="outlet-top">
                  <span class="outlet-badge">
                    <i data-lucide="radio" style="width: 12px; height: 12px; animation: pulse 1.5s infinite;"></i>
                    <?= htmlspecialchars($out['city']) ?>
                  </span>
                  <span class="outlet-type">OFFICIAL VENUE</span>
                </div>
                <h3 class="outlet-name"><?= htmlspecialchars($out['name']) ?></h3>
                <p class="outlet-address">
                  <i data-lucide="map-pin" style="width: 15px; height: 15px; color: var(--wl-green); flex-shrink: 0; margin-top: 3px;"></i>
                  <span><?= htmlspecialchars($out['address'] ?: 'Prime Activation Zone, ' . $out['city']) ?></span>
                </p>
                <div style="margin-top: 1rem; padding: 0.75rem; background: var(--bg-surface); border-radius: 6px; border: 1px solid var(--border-subtle);">
                  <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-dim); margin-bottom: 0.35rem;">On-Premise Activations:</div>
                  <div style="font-size: 0.82rem; color: #ffffff; display: flex; flex-direction: column; gap: 0.25rem;">
                    <div><strong>Challenges:</strong> Bottle Spin • Speed Pour • Trivia</div>
                    <div><strong>Activation Hours:</strong> 8:00 PM – Late</div>
                  </div>
                </div>
              </div>

              <div class="outlet-footer" style="margin-top: 1.5rem;">
                <span class="outlet-action-note">Ambassador On-Premise</span>
                <a href="#check-points-anchor" class="btn btn-outline btn-sm">
                  <i data-lucide="award"></i> Check Points
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- How It Works at the Outlet Guide -->
        <div style="margin-top: 4.5rem; background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 12px; padding: 2.5rem 2rem;">
          <div class="section-header" style="text-align: center; margin-bottom: 2rem;">
            <span class="section-kicker">On-Premise Playbook</span>
            <h2 class="section-heading" style="font-size: 2.2rem; margin-bottom: 0.25rem;">HOW TO PLAY AT THE OUTLET</h2>
            <p class="section-subheading" style="margin: 0 auto; max-width: 600px;">
              3 simple steps to conquer challenges and get points locked onto your profile.
            </p>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.75rem;">
            <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.5rem;">
              <div style="font-family: var(--font-display); font-size: 2rem; color: var(--wl-green); line-height: 1; margin-bottom: 0.75rem;">01</div>
              <h4 style="font-size: 1.1rem; color: #ffffff; margin-bottom: 0.5rem;">Arrive at the Partner Outlet</h4>
              <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                Spot the William Lawson's branded activation booth and our brand ambassador team upon entry.
              </p>
            </div>

            <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.5rem;">
              <div style="font-family: var(--font-display); font-size: 2rem; color: var(--wl-green); line-height: 1; margin-bottom: 0.75rem;">02</div>
              <h4 style="font-size: 1.1rem; color: #ffffff; margin-bottom: 0.5rem;">Present Your Clan ID</h4>
              <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                Give your Clan ID (e.g. <code>TNH-0042</code>) or phone number to the ambassador. First time? They'll register you on-site in 30 seconds.
              </p>
            </div>

            <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.5rem;">
              <div style="font-family: var(--font-display); font-size: 2rem; color: var(--wl-green); line-height: 1; margin-bottom: 0.75rem;">03</div>
              <h4 style="font-size: 1.1rem; color: #ffffff; margin-bottom: 0.5rem;">Spin, Play & Climb Standings</h4>
              <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                Spin the William Lawson's bottle to select your challenge. Conquering it instantly records locked points to your national score.
              </p>
            </div>
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
            <li><a href="./#scorecard">Challenge Scorecard</a></li>
          </ul>
        </div>

        <div class="footer-links-col">
          <h4 class="footer-col-title">Internal Portals</h4>
          <ul class="footer-menu">
            <li><a href="promoter">On-Site Ambassador Console</a></li>
            <li><a href="admin">Campaign Management & Audit</a></li>
            <li><span>Official Domain: <strong>TNHNG.COM</strong></span></li>
            <li><span>Strictly 18+ Drink Responsibly</span></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> William Lawson's • The Next Highlandah (Season 3). All rights reserved.</p>
        <p class="footer-credits">William Lawson's Blended Scotch Whisky encourages responsible consumption.</p>
      </div>
    </div>
  </footer>

  

  <script src="js/highlandah-alert.js?v=<?= filemtime(__DIR__ . '/js/highlandah-alert.js') ?>"></script>
  <script src="js/main.js?v=<?= filemtime(__DIR__ . '/js/main.js') ?>"></script>
  <script>
    lucide.createIcons();

    // City Filter Pills Interaction
    document.querySelectorAll('.filter-pill-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const city = btn.getAttribute('data-city');
        const cards = document.querySelectorAll('#outlets-list .outlet-card');
        cards.forEach(card => {
          if (city === 'all' || card.getAttribute('data-city') === city) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  </script>
</body>
</html>
