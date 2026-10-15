<?php
// promoter.php - Brand Ambassador & Outlet Game Master Portal
session_start();
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();

$user = $_SESSION['user'] ?? null;
$outlets = [];
$challenges = [];
if ($pdo) {
    $outlets = $pdo->query("SELECT * FROM outlets WHERE status = 'active' ORDER BY id ASC")->fetchAll();
    $challenges = $pdo->query("SELECT * FROM challenges WHERE is_active = 1 ORDER BY category ASC, points DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ambassador Game Master Portal | The Next Highlandah</title>
  <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
  <link rel="stylesheet" href="css/style.css?v=<?= filemtime(__DIR__ . '/css/style.css') ?>">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    .ambassador-badge {
      background: rgba(0, 200, 83, 0.15);
      border: 1px solid var(--wl-green);
      color: var(--wl-green);
      padding: 0.25rem 0.65rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
    }
    .active-tab {
      background: var(--wl-green) !important;
      color: #000 !important;
      border-color: var(--wl-green) !important;
    }
  </style>
</head>
<body style="padding-bottom: 4rem;">

  <!-- ================= TOP HEADER ================= -->
  <header class="portal-header">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <a href="./" style="text-decoration: none; display: flex; align-items: center;">
          <img src="images/logo.svg" alt="William Lawson's" class="brand-logo-img" style="height: 38px;">
        </a>
        <div>
          <div style="font-family: var(--font-display); font-size: 1.25rem; color: #fff; line-height: 1;">
            AMBASSADOR PORTAL
          </div>
          <div style="font-size: 0.7rem; color: var(--wl-green); font-weight: 700;">
            TNH SEASON 3 • ON-SITE GAME MASTER
          </div>
        </div>
      </div>

      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <?php if ($user): ?>
          <div style="text-align: right;" class="portal-user-meta">
            <div style="font-size: 0.85rem; font-weight: 700; color: #fff;"><?= htmlspecialchars($user['name']) ?></div>
            <div style="font-size: 0.75rem; color: var(--wl-green); font-family: var(--font-mono);"><?= htmlspecialchars($user['staff_code']) ?></div>
          </div>
          <button id="logout-btn" class="btn btn-outline btn-sm">
            <i data-lucide="log-out"></i> Logout
          </button>
        <?php else: ?>
          <a href="./" class="btn btn-outline btn-sm">
            <i data-lucide="arrow-left"></i> Public Site
          </a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="container" style="margin-top: 2rem;">

    <?php if (!$user): ?>
      <!-- ================= LOGIN SCREEN ================= -->
      <div class="auth-split-card">
        <div class="auth-split-media">
          <img src="images/image.png" alt="William Lawson's Highlandahs Only" class="auth-split-img">
          <div class="auth-split-overlay"></div>
          <div class="auth-split-badge-wrap">
            <span class="ambassador-badge">On-Site Staff Portal</span>
          </div>
          <div class="auth-split-caption">
            <h3>THE NEXT HIGHLANDAH</h3>
            <p>On-site game master & ambassador point allocation command.</p>
          </div>
        </div>
        <div class="auth-split-form-wrap">
          <div class="auth-form-header">
            <span class="section-kicker" style="margin-bottom: 0.25rem;">Staff Access</span>
            <h2 class="auth-title">AMBASSADOR SIGN IN</h2>
            <p class="auth-subtitle">
              Log in to register consumers, look up Clan IDs, and allocate points at your outlet.
            </p>
          </div>

          <form id="promoter-login-form">
            <div class="form-group">
              <label class="form-label">Staff Code or Email</label>
              <input type="text" name="identifier" class="form-control" placeholder="e.g. PRM-101 or emeka@tnhng.com" value="PRM-101" required>
            </div>

            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Enter password" value="promoter123" required>
            </div>

            <div class="form-group">
              <label class="form-label">Current Outlet Shift Location</label>
              <select name="current_outlet_id" class="form-control" required>
                <?php foreach ($outlets as $o): ?>
                  <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?> (<?= htmlspecialchars($o['city']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
              <i data-lucide="key"></i> Enter Ambassador Console
            </button>

            <div class="auth-hint-box">
              <strong style="color: var(--wl-green);">Sample Credentials:</strong><br>
              Staff Code: <code>PRM-101</code> / Password: <code>promoter123</code> (VI Outlet)<br>
              Staff Code: <code>PRM-102</code> / Password: <code>promoter123</code> (Ikeja Outlet)
            </div>
          </form>
        </div>
      </div>

    <?php else: ?>
      <!-- ================= AMBASSADOR CONSOLE ================= -->
      
      <!-- Outlet Banner & Shift Switcher -->
      <div class="glass-panel promoter-outlet-banner">
        <div>
          <div style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px;">Current Active Outlet:</div>
          <div style="font-size: 1.3rem; font-weight: 700; color: var(--wl-green); display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="map-pin" style="color: var(--wl-green);"></i>
            <span id="active-outlet-display"><?= htmlspecialchars($user['outlet_name']) ?></span>
          </div>
          <div style="font-size: 0.8rem; color: var(--text-muted);">
            Logged in as: <strong><?= htmlspecialchars($user['name']) ?></strong> (<?= htmlspecialchars($user['staff_code']) ?>)
          </div>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <select id="switch-outlet-select" class="form-control" style="width: auto; min-width: 180px; padding: 0.5rem 0.8rem; font-size: 0.85rem;">
            <?php foreach ($outlets as $o): ?>
              <option value="<?= $o['id'] ?>" <?= ($o['id'] == $user['outlet_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($o['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button id="switch-outlet-btn" class="btn btn-outline btn-sm">Switch Venue</button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="filter-tabs">
        <button class="filter-tab active-tab" onclick="switchTab('tab-lookup', this)">
          <i data-lucide="search"></i> Lookup & Award Points
        </button>
        <button class="filter-tab" onclick="switchTab('tab-register', this)">
          <i data-lucide="user-plus"></i> Fast On-Site Registration
        </button>
      </div>

      <!-- TAB 1: LOOKUP & AWARD POINTS -->
      <div id="tab-lookup" class="tab-content">
        <div class="promoter-layout-grid">
          
          <!-- Participant Search Card -->
          <div class="glass-panel promoter-main-panel">
            <h3 class="promoter-step-title">
              <i data-lucide="user-check" style="color: var(--wl-green);"></i> 1. FIND PARTICIPANT
            </h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">
              Enter the participant's Clan ID (e.g. <code>TNH-7701</code>) or registered Email Address. Any participant registered at any outlet can play here!
            </p>

            <form id="promoter-search-form" class="lookup-form-wrap">
              <input type="text" id="promoter-search-input" class="form-control" placeholder="Enter Clan ID (TNH-XXXX), Email, or Phone..." required>
              <button type="submit" class="btn btn-primary lookup-submit-btn">
                <i data-lucide="search"></i> Lookup
              </button>
            </form>

            <!-- Participant Found Card (Hidden until searched) -->
            <div id="participant-card-wrap" class="participant-card-wrap" style="display: none;">
              <div class="participant-summary-card">
                <div class="participant-header-row">
                  <div class="participant-info">
                    <span class="ambassador-badge" id="card-clan-id">TNH-0000</span>
                    <h2 class="participant-name" id="card-name">Participant Name</h2>
                    <div class="participant-contact" id="card-contact">08012345678 • email@example.com</div>
                    <div class="participant-origin" id="card-origin">Origin: VI Outlet</div>
                  </div>
                  <div class="participant-score-box">
                    <div class="score-label">Current Score</div>
                    <div class="score-points" id="card-points">0 PTS</div>
                    <div id="card-badge" style="margin-top: 0.25rem;"></div>
                  </div>
                </div>

                <div class="progress-track" style="height: 10px; margin: 0.75rem 0 0.5rem;">
                  <div class="progress-fill" id="card-progress" style="width: 0%;"></div>
                </div>
                <div class="participant-progress-meta">
                  <span>0 PTS</span>
                  <span id="card-needed">100 PTS to Grand Finale</span>
                  <span>100 PTS</span>
                </div>
              </div>

              <!-- ================= 2. PICK A NUMBER (1 – 7) CHALLENGE SELECTOR ================= -->
              <div class="number-picker-section" id="challenge-picker-section">
                <div class="wheel-section-header" style="margin-bottom: 0.75rem;">
                  <div class="wheel-section-title-wrap">
                    <span class="ambassador-badge" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                      <i data-lucide="dices" style="width: 13px; height: 13px;"></i> Official Challenge Matrix
                    </span>
                    <h3 class="wheel-section-title">
                      <i data-lucide="hash" style="color: var(--wl-green);"></i> 2. PICK A NUMBER (1 – 7)
                    </h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                      Ask <strong id="picker-participant-name" style="color: var(--wl-green);">Participant</strong> to pick a mystery number from 1 to 7 to reveal their challenge!
                    </p>
                  </div>
                </div>

                <div class="number-picker-grid">
                  <button type="button" class="number-tile-btn mystery-tile" data-number="1" onclick="selectChallengeNumber(1)">
                    <span class="number-mystery-icon"><i data-lucide="help-circle"></i></span>
                    <span class="number-tile-digit">1</span>
                    <span class="number-tile-label">NUMBER 1</span>
                  </button>

                  <button type="button" class="number-tile-btn mystery-tile" data-number="2" onclick="selectChallengeNumber(2)">
                    <span class="number-mystery-icon"><i data-lucide="help-circle"></i></span>
                    <span class="number-tile-digit">2</span>
                    <span class="number-tile-label">NUMBER 2</span>
                  </button>

                  <button type="button" class="number-tile-btn mystery-tile" data-number="3" onclick="selectChallengeNumber(3)">
                    <span class="number-mystery-icon"><i data-lucide="help-circle"></i></span>
                    <span class="number-tile-digit">3</span>
                    <span class="number-tile-label">NUMBER 3</span>
                  </button>

                  <button type="button" class="number-tile-btn mystery-tile" data-number="4" onclick="selectChallengeNumber(4)">
                    <span class="number-mystery-icon"><i data-lucide="help-circle"></i></span>
                    <span class="number-tile-digit">4</span>
                    <span class="number-tile-label">NUMBER 4</span>
                  </button>

                  <button type="button" class="number-tile-btn mystery-tile" data-number="5" onclick="selectChallengeNumber(5)">
                    <span class="number-mystery-icon"><i data-lucide="help-circle"></i></span>
                    <span class="number-tile-digit">5</span>
                    <span class="number-tile-label">NUMBER 5</span>
                  </button>

                  <button type="button" class="number-tile-btn mystery-tile" data-number="6" onclick="selectChallengeNumber(6)">
                    <span class="number-mystery-icon"><i data-lucide="help-circle"></i></span>
                    <span class="number-tile-digit">6</span>
                    <span class="number-tile-label">NUMBER 6</span>
                  </button>

                  <button type="button" class="number-tile-btn mystery-tile" data-number="7" onclick="selectChallengeNumber(7)">
                    <span class="number-mystery-icon"><i data-lucide="help-circle"></i></span>
                    <span class="number-tile-digit">7</span>
                    <span class="number-tile-label">NUMBER 7</span>
                  </button>
                </div>
              </div>

              <!-- ================= DIRECT CHALLENGE EVALUATION (FOR NUMBERS 2 – 7) ================= -->
              <div id="direct-challenge-card" class="direct-challenge-card" style="display: none;">
                <!-- Top Reset / Back Bar -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 0.5rem;">
                  <button type="button" class="btn btn-outline btn-sm reset-number-btn" onclick="resetNumberPicker()">
                    <i data-lucide="arrow-left"></i> Pick Another Number
                  </button>
                  <span id="direct-number-tag" style="font-size: 0.8rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700;">Challenge #5</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; flex-wrap: wrap;">
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span id="direct-category-badge" class="ambassador-badge">BOLD</span>
                  </div>
                  <div style="font-family: var(--font-display); font-size: 2.2rem; color: var(--wl-green); line-height: 1;" id="direct-points-badge">+5 PTS</div>
                </div>

                <h3 id="direct-title" style="font-size: 1.6rem; color: #fff; margin: 0.65rem 0 0.35rem; line-height: 1.2;">
                  CHALLENGE TITLE
                </h3>

                <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 0.95rem; margin: 0.95rem 0;">
                  <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem;">
                    Official Challenge Rule & Expectation:
                  </div>
                  <p id="direct-rule" style="font-size: 0.9rem; color: var(--text-secondary); margin: 0; line-height: 1.45;">
                    Rule description
                  </p>
                </div>

                <!-- Interactive Countdown Timer (for 15s push-up or 60s planking) -->
                <div id="direct-timer-wrap" style="display: none; margin-bottom: 1.25rem;">
                  <div class="timer-box">
                    <div class="timer-display-wrap">
                      <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase;">Challenge Countdown</div>
                      <div class="timer-digits" id="direct-timer-display">00:15</div>
                    </div>
                    <div class="timer-controls">
                      <button type="button" id="direct-timer-start-btn" class="btn btn-outline btn-sm">
                        <i data-lucide="play"></i> Start
                      </button>
                      <button type="button" id="direct-timer-pause-btn" class="btn btn-outline btn-sm" style="display: none;">
                        <i data-lucide="pause"></i> Pause
                      </button>
                      <button type="button" id="direct-timer-reset-btn" class="btn btn-outline btn-sm">
                        <i data-lucide="rotate-ccw"></i> Reset
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Referee Verdict Section -->
                <div style="border-top: 1px dashed var(--border-subtle); padding-top: 1.25rem;">
                  <label style="font-size: 0.8rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.35rem;">
                    Referee Evaluation Verdict *
                  </label>
                  <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                    Did the participant complete the challenge according to official rules?
                  </p>

                  <div class="form-group" style="margin-bottom: 1rem;">
                    <input type="text" id="direct-verdict-notes" class="form-control" style="font-size: 0.85rem; padding: 0.6rem 0.85rem;" placeholder="Optional referee notes (e.g. 25 pushups completed cleanly)">
                  </div>

                  <div class="verdict-actions">
                    <button type="button" id="btn-direct-pass" class="btn-verdict-pass">
                      <i data-lucide="check-circle-2"></i> PASS — AWARD POINTS
                    </button>
                    <button type="button" id="btn-direct-fail" class="btn-verdict-fail">
                      <i data-lucide="x-circle"></i> FAIL — INCOMPLETE (0 PTS)
                    </button>
                  </div>

                  <div style="text-align: center; margin-top: 0.85rem;">
                    <button type="button" onclick="resetNumberPicker()" style="background: none; border: none; color: var(--text-dim); font-size: 0.82rem; cursor: pointer; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.35rem;">
                      <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i> Cancel & choose a different number
                    </button>
                  </div>
                </div>
              </div>

              <!-- ================= 3. ON-SITE SPIN THE WHEEL GAME (UNLOCKED BY NUMBER 1) ================= -->
              <div id="promoter-wheel-section" class="wheel-section" style="display: none;">
                <div class="wheel-section-header">
                  <div class="wheel-section-title-wrap">
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                      <button type="button" class="btn btn-outline btn-sm reset-number-btn" onclick="resetNumberPicker()">
                        <i data-lucide="arrow-left"></i> Pick Another Number
                      </button>
                      <span class="ambassador-badge" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i data-lucide="sparkles" style="width: 13px; height: 13px;"></i> Unlocked by Challenge #1
                      </span>
                    </div>
                    <h3 class="wheel-section-title">
                      <i data-lucide="disc-3" style="color: var(--wl-green);"></i> SPIN THE WHEEL
                    </h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                      Random challenge draw for <strong id="wheel-participant-name" style="color: var(--wl-green);">Participant</strong>. Maintain Highlandah fair play!
                    </p>
                  </div>
                  <div id="wheel-status-pill" class="wheel-status-pill">
                    Ready to Spin
                  </div>
                </div>

                <div class="wheel-layout">
                  <!-- Wheel Canvas Column -->
                  <div class="wheel-canvas-card">
                    <div class="wheel-canvas-wrap">
                      <canvas id="highlandah-wheel-canvas"></canvas>
                    </div>
                    <button type="button" id="spin-wheel-btn" class="btn btn-primary btn-lg wheel-spin-cta">
                      <i data-lucide="rotate-cw"></i> SPIN THE WHEEL
                    </button>
                    <span style="font-size: 0.72rem; color: var(--text-dim); margin-top: 0.5rem; display: block;">
                      Tap with the participant to start random challenge draw
                    </span>
                  </div>

                  <!-- Challenge Evaluation & Pass/Fail Verdict Column -->
                  <div id="wheel-result-card" class="wheel-result-card">
                    <!-- Awaiting Spin State -->
                    <div id="wheel-empty-state" style="text-align: center; margin: auto; padding: 2rem 1rem;">
                      <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(0, 200, 83, 0.1); border: 1px dashed var(--wl-green); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i data-lucide="disc-3" style="width: 32px; height: 32px; color: var(--wl-green);"></i>
                      </div>
                      <h4 style="font-size: 1.25rem; color: #fff; margin-bottom: 0.35rem;">AWAITING WHEEL DRAW</h4>
                      <p style="color: var(--text-muted); font-size: 0.85rem; max-width: 320px; margin: 0 auto;">
                        Tap <strong>SPIN THE WHEEL</strong> with the participant to randomly draw their on-premise challenge.
                      </p>
                    </div>

                    <!-- Active Drawn Challenge State -->
                    <div id="wheel-challenge-state" style="display: none;">
                      <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                        <span id="drawn-category-badge" class="ambassador-badge">BOLD</span>
                        <div style="font-family: var(--font-display); font-size: 2.2rem; color: var(--wl-green); line-height: 1;" id="drawn-points-badge">+5 PTS</div>
                      </div>

                      <h3 id="drawn-title" style="font-size: 1.45rem; color: #fff; margin-top: 0.5rem; line-height: 1.2; word-break: break-word;">
                        CHALLENGE TITLE
                      </h3>

                      <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 0.85rem; margin: 0.85rem 0;">
                        <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem;">
                          Official Challenge Rule:
                        </div>
                        <p id="drawn-rule" style="font-size: 0.85rem; color: var(--text-secondary); margin: 0; line-height: 1.4; word-break: break-word;">
                          Rule description
                        </p>
                      </div>

                      <!-- Interactive Countdown Timer (for 15s or 60s timed tasks) -->
                      <div id="drawn-timer-wrap" style="display: none;">
                        <div class="timer-box">
                          <div class="timer-display-wrap">
                            <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase;">Challenge Countdown</div>
                            <div class="timer-digits" id="timer-display">00:15</div>
                          </div>
                          <div class="timer-controls">
                            <button type="button" id="timer-start-btn" class="btn btn-outline btn-sm">
                              <i data-lucide="play"></i> Start
                            </button>
                            <button type="button" id="timer-pause-btn" class="btn btn-outline btn-sm" style="display: none;">
                              <i data-lucide="pause"></i> Pause
                            </button>
                            <button type="button" id="timer-reset-btn" class="btn btn-outline btn-sm">
                              <i data-lucide="rotate-ccw"></i> Reset
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Referee Verdict Section (Mandatory Pass/Fail) -->
                      <div style="margin-top: 1rem; border-top: 1px dashed var(--border-subtle); padding-top: 1rem;">
                        <label style="font-size: 0.78rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.25rem;">
                          Promoter Evaluation Verdict *
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.65rem;">
                          Did the participant complete the challenge according to official rules?
                        </p>

                        <div class="form-group" style="margin-bottom: 0.75rem;">
                          <input type="text" id="verdict-notes-input" class="form-control" style="font-size: 0.82rem; padding: 0.5rem 0.75rem;" placeholder="Optional referee notes (e.g. 20 squats completed cleanly)">
                        </div>

                        <div class="verdict-actions">
                          <button type="button" id="btn-verdict-pass" class="btn-verdict-pass">
                            <i data-lucide="check-circle-2"></i> PASS — AWARD POINTS
                          </button>
                          <button type="button" id="btn-verdict-fail" class="btn-verdict-fail">
                            <i data-lucide="x-circle"></i> FAIL — INCOMPLETE (0 PTS)
                          </button>
                        </div>

                        <div style="text-align: center; margin-top: 0.85rem;">
                          <button type="button" onclick="resetNumberPicker()" style="background: none; border: none; color: var(--text-dim); font-size: 0.82rem; cursor: pointer; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i> Cancel & choose a different number
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Free Re-Spin State (When wheel lands on "Spin Again") -->
                    <div id="wheel-respin-state" style="display: none; text-align: center; margin: auto; padding: 2rem 1rem;">
                      <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255, 213, 79, 0.15); border: 2px solid #ffd54f; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i data-lucide="rotate-cw" style="width: 32px; height: 32px; color: #ffd54f;"></i>
                      </div>
                      <span class="ambassador-badge" style="background: rgba(255, 213, 79, 0.2); color: #ffd54f; border-color: #ffd54f;">FREE RE-SPIN</span>
                      <h3 style="font-size: 1.6rem; color: #fff; margin: 0.5rem 0 0.35rem;">SPIN AGAIN!</h3>
                      <p style="color: var(--text-muted); font-size: 0.85rem; max-width: 340px; margin: 0 auto 1.25rem;">
                        The Highlandah Clan gives you another shot! No points added or lost. Tap below to spin the wheel again immediately.
                      </p>
                      <button type="button" id="btn-respin-trigger" class="btn btn-primary btn-lg" style="width: 100%; max-width: 260px;">
                        <i data-lucide="rotate-cw"></i> Spin Again Now
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Hidden form inputs for fallback -->
              <input type="hidden" id="alloc-participant-id" name="participant_id">

              <!-- History at this outlet -->
              <div class="history-section-wrap">
                <div class="history-header">
                  <h4 class="history-title">
                    <i data-lucide="history"></i> Previous Points Earned by this Participant
                  </h4>
                  <span class="history-hint"><i data-lucide="move-horizontal"></i> Scroll table horizontally</span>
                </div>
                <div class="history-table-container">
                  <table class="history-table">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Challenge</th>
                        <th>Points</th>
                        <th>Outlet</th>
                        <th>Awarded By</th>
                      </tr>
                    </thead>
                    <tbody id="participant-mini-history"></tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>

      <!-- TAB 2: FAST ON-SITE REGISTRATION -->
      <div id="tab-register" class="tab-content" style="display: none;">
        <div class="glass-panel" style="max-width: 680px; margin: 0 auto; padding: 2.5rem;">
          <h3 style="font-size: 1.8rem; color: #fff; margin-bottom: 0.5rem;">
            <i data-lucide="user-plus" style="color: var(--wl-green);"></i> REGISTER NEW HIGHLANDAH ON-SITE
          </h3>
          <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.75rem;">
            When a participant walks up to your outlet without a Clan ID, register them here. You will be recorded as the registrar, and they can immediately play!
          </p>

          <form id="promoter-onsite-register-form">
            <div class="form-grid-2col">
              <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
              </div>

              <div class="form-group">
                <label class="form-label">Phone Number (WhatsApp) *</label>
                <input type="tel" name="phone" class="form-control" placeholder="080XXXXXXXX" required>
              </div>
            </div>

            <div class="form-grid-2col">
              <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
              </div>

              <div class="form-group">
                <label class="form-label">Date of Birth (18+) *</label>
                <input type="date" name="dob" class="form-control" required>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Instagram / Social Handle (Optional)</label>
              <input type="text" name="instagram" class="form-control" placeholder="@username">
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
              <i data-lucide="sparkles"></i> Enroll & Issue Clan Pass
            </button>
          </form>
        </div>
      </div>

    <?php endif; ?>

  </main>

  <script src="js/highlandah-alert.js"></script>
  <script src="js/main.js"></script>
  <script src="js/promoter-wheel.js?v=<?= filemtime(__DIR__ . '/js/promoter-wheel.js') ?>"></script>
  <script>
    lucide.createIcons();

    // Wheel & Active State Variables
    let wheelInstance = null;
    let activeParticipantData = null;
    let activeDrawnChallenge = null;
    let timerInterval = null;
    let timerSecondsLeft = 0;

    // Promoter Login
    const loginForm = document.getElementById('promoter-login-form');
    if (loginForm) {
      loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(loginForm);
        fd.append('action', 'login');

        try {
          const res = await fetch('api/auth.php', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            window.location.reload();
          } else {
            highlandahAlert({ title: 'ACCESS DENIED', message: data.message || 'Login failed. Check your staff credentials.', type: 'error' });
          }
        } catch (err) {
          highlandahAlert({ title: 'NETWORK ERROR', message: 'Unable to reach the server. Please check your network connection.', type: 'error' });
        }
      });
    }

    // Logout
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', async () => {
        await fetch('api/auth.php?action=logout');
        window.location.reload();
      });
    }

    // Switch Outlet
    const switchBtn = document.getElementById('switch-outlet-btn');
    if (switchBtn) {
      switchBtn.addEventListener('click', async () => {
        const sel = document.getElementById('switch-outlet-select');
        const fd = new FormData();
        fd.append('action', 'switch_outlet');
        fd.append('outlet_id', sel.value);

        const res = await fetch('api/auth.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          document.getElementById('active-outlet-display').textContent = data.user.outlet_name;
          showToast(`Switched active venue to: ${data.user.outlet_name}`);
          setTimeout(() => window.location.reload(), 1000);
        }
      });
    }

    // Tab Switching
    function switchTab(tabId, btnEl) {
      document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active-tab'));
      const targetContent = document.getElementById(tabId);
      if (targetContent) targetContent.style.display = 'block';
      const activeBtn = btnEl || (window.event && window.event.target ? window.event.target.closest('.filter-tab') : null);
      if (activeBtn) activeBtn.classList.add('active-tab');

      if (tabId === 'tab-lookup' && wheelInstance && activeParticipantData) {
        setTimeout(() => {
          wheelInstance.setupCanvas();
          wheelInstance.drawWheel();
        }, 50);
      }
    }

    // Promoter Search Participant
    const searchForm = document.getElementById('promoter-search-form');
    if (searchForm) {
      searchForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('promoter-search-input');
        const query = input ? input.value.trim() : '';
        if (!query) return;

        const btn = searchForm.querySelector('button[type="submit"]');
        if (btn) {
          btn.disabled = true;
          btn.innerHTML = '<i data-lucide="loader" style="animation: spin 1s linear infinite;"></i> Searching...';
          lucide.createIcons();
        }

        try {
          const res = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(query)}`);
          if (!res.ok) {
            throw new Error(`Server returned HTTP ${res.status}`);
          }
          const data = await res.json();

          if (data.success && data.participant) {
            displayFoundParticipant(data);
            showToast(`Found Clan member ${data.participant.full_name}!`, 'success');
          } else {
            highlandahAlert({ 
              title: 'PARTICIPANT NOT FOUND', 
              message: data.message || `No participant found matching "${query}". Please check the ID or Email, or enroll them under the Fast On-Site Registration tab.`, 
              type: 'warning' 
            });
            const wrap = document.getElementById('participant-card-wrap');
            if (wrap) wrap.style.display = 'none';
          }
        } catch (err) {
          console.error("Lookup error:", err);
          highlandahAlert({ 
            title: 'SEARCH ERROR', 
            message: 'Unable to complete participant lookup. Please check internet connection and try again.', 
            type: 'error' 
          });
        } finally {
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="search"></i> Lookup';
            lucide.createIcons();
          }
        }
      });
    }

    // Numbered Challenges Configuration (1 to 7)
    const NUMBERED_CHALLENGES = {
      1: {
        number: 1,
        id: 8,
        category: 'Good humored',
        title: 'Spin the Bottle / Wheel',
        rule: 'Spin the wheel and answer a question or do a challenge drawn by the wheel!',
        points: 2,
        timeLimit: 0,
        isWheel: true
      },
      2: {
        number: 2,
        id: 9,
        category: 'Good humored',
        title: 'Spell WL Backwards',
        rule: 'Spell William Lawson backward without misspelling (S-N-O-S-W-A-L M-A-I-L-L-I-W).',
        points: 2,
        timeLimit: 0,
        isWheel: false
      },
      3: {
        number: 3,
        id: 10,
        category: 'Good humored',
        title: 'Imitate a Celebrity',
        rule: 'Do an impression of a celebrity boldly in front of the crowd.',
        points: 3,
        timeLimit: 0,
        isWheel: false
      },
      4: {
        number: 4,
        id: 11,
        category: 'Unconventional',
        title: 'Spin Around and Walk on a Straight Line',
        rule: 'Spin around 5 times and walk in a straight line without tripping.',
        points: 3,
        timeLimit: 0,
        isWheel: false
      },
      5: {
        number: 5,
        id: 12,
        category: 'Bold',
        title: 'Push-Up',
        rule: 'Highest number of push ups within 15 seconds.',
        points: 5,
        timeLimit: 15,
        isWheel: false
      },
      6: {
        number: 6,
        id: 13,
        category: 'Bold',
        title: 'Planking',
        rule: 'Participants to maintain their body in a straight line for 60 seconds.',
        points: 5,
        timeLimit: 60,
        isWheel: false
      },
      7: {
        number: 7,
        id: 14,
        category: 'Bold',
        title: 'Foot Ball Leg Juggling',
        rule: 'Juggle a football 50 times with your legs.',
        points: 5,
        timeLimit: 0,
        isWheel: false
      }
    };

    let directTimerInterval = null;
    let directTimerSeconds = 0;

    window.resetNumberPicker = function() {
      // Clear all timers
      clearInterval(timerInterval);
      clearInterval(directTimerInterval);

      // Hide challenge views
      const directCard = document.getElementById('direct-challenge-card');
      if (directCard) directCard.style.display = 'none';

      const wheelSection = document.getElementById('promoter-wheel-section');
      if (wheelSection) wheelSection.style.display = 'none';

      // Unhide number picker section
      const pickerSection = document.getElementById('challenge-picker-section');
      if (pickerSection) {
        pickerSection.style.display = 'block';
        pickerSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }

      // Remove active highlight from tiles
      document.querySelectorAll('.number-tile-btn').forEach(btn => {
        btn.classList.remove('active-number');
      });

      // Clear active drawn challenge & verdict inputs
      activeDrawnChallenge = null;
      resetWheelResultState();

      if (window.lucide) lucide.createIcons();
    };

    window.selectChallengeNumber = function(num) {
      if (!activeParticipantData) {
        highlandahAlert({ title: 'LOOKUP REQUIRED', message: 'Please search and select a participant first.', type: 'warning' });
        return;
      }

      // Highlight active button
      document.querySelectorAll('.number-tile-btn').forEach(btn => {
        btn.classList.toggle('active-number', btn.dataset.number == num);
      });

      const ch = NUMBERED_CHALLENGES[num];
      if (!ch) return;

      const pickerSection = document.getElementById('challenge-picker-section');
      const wheelSection = document.getElementById('promoter-wheel-section');
      const directCard = document.getElementById('direct-challenge-card');

      // Hide the number picker so the challenge takes the space
      if (pickerSection) {
        pickerSection.style.display = 'none';
      }

      if (ch.isWheel) {
        // Number 1 selected: Unhide wheel section, hide direct challenge card
        if (directCard) directCard.style.display = 'none';
        if (wheelSection) {
          wheelSection.style.display = 'block';
          resetWheelResultState();
          setTimeout(() => {
            initOrResetWheel();
          }, 50);
          wheelSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        showToast("Challenge #1 selected: Spin the Wheel is unlocked!", "success");
      } else {
        // Numbers 2 to 7 selected: Hide wheel section, show direct challenge card
        if (wheelSection) wheelSection.style.display = 'none';
        activeDrawnChallenge = {
          id: ch.id,
          label: ch.title,
          shortTitle: ch.title,
          points: ch.points,
          category: ch.category,
          timeLimit: ch.timeLimit,
          rule: ch.rule
        };
        displayDirectChallenge(ch);
      }
      if (window.lucide) lucide.createIcons();
    };

    function displayDirectChallenge(ch) {
      const directCard = document.getElementById('direct-challenge-card');
      if (!directCard) return;

      document.getElementById('direct-category-badge').textContent = ch.category.toUpperCase();
      document.getElementById('direct-number-tag').textContent = `Challenge #${ch.number}`;
      document.getElementById('direct-points-badge').textContent = `+${ch.points} PTS`;
      document.getElementById('direct-title').textContent = ch.title;
      document.getElementById('direct-rule').textContent = ch.rule;
      document.getElementById('direct-verdict-notes').value = '';

      setupDirectTimer(ch.timeLimit);

      directCard.style.display = 'block';
      directCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      if (window.lucide) lucide.createIcons();
    }

    function setupDirectTimer(seconds) {
      clearInterval(directTimerInterval);
      const wrap = document.getElementById('direct-timer-wrap');
      const display = document.getElementById('direct-timer-display');
      const startBtn = document.getElementById('direct-timer-start-btn');
      const pauseBtn = document.getElementById('direct-timer-pause-btn');

      if (!seconds || seconds <= 0) {
        if (wrap) wrap.style.display = 'none';
        return;
      }

      if (wrap) wrap.style.display = 'block';
      directTimerSeconds = seconds;
      updateDirectTimerDisplay(directTimerSeconds);
      if (display) display.classList.remove('time-up');
      if (startBtn) startBtn.style.display = 'inline-flex';
      if (pauseBtn) pauseBtn.style.display = 'none';
    }

    function updateDirectTimerDisplay(secs) {
      const m = Math.floor(secs / 60);
      const s = secs % 60;
      const el = document.getElementById('direct-timer-display');
      if (el) {
        el.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
      }
    }

    const directTimerStart = document.getElementById('direct-timer-start-btn');
    if (directTimerStart) {
      directTimerStart.addEventListener('click', () => {
        const startBtn = document.getElementById('direct-timer-start-btn');
        const pauseBtn = document.getElementById('direct-timer-pause-btn');
        if (startBtn) startBtn.style.display = 'none';
        if (pauseBtn) pauseBtn.style.display = 'inline-flex';

        clearInterval(directTimerInterval);
        directTimerInterval = setInterval(() => {
          directTimerSeconds--;
          updateDirectTimerDisplay(directTimerSeconds);
          if (directTimerSeconds <= 0) {
            clearInterval(directTimerInterval);
            if (pauseBtn) pauseBtn.style.display = 'none';
            if (startBtn) startBtn.style.display = 'inline-flex';
            const el = document.getElementById('direct-timer-display');
            if (el) el.classList.add('time-up');
            showToast("TIME'S UP! Evaluate if participant completed the challenge.", 'warning');
          }
        }, 1000);
      });
    }

    const directTimerPause = document.getElementById('direct-timer-pause-btn');
    if (directTimerPause) {
      directTimerPause.addEventListener('click', () => {
        clearInterval(directTimerInterval);
        const startBtn = document.getElementById('direct-timer-start-btn');
        const pauseBtn = document.getElementById('direct-timer-pause-btn');
        if (pauseBtn) pauseBtn.style.display = 'none';
        if (startBtn) startBtn.style.display = 'inline-flex';
      });
    }

    const directTimerReset = document.getElementById('direct-timer-reset-btn');
    if (directTimerReset) {
      directTimerReset.addEventListener('click', () => {
        clearInterval(directTimerInterval);
        if (activeDrawnChallenge && activeDrawnChallenge.timeLimit) {
          directTimerSeconds = activeDrawnChallenge.timeLimit;
          updateDirectTimerDisplay(directTimerSeconds);
          const el = document.getElementById('direct-timer-display');
          if (el) el.classList.remove('time-up');
        }
        const startBtn = document.getElementById('direct-timer-start-btn');
        const pauseBtn = document.getElementById('direct-timer-pause-btn');
        if (pauseBtn) pauseBtn.style.display = 'none';
        if (startBtn) startBtn.style.display = 'inline-flex';
      });
    }

    function initOrResetWheel() {
      if (!wheelInstance) {
        wheelInstance = new HighlandahWheel('highlandah-wheel-canvas');
      } else {
        wheelInstance.setupCanvas();
        wheelInstance.drawWheel();
      }
    }

    function resetWheelResultState() {
      clearInterval(timerInterval);
      clearInterval(directTimerInterval);
      activeDrawnChallenge = null;
      document.getElementById('wheel-empty-state').style.display = 'block';
      document.getElementById('wheel-challenge-state').style.display = 'none';
      document.getElementById('wheel-respin-state').style.display = 'none';
      document.getElementById('wheel-result-card').classList.remove('active-challenge');
      document.getElementById('wheel-status-pill').textContent = 'Ready to Spin';
      document.getElementById('verdict-notes-input').value = '';
      const directNotes = document.getElementById('direct-verdict-notes');
      if (directNotes) directNotes.value = '';
    }

    function displayFoundParticipant(data) {
      activeParticipantData = data;
      const p = data.participant;
      const s = data.stats;

      document.getElementById('participant-card-wrap').style.display = 'block';
      document.getElementById('card-clan-id').textContent = p.clan_id;
      document.getElementById('card-name').textContent = p.full_name;
      document.getElementById('card-contact').textContent = `${p.phone} • ${p.email}`;
      document.getElementById('card-origin').textContent = `Enrolled at: ${p.registered_outlet_name || 'Web Activation'} by ${p.registered_by_name || 'Self-Registered'}`;
      document.getElementById('card-points').textContent = `${s.total_points} PTS`;
      document.getElementById('card-progress').style.width = `${s.progress_percent}%`;
      document.getElementById('card-needed').textContent = s.is_qualified ? 'QUALIFIED!' : `${s.points_needed} pts needed`;

      // Update name on picker & wheel titles
      const pickerPartName = document.getElementById('picker-participant-name');
      if (pickerPartName) pickerPartName.textContent = p.full_name;
      const wheelPartName = document.getElementById('wheel-participant-name');
      if (wheelPartName) wheelPartName.textContent = p.full_name;

      const badgeEl = document.getElementById('card-badge');
      if (s.is_qualified) {
        badgeEl.innerHTML = `<span class="qualified-tag">GRAND FINALE QUALIFIED</span>`;
      } else {
        badgeEl.innerHTML = ``;
      }

      // Hidden form input
      const allocIdInput = document.getElementById('alloc-participant-id');
      if (allocIdInput) allocIdInput.value = p.id;

      // Render mini history
      const tbody = document.getElementById('participant-mini-history');
      if (data.history.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 1rem; color: var(--text-dim);">No points logged yet. Be the first to award points!</td></tr>`;
      } else {
        tbody.innerHTML = data.history.map(h => `
          <tr>
            <td class="col-date">${h.created_at.substring(0, 16)}</td>
            <td class="col-challenge">${h.challenge_name}</td>
            <td class="col-points ${h.points > 0 ? 'pts-pass' : 'pts-fail'}">${h.points > 0 ? '+' + h.points : '0 (Failed)'}</td>
            <td class="col-outlet">${h.outlet_name}</td>
            <td class="col-promoter">${h.promoter_name || 'Staff'}</td>
          </tr>
        `).join('');
      }

      // Reset number selection and challenge cards initially
      document.querySelectorAll('.number-tile-btn').forEach(btn => btn.classList.remove('active-number'));
      const pickerSec = document.getElementById('challenge-picker-section');
      if (pickerSec) pickerSec.style.display = 'block';
      const directCard = document.getElementById('direct-challenge-card');
      if (directCard) directCard.style.display = 'none';
      const wheelSec = document.getElementById('promoter-wheel-section');
      if (wheelSec) wheelSec.style.display = 'none';

      resetWheelResultState();
    }

    // Spin Wheel Button Trigger
    const spinWheelBtn = document.getElementById('spin-wheel-btn');
    if (spinWheelBtn) {
      spinWheelBtn.addEventListener('click', () => {
        if (!activeParticipantData) {
          highlandahAlert({ title: 'LOOKUP REQUIRED', message: 'Please search and select a participant first before spinning the wheel.', type: 'warning' });
          return;
        }
        if (!wheelInstance || wheelInstance.isSpinning) return;

        spinWheelBtn.disabled = true;
        spinWheelBtn.innerHTML = '<i data-lucide="rotate-cw" style="animation: spin 1s linear infinite;"></i> SPINNING THE WHEEL...';
        lucide.createIcons();
        document.getElementById('wheel-status-pill').textContent = 'Spinning...';
        resetWheelResultState();

        wheelInstance.spin((selectedSlice) => {
          spinWheelBtn.disabled = false;
          spinWheelBtn.innerHTML = '<i data-lucide="rotate-cw"></i> SPIN THE WHEEL';
          lucide.createIcons();
          handleWheelSelection(selectedSlice);
        });
      });
    }

    // Re-Spin Button Trigger
    const respinBtn = document.getElementById('btn-respin-trigger');
    if (respinBtn) {
      respinBtn.addEventListener('click', () => {
        if (spinWheelBtn) spinWheelBtn.click();
      });
    }

    // Handle Wheel Landing
    function handleWheelSelection(slice) {
      activeDrawnChallenge = slice;

      if (slice.id === 15) {
        // Free Re-Spin
        document.getElementById('wheel-empty-state').style.display = 'none';
        document.getElementById('wheel-challenge-state').style.display = 'none';
        document.getElementById('wheel-respin-state').style.display = 'block';
        document.getElementById('wheel-status-pill').textContent = 'Lucky Re-Spin!';
        document.getElementById('wheel-result-card').classList.add('active-challenge');
        lucide.createIcons();
        return;
      }

      // Regular Challenge Drawn
      document.getElementById('wheel-empty-state').style.display = 'none';
      document.getElementById('wheel-respin-state').style.display = 'none';
      document.getElementById('wheel-challenge-state').style.display = 'block';
      document.getElementById('wheel-result-card').classList.add('active-challenge');
      document.getElementById('wheel-status-pill').textContent = `Challenge Drawn (+${slice.points} PTS)`;

      document.getElementById('drawn-category-badge').textContent = slice.category.toUpperCase();
      document.getElementById('drawn-points-badge').textContent = `+${slice.points} PTS`;
      document.getElementById('drawn-title').textContent = slice.label;
      document.getElementById('drawn-rule').textContent = slice.rule;
      document.getElementById('verdict-notes-input').value = '';

      // Configure timer if time-bound (e.g. 15s or 60s)
      setupChallengeTimer(slice.timeLimit);
      lucide.createIcons();

      // Smooth scroll to challenge card
      document.getElementById('wheel-result-card').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Countdown Timer Functions (for Wheel Drawn Challenges)
    function setupChallengeTimer(seconds) {
      clearInterval(timerInterval);
      const timerWrap = document.getElementById('drawn-timer-wrap');
      const timerDisplay = document.getElementById('timer-display');
      const startBtn = document.getElementById('timer-start-btn');
      const pauseBtn = document.getElementById('timer-pause-btn');

      if (!seconds || seconds <= 0) {
        timerWrap.style.display = 'none';
        return;
      }

      timerWrap.style.display = 'block';
      timerSecondsLeft = seconds;
      updateTimerDisplay(timerSecondsLeft);
      timerDisplay.classList.remove('time-up');
      startBtn.style.display = 'inline-flex';
      pauseBtn.style.display = 'none';
    }

    function updateTimerDisplay(secs) {
      const m = Math.floor(secs / 60);
      const s = secs % 60;
      const el = document.getElementById('timer-display');
      if (el) {
        el.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
      }
    }

    const timerStartBtn = document.getElementById('timer-start-btn');
    if (timerStartBtn) {
      timerStartBtn.addEventListener('click', () => {
        const startBtn = document.getElementById('timer-start-btn');
        const pauseBtn = document.getElementById('timer-pause-btn');
        startBtn.style.display = 'none';
        pauseBtn.style.display = 'inline-flex';

        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
          timerSecondsLeft--;
          updateTimerDisplay(timerSecondsLeft);
          if (timerSecondsLeft <= 0) {
            clearInterval(timerInterval);
            pauseBtn.style.display = 'none';
            startBtn.style.display = 'inline-flex';
            const el = document.getElementById('timer-display');
            if (el) el.classList.add('time-up');
            if (wheelInstance) wheelInstance.playWinSound();
            showToast("TIME'S UP! Evaluate if participant completed the challenge.", 'warning');
          }
        }, 1000);
      });
    }

    const timerPauseBtn = document.getElementById('timer-pause-btn');
    if (timerPauseBtn) {
      timerPauseBtn.addEventListener('click', () => {
        clearInterval(timerInterval);
        document.getElementById('timer-pause-btn').style.display = 'none';
        document.getElementById('timer-start-btn').style.display = 'inline-flex';
      });
    }

    const timerResetBtn = document.getElementById('timer-reset-btn');
    if (timerResetBtn) {
      timerResetBtn.addEventListener('click', () => {
        clearInterval(timerInterval);
        if (activeDrawnChallenge && activeDrawnChallenge.timeLimit) {
          timerSecondsLeft = activeDrawnChallenge.timeLimit;
          updateTimerDisplay(timerSecondsLeft);
          const el = document.getElementById('timer-display');
          if (el) el.classList.remove('time-up');
        }
        document.getElementById('timer-pause-btn').style.display = 'none';
        document.getElementById('timer-start-btn').style.display = 'inline-flex';
      });
    }

    // In-flight guard to prevent double-awarding / multiple rapid clicks
    let isSubmittingVerdict = false;

    // Submit Pass / Fail Verdict (Unified for both Direct and Wheel challenges)
    async function submitChallengeVerdict(outcome) {
      if (isSubmittingVerdict) {
        console.warn('Point submission verdict already in flight. Blocking duplicate click.');
        return;
      }

      if (!activeParticipantData || !activeDrawnChallenge) {
        highlandahAlert({ title: 'NO CHALLENGE ACTIVE', message: 'Please select a number (1–7) first to pick a challenge.', type: 'warning' });
        return;
      }

      const directCard = document.getElementById('direct-challenge-card');
      const isDirect = directCard && directCard.style.display !== 'none';
      const notesInput = isDirect ? document.getElementById('direct-verdict-notes') : document.getElementById('verdict-notes-input');
      const notes = notesInput ? notesInput.value.trim() : '';

      // Immediately lock and disable all verdict buttons to eliminate double-clicks
      isSubmittingVerdict = true;
      const allVerdictBtns = document.querySelectorAll('.btn-verdict-pass, .btn-verdict-fail');
      allVerdictBtns.forEach(b => {
        b.disabled = true;
        b.style.pointerEvents = 'none';
        b.style.opacity = '0.65';
        if (!b.dataset.origHtml) b.dataset.origHtml = b.innerHTML;
      });

      const activeBtn = outcome === 'passed' 
        ? (isDirect ? document.getElementById('btn-direct-pass') : document.getElementById('btn-verdict-pass'))
        : (isDirect ? document.getElementById('btn-direct-fail') : document.getElementById('btn-verdict-fail'));

      if (activeBtn) {
        activeBtn.innerHTML = '<i data-lucide="loader-2" class="spin-icon"></i> Awarding Points...';
        if (window.lucide) lucide.createIcons();
      }

      const fd = new FormData();
      fd.append('participant_id', activeParticipantData.participant.id);
      fd.append('challenge_id', activeDrawnChallenge.id);
      fd.append('outcome', outcome);
      fd.append('notes', notes);

      try {
        const res = await fetch('api/allocate_points.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
          if (outcome === 'passed') {
            showToast(data.message, 'success');
            if (data.participant.just_qualified) {
              await highlandahAlert({
                title: 'GRAND FINALE QUALIFIED!',
                message: `CONGRATULATIONS!\n\nParticipant ${data.participant.full_name} has reached ${data.participant.total_points} points and officially QUALIFIED for the William Lawson's TNH Grand Finale!`,
                type: 'finale',
                btnText: 'SALUTE HIGHLANDAH'
              });
            }
          } else {
            showToast("Challenge recorded as Incomplete (0 points).", 'warning');
            await highlandahAlert({
              title: 'CHALLENGE INCOMPLETE',
              message: `Challenge '${activeDrawnChallenge.label}' marked as Incomplete (0 points awarded).\n\nParticipant can pick another number or spin again!`,
              type: 'info',
              btnText: 'CONTINUE ACTIVATION'
            });
          }

          // Refresh participant details & history table
          const relookup = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(data.participant.clan_id)}`);
          const refreshed = await relookup.json();
          displayFoundParticipant(refreshed);

          // Reset views and return to number picker for the next challenge
          resetNumberPicker();
        } else {
          highlandahAlert({ title: 'ALLOCATION NOTICE', message: data.message || 'Failed to submit challenge verdict.', type: 'error' });
        }
      } catch (err) {
        highlandahAlert({ title: 'NETWORK ERROR', message: 'Network error while submitting challenge verdict.', type: 'error' });
      } finally {
        allVerdictBtns.forEach(b => {
          b.disabled = false;
          b.style.pointerEvents = '';
          b.style.opacity = '';
          if (b.dataset.origHtml) {
            b.innerHTML = b.dataset.origHtml;
          }
        });
        isSubmittingVerdict = false;
        if (window.lucide) lucide.createIcons();
      }
    }

    // Verdict event listeners
    const btnVerdictPass = document.getElementById('btn-verdict-pass');
    if (btnVerdictPass) {
      btnVerdictPass.addEventListener('click', () => submitChallengeVerdict('passed'));
    }

    const btnVerdictFail = document.getElementById('btn-verdict-fail');
    if (btnVerdictFail) {
      btnVerdictFail.addEventListener('click', () => submitChallengeVerdict('failed'));
    }

    const btnDirectPass = document.getElementById('btn-direct-pass');
    if (btnDirectPass) {
      btnDirectPass.addEventListener('click', () => submitChallengeVerdict('passed'));
    }

    const btnDirectFail = document.getElementById('btn-direct-fail');
    if (btnDirectFail) {
      btnDirectFail.addEventListener('click', () => submitChallengeVerdict('failed'));
    }



    // On-site Registration Form
    const onsiteForm = document.getElementById('promoter-onsite-register-form');
    if (onsiteForm) {
      onsiteForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = onsiteForm.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = 'Enrolling Participant...';

        const fd = new FormData(onsiteForm);

        try {
          const res = await fetch('api/register_participant.php', { method: 'POST', body: fd });
          const data = await res.json();

          if (data.success && data.participant) {
            showToast(`Registered successfully! Clan ID: ${data.participant.clan_id}`);
            await highlandahAlert({
              title: 'HIGHLANDAH PASS ISSUED',
              message: `Participant ${data.participant.full_name} has been enrolled!\n\nClan ID: ${data.participant.clan_id}\n\nProceed to award their first challenge points.`,
              type: 'success',
              btnText: 'GO TO POINTS ALLOCATION'
            });
            onsiteForm.reset();

            // Switch to lookup tab and load participant card immediately
            const lookupTabBtn = document.querySelectorAll('.filter-tab')[0];
            switchTab('tab-lookup', lookupTabBtn);

            const searchInput = document.getElementById('promoter-search-input');
            if (searchInput) searchInput.value = data.participant.clan_id;

            // Immediately display participant card from registration response
            const initialPayload = {
              success: true,
              participant: {
                ...data.participant,
                registered_outlet_name: data.participant.outlet_name || data.participant.registered_outlet_name || 'Activation Outlet',
                registered_by_name: data.participant.registered_by_name || 'Ambassador'
              },
              stats: {
                total_points: parseInt(data.participant.total_points || 0),
                rank: '-',
                is_qualified: parseInt(data.participant.is_qualified || 0),
                progress_percent: Math.min(100, parseInt(data.participant.total_points || 0)),
                points_needed: Math.max(0, 100 - parseInt(data.participant.total_points || 0)),
                outlets_visited_count: 1,
                outlets_visited: [data.participant.outlet_name || 'Activation Outlet'],
                total_challenges_played: 0
              },
              history: []
            };
            displayFoundParticipant(initialPayload);

            // Background refresh to pull complete stats asynchronously without blocking
            try {
              const bgRes = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(data.participant.clan_id)}`);
              if (bgRes.ok) {
                const refreshed = await bgRes.json();
                if (refreshed.success) {
                  displayFoundParticipant(refreshed);
                }
              }
            } catch (bgErr) {
              // Non-blocking background sync failure
              console.warn("Background lookup sync notice:", bgErr);
            }
          } else {
            highlandahAlert({ title: 'ENROLLMENT FAILED', message: data.message || 'Registration failed.', type: 'error' });
          }
        } catch (err) {
          console.error("Enrollment error:", err);
          highlandahAlert({ title: 'ERROR', message: 'Error registering participant. Please check the network and form details.', type: 'error' });
        } finally {
          btn.disabled = false;
          btn.innerHTML = '<i data-lucide="sparkles"></i> Enroll & Issue Clan Pass';
          lucide.createIcons();
        }
      });
    }
  </script>
</body>
</html>
