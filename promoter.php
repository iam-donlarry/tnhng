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

              <!-- ================= 2. ON-SITE SPIN THE WHEEL GAME ================= -->
              <div id="promoter-wheel-section" class="wheel-section">
                <div class="wheel-section-header">
                  <div class="wheel-section-title-wrap">
                    <span class="ambassador-badge" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                      <i data-lucide="sparkles" style="width: 13px; height: 13px;"></i> On-Site Wheel Spin Challenge
                    </span>
                    <h3 class="wheel-section-title">
                      <i data-lucide="disc-3" style="color: var(--wl-green);"></i> 2. SPIN THE WHEEL
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

              <!-- ================= 3. MANUAL OVERRIDE / POINTS ALLOCATOR FORM ================= -->
              <div class="manual-override-card">
                <h3 class="manual-override-title">
                  <i data-lucide="award" style="color: var(--wl-green);"></i> 3. MANUAL CHALLENGE SELECTION & OVERRIDE
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.25rem;">
                  Points will be accurately credited to this participant, tagging <strong><?= htmlspecialchars($user['outlet_name']) ?></strong> as the location and you as the referee.
                </p>

                <form id="allocate-points-form">
                  <input type="hidden" id="alloc-participant-id" name="participant_id">

                  <div class="form-group">
                    <label class="form-label">Select Challenge Completed *</label>
                    <select id="alloc-challenge" name="challenge_id" class="form-control" required>
                      <option value="">-- Choose Challenge From Scorecard --</option>
                      <?php foreach ($challenges as $c): ?>
                        <option value="<?= $c['id'] ?>" data-points="<?= $c['points'] ?>" data-cat="<?= $c['category'] ?>">
                          [<?= $c['category'] ?>] <?= htmlspecialchars($c['title']) ?> (+<?= $c['points'] ?> pts)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="form-grid-2col">
                    <div class="form-group">
                      <label class="form-label" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.25rem;">
                        <span>Scorecard Points *</span>
                        <span style="font-size: 0.7rem; color: var(--wl-green); text-transform: uppercase;">
                          <i data-lucide="lock" style="width: 12px; height: 12px; display: inline;"></i> Fixed (Non-Editable)
                        </span>
                      </label>
                      <div style="position: relative;">
                        <input type="number" id="alloc-points" name="points" class="form-control alloc-points-input" readonly required 
                                placeholder="Select challenge above...">
                        <span class="alloc-points-suffix">PTS</span>
                      </div>
                      <small style="font-size: 0.72rem; color: var(--text-dim); margin-top: 0.35rem; display: block;">
                        Points are locked strictly to official scorecard value.
                      </small>
                    </div>

                    <div class="form-group">
                      <label class="form-label">Location (Auto-Tagged Outlet)</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($user['outlet_name']) ?>" readonly style="background: rgba(0,0,0,0.45); color: var(--wl-green); font-weight: 600; cursor: not-allowed; border: 1px solid var(--border-glass);">
                      <small style="font-size: 0.72rem; color: var(--text-dim); margin-top: 0.35rem; display: block;">
                        Points are automatically attributed to this outlet.
                      </small>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label">Referee Notes / Proof (Optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="e.g. Completed 35 squats cleanly within 15 seconds.">
                  </div>

                  <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                    <i data-lucide="check-circle-2"></i> Confirm & Award Points
                  </button>
                </form>
              </div>

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
        const query = document.getElementById('promoter-search-input').value.trim();
        if (!query) return;

        const res = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(query)}`);
        const data = await res.json();

        if (data.success) {
          displayFoundParticipant(data);
        } else {
          highlandahAlert({ title: 'PARTICIPANT NOT FOUND', message: data.message || 'No Clan member found with that Clan ID or Email. Please verify or use the On-Site Registration tab to enroll them.', type: 'warning' });
          document.getElementById('participant-card-wrap').style.display = 'none';
        }
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
      activeDrawnChallenge = null;
      document.getElementById('wheel-empty-state').style.display = 'block';
      document.getElementById('wheel-challenge-state').style.display = 'none';
      document.getElementById('wheel-respin-state').style.display = 'none';
      document.getElementById('wheel-result-card').classList.remove('active-challenge');
      document.getElementById('wheel-status-pill').textContent = 'Ready to Spin';
      document.getElementById('verdict-notes-input').value = '';
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

      // Update name on wheel title
      const wheelPartName = document.getElementById('wheel-participant-name');
      if (wheelPartName) wheelPartName.textContent = p.full_name;

      const badgeEl = document.getElementById('card-badge');
      if (s.is_qualified) {
        badgeEl.innerHTML = `<span class="qualified-tag">GRAND FINALE QUALIFIED</span>`;
      } else {
        badgeEl.innerHTML = ``;
      }

      // Hidden form input
      document.getElementById('alloc-participant-id').value = p.id;

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

      // Initialize wheel and reset result panel
      resetWheelResultState();
      setTimeout(() => {
        initOrResetWheel();
      }, 50);
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

      // Auto-fill fallback form below
      const chSelect = document.getElementById('alloc-challenge');
      if (chSelect) {
        chSelect.value = slice.id;
        document.getElementById('alloc-points').value = slice.points;
      }

      // Configure timer if time-bound (e.g. 15s or 60s)
      setupChallengeTimer(slice.timeLimit);
      lucide.createIcons();

      // Smooth scroll to challenge card
      document.getElementById('wheel-result-card').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Countdown Timer Functions
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

    // Submit Pass / Fail Verdict
    async function submitChallengeVerdict(outcome) {
      if (!activeParticipantData || !activeDrawnChallenge) {
        highlandahAlert({ title: 'NO CHALLENGE ACTIVE', message: 'Please spin the wheel first to draw a challenge.', type: 'warning' });
        return;
      }

      const notes = document.getElementById('verdict-notes-input').value.trim();
      const passBtn = document.getElementById('btn-verdict-pass');
      const failBtn = document.getElementById('btn-verdict-fail');
      passBtn.disabled = true;
      failBtn.disabled = true;

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
              message: `Challenge '${activeDrawnChallenge.label}' marked as Incomplete (0 points awarded).\n\nParticipant can spin again or attempt another challenge at this outlet!`,
              type: 'info',
              btnText: 'CONTINUE ACTIVATION'
            });
          }

          // Refresh participant details & history table
          const relookup = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(data.participant.clan_id)}`);
          const refreshed = await relookup.json();
          displayFoundParticipant(refreshed);

          // Reset drawn card state
          resetWheelResultState();
        } else {
          highlandahAlert({ title: 'ALLOCATION NOTICE', message: data.message || 'Failed to submit challenge verdict.', type: 'error' });
        }
      } catch (err) {
        highlandahAlert({ title: 'NETWORK ERROR', message: 'Network error while submitting challenge verdict.', type: 'error' });
      } finally {
        passBtn.disabled = false;
        failBtn.disabled = false;
      }
    }

    const btnVerdictPass = document.getElementById('btn-verdict-pass');
    if (btnVerdictPass) {
      btnVerdictPass.addEventListener('click', () => submitChallengeVerdict('passed'));
    }

    const btnVerdictFail = document.getElementById('btn-verdict-fail');
    if (btnVerdictFail) {
      btnVerdictFail.addEventListener('click', () => submitChallengeVerdict('failed'));
    }

    // Auto-fill challenge points on select (strictly locked from scorecard)
    const chSelect = document.getElementById('alloc-challenge');
    if (chSelect) {
      chSelect.addEventListener('change', () => {
        const opt = chSelect.selectedOptions[0];
        if (opt && opt.dataset.points !== undefined && opt.value !== '') {
          document.getElementById('alloc-points').value = opt.dataset.points;
        } else {
          document.getElementById('alloc-points').value = '';
        }
      });
    }

    // Allocate Points Submit
    const allocForm = document.getElementById('allocate-points-form');
    if (allocForm) {
      allocForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = allocForm.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = 'Logging Points to Outlet & Participant...';

        const fd = new FormData(allocForm);

        try {
          const res = await fetch('api/allocate_points.php', { method: 'POST', body: fd });
          const data = await res.json();

          if (data.success) {
            showToast(data.message, 'success');
            if (data.participant.just_qualified) {
              await highlandahAlert({
                title: 'GRAND FINALE QUALIFIED!',
                message: `CONGRATULATIONS!\n\nParticipant ${data.participant.full_name} has just reached ${data.participant.total_points} points and officially QUALIFIED for the William Lawson's TNH Grand Finale!`,
                type: 'finale',
                btnText: 'SALUTE HIGHLANDAH'
              });
            }
            // Refresh participant details
            const relookup = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(data.participant.clan_id)}`);
            const refreshed = await relookup.json();
            displayFoundParticipant(refreshed);
            allocForm.reset();
          } else {
            highlandahAlert({ title: 'ALLOCATION NOTICE', message: data.message || 'Failed to allocate points.', type: 'error' });
          }
        } catch (err) {
          highlandahAlert({ title: 'NETWORK ERROR', message: 'Network error while allocating points.', type: 'error' });
        } finally {
          btn.disabled = false;
          btn.innerHTML = '<i data-lucide="check-circle-2"></i> Confirm & Award Points';
          lucide.createIcons();
        }
      });
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

          if (data.success) {
            showToast(`Registered successfully! Clan ID: ${data.participant.clan_id}`);
            await highlandahAlert({
              title: 'HIGHLANDAH PASS ISSUED',
              message: `Participant ${data.participant.full_name} has been enrolled!\n\nClan ID: ${data.participant.clan_id}\n\nProceed to award their first challenge points.`,
              type: 'success',
              btnText: 'GO TO POINTS ALLOCATION'
            });
            onsiteForm.reset();

            // Switch to lookup tab and load them automatically
            document.querySelectorAll('.filter-tab')[0].click();
            document.getElementById('promoter-search-input').value = data.participant.clan_id;
            const relookup = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(data.participant.clan_id)}`);
            const refreshed = await relookup.json();
            displayFoundParticipant(refreshed);
          } else {
            highlandahAlert({ title: 'ENROLLMENT FAILED', message: data.message || 'Registration failed.', type: 'error' });
          }
        } catch (err) {
          highlandahAlert({ title: 'ERROR', message: 'Error registering participant.', type: 'error' });
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
