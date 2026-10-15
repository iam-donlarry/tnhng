// js/main.js - Interactive Client Logic for The Next Highlandah Microsite

document.addEventListener('DOMContentLoaded', () => {
  initMobileNav();
  initAgeGate();
  initRegistrationForm();
  initLeaderboard();
  initPointsLookup();
  initKineticScroll();
  init3DCardTilt();
  initScrollFocalStepTracker();
  initImageParallax();
});

/* ==========================================================================
   0. MOBILE NAVIGATION DRAWER
   ========================================================================== */
function initMobileNav() {
  const toggleBtn = document.getElementById('mobile-nav-toggle');
  const drawer = document.getElementById('mobile-nav-drawer');
  if (!toggleBtn || !drawer) return;

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    drawer.classList.toggle('open');
    const isOpen = drawer.classList.contains('open');
    toggleBtn.innerHTML = isOpen ? '<i data-lucide="x"></i>' : '<i data-lucide="menu"></i>';
    if (window.lucide) lucide.createIcons();
  });

  // Close when clicking any link inside drawer
  drawer.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', () => {
      drawer.classList.remove('open');
      toggleBtn.innerHTML = '<i data-lucide="menu"></i>';
      if (window.lucide) lucide.createIcons();
    });
  });

  // Close when clicking outside drawer
  document.addEventListener('click', (e) => {
    if (drawer.classList.contains('open') && !drawer.contains(e.target) && !toggleBtn.contains(e.target)) {
      drawer.classList.remove('open');
      toggleBtn.innerHTML = '<i data-lucide="menu"></i>';
      if (window.lucide) lucide.createIcons();
    }
  });
}

// Toast notification helper
function showToast(message, type = 'success') {
  let toast = document.getElementById('global-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'global-toast';
    toast.className = 'toast-msg';
    document.body.appendChild(toast);
  }
  toast.innerHTML = `<span class="toast-indicator" style="font-weight: 800; color: ${type === 'success' ? 'var(--wl-green)' : '#ffab00'};">•</span> <span>${message}</span>`;
  toast.classList.add('show');
  setTimeout(() => {
    toast.classList.remove('show');
  }, 4000);
}

/* ==========================================================================
   1. AGE GATE (18+ Verification for Alcohol Campaign)
   ========================================================================== */
function initAgeGate() {
  const ageGateModal = document.getElementById('age-gate-modal');
  if (!ageGateModal) return;

  const isVerified = localStorage.getItem('tnh_age_verified');
  if (!isVerified) {
    ageGateModal.classList.add('active');
  }

  const confirmBtn = document.getElementById('age-gate-confirm');
  const yearInput = document.getElementById('age-gate-year');

  if (yearInput) {
    // Only allow numeric digits up to 4 characters
    yearInput.addEventListener('input', (e) => {
      e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
    });

    // Enter key triggers confirmation
    yearInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        if (confirmBtn) confirmBtn.click();
      }
    });

    // Auto-focus year input when modal is visible
    if (!isVerified) {
      setTimeout(() => yearInput.focus(), 300);
    }
  }

  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      if (!yearInput) return;
      const rawVal = yearInput.value.trim();

      if (!rawVal || rawVal.length !== 4) {
        highlandahAlert({
          title: 'YEAR REQUIRED',
          message: 'Please enter your 4-digit birth year (e.g. 1998) to proceed.',
          type: 'warning'
        });
        yearInput.focus();
        return;
      }

      const birthYear = parseInt(rawVal, 10);
      const currentYear = new Date().getFullYear();

      if (isNaN(birthYear) || birthYear < 1920 || birthYear > currentYear) {
        highlandahAlert({
          title: 'INVALID YEAR',
          message: 'Please enter a valid 4-digit calendar year.',
          type: 'warning'
        });
        yearInput.focus();
        return;
      }

      const age = currentYear - birthYear;
      if (age < 18) {
        highlandahAlert({
          title: 'LEGAL DRINKING AGE REQUIRED',
          message: 'You must be 18 years or older of legal drinking age in Nigeria to access The Next Highlandah microsite.',
          type: 'error',
          btnText: 'TRY AGAIN'
        });
        yearInput.value = '';
        yearInput.focus();
        return;
      }

      localStorage.setItem('tnh_age_verified', 'true');
      ageGateModal.classList.remove('active');
      showToast('Welcome to The Clan. Boldness awaits.');
    });
  }
}

/* ==========================================================================
   2. PARTICIPANT REGISTRATION ("JOIN THE CLAN")
   ========================================================================== */
function initRegistrationForm() {
  const form = document.getElementById('clan-register-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Forging Clan Pass...';

    const formData = new FormData(form);

    try {
      const response = await fetch('api/register_participant.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        showToast(data.message);
        form.reset();

        // Update the hero/preview Clan pass
        updatePassPreview(data.participant);

        // Open pass modal to show digital pass & Clan ID
        openPassModal(data.participant);

        // Reload leaderboard in case they're listed
        loadLeaderboard();
      } else {
        highlandahAlert({
          title: 'REGISTRATION NOTICE',
          message: data.message || 'Registration could not be completed. Please check your inputs.',
          type: 'warning'
        });
      }
    } catch (err) {
      console.error(err);
      highlandahAlert({
        title: 'TRANSMISSION ERROR',
        message: 'A network error occurred while forging your Clan Pass. Please try again.',
        type: 'error'
      });
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
}

function maskMajorityName(fullName) {
  if (!fullName) return 'Cl*** M*****';
  return fullName.split(' ').map(part => {
    if (part.length <= 2) return part[0] + '*';
    const keep = Math.min(2, Math.max(1, Math.floor(part.length / 3)));
    return part.substring(0, keep) + '*'.repeat(Math.max(3, part.length - keep));
  }).join(' ');
}

function updatePassPreview(participant) {
  const preview = document.getElementById('hero-clan-pass');
  if (!preview) return;

  const idEl = preview.querySelector('.pass-id');
  const nameEl = preview.querySelector('.pass-info h4');
  const avatarEl = preview.querySelector('.pass-avatar');
  const pointsEl = preview.querySelector('.points-val');
  const fillEl = preview.querySelector('.progress-fill');

  if (idEl) idEl.textContent = participant.clan_id;
  if (nameEl) nameEl.textContent = maskMajorityName(participant.full_name);
  if (avatarEl) {
    const initials = participant.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    avatarEl.textContent = initials;
  }
  if (pointsEl) pointsEl.textContent = `${participant.total_points || 0} / 100 PTS`;
  if (fillEl) fillEl.style.width = `${Math.min(100, participant.total_points || 0)}%`;
}

function openPassModal(participant) {
  const modal = document.getElementById('clan-pass-modal');
  if (!modal) return;

  const idEl = document.getElementById('modal-pass-id');
  const nameEl = document.getElementById('modal-pass-name');
  const phoneEl = document.getElementById('modal-pass-phone');
  const pointsEl = document.getElementById('modal-pass-points');
  const qrEl = document.getElementById('modal-pass-qr');

  if (idEl) idEl.textContent = participant.clan_id;
  if (nameEl) nameEl.textContent = participant.full_name;
  if (phoneEl) phoneEl.textContent = participant.phone;
  if (pointsEl) pointsEl.textContent = `${participant.total_points || 0} PTS`;

  // Dynamic QR code API representation
  if (qrEl) {
    qrEl.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(participant.clan_id)}&color=00c853&bgcolor=12161b`;
  }

  modal.classList.add('active');
}

/* ==========================================================================
   3. LIVE LEADERBOARD
   ========================================================================== */
let leaderboardData = [];

async function initLeaderboard() {
  const searchInput = document.getElementById('leaderboard-search');
  const outletFilter = document.getElementById('leaderboard-outlet-filter');

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      renderLeaderboard(searchInput.value, outletFilter ? outletFilter.value : '');
    });
  }

  if (outletFilter) {
    outletFilter.addEventListener('change', () => {
      loadLeaderboard(outletFilter.value);
    });
  }

  await loadLeaderboard();
}

async function loadLeaderboard(outletId = '') {
  const tbody = document.getElementById('leaderboard-tbody');
  if (!tbody) return;

  try {
    const url = outletId ? `api/leaderboard.php?outlet_id=${outletId}` : 'api/leaderboard.php';
    const res = await fetch(url);
    const data = await res.json();

    if (data.success) {
      leaderboardData = data.leaderboard;
      renderLeaderboard();

      // Update counters if on page
      const totalPart = document.getElementById('stat-total-participants');
      const totalPts = document.getElementById('stat-total-points');
      const totalQual = document.getElementById('stat-total-qualified');

      if (totalPart) totalPart.textContent = data.stats.total_participants;
      if (totalPts) totalPts.textContent = data.stats.total_points_distributed.toLocaleString();
      if (totalQual) totalQual.textContent = data.stats.total_qualified_finalists;
    }
  } catch (e) {
    console.error('Failed to load leaderboard', e);
  }
}

function renderLeaderboard(filterText = '', outletId = '') {
  const tbody = document.getElementById('leaderboard-tbody');
  if (!tbody) return;

  let filtered = leaderboardData;

  if (filterText) {
    const q = filterText.toLowerCase();
    filtered = filtered.filter(p => 
      p.clan_id.toLowerCase().includes(q) || 
      (p.email && p.email.toLowerCase().includes(q)) ||
      p.full_name.toLowerCase().includes(q)
    );
  }

  if (filtered.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-dim);">No Highlandahs found matching your criteria.</td></tr>`;
    return;
  }

  tbody.innerHTML = filtered.map((p) => {
    const rankClass = p.rank === 1 ? 'rank-1' : (p.rank === 2 ? 'rank-2' : (p.rank === 3 ? 'rank-3' : ''));
    const isQual = parseInt(p.total_points) >= 100;

    return `
      <tr>
        <td>
          <span class="rank-badge ${rankClass}">#${p.rank}</span>
        </td>
        <td>
          <strong style="color: var(--wl-green); font-family: var(--font-mono);">${p.clan_id}</strong>
        </td>
        <td>
          <div style="font-weight: 600; color: #fff;">${p.display_name}</div>
        </td>
        <td>
          <span style="font-family: var(--font-display); font-size: 1.4rem; color: var(--wl-green);">${p.total_points}</span>
          <span style="font-size: 0.8rem; color: var(--text-dim);"> PTS</span>
        </td>
        <td>
          <span style="font-size: 0.85rem; color: var(--text-muted);">${p.outlets_visited_count || 1} Outlets</span>
        </td>
        <td>
          ${isQual ? 
            `<span class="qualified-tag">GRAND FINALE QUALIFIED</span>` : 
            `<span style="font-size: 0.8rem; color: var(--text-dim);">${100 - parseInt(p.total_points)} pts to Grand Finale</span>`}
        </td>
      </tr>
    `;
  }).join('');
}

/* ==========================================================================
   5. CHECK MY CLAN POINTS & MULTI-OUTLET ACTIVITY DRAWER
   ========================================================================== */
window.quickLookup = function(id) {
  const queryInput = document.getElementById('lookup-query');
  const form = document.getElementById('points-lookup-form');
  if (queryInput && form) {
    queryInput.value = id;
    form.dispatchEvent(new Event('submit'));
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

function updateHeroClanCard(data) {
  const p = data.participant;
  const s = data.stats;

  const passId = document.getElementById('hero-pass-id');
  const passName = document.getElementById('hero-pass-name');
  const passRankLabel = document.getElementById('hero-pass-rank-label');
  const passPoints = document.getElementById('hero-pass-points');
  const passProgress = document.getElementById('hero-pass-progress');
  const passStatusBadge = document.getElementById('hero-pass-status-badge');
  const heroCard = document.getElementById('hero-clan-pass');
  const viewHistoryBtn = document.getElementById('hero-pass-view-history');

  if (passId) passId.textContent = p.clan_id;
  if (passName) passName.textContent = p.full_name;
  if (passRankLabel) passRankLabel.textContent = `NATIONAL RANK #${s.rank}`;
  if (passPoints) passPoints.textContent = `${s.total_points} / 100 PTS`;
  if (passProgress) passProgress.style.width = `${s.progress_percent}%`;
  
  if (passStatusBadge) {
    if (s.is_qualified) {
      passStatusBadge.textContent = 'GRAND FINALE QUALIFIED';
      passStatusBadge.className = 'ticket-badge-qualified';
    } else {
      passStatusBadge.textContent = `${s.points_needed} PTS TO GRAND FINALE`;
      passStatusBadge.className = 'ticket-badge-pending';
    }
  }

  if (viewHistoryBtn) {
    viewHistoryBtn.style.display = 'inline-block';
    viewHistoryBtn.onclick = () => openParticipantDetailsModal(data);
  }

  if (heroCard) {
    heroCard.classList.remove('hero-card-pulse');
    void heroCard.offsetWidth; // Trigger reflow
    heroCard.classList.add('hero-card-pulse');
  }
}

function initPointsLookup() {
  const form = document.getElementById('points-lookup-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const queryInput = document.getElementById('lookup-query');
    if (!queryInput || !queryInput.value.trim()) return;

    const query = queryInput.value.trim();
    const btn = form.querySelector('button');
    const originalBtnHtml = btn ? btn.innerHTML : 'Check Standings';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = 'Checking Standings...';
    }

    try {
      const res = await fetch(`api/lookup_participant.php?query=${encodeURIComponent(query)}`);
      const data = await res.json();

      if (data.success) {
        updateHeroClanCard(data);
        openParticipantDetailsModal(data);
      } else {
        highlandahAlert({
          title: 'CLAN RECORD NOT FOUND',
          message: data.message || 'No participant found matching that Clan ID or Email Address. Please verify your details or Join The Clan.',
          type: 'warning'
        });
      }
    } catch (e) {
      console.error(e);
      highlandahAlert({
        title: 'ARCHIVE ERROR',
        message: 'Error fetching points history. Please try again.',
        type: 'error'
      });
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;
        if (window.lucide) lucide.createIcons();
      }
    }
  });
}

function openParticipantDetailsModal(data) {
  const modal = document.getElementById('participant-details-modal');
  if (!modal) return;

  const p = data.participant;
  const s = data.stats;
  const history = data.history;

  document.getElementById('detail-clan-id').textContent = p.clan_id;
  document.getElementById('detail-name').textContent = p.full_name;
  document.getElementById('detail-points').textContent = `${s.total_points} PTS`;
  document.getElementById('detail-rank').textContent = `#${s.rank}`;
  document.getElementById('detail-progress').style.width = `${s.progress_percent}%`;

  const qualBadge = document.getElementById('detail-qual-badge');
  if (s.is_qualified) {
    qualBadge.innerHTML = `<span class="qualified-tag" style="font-size: 0.9rem; padding: 0.4rem 0.9rem;">GRAND FINALE SEAT SECURED</span>`;
  } else {
    qualBadge.innerHTML = `<span style="font-size: 0.85rem; color: var(--wl-green);">Need ${s.points_needed} more points to qualify for Grand Finale</span>`;
  }

  // Populate history table
  const tbody = document.getElementById('detail-history-tbody');
  if (tbody) {
    if (history.length === 0) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: var(--text-dim); padding: 1.5rem;">No games recorded yet. Visit any outlet to start earning points!</td></tr>`;
    } else {
      tbody.innerHTML = history.map(item => `
        <tr>
          <td style="font-size: 0.8rem; color: var(--text-dim);">${item.created_at}</td>
          <td><strong style="color: #fff;">${item.challenge_name}</strong> <span style="font-size: 0.75rem; color: var(--text-dim);">(${item.category})</span></td>
          <td style="color: var(--wl-green); font-family: var(--font-display); font-size: 1.2rem;">+${item.points}</td>
          <td style="color: var(--wl-green); font-weight: 600;">${item.outlet_name}</td>
          <td style="font-size: 0.85rem; color: var(--text-muted);">${item.promoter_name || item.promoter_staff_code || 'Staff'}</td>
        </tr>
      `).join('');
    }
  }

  modal.classList.add('active');
}

// Modal closing generic handler
document.querySelectorAll('.modal-overlay').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal || e.target.classList.contains('modal-close')) {
      modal.classList.remove('active');
    }
  });
});

/* ==========================================================================
   6. KINETIC TYPOGRAPHY & OPPOSING SCROLL PARALLAX
   ========================================================================== */
function initKineticScroll() {
  const strips = document.querySelectorAll('.kinetic-text-strip');
  if (!strips.length) return;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  let ticking = false;

  function onScroll() {
    const vh = window.innerHeight;

    strips.forEach(strip => {
      const rect = strip.getBoundingClientRect();
      if (rect.top < vh + 150 && rect.bottom > -150) {
        const progress = (vh - rect.top) / (vh + rect.height);
        const offset = (progress - 0.5) * 220;

        const trackLeft = strip.querySelector('.kinetic-track-left');
        const trackRight = strip.querySelector('.kinetic-track-right');

        if (trackLeft) {
          trackLeft.style.transform = `translate3d(${-offset}px, 0, 0)`;
        }
        if (trackRight) {
          trackRight.style.transform = `translate3d(${offset}px, 0, 0)`;
        }
      }
    });

    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(onScroll);
      ticking = true;
    }
  }, { passive: true });

  onScroll();
}

/* ==========================================================================
   7. INTERACTIVE 3D PERSPECTIVE TILT
   ========================================================================== */
function init3DCardTilt() {
  const cards = document.querySelectorAll('.tilt-card');
  if (!cards.length) return;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  cards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;

      const rotateX = ((y - centerY) / centerY) * -6.5;
      const rotateY = ((x - centerX) / centerX) * 6.5;

      card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.02, 1.02, 1.02)`;
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
    });
  });
}

/* ==========================================================================
   8. SCROLL FOCAL STEP TRACKER
   ========================================================================== */
function initScrollFocalStepTracker() {
  const steps = document.querySelectorAll('.playbook-step');
  if (!steps.length) return;

  let ticking = false;

  function updateActiveStep() {
    const vh = window.innerHeight;
    const focusLine = vh * 0.55;

    let closestStep = null;
    let minDistance = Infinity;

    steps.forEach(step => {
      const rect = step.getBoundingClientRect();
      const stepCenter = rect.top + rect.height / 2;
      const distance = Math.abs(stepCenter - focusLine);

      if (rect.top < vh && rect.bottom > 0) {
        if (distance < minDistance) {
          minDistance = distance;
          closestStep = step;
        }
      }
    });

    steps.forEach(step => {
      if (step === closestStep) {
        step.classList.add('focal-step');
      } else {
        step.classList.remove('focal-step');
      }
    });

    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(updateActiveStep);
      ticking = true;
    }
  }, { passive: true });

  updateActiveStep();
}

/* ==========================================================================
   9. SCROLL IMAGE PARALLAX DEPTH
   ========================================================================== */
function initImageParallax() {
  const parallaxImgs = document.querySelectorAll('.parallax-img');
  if (!parallaxImgs.length) return;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  let ticking = false;

  function updateImageParallax() {
    const vh = window.innerHeight;

    parallaxImgs.forEach(img => {
      const rect = img.getBoundingClientRect();
      if (rect.top < vh + 100 && rect.bottom > -100) {
        const progress = (vh - rect.top) / (vh + rect.height);
        const shiftY = (progress - 0.5) * 36;
        img.style.transform = `scale(1.06) translate3d(0, ${shiftY.toFixed(1)}px, 0)`;
      }
    });

    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(updateImageParallax);
      ticking = true;
    }
  }, { passive: true });

  updateImageParallax();
}
