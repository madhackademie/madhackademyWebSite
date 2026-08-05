/**
 * Lightbox panneau Sciensocrate — clic sur .wip-panel-open
 * Inclure : <link rel="stylesheet" href="/wip-panel.css">
 *           <script src="/wip-panel.js" defer></script>
 */
(function () {
    var isEn = (document.documentElement.lang || '').toLowerCase().indexOf('en') === 0;
    var closeLabel = isEn ? 'Close panel' : 'Fermer le panneau';
    var hint = isEn ? 'Tap outside or Esc to close' : 'Clic dehors ou Échap pour fermer';
    var imgSrc = isEn
        ? '/Image/wip-cone-fused-en.png?v=3'
        : '/Image/wip-cone.png?v=2';
    var imgAlt = isEn
        ? 'Sciensocrate warning panel — module under construction'
        : 'Panneau Sciensocrate — module en construction';

    var modal = document.getElementById('wip-panel-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'wip-panel-modal';
        modal.className = 'wip-panel-modal is-hidden';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'wip-panel-title');
        modal.innerHTML =
            '<div class="wip-panel-backdrop" data-wip-close></div>' +
            '<div class="wip-panel-dialog">' +
            '<p id="wip-panel-title" class="sr-only">' + imgAlt + '</p>' +
            '<button type="button" class="wip-panel-close" data-wip-close aria-label="' + closeLabel + '">×</button>' +
            '<img src="' + imgSrc + '" alt="' + imgAlt + '">' +
            '<p class="wip-panel-hint">' + hint + '</p>' +
            '</div>';
        document.body.appendChild(modal);

        if (!document.getElementById('wip-panel-sr-only')) {
            var sr = document.createElement('style');
            sr.id = 'wip-panel-sr-only';
            sr.textContent = '.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}';
            document.head.appendChild(sr);
        }
    }

    var lastFocus = null;

    function openPanel(trigger) {
        lastFocus = trigger || document.activeElement;
        modal.classList.remove('is-hidden');
        document.body.classList.add('nav-open');
        var closeBtn = modal.querySelector('.wip-panel-close');
        if (closeBtn) closeBtn.focus();
    }

    function closePanel() {
        modal.classList.add('is-hidden');
        document.body.classList.remove('nav-open');
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    document.addEventListener('click', function (e) {
        var openBtn = e.target.closest('.wip-panel-open');
        if (openBtn) {
            e.preventDefault();
            openPanel(openBtn);
            return;
        }
        if (e.target.closest('[data-wip-close]')) {
            closePanel();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('is-hidden')) {
            closePanel();
        }
    });
})();
