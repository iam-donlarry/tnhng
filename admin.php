<?php
// admin.php - Company Management & Supervisor Portal
session_start();
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();

$user = $_SESSION['user'] ?? null;
$isAdmin = ($user && in_array($user['role'], ['admin', 'supervisor']));

$outlets = [];
if ($pdo) {
    $outlets = $pdo->query("SELECT * FROM outlets ORDER BY id ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin & Supervisor Console | The Next Highlandah</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    .admin-nav-item {
      padding: 0.6rem 1rem;
      border-radius: 8px;
      color: var(--text-muted);
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      transition: var(--transition);
      border: 1px solid transparent;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .admin-nav-item.active {
      background: rgba(0, 200, 83, 0.15);
      color: var(--wl-green);
      border-color: rgba(0, 200, 83, 0.4);
    }
  </style>
</head>
<body style="padding-bottom: 4rem;">

  <!-- ================= TOP HEADER ================= -->
  <header class="portal-header">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <a href="index.php" style="text-decoration: none;">
          <div class="brand-badge" style="background: var(--wl-green); color: #000; font-size: 1rem; padding: 0.2rem 0.5rem;">WL</div>
        </a>
        <div>
          <div style="font-family: var(--font-display); font-size: 1.25rem; color: #fff; line-height: 1;">
            CAMPAIGN COMMAND CENTER
          </div>
          <div style="font-size: 0.7rem; color: var(--wl-green); font-weight: 700;">
            SUPERVISOR & 1PD DATA MANAGEMENT
          </div>
        </div>
      </div>

      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <?php if ($isAdmin): ?>
          <span style="font-size: 0.85rem; color: var(--text-muted);">
            Admin: <strong style="color: #fff;"><?= htmlspecialchars($user['name']) ?></strong>
          </span>
          <button id="admin-logout-btn" class="btn btn-outline btn-sm">
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

    <?php if (!$isAdmin): ?>
      <!-- ================= ADMIN SIGN IN ================= -->
      <div class="glass-panel" style="max-width: 460px; margin: 3rem auto; padding: 2.5rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
          <span style="display: inline-block; background: rgba(0, 200, 83, 0.15); border: 1px solid var(--wl-green); color: var(--wl-green); font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 6px; margin-bottom: 0.75rem;">
            Supervisor Access Only
          </span>
          <h2 style="font-size: 2.2rem; line-height: 1; margin-bottom: 0.5rem;">ADMIN SIGN IN</h2>
          <p style="color: var(--text-muted); font-size: 0.9rem;">
            Sign in to manage ambassadors, review points logs across outlets, and export 1PD data.
          </p>
        </div>

        <form id="admin-login-form">
          <div class="form-group">
            <label class="form-label">Email or Staff Code</label>
            <input type="text" name="identifier" class="form-control" value="admin@tnhng.com" placeholder="e.g. admin@tnhng.com" required>
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" value="admin123" placeholder="Enter password" required>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
            <i data-lucide="shield-check"></i> Sign In to Dashboard
          </button>

          <div style="background: rgba(0, 200, 83, 0.08); border: 1px solid rgba(0, 200, 83, 0.2); border-radius: 8px; padding: 0.75rem; margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
            <strong style="color: var(--wl-green);">Default Admin Credentials:</strong><br>
            Email: <code>admin@tnhng.com</code> / Password: <code>admin123</code>
          </div>
        </form>
      </div>

    <?php else: ?>
      <!-- ================= ADMIN DASHBOARD ================= -->

      <!-- Top KPI Metrics -->
      <div class="kpi-grid" id="kpi-container">
        <div class="kpi-card">
          <span class="kpi-title"><i data-lucide="users" style="width: 15px; height: 15px; display: inline;"></i> Registered 1PD Participants</span>
          <div class="kpi-val" id="kpi-participants">-</div>
          <span style="font-size: 0.75rem; color: var(--wl-green); margin-top: 0.5rem;">Total captured consumers</span>
        </div>

        <div class="kpi-card">
          <span class="kpi-title"><i data-lucide="award" style="width: 15px; height: 15px; display: inline;"></i> Total Points Allocated</span>
          <div class="kpi-val" style="color: var(--wl-green);" id="kpi-points">-</div>
          <span style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.5rem;">Across all nationwide outlets</span>
        </div>

        <div class="kpi-card">
          <span class="kpi-title"><i data-lucide="crown" style="width: 15px; height: 15px; display: inline;"></i> Qualified Finalists (100+ Pts)</span>
          <div class="kpi-val" style="color: var(--wl-green);" id="kpi-qualified">-</div>
          <span style="font-size: 0.75rem; color: var(--wl-green); margin-top: 0.5rem;">Eligible for Grand Finale</span>
        </div>

        <div class="kpi-card">
          <span class="kpi-title"><i data-lucide="badge-check" style="width: 15px; height: 15px; display: inline;"></i> Brand Ambassadors</span>
          <div class="kpi-val" id="kpi-promoters">-</div>
          <span style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.5rem;">Active on-site registrars</span>
        </div>
      </div>

      <!-- Quick Action CSV Export Bar (Slide 2: "CEP / Platform for Logging Points (Sheets)") -->
      <div class="glass-panel" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
          <strong style="color: #fff; font-size: 1.05rem;">Data Reporting & Sheets Export:</strong>
          <span style="font-size: 0.85rem; color: var(--text-muted); display: block;">Export live datasets for Google Sheets / Excel, Retargeting & Email CRM.</span>
        </div>
        <div style="display: flex; gap: 0.75rem;">
          <a href="api/admin_actions.php?action=export_points_csv" class="btn btn-outline btn-sm">
            <i data-lucide="download"></i> Export Points Audit Log (CSV)
          </a>
          <a href="api/admin_actions.php?action=export_participants_csv" class="btn btn-primary btn-sm">
            <i data-lucide="file-spreadsheet"></i> Export 1PD Participants (CSV)
          </a>
        </div>
      </div>

      <!-- Sub-Navigation -->
      <div style="display: flex; gap: 0.75rem; margin-bottom: 1.75rem; border-bottom: 1px solid var(--border-glass); padding-bottom: 1rem; overflow-x: auto;">
        <button class="admin-nav-item active" onclick="showAdminSection('sec-promoters')">
          <i data-lucide="users"></i> Brand Ambassadors & Staff
        </button>
        <button class="admin-nav-item" onclick="showAdminSection('sec-points-log')">
          <i data-lucide="receipt"></i> Points Allocation Audit Log
        </button>
        <button class="admin-nav-item" onclick="showAdminSection('sec-participants')">
          <i data-lucide="contact-2"></i> 1PD Participant Directory
        </button>
        <button class="admin-nav-item" onclick="showAdminSection('sec-outlets')">
          <i data-lucide="map-pin"></i> Outlets & Venues
        </button>
      </div>

      <!-- ================= SECTION 1: PROMOTERS MANAGEMENT ================= -->
      <section id="sec-promoters" class="admin-section">
        <div class="glass-panel" style="padding: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
              <h3 style="font-size: 1.6rem; color: #fff;">BRAND AMBASSADORS & REGISTRARS</h3>
              <p style="color: var(--text-muted); font-size: 0.85rem;">
                Create and track the staff members who register participants and allocate game points at the outlets.
              </p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openCreatePromoterModal()">
              <i data-lucide="user-plus"></i> Create New Ambassador
            </button>
          </div>

          <div style="overflow-x: auto;">
            <table class="leaderboard-table">
              <thead>
                <tr>
                  <th>Staff Code</th>
                  <th>Staff Name</th>
                  <th>Contact Info</th>
                  <th>Assigned Outlet</th>
                  <th>Participants Registered</th>
                  <th>Points Allocated</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="promoters-tbody">
                <tr><td colspan="8" style="text-align: center; padding: 2rem;">Loading ambassadors...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= SECTION 2: POINTS LOG AUDIT TRAIL ================= -->
      <section id="sec-points-log" class="admin-section" style="display: none;">
        <div class="glass-panel" style="padding: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
              <h3 style="font-size: 1.6rem; color: #fff;">POINTS ALLOCATION AUDIT LOG</h3>
              <p style="color: var(--text-muted); font-size: 0.85rem;">
                Live immutable record of each point awarded, tracking the <strong>exact outlet</strong> and <strong>promoter</strong> responsible.
              </p>
            </div>
            <a href="api/admin_actions.php?action=export_points_csv" class="btn btn-outline btn-sm">
              <i data-lucide="download"></i> Download CSV
            </a>
          </div>

          <div style="overflow-x: auto;">
            <table class="leaderboard-table">
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>Participant</th>
                  <th>Clan ID</th>
                  <th>Challenge</th>
                  <th>Points</th>
                  <th>Outlet Where Allocated</th>
                  <th>Allocated By (Promoter)</th>
                  <th>Notes</th>
                </tr>
              </thead>
              <tbody id="audit-log-tbody">
                <tr><td colspan="8" style="text-align: center; padding: 2rem;">Loading audit records...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= SECTION 3: 1PD PARTICIPANTS ================= -->
      <section id="sec-participants" class="admin-section" style="display: none;">
        <div class="glass-panel" style="padding: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
              <h3 style="font-size: 1.6rem; color: #fff;">1PD PARTICIPANT DIRECTORY</h3>
              <p style="color: var(--text-muted); font-size: 0.85rem;">
                First-party data capture for SMS/Email retargeting and campaign analytics.
              </p>
            </div>
            <a href="api/admin_actions.php?action=export_participants_csv" class="btn btn-primary btn-sm">
              <i data-lucide="download"></i> Export 1PD Data
            </a>
          </div>

          <div style="overflow-x: auto;">
            <table class="leaderboard-table">
              <thead>
                <tr>
                  <th>Clan ID</th>
                  <th>Participant Name</th>
                  <th>Phone (WhatsApp)</th>
                  <th>Email</th>
                  <th>Registered At Outlet</th>
                  <th>Registered By (Promoter)</th>
                  <th>Total Points</th>
                  <th>Grand Finale</th>
                </tr>
              </thead>
              <tbody id="participants-tbody">
                <tr><td colspan="8" style="text-align: center; padding: 2rem;">Loading participant records...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= SECTION 4: OUTLETS & VENUES ================= -->
      <section id="sec-outlets" class="admin-section" style="display: none;">
        <div class="admin-grid-2col">
          
          <!-- Outlets List -->
          <div class="glass-panel" style="padding: 2rem;">
            <h3 style="font-size: 1.5rem; color: #fff; margin-bottom: 1rem;">ACTIVE CAMPAIGN OUTLETS</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;" id="outlets-list-wrap">
              <?php foreach ($outlets as $o): ?>
                <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 1.25rem;">
                  <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <strong style="color: #fff; font-size: 1.1rem;"><?= htmlspecialchars($o['name']) ?></strong>
                    <span style="font-size: 0.75rem; color: var(--wl-green); background: rgba(0,200,83,0.1); padding: 0.2rem 0.5rem; border-radius: 4px;">ACTIVE</span>
                  </div>
                  <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                    <?= htmlspecialchars($o['address']) ?>, <?= htmlspecialchars($o['city']) ?>
                  </div>
                  <div style="font-size: 0.75rem; color: var(--wl-green); margin-top: 0.5rem;">
                    <i data-lucide="calendar" style="width: 12px; height: 12px; display: inline;"></i> <?= htmlspecialchars($o['event_date']) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Add Outlet Form -->
          <div class="glass-panel" style="padding: 2rem;">
            <h3 style="font-size: 1.5rem; color: #fff; margin-bottom: 0.5rem;">ADD ACTIVATION OUTLET</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">
              Add new venue to the campaign calendar. Ambassadors can immediately be assigned to this location.
            </p>

            <form id="add-outlet-form">
              <div class="form-group">
                <label class="form-label">Outlet / Venue Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. The Highland Den - Lekki" required>
              </div>

              <div class="form-group">
                <label class="form-label">City *</label>
                <input type="text" name="city" class="form-control" placeholder="e.g. Lagos" required>
              </div>

              <div class="form-group">
                <label class="form-label">Physical Address</label>
                <input type="text" name="address" class="form-control" placeholder="e.g. Plot 14 Admiralty Way, Lekki">
              </div>

              <div class="form-group">
                <label class="form-label">Activation Date & Time</label>
                <input type="text" name="event_date" class="form-control" placeholder="e.g. Nov 08, 2026 - 6:00 PM">
              </div>

              <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                <i data-lucide="plus-circle"></i> Save Activation Outlet
              </button>
            </form>
          </div>

        </div>
      </section>

      <!-- ================= MODAL: CREATE PROMOTER ================= -->
      <div class="modal-overlay" id="create-promoter-modal">
        <div class="modal-card" style="max-width: 520px;">
          <button class="modal-close">&times;</button>
          <div style="color: var(--wl-green); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">
            Staff Access Creation
          </div>
          <h3 style="font-size: 1.8rem; color: #fff; margin-bottom: 1.25rem;">CREATE BRAND AMBASSADOR</h3>

          <form id="create-promoter-form">
            <div class="form-group">
              <label class="form-label">Ambassador Full Name *</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Kelechi Okafor" required>
            </div>

            <div class="form-grid-2col">
              <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="kelechi@tnhng.com" required>
              </div>

              <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="tel" name="phone" class="form-control" placeholder="080XXXXXXXX" required>
              </div>
            </div>

            <div class="form-grid-2col">
              <div class="form-group">
                <label class="form-label">Assigned Outlet *</label>
                <select name="outlet_id" class="form-control" required>
                  <?php foreach ($outlets as $o): ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Temporary Password *</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" value="promoter123" required>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
              <i data-lucide="shield-check"></i> Generate Staff Account
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

    // Admin Login
    const adminLoginForm = document.getElementById('admin-login-form');
    if (adminLoginForm) {
      adminLoginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(adminLoginForm);
        fd.append('action', 'login');

        try {
          const res = await fetch('api/auth.php', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            window.location.reload();
          } else {
            highlandahAlert({ title: 'COMMAND ACCESS DENIED', message: data.message || 'Invalid administrator credentials.', type: 'error' });
          }
        } catch (err) {
          highlandahAlert({ title: 'SERVER ERROR', message: 'Error signing in to supervisor console.', type: 'error' });
        }
      });
    }

    // Admin Logout
    const adminLogoutBtn = document.getElementById('admin-logout-btn');
    if (adminLogoutBtn) {
      adminLogoutBtn.addEventListener('click', async () => {
        await fetch('api/auth.php?action=logout');
        window.location.reload();
      });
    }

    // Switch admin sections
    function showAdminSection(secId) {
      document.querySelectorAll('.admin-section').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.admin-nav-item').forEach(el => el.classList.remove('active'));
      document.getElementById(secId).style.display = 'block';
      event.target.closest('.admin-nav-item').classList.add('active');
    }

    // Load Admin Data
    <?php if ($isAdmin): ?>
      loadDashboardMetrics();
      loadPromotersList();
      loadAuditLog();
      loadParticipantsList();

      async function loadDashboardMetrics() {
        const res = await fetch('api/admin_actions.php?action=dashboard_metrics');
        const data = await res.json();
        if (data.success) {
          document.getElementById('kpi-participants').textContent = data.metrics.total_participants;
          document.getElementById('kpi-points').textContent = data.metrics.total_points.toLocaleString();
          document.getElementById('kpi-qualified').textContent = data.metrics.qualified_count;
          document.getElementById('kpi-promoters').textContent = data.metrics.total_promoters;
        }
      }

      async function loadPromotersList() {
        const res = await fetch('api/admin_actions.php?action=list_promoters');
        const data = await res.json();
        const tbody = document.getElementById('promoters-tbody');
        if (!tbody) return;

        if (data.success && data.promoters.length > 0) {
          tbody.innerHTML = data.promoters.map(p => `
            <tr>
              <td><strong style="color: var(--wl-green); font-family: var(--font-mono);">${p.staff_code}</strong></td>
              <td><strong style="color: #fff;">${p.name}</strong> <div style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase;">${p.role}</div></td>
              <td><div style="font-size: 0.85rem; color: var(--text-muted);">${p.phone}</div><div style="font-size: 0.75rem; color: var(--text-dim);">${p.email}</div></td>
              <td><span style="color: var(--wl-green); font-weight: 600;">${p.outlet_name || 'All Outlets'}</span></td>
              <td><strong style="font-size: 1.1rem; color: #fff;">${p.participants_registered}</strong> <span style="font-size: 0.75rem; color: var(--text-dim);">registered</span></td>
              <td><strong style="font-size: 1.1rem; color: var(--wl-green);">+${p.total_points_allocated}</strong> <span style="font-size: 0.75rem; color: var(--text-dim);">pts</span></td>
              <td><span style="font-size: 0.75rem; color: ${p.status === 'active' ? 'var(--wl-green)' : 'var(--text-dim)'};">${p.status.toUpperCase()}</span></td>
              <td>
                <button class="btn btn-outline btn-sm" onclick="togglePromoter('${p.id}', '${p.status === 'active' ? 'inactive' : 'active'}')">
                  ${p.status === 'active' ? 'Deactivate' : 'Activate'}
                </button>
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 2rem;">No promoters created yet.</td></tr>`;
        }
      }

      async function togglePromoter(promoterId, targetStatus) {
        const fd = new FormData();
        fd.append('action', 'toggle_promoter_status');
        fd.append('promoter_id', promoterId);
        fd.append('status', targetStatus);

        const res = await fetch('api/admin_actions.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          showToast(data.message);
          loadPromotersList();
        }
      }

      async function loadAuditLog() {
        const res = await fetch('api/admin_actions.php?action=get_points_log');
        const data = await res.json();
        const tbody = document.getElementById('audit-log-tbody');
        if (!tbody) return;

        if (data.success && data.logs.length > 0) {
          tbody.innerHTML = data.logs.map(l => `
            <tr>
              <td style="font-size: 0.8rem; color: var(--text-dim);">${l.created_at}</td>
              <td><strong style="color: #fff;">${l.participant_name || 'Participant'}</strong></td>
              <td><span style="font-family: var(--font-mono); color: var(--wl-green); font-weight: 700;">${l.clan_id || 'N/A'}</span></td>
              <td><span style="color: #fff;">${l.challenge_name}</span> <span style="font-size: 0.75rem; color: var(--text-dim);">(${l.category})</span></td>
              <td><strong style="color: var(--wl-green); font-size: 1.15rem;">+${l.points}</strong></td>
              <td><span style="color: var(--wl-green); font-weight: 600;">${l.outlet_name}</span></td>
              <td><span style="color: var(--text-muted);">${l.promoter_name || l.staff_code || 'System'}</span></td>
              <td style="font-size: 0.8rem; color: var(--text-dim);">${l.notes || '-'}</td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 2rem;">No points records found.</td></tr>`;
        }
      }

      async function loadParticipantsList() {
        const res = await fetch('api/leaderboard.php?limit=100');
        const data = await res.json();
        const tbody = document.getElementById('participants-tbody');
        if (!tbody) return;

        if (data.success && data.leaderboard.length > 0) {
          tbody.innerHTML = data.leaderboard.map(p => `
            <tr>
              <td><strong style="color: var(--wl-green); font-family: var(--font-mono);">${p.clan_id}</strong></td>
              <td><strong style="color: #fff;">${p.full_name}</strong></td>
              <td><span style="font-size: 0.85rem; color: var(--text-muted);">${p.phone || '080XXXXXXXX'}</span></td>
              <td><span style="font-size: 0.85rem; color: var(--text-dim);">${p.email || 'N/A'}</span></td>
              <td><span style="color: #fff;">${p.registered_outlet || 'Web Activation'}</span></td>
              <td><span style="color: var(--text-muted);">${p.registered_by_name || 'Direct Web'}</span></td>
              <td><strong style="color: var(--wl-green); font-size: 1.15rem;">${p.total_points} PTS</strong></td>
              <td>${p.is_qualified ? '<span class="qualified-tag">👑 QUALIFIED</span>' : '<span style="font-size: 0.8rem; color: var(--text-dim);">' + (100 - parseInt(p.total_points)) + ' pts to go</span>'}</td>
            </tr>
          `).join('');
        }
      }

      function openCreatePromoterModal() {
        document.getElementById('create-promoter-modal').classList.add('active');
      }

      // Create Promoter Form Submit
      const createPromoterForm = document.getElementById('create-promoter-form');
      if (createPromoterForm) {
        createPromoterForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const fd = new FormData(createPromoterForm);
          fd.append('action', 'create_promoter');

          const res = await fetch('api/admin_actions.php', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            showToast(data.message);
            document.getElementById('create-promoter-modal').classList.remove('active');
            createPromoterForm.reset();
            loadPromotersList();
            loadDashboardMetrics();
            highlandahAlert({ title: 'STAFF PROMOTER ENROLLED', message: data.message, type: 'success' });
          } else {
            highlandahAlert({ title: 'PROMOTER CREATION FAILED', message: data.message || 'Failed to create promoter.', type: 'error' });
          }
        });
      }

      // Add Outlet Form Submit
      const addOutletForm = document.getElementById('add-outlet-form');
      if (addOutletForm) {
        addOutletForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const fd = new FormData(addOutletForm);
          fd.append('action', 'create_outlet');

          const res = await fetch('api/admin_actions.php', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            showToast(data.message);
            addOutletForm.reset();
            await highlandahAlert({ title: 'OUTLET SAVED', message: 'New activation outlet added to tour schedule!', type: 'success' });
            window.location.reload();
          } else {
            highlandahAlert({ title: 'OUTLET ERROR', message: data.message || 'Failed to create outlet.', type: 'error' });
          }
        });
      }
    <?php endif; ?>
  </script>
</body>
</html>
