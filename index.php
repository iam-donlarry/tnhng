<?php
// index.php - The Next Highlandah (TNH Season 3) Public Campaign Microsite
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();

$stats = [
    'total_participants' => 0,
    'total_points_distributed' => 0,
    'total_qualified_finalists' => 0,
];
$topThree = [];

if ($pdo) {
    $statsRow = $pdo->query("SELECT 
        COUNT(*) AS total_participants,
        COALESCE(SUM(total_points), 0) AS total_points,
        COALESCE(SUM(CASE WHEN total_points >= 100 THEN 1 ELSE 0 END), 0) AS total_qualified
    FROM participants")->fetch();
    
    if ($statsRow) {
        $stats['total_participants'] = intval($statsRow['total_participants']);
        $stats['total_points_distributed'] = intval($statsRow['total_points']);
        $stats['total_qualified_finalists'] = intval($statsRow['total_qualified']);
    }

    $topThree = $pdo->query("SELECT p.id, p.clan_id, p.full_name, p.total_points, o.name AS outlet_name, o.city AS outlet_city
        FROM participants p
        LEFT JOIN outlets o ON p.outlet_id = o.id
        ORDER BY p.total_points DESC, p.created_at ASC
        LIMIT 3")->fetchAll();
}

function maskName($fullName) {
    $parts = explode(' ', trim($fullName));
    $first = $parts[0] ?? 'Highlandah';
    if (count($parts) > 1) {
        $last = end($parts);
        return $first . ' ' . substr($last, 0, 1) . '***';
    }
    return $first;
}

function getInitials($fullName) {
    $clean = preg_replace('/["\'\(\)]+/', '', $fullName);
    $parts = preg_split('/\s+/', trim($clean));
    $initials = '';
    foreach ($parts as $p) {
        if (!empty($p)) $initials .= strtoupper($p[0]);
    }
    return substr($initials, 0, 2) ?: 'WL';
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
  <meta name="description" content="TNH Season 3 is live in Nigeria. Check your official Clan standing, conquer wheel spin challenges at participating bars, and bank 100 points to enter the Grand Finale VIP draw.">
  <meta name="keywords" content="The Next Highlandah, William Lawson's Nigeria, TNH Season 3, Scotch Whisky, Highlandah Clan Pass, check points, clan standings, wheel spin challenge, earn points, Lagos nightlife, Abuja bars, Port Harcourt lounges, Earn It">
  <meta name="author" content="William Lawson's Blended Scotch Whisky">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <link rel="canonical" href="https://tnhng.com/">

  <!-- Open Graph / Facebook / WhatsApp Meta Tags -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://tnhng.com/">
  <meta property="og:site_name" content="The Next Highlandah | William Lawson's">
  <meta property="og:title" content="The Next Highlandah Season 3 | Don't Ask For It. Earn It!">
  <meta property="og:description" content="Enter your Clan ID to check your live points, verified national ranking, and road to the Grand Finale 100-point VIP draw.">
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
  <meta name="twitter:description" content="Don't ask for it. Earn it! Check your Clan standings, hit partner outlets across Nigeria, and bank 100 points for the Grand Finale draw.">
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
        <a href="#hero-anchor" class="btn btn-primary btn-sm" onclick="document.getElementById('lookup-query').focus()">
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
        <li><a href="#how-it-works" class="nav-link mobile-link">How It Works</a></li>
      </ul>
      <div class="mobile-nav-actions">
        <a href="#hero-anchor" class="btn btn-primary mobile-link" style="width: 100%;" onclick="document.getElementById('lookup-query').focus()">
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
    <!-- ================= HERO SECTION (PRIMARY CLAN UTILITY & LOOKUP) ================= -->
    <section class="hero-section" id="hero-anchor">
      <div class="container hero-container">
        <div class="hero-content">
          <div class="campaign-pill">
            <span class="status-indicator"></span>
            <span>WILLIAM LAWSON'S OFFICIAL PROMOTION</span>
          </div>

          <h1 class="hero-title">
            DON'T ASK FOR IT.<br>
            <span class="text-accent">EARN IT!</span>
          </h1>

          <p class="hero-lead">
            Enter your Clan ID to check your live points, verified national ranking, and road to the Grand Finale 100-point VIP draw.
          </p>

          <!-- High-Impact Hero Clan Lookup Box -->
          <div class="hero-lookup-box">
            <div class="hero-lookup-header">
              <span class="hero-lookup-badge"><i data-lucide="shield"></i> CHECK YOUR CLAN STANDINGS</span>
              <span class="hero-lookup-hint">Live National Sync</span>
            </div>
            <form id="points-lookup-form" class="hero-lookup-form">
              <div class="hero-lookup-bar">
                <div class="hero-lookup-input-wrap">
                  <i data-lucide="search" class="hero-lookup-icon"></i>
                  <input 
                    type="text" 
                    id="lookup-query" 
                    class="form-control hero-lookup-input" 
                    placeholder="Enter Clan ID (e.g. TNH-7701 or 7701), Phone or Email..." 
                    autocomplete="off"
                    required
                  >
                </div>
                <button type="submit" class="btn btn-primary hero-lookup-btn" id="hero-lookup-btn">
                  <i data-lucide="award"></i> Check Standings
                </button>
              </div>
              <div class="hero-lookup-meta">
                <div class="hero-quick-chips">
                  <span>Quick:</span>
                  <?php if (!empty($topThree[0])): ?>
                    <button type="button" class="quick-chip" onclick="quickLookup('<?= htmlspecialchars($topThree[0]['clan_id']) ?>')"><?= htmlspecialchars($topThree[0]['clan_id']) ?></button>
                  <?php else: ?>
                    <button type="button" class="quick-chip" onclick="quickLookup('TNH-7701')">TNH-7701</button>
                  <?php endif; ?>
                  <?php if (!empty($topThree[1])): ?>
                    <button type="button" class="quick-chip" onclick="quickLookup('<?= htmlspecialchars($topThree[1]['clan_id']) ?>')"><?= htmlspecialchars($topThree[1]['clan_id']) ?></button>
                  <?php endif; ?>
                </div>
                <a href="leaderboard" class="hero-meta-link">
                  <i data-lucide="trophy" style="width: 13px; height: 13px;"></i> Full Top 50 Leaderboard &rarr;
                </a>
              </div>
            </form>
          </div>
        </div>

        <!-- Hero Visual: Bottles + Live Reactive Clan Pass Card -->
        <div class="hero-visual">
          <div class="hero-product-combo">
            <div class="hero-bottles-pod">
              <img src="images/whiskey_bottles.png" alt="William Lawson's Blended Scotch Whisky" class="hero-bottles-img">
              <div class="hero-bottles-tag">
                <span>NO RULES. GREAT SCOTCH.</span>
              </div>
            </div>

            <!-- Live Reactive Clan Pass Card -->
            <div class="hero-ticket-frame tilt-card sheen-glow" id="hero-clan-pass">
              <div class="ticket-bg-art" aria-hidden="true"></div>

              <div class="ticket-header-strip">
                <div class="ticket-logo-mark">
                  <span>WL</span> OFFICIAL CLAN PASS
                </div>
                <span class="ticket-status-stamp" id="hero-pass-stamp">ACTIVE PASS</span>
              </div>

              <div class="ticket-details-block">
                <div class="ticket-id-display">
                  <span class="ticket-label">OFFICIAL CLAN ID</span>
                  <span class="pass-id" id="hero-pass-id"><?= !empty($topThree[0]) ? htmlspecialchars($topThree[0]['clan_id']) : 'TNH-7701' ?></span>
                </div>
                <div class="ticket-holder-info pass-info">
                  <span class="ticket-label">PARTICIPANT</span>
                  <h4 id="hero-pass-name"><?= !empty($topThree[0]) ? htmlspecialchars(maskName($topThree[0]['full_name'])) : 'Clan Warrior' ?></h4>
                </div>
              </div>

              <!-- Road to Finale Progress Meter -->
              <div class="ticket-meter-box">
                <div class="meter-labels">
                  <span id="hero-pass-rank-label">NATIONAL RANK #1</span>
                  <span class="points-val" id="hero-pass-points"><?= !empty($topThree[0]) ? intval($topThree[0]['total_points']) : 105 ?> / 100 PTS</span>
                </div>
                <div class="progress-track">
                  <div class="progress-fill" id="hero-pass-progress" style="width: <?= !empty($topThree[0]) ? min(100, intval($topThree[0]['total_points'])) : 100 ?>%;"></div>
                </div>
                <div class="ticket-meter-footer">
                  <span class="ticket-badge-qualified" id="hero-pass-status-badge">
                    <?= (!empty($topThree[0]) && intval($topThree[0]['total_points']) >= 100) ? 'GRAND FINALE QUALIFIED' : 'ON ROAD TO 100 PTS' ?>
                  </span>
                  <button type="button" class="hero-pass-action-btn" id="hero-pass-view-history" style="display: none;">
                    View History &rarr;
                  </button>
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
    </section>

    <!-- ================= TOP 3 PODIUM CHAMPIONS SHOWCASE ================= -->
    <?php if (!empty($topThree)): ?>
    <section class="section section-standings" style="padding: 4rem 0 3.5rem; background: var(--bg-surface);">
      <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 2.5rem;">
          <span class="section-kicker">Live National Contenders</span>
          <h2 class="section-heading" style="margin-bottom: 0.35rem;">CLAN CHAMPIONS PODIUM</h2>
          <p class="section-subheading" style="margin: 0 auto; max-width: 600px;">
            Warriors currently holding the top three positions in Nigeria. Earn 100 points at partner outlets to secure your Grand Finale draw entry.
          </p>
        </div>

        <div class="podium-grid">
          <!-- Rank #2 (Silver) -->
          <?php if (isset($topThree[1])): $p2 = $topThree[1]; ?>
          <div class="podium-card rank-silver" style="cursor: pointer;" onclick="quickLookup('<?= htmlspecialchars($p2['clan_id']) ?>')">
            <div class="podium-rank-crown">2</div>
            <div class="podium-avatar"><?= getInitials($p2['full_name']) ?></div>
            <span class="podium-clan-id"><?= htmlspecialchars($p2['clan_id']) ?></span>
            <h3 class="podium-name"><?= htmlspecialchars(maskName($p2['full_name'])) ?></h3>
            <div class="podium-outlet">Origin: <?= htmlspecialchars($p2['outlet_name'] ?? 'Web Activation') ?></div>
            <div class="podium-score-box">
              <span class="podium-score-num"><?= intval($p2['total_points']) ?></span>
              <span class="podium-score-label">Verified Clan Points</span>
            </div>
            <?php if (intval($p2['total_points']) >= 100): ?>
              <span class="qualified-tag">GRAND FINALE QUALIFIED</span>
            <?php else: ?>
              <span style="font-size: 0.78rem; color: var(--text-dim);"><?= 100 - intval($p2['total_points']) ?> pts to Grand Finale</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <!-- Rank #1 (Gold Leader) -->
          <?php if (isset($topThree[0])): $p1 = $topThree[0]; ?>
          <div class="podium-card rank-gold" style="cursor: pointer;" onclick="quickLookup('<?= htmlspecialchars($p1['clan_id']) ?>')">
            <div class="podium-rank-crown">1</div>
            <div class="podium-avatar"><?= getInitials($p1['full_name']) ?></div>
            <span class="podium-clan-id"><?= htmlspecialchars($p1['clan_id']) ?></span>
            <h3 class="podium-name"><?= htmlspecialchars(maskName($p1['full_name'])) ?></h3>
            <div class="podium-outlet">Origin: <?= htmlspecialchars($p1['outlet_name'] ?? 'Web Activation') ?></div>
            <div class="podium-score-box">
              <span class="podium-score-num"><?= intval($p1['total_points']) ?></span>
              <span class="podium-score-label">Verified Clan Points</span>
            </div>
            <?php if (intval($p1['total_points']) >= 100): ?>
              <span class="qualified-tag">GRAND FINALE QUALIFIED</span>
            <?php else: ?>
              <span style="font-size: 0.78rem; color: var(--text-dim);"><?= 100 - intval($p1['total_points']) ?> pts to Grand Finale</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <!-- Rank #3 (Bronze) -->
          <?php if (isset($topThree[2])): $p3 = $topThree[2]; ?>
          <div class="podium-card rank-bronze" style="cursor: pointer;" onclick="quickLookup('<?= htmlspecialchars($p3['clan_id']) ?>')">
            <div class="podium-rank-crown">3</div>
            <div class="podium-avatar"><?= getInitials($p3['full_name']) ?></div>
            <span class="podium-clan-id"><?= htmlspecialchars($p3['clan_id']) ?></span>
            <h3 class="podium-name"><?= htmlspecialchars(maskName($p3['full_name'])) ?></h3>
            <div class="podium-outlet">Origin: <?= htmlspecialchars($p3['outlet_name'] ?? 'Web Activation') ?></div>
            <div class="podium-score-box">
              <span class="podium-score-num"><?= intval($p3['total_points']) ?></span>
              <span class="podium-score-label">Verified Clan Points</span>
            </div>
            <?php if (intval($p3['total_points']) >= 100): ?>
              <span class="qualified-tag">GRAND FINALE QUALIFIED</span>
            <?php else: ?>
              <span style="font-size: 0.78rem; color: var(--text-dim);"><?= 100 - intval($p3['total_points']) ?> pts to Grand Finale</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 2.5rem;">
          <a href="leaderboard" class="btn btn-primary btn-lg">
            <i data-lucide="trophy"></i> View Full National Leaderboard &rarr;
          </a>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ================= HOW THE PROMOTION WORKS (COMPACT 3-STEP PLAYBOOK) ================= -->
    <section class="section section-playbook" id="how-it-works" style="padding: 4.5rem 0;">
      <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 2.5rem;">
          <span class="section-kicker">Simple 3-Step Playbook</span>
          <h2 class="section-heading" style="margin-bottom: 0.35rem;">HOW THE PROMOTION WORKS</h2>
          <p class="section-subheading" style="margin: 0 auto; max-width: 600px;">
            No online games are played. Your Clan Pass tracks your points; the real action happens in-person at partner bars across Nigeria.
          </p>
        </div>

        <div class="playbook-cards-grid">
          <div class="playbook-card">
            <div class="playbook-num">01</div>
            <div class="playbook-icon-wrap"><i data-lucide="shield-check"></i></div>
            <h3>One Universal Pass</h3>
            <p>Your unique Clan ID works across every partner lounge nationwide. Use it to check your live points and standing anytime.</p>
          </div>

          <div class="playbook-card">
            <div class="playbook-num">02</div>
            <div class="playbook-icon-wrap"><i data-lucide="sparkles"></i></div>
            <h3>Spin & Conquer</h3>
            <p>Visit any scheduled activation bar. Our ambassador spins the wheel to assign your physical challenge: Bold, Unconventional, or Good-Humored.</p>
          </div>

          <div class="playbook-card highlight-step">
            <div class="playbook-num">03</div>
            <div class="playbook-icon-wrap"><i data-lucide="crown"></i></div>
            <h3>Bank 100 Points</h3>
            <p>Locked points are credited to your pass immediately. Accumulate 100 points to enter the Grand Finale draw and hangout with the creator at the VIP table.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= ACTIVATION TOUR TEASER BANNER ================= -->
    <section class="section section-tour-teaser" id="tour-banner" style="padding: 1rem 0 5rem;">
      <div class="container">
        <div class="tour-banner-card sheen-glow">
          <div class="tour-banner-content">
            <span class="tour-badge"><i data-lucide="map-pin"></i> NATIONWIDE ACTIVATION TOUR</span>
            <h2 class="tour-heading">PLAY LIVE AT PARTNER OUTLETS</h2>
            <p class="tour-subheading">
              Experience live Highlandah energy across premier bars and lounges in Lagos, Abuja, and Port Harcourt. Meet ambassadors and rack up locked points on your pass.
            </p>
            <div class="tour-cities-row">
              <span class="tour-city-pill">Lagos Venues</span>
              <span class="tour-city-pill">Abuja Lounges</span>
              <span class="tour-city-pill">Port Harcourt Clubs</span>
            </div>
            <div class="tour-actions">
              <a href="outlets" class="btn btn-primary btn-lg">
                <i data-lucide="calendar"></i> View Tour Dates & Outlets &rarr;
              </a>
              <a href="leaderboard" class="btn btn-outline btn-lg">
                <i data-lucide="trophy"></i> National Standings
              </a>
            </div>
          </div>
          <div class="tour-banner-img-wrap">
            <img src="images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg" alt="William Lawson's Tour Nights" class="tour-banner-img">
          </div>
        </div>
      </div>
    </section>
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
            <li><a href="promoter">Ambassador Console</a></li>
          </ul>
        </div>

        <div class="footer-links-col">
          <h4 class="footer-col-title">Campaign Infrastructure</h4>
          <ul class="footer-menu">
            <li><span>Official Domain: <strong>TNHNG.COM</strong></span></li>
            <li><span>Instant Clan ID Lookup & Verification</span></li>
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
