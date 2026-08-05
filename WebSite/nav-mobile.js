/**
 * Menu mobile MadHackAdemy
 * Charger AVANT nav-session.js (cache-bust : ?v=3)
 */
(function () {
    var isEn = (document.documentElement.lang || '').toLowerCase().indexOf('en') === 0;
    var labelOpen = isEn ? 'Open menu' : 'Ouvrir le menu';
    var labelClose = isEn ? 'Close menu' : 'Fermer le menu';

    function bindToggle(toggle, panel) {
        if (!toggle || !panel || toggle.getAttribute('data-nav-wired') === '1') return;
        toggle.setAttribute('data-nav-wired', '1');

        function setOpen(open) {
            panel.classList.toggle('is-open', open);
            panel.classList.toggle('hidden', !open);
            toggle.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? labelClose : labelOpen);
            document.body.classList.toggle('nav-open', open);
        }

        setOpen(false);

        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            setOpen(toggle.getAttribute('aria-expanded') !== 'true');
        });

        panel.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { setOpen(false); });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setOpen(false);
        });
    }

    function buildBars() {
        return '<span class="nav-bar"></span><span class="nav-bar"></span><span class="nav-bar"></span>';
    }

    var firstLinks = document.querySelector('.nav-site-links');
    if (!firstLinks) return;
    var nav = firstLinks.closest('nav');
    if (!nav) return;

    var existingToggle = document.getElementById('nav-toggle') || nav.querySelector('[data-nav-toggle]');
    var existingPanel = document.getElementById('nav-mobile-menu') || nav.querySelector('[data-nav-mobile-panel]');

    if (existingToggle && existingPanel) {
        firstLinks.classList.add('nav-site-desktop');
        var existingMobile = existingPanel.querySelector('.nav-site-links');
        if (existingMobile) existingMobile.classList.add('nav-site-mobile');
        existingToggle.classList.add('nav-toggle');
        if (!existingToggle.querySelector('.nav-bar')) {
            existingToggle.innerHTML = buildBars();
        }
        existingPanel.classList.add('nav-mobile-panel');

        // Toujours un host session dans la barre (comme Formation)
        if (!nav.querySelector('[data-nav-session-host]')) {
            var hostEarly = document.createElement('span');
            hostEarly.setAttribute('data-nav-session-host', '');
            hostEarly.className = 'nav-session-host';
            var actionsEarly = existingToggle.parentElement;
            if (actionsEarly) actionsEarly.insertBefore(hostEarly, existingToggle);
            else existingToggle.parentNode.insertBefore(hostEarly, existingToggle);
        }

        bindToggle(existingToggle, existingPanel);
        return;
    }

    var desktopLinks = firstLinks;
    desktopLinks.classList.add('nav-site-desktop');

    var row = desktopLinks.parentElement;
    if (!row || row === nav) {
        row = document.createElement('div');
        row.className = 'nav-site-row max-w-6xl mx-auto px-4';
        while (nav.firstChild) row.appendChild(nav.firstChild);
        nav.appendChild(row);
    }
    row.classList.add('nav-site-row');

    var actions = nav.querySelector('.nav-site-actions');
    if (!actions) {
        actions = document.createElement('div');
        actions.className = 'nav-site-actions';
        if (desktopLinks.parentNode === row || desktopLinks.parentElement) {
            desktopLinks.parentNode.insertBefore(actions, desktopLinks);
        }
        actions.appendChild(desktopLinks);
    } else if (desktopLinks.parentNode !== actions) {
        actions.insertBefore(desktopLinks, actions.firstChild);
    }

    var sessionHost = nav.querySelector('[data-nav-session-host]');
    if (!sessionHost) {
        sessionHost = document.createElement('span');
        sessionHost.setAttribute('data-nav-session-host', '');
        sessionHost.className = 'nav-session-host';
        actions.appendChild(sessionHost);
    } else if (sessionHost.parentNode !== actions) {
        actions.appendChild(sessionHost);
    }

    var toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.id = 'nav-toggle';
    toggle.className = 'nav-toggle';
    toggle.setAttribute('data-nav-toggle', '');
    toggle.setAttribute('aria-label', labelOpen);
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-controls', 'nav-mobile-menu');
    toggle.innerHTML = buildBars();
    actions.appendChild(toggle);

    var panel = document.createElement('div');
    panel.id = 'nav-mobile-menu';
    panel.className = 'nav-mobile-panel hidden';
    panel.setAttribute('data-nav-mobile-panel', '');

    var mobileLinks = document.createElement('div');
    mobileLinks.className = 'nav-site-links nav-site-mobile';
    if (desktopLinks.hasAttribute('data-nav-login')) {
        mobileLinks.setAttribute('data-nav-login', '');
    }

    Array.prototype.forEach.call(desktopLinks.children, function (child) {
        if (child.getAttribute && child.getAttribute('data-nav-session') !== null) return;
        var clone = child.cloneNode(true);
        if (clone.id === 'auth-nav') clone.removeAttribute('id');
        if (clone.classList && clone.classList.contains('nav-item')) {
            clone.classList.add('nav-mobile-item');
        }
        mobileLinks.appendChild(clone);
    });

    panel.appendChild(mobileLinks);
    nav.appendChild(panel);
    bindToggle(toggle, panel);
})();
