/**
 * highlandah-alert.js
 * Custom Branded Alert & Confirmation System for William Lawson's (TNH Season 3)
 * Built 100% from scratch with no external libraries.
 */

(function () {
  // Ensure styles exist
  function ensureAlertStyles() {
    if (document.getElementById('highlandah-alert-styles')) return;
    const style = document.createElement('style');
    style.id = 'highlandah-alert-styles';
    style.textContent = `
      .hl-alert-overlay {
        position: fixed;
        inset: 0;
        background: rgba(5, 7, 9, 0.88);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      }
      .hl-alert-overlay.hl-active {
        opacity: 1;
        pointer-events: auto;
      }
      .hl-alert-box {
        background: #11151a;
        border-radius: 18px;
        padding: 2.25rem 2rem;
        max-width: 480px;
        width: 100%;
        text-align: center;
        position: relative;
        transform: scale(0.92) translateY(20px);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.9);
      }
      .hl-alert-overlay.hl-active .hl-alert-box {
        transform: scale(1) translateY(0);
      }
      
      /* Types */
      .hl-type-success {
        border: 1px solid #00c853;
        box-shadow: 0 20px 50px #000000, 0 0 35px rgba(0, 200, 83, 0.35);
      }
      .hl-type-finale {
        border: 2px solid #00c853;
        box-shadow: 0 20px 55px #000000, 0 0 45px rgba(0, 200, 83, 0.45);
        background: #0d1217;
      }
      .hl-type-warning {
        border: 1px solid #00701a;
        box-shadow: 0 20px 50px #000000, 0 0 30px rgba(0, 200, 83, 0.25);
      }
      .hl-type-error {
        border: 1px solid #00701a;
        box-shadow: 0 20px 50px #000000, 0 0 30px rgba(0, 200, 83, 0.25);
      }

      .hl-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        margin: 0 auto 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
      }
      .hl-type-success .hl-icon-wrap { background: rgba(0, 200, 83, 0.15); color: #00c853; border: 1px solid #00c853; }
      .hl-type-finale .hl-icon-wrap { background: rgba(0, 200, 83, 0.25); color: #00c853; border: 1px solid #00c853; animation: hlPulse 2s infinite; }
      .hl-type-warning .hl-icon-wrap { background: #00701a; color: #ffffff; border: 1px solid #00c853; }
      .hl-type-error .hl-icon-wrap { background: #00701a; color: #ffffff; border: 1px solid #00c853; }

      @keyframes hlPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 rgba(0, 200, 83, 0.4); }
        50% { transform: scale(1.08); box-shadow: 0 0 20px rgba(0, 200, 83, 0.6); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 rgba(0, 200, 83, 0); }
      }

      .hl-brand-tag {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: inline-block;
        color: #00c853;
      }

      .hl-title {
        font-family: 'Syne', 'Outfit', sans-serif;
        font-size: 1.55rem;
        line-height: 1.2;
        color: #fff;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.5px;
      }
      .hl-desc {
        color: #9ca3af;
        font-size: 0.95rem;
        line-height: 1.55;
        margin-bottom: 1.75rem;
        font-family: 'Outfit', sans-serif;
      }
      .hl-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
      }
      .hl-btn {
        padding: 0.8rem 1.6rem;
        border-radius: 8px;
        font-family: 'Syne', sans-serif;
        font-size: 0.88rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        flex: 1;
      }
      .hl-btn-confirm {
        background: #00c853;
        color: #000;
        box-shadow: 0 4px 15px rgba(0, 200, 83, 0.4);
      }
      .hl-btn-confirm:hover {
        background: #00701a;
        color: #fff;
        transform: translateY(-2px);
      }
      .hl-btn-finale {
        background: #00c853;
        color: #000;
        box-shadow: 0 4px 20px rgba(0, 200, 83, 0.5);
      }
      .hl-btn-finale:hover {
        background: #00701a;
        color: #fff;
        transform: translateY(-2px);
      }
      .hl-btn-cancel {
        background: transparent;
        color: #9ca3af;
        border: 1px solid rgba(0, 200, 83, 0.3);
      }
      .hl-btn-cancel:hover {
        color: #00c853;
        border-color: #00c853;
      }
    `;
    document.head.appendChild(style);
  }

  /**
   * Main Alert Dialog Function
   * @param {Object|string} opts - Options or message string
   */
  function highlandahAlert(opts) {
    ensureAlertStyles();

    if (typeof opts === 'string') {
      opts = { message: opts };
    }

    const title = opts.title || 'THE CLAN COMMUNIQUE';
    const message = opts.message || '';
    const type = opts.type || (message.toLowerCase().includes('congratulations') || message.toLowerCase().includes('qualif') ? 'finale' : (message.toLowerCase().includes('fail') || message.toLowerCase().includes('error') ? 'error' : 'success'));
    const btnText = opts.btnText || 'ACKNOWLEDGE';

    let iconChar = '⚡';
    if (type === 'finale') iconChar = '👑';
    else if (type === 'warning') iconChar = '⚠️';
    else if (type === 'error') iconChar = '🛑';
    else if (type === 'success') iconChar = '🛡️';

    return new Promise((resolve) => {
      // Remove any existing active overlay
      const existing = document.querySelector('.hl-alert-overlay');
      if (existing) existing.remove();

      const overlay = document.createElement('div');
      overlay.className = 'hl-alert-overlay';

      const btnClass = (type === 'finale') ? 'hl-btn-finale' : 'hl-btn-confirm';

      overlay.innerHTML = `
        <div class="hl-alert-box hl-type-${type}">
          <div class="hl-icon-wrap">${iconChar}</div>
          <span class="hl-brand-tag">WILLIAM LAWSON'S • HIGHLANDAH NOTICE</span>
          <h3 class="hl-title">${escapeHtml(title)}</h3>
          <div class="hl-desc">${formatMessage(message)}</div>
          <div class="hl-actions">
            <button class="hl-btn ${btnClass}" id="hl-ok-btn">${escapeHtml(btnText)}</button>
          </div>
        </div>
      `;

      document.body.appendChild(overlay);

      // Trigger enter animation
      requestAnimationFrame(() => {
        overlay.classList.add('hl-active');
        const okBtn = overlay.querySelector('#hl-ok-btn');
        if (okBtn) okBtn.focus();
      });

      function closeDialog() {
        overlay.classList.remove('hl-active');
        setTimeout(() => {
          overlay.remove();
          document.removeEventListener('keydown', keyHandler);
          resolve(true);
        }, 260);
      }

      function keyHandler(e) {
        if (e.key === 'Escape' || e.key === 'Enter') {
          closeDialog();
        }
      }

      overlay.querySelector('#hl-ok-btn').addEventListener('click', closeDialog);
      document.addEventListener('keydown', keyHandler);
    });
  }

  /**
   * Confirmation Dialog Function
   * @param {Object} opts - Options
   */
  function highlandahConfirm(opts) {
    ensureAlertStyles();

    const title = opts.title || 'CONFIRM ACTION';
    const message = opts.message || 'Are you sure you want to proceed?';
    const type = opts.type || 'warning';
    const confirmText = opts.confirmText || 'PROCEED';
    const cancelText = opts.cancelText || 'CANCEL';

    return new Promise((resolve) => {
      const existing = document.querySelector('.hl-alert-overlay');
      if (existing) existing.remove();

      const overlay = document.createElement('div');
      overlay.className = 'hl-alert-overlay';

      overlay.innerHTML = `
        <div class="hl-alert-box hl-type-${type}">
          <div class="hl-icon-wrap">⚠️</div>
          <span class="hl-brand-tag">WILLIAM LAWSON'S • DECISION</span>
          <h3 class="hl-title">${escapeHtml(title)}</h3>
          <div class="hl-desc">${formatMessage(message)}</div>
          <div class="hl-actions">
            <button class="hl-btn hl-btn-cancel" id="hl-cancel-btn">${escapeHtml(cancelText)}</button>
            <button class="hl-btn hl-btn-confirm" id="hl-confirm-btn">${escapeHtml(confirmText)}</button>
          </div>
        </div>
      `;

      document.body.appendChild(overlay);

      requestAnimationFrame(() => {
        overlay.classList.add('hl-active');
      });

      function cleanup(val) {
        overlay.classList.remove('hl-active');
        setTimeout(() => {
          overlay.remove();
          resolve(val);
        }, 260);
      }

      overlay.querySelector('#hl-confirm-btn').addEventListener('click', () => cleanup(true));
      overlay.querySelector('#hl-cancel-btn').addEventListener('click', () => cleanup(false));
    });
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function formatMessage(str) {
    if (!str) return '';
    // Preserve linebreaks and highlight bold terms
    return escapeHtml(str)
      .replace(/\n\n/g, '<br><br>')
      .replace(/\n/g, '<br>');
  }

  // Expose globally
  window.highlandahAlert = highlandahAlert;
  window.highlandahConfirm = highlandahConfirm;

  // Intercept standard browser window.alert so no raw native browser alerts ever appear!
  window.alert = function (msg) {
    return highlandahAlert(msg);
  };

})();
