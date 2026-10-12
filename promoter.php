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
  <link rel="stylesheet" href="css/style.css">
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
        <a href="index.php" style="text-decoration: none;">
          <div class="brand-badge" style="font-size: 1rem; padding: 0.2rem 0.5rem;">WL</div>
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
          <div style="text-align: right; display: none; @media(min-width: 600px){ display: block; }">
            <div style="font-size: 0.85rem; font-weight: 700; color: #fff;"><?= htmlspecialchars($user['name']) ?></div>
            <div style="font-size: 0.75rem; color: var(--wl-green); font-family: var(--font-mono);"><?= htmlspecialchars($user['staff_code']) ?></div>
          </div>
          <button id="logout-btn" class="btn btn-outline btn-sm">
            <i data-lucide="log-out"></i> Logout
          </button>
        <?php else: ?>
          <a href="index.php" class="btn btn-outline btn-sm">
            <i data-lucide="arrow-left"></i> Public Site
          </a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="container" style="margin-top: 2rem;">

    <?php if (!$user): ?>
      <!-- ================= LOGIN SCREEN ================= -->
      <div class="glass-panel" style="max-width: 480px; margin: 2.5rem auto; padding: 0; overflow: hidden; border: 1px solid rgba(0,200,83,0.3);">
        <div style="height: 200px; overflow: hidden; position: relative; background: #06080a;">
          <img src="images/image.png" alt="William Lawson's - Highlandahs Only!" style="width: 100%; height: 100%; object-fit: cover; object-position: 50% 25%;">
          <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.45);"></div>
          <div style="position: absolute; top: 12px; left: 12px;">
            <span class="ambassador-badge" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);">On-Site Staff Portal</span>
          </div>
        </div>
        <div style="padding: 2rem;">
          <div style="text-align: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 2rem; line-height: 1; margin-bottom: 0.5rem; color: #fff;">AMBASSADOR SIGN IN</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem;">
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

          <div style="background: rgba(0,200,83,0.08); border: 1px solid rgba(0,200,83,0.2); border-radius: 8px; padding: 0.75rem; margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
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
      <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
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
      <div class="filter-tabs" style="justify-content: flex-start; margin-bottom: 1.75rem;">
        <button class="filter-tab active-tab" onclick="switchTab('tab-lookup')">
          <i data-lucide="search" style="width: 15px; height: 15px; display: inline;"></i> Lookup & Award Points
        </button>
        <button class="filter-tab" onclick="switchTab('tab-register')">
          <i data-lucide="user-plus" style="width: 15px; height: 15px; display: inline;"></i> Fast On-Site Registration
        </button>
      </div>

      <!-- TAB 1: LOOKUP & AWARD POINTS -->
      <div id="tab-lookup" class="tab-content">
        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
          
          <!-- Participant Search Card -->
          <div class="glass-panel" style="padding: 2rem;">
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #fff;">
              <i data-lucide="user-check" style="color: var(--wl-green);"></i> 1. FIND PARTICIPANT
            </h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">
              Enter the participant's Clan ID (e.g. <code>TNH-7701</code>) or registered Email Address. Any participant registered at any outlet can play here!
            </p>

            <form id="promoter-search-form" class="lookup-form-wrap" style="margin: 0; max-width: 100%;">
              <input type="text" id="promoter-search-input" class="form-control" placeholder="Enter Clan ID (TNH-XXXX), Email, or Phone..." required>
              <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                <i data-lucide="search"></i> Lookup
              </button>
            </form>

            <!-- Participant Found Card (Hidden until searched) -->
            <div id="participant-card-wrap" style="display: none; margin-top: 1.5rem; border-top: 1px dashed var(--border-glass); padding-top: 1.5rem;">
              <div style="background: rgba(0, 0, 0, 0.4); border: 1px solid var(--wl-green); border-radius: 12px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                  <div>
                    <span class="ambassador-badge" id="card-clan-id">TNH-0000</span>
                    <h2 style="font-size: 1.8rem; color: #fff; margin-top: 0.25rem;" id="card-name">Participant Name</h2>
                    <div style="font-size: 0.85rem; color: var(--text-muted);" id="card-contact">08012345678 • email@example.com</div>
                    <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.25rem;" id="card-origin">Origin: VI Outlet</div>
                  </div>
                  <div style="text-align: right;">
                    <div style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase;">Current Score</div>
                    <div style="font-family: var(--font-display); font-size: 2.5rem; color: var(--wl-green); line-height: 1;" id="card-points">0 PTS</div>
                    <div id="card-badge" style="margin-top: 0.25rem;"></div>
                  </div>
                </div>

                <div class="progress-track" style="height: 10px; margin-bottom: 0.5rem;">
                  <div class="progress-fill" id="card-progress" style="width: 0%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-dim);">
                  <span>0 PTS</span>
                  <span id="card-needed">100 PTS to Grand Finale</span>
                  <span>100 PTS</span>
                </div>
              </div>

              <!-- POINTS ALLOCATOR FORM -->
              <div style="margin-top: 2rem; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: 14px; padding: 1.75rem;">
                <h3 style="font-size: 1.4rem; color: #fff; margin-bottom: 0.5rem;">
                  <i data-lucide="award" style="color: var(--wl-green);"></i> 2. ALLOCATE POINTS FOR CHALLENGE
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
                      <label class="form-label" style="display: flex; align-items: center; justify-content: space-between;">
                        <span>Scorecard Points *</span>
                        <span style="font-size: 0.7rem; color: var(--wl-green); text-transform: uppercase;">
                          <i data-lucide="lock" style="width: 12px; height: 12px; display: inline;"></i> Fixed (Non-Editable)
                        </span>
                      </label>
                      <div style="position: relative;">
                        <input type="number" id="alloc-points" name="points" class="form-control" readonly required 
                                placeholder="Select challenge above..." 
                                style="background: rgba(0, 0, 0, 0.45); color: var(--wl-green); font-family: var(--font-display); font-size: 1.5rem; letter-spacing: 1px; cursor: not-allowed; border: 1px solid rgba(0,200,83,0.35);">
                        <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-family: var(--font-display); color: var(--text-dim); font-size: 1.1rem;">PTS</span>
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
              <div style="margin-top: 2rem;">
                <h4 style="font-size: 1rem; color: #fff; text-transform: uppercase; margin-bottom: 0.75rem;">
                  <i data-lucide="history"></i> Previous Points Earned by this Participant
                </h4>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--border-glass); border-radius: 8px;">
                  <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead style="background: #14181d;">
                      <tr>
                        <th style="padding: 0.5rem; text-align: left; color: var(--text-dim);">Date</th>
                        <th style="padding: 0.5rem; text-align: left; color: var(--text-dim);">Challenge</th>
                        <th style="padding: 0.5rem; text-align: left; color: var(--text-dim);">Points</th>
                        <th style="padding: 0.5rem; text-align: left; color: var(--text-dim);">Outlet</th>
                        <th style="padding: 0.5rem; text-align: left; color: var(--text-dim);">Awarded By</th>
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
  <script>
    lucide.createIcons();

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
    function switchTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active-tab'));
      document.getElementById(tabId).style.display = 'block';
      event.target.closest('.filter-tab').classList.add('active-tab');
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

    let activeParticipantData = null;

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

      const badgeEl = document.getElementById('card-badge');
      if (s.is_qualified) {
        badgeEl.innerHTML = `<span class="qualified-tag">👑 GRAND FINALE QUALIFIED</span>`;
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
            <td style="padding: 0.4rem 0.5rem; color: var(--text-dim);">${h.created_at.substring(0, 16)}</td>
            <td style="padding: 0.4rem 0.5rem; color: #fff;">${h.challenge_name}</td>
            <td style="padding: 0.4rem 0.5rem; color: var(--wl-green); font-weight: 700;">+${h.points}</td>
            <td style="padding: 0.4rem 0.5rem; color: var(--wl-green);">${h.outlet_name}</td>
            <td style="padding: 0.4rem 0.5rem; color: var(--text-muted);">${h.promoter_name || 'Staff'}</td>
          </tr>
        `).join('');
      }
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
                title: '👑 GRAND FINALE QUALIFIED!',
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
              title: '⚡ HIGHLANDAH PASS ISSUED',
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
