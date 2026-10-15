<?php
// leaderboard.php - The Next Highlandah (Season 3) National Leaderboard
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();

$outlets = [];
$stats = [
    'total_participants' => 0,
    'total_points_distributed' => 0,
    'total_qualified_finalists' => 0,
    'highest_score' => 0,
];
$topThree = [];

if ($pdo) {
    $outlets = $pdo->query("SELECT * FROM outlets WHERE status = 'active' ORDER BY city ASC, name ASC")->fetchAll();
    
    $statsQuery = $pdo->query("SELECT 
        COUNT(*) AS total_participants,
        COALESCE(SUM(total_points), 0) AS total_points_distributed,
        COALESCE(SUM(CASE WHEN total_points >= 100 THEN 1 ELSE 0 END), 0) AS total_qualified_finalists,
        COALESCE(MAX(total_points), 0) AS highest_score
    FROM participants")->fetch();

    if ($statsQuery) {
        $stats['total_participants'] = intval($statsQuery['total_participants']);
        $stats['total_points_distributed'] = intval($statsQuery['total_points_distributed']);
        $stats['total_qualified_finalists'] = intval($statsQuery['total_qualified_finalists']);
        $stats['highest_score'] = intval($statsQuery['highest_score']);
    }

    // Top 3 Podium Warriors
    $topThree = $pdo->query("SELECT p.id, p.clan_id, p.full_name, p.total_points, o.name AS outlet_name, o.city AS outlet_city
        FROM participants p
        LEFT JOIN outlets o ON p.outlet_id = o.id
        ORDER BY p.total_points DESC, p.created_at ASC
        LIMIT 3")->fetchAll();
}

function maskParticipantName($fullName) {
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
  <title>Live National Clan Standings | The Next Highlandah Season 3</title>
  <meta name="title" content="Live National Clan Standings | The Next Highlandah Season 3">
  <meta name="description" content="Official real-time leaderboard for William Lawson's The Next Highlandah Season 3 in Nigeria. Track points leaders, top clan warriors, and finalists qualified for the 100-point VIP finale.">
  <meta name="keywords" content="The Next Highlandah leaderboard, William Lawson's standings, clan points, finalists qualified, Nigeria scotch whisky challenge, top highlandahs, Earn It">
  <meta name="author" content="William Lawson's Blended Scotch Whisky">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <link rel="canonical" href="https://tnhng.com/leaderboard">

  <!-- Open Graph / Facebook / WhatsApp Meta Tags -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://tnhng.com/leaderboard">
  <meta property="og:site_name" content="The Next Highlandah | William Lawson's">
  <meta property="og:title" content="Live National Clan Standings | The Next Highlandah Season 3">
  <meta property="og:description" content="Live national leaderboard for William Lawson's The Next Highlandah Season 3. Track top points leaders and qualified finalists across Nigeria.">
  <meta property="og:image" content="https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg">
  <meta property="og:image:secure_url" content="https://tnhng.com/images/WL_Outdoor_Can_Tower_V3_KV_Landscape.jpg">
  <meta property="og:image:type" content="image/jpeg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="The Next Highlandah Season 3 Live National Clan Leaderboard">
  <meta property="og:locale" content="en_NG">

  <!-- Twitter / X Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="https://tnhng.com/leaderboard">
  <meta name="twitter:title" content="Live Clan Standings | The Next Highlandah Season 3">
  <meta name="twitter:description" content="Official live standings for William Lawson's The Next Highlandah Season 3. Track points leaders across Nigeria competing for the 100-point Grand Finale threshold.">
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

  <!-- Structured Data: BreadcrumbList & CollectionPage Schema -->
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
            "name": "Leaderboard",
            "item": "https://tnhng.com/leaderboard"
          }
        ]
      },
      {
        "@type": "CollectionPage",
        "@id": "https://tnhng.com/leaderboard#webpage",
        "url": "https://tnhng.com/leaderboard",
        "name": "National Clan Leaderboard | The Next Highlandah Season 3",
        "description": "Live points leaderboard tracking registered participants, points awarded, and qualified finalists for William Lawson's Season 3 promotional campaign in Nigeria.",
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
          <li><a href="leaderboard" class="nav-link active">Leaderboard</a></li>
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
        <li><a href="./" class="nav-link mobile-link">Home</a></li>
        <li><a href="leaderboard" class="nav-link mobile-link active">Full Leaderboard</a></li>
        <li><a href="outlets" class="nav-link mobile-link">Outlet Tour & Dates</a></li>
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
          <span class="breadcrumb-current">National Leaderboard</span>
        </div>

        <div>
          <span class="section-kicker">Verified TNH Season 3 Standings</span>
          <h1 class="page-hero-title">THE CLAN LEADERBOARD</h1>
          <p class="page-hero-subtitle">
            Live multi-outlet score audit across Nigeria. Every bottle challenge conquered at partner bars adds to your verified tally. Reach 100 points to secure your entry into the Grand Finale VIP Creator Table pool.
          </p>
        </div>
      </div>
    </section>

    <!-- SECTION SEPARATOR (GREEN BOTTOM DISTRESSED EDGE) -->
    <div class="section-separator sep-bottom-green"></div>

    <!-- ================= MAIN LEADERBOARD CONTENT ================= -->
    <section class="section section-standings" style="padding-top: 3.5rem;">
      <div class="container">

        <!-- Top 3 Podium Champions Showcase -->
        <?php if (!empty($topThree)): ?>
        <div class="podium-section">
          <div class="section-header" style="text-align: center; margin-bottom: 2.5rem;">
            <span class="section-kicker">National Top Contenders</span>
            <h2 class="section-heading" style="margin-bottom: 0.25rem;">CLAN CHAMPIONS PODIUM</h2>
            <p class="section-subheading" style="margin: 0 auto; max-width: 600px;">
              Warriors currently holding the top three positions in Nigeria.
            </p>
          </div>

          <div class="podium-grid">
            <!-- Rank #2 (Silver) -->
            <?php if (isset($topThree[1])): $p2 = $topThree[1]; ?>
            <div class="podium-card rank-silver">
              <div class="podium-rank-crown">2</div>
              <div class="podium-avatar"><?= getInitials($p2['full_name']) ?></div>
              <span class="podium-clan-id"><?= htmlspecialchars($p2['clan_id']) ?></span>
              <h3 class="podium-name"><?= htmlspecialchars(maskParticipantName($p2['full_name'])) ?></h3>
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
            <div class="podium-card rank-gold">
              <div class="podium-rank-crown">1</div>
              <div class="podium-avatar"><?= getInitials($p1['full_name']) ?></div>
              <span class="podium-clan-id"><?= htmlspecialchars($p1['clan_id']) ?></span>
              <h3 class="podium-name"><?= htmlspecialchars(maskParticipantName($p1['full_name'])) ?></h3>
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
            <div class="podium-card rank-bronze">
              <div class="podium-rank-crown">3</div>
              <div class="podium-avatar"><?= getInitials($p3['full_name']) ?></div>
              <span class="podium-clan-id"><?= htmlspecialchars($p3['clan_id']) ?></span>
              <h3 class="podium-name"><?= htmlspecialchars(maskParticipantName($p3['full_name'])) ?></h3>
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
        </div>
        <?php endif; ?>

        <!-- Points Checker Command Banner -->
        <div class="lookup-banner" id="check-points-anchor" style="margin-bottom: 3.5rem;">
          <div class="lookup-banner-content">
            <span class="lookup-pill"><i data-lucide="search" style="width: 13px; height: 13px;"></i> PARTICIPANT ARCHIVE</span>
            <h3 class="lookup-heading">CHECK YOUR VERIFIED CLAN POINTS</h3>
            <p class="lookup-desc">
              Played at an activation outlet? Enter your Clan ID (e.g. <code>TNH-7701</code>) or registered Email Address to inspect your complete score card, road-to-100 meter, and multi-outlet points audit.
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

        <!-- Full Interactive Standings Table -->
        <div class="leaderboard-wrapper">
          <div class="leaderboard-header">
            <div>
              <span class="section-kicker">Multi-Outlet Aggregator</span>
              <h2 class="section-heading" style="margin-bottom: 0.25rem;">NATIONWIDE AUDIT ROSTER</h2>
              <p class="section-subheading" style="margin: 0;">Real-time synchronized data from all activation outlets nationwide.</p>
            </div>

            <div class="leaderboard-filters">
              <div class="search-filter-wrap">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" id="leaderboard-search" class="form-control filter-input" placeholder="Search by ID, Name, or Email...">
              </div>

              <div class="outlet-filter-wrap">
                <select id="leaderboard-outlet-filter" class="form-control filter-select">
                  <option value="">All Outlets Nationwide</option>
                  <?php foreach ($outlets as $o): ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?> (<?= htmlspecialchars($o['city']) ?>)</option>
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
        </div>

        <!-- Navigation back to Outlets & Tour -->
        <div class="view-all-cta-box">
          <h3 style="font-family: var(--font-display); font-size: 1.8rem; letter-spacing: 1px; color: #ffffff; margin-bottom: 0.5rem;">READY TO STACK MORE POINTS?</h3>
          <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 1.5rem auto;">
            Find upcoming activation nights across partner bars, lounges, and clubs in Lagos, Abuja, and Port Harcourt.
          </p>
          <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="outlets" class="btn btn-primary btn-lg">
              <i data-lucide="map-pin"></i> Find Outlets & Tour Dates
            </a>
            <a href="./#scorecard" class="btn btn-outline btn-lg">
              <i data-lucide="target"></i> View Challenge Scorecard
            </a>
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

  <!-- ================= PARTICIPANT DETAILS & AUDIT MODAL ================= -->
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
