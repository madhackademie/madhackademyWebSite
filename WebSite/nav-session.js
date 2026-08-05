/**
 * Session nav MadHackAdemy — Connexion / Déconnexion dans la barre
 * Charger APRÈS nav-mobile.js
 */
(function () {
    var isEn = (document.documentElement.lang || '').toLowerCase().indexOf('en') === 0;
    var logoutLabel = isEn ? 'Log out' : 'Déconnexion';
    var loginLabel = isEn ? 'Log in' : 'Connexion';
    var redirect = encodeURIComponent(window.location.pathname + window.location.search);

    function ensureBarSlot() {
        var nav = document.querySelector('nav');
        if (!nav) return null;

        var host = nav.querySelector('[data-nav-session-host]');
        if (!host) {
            host = document.createElement('span');
            host.setAttribute('data-nav-session-host', '');
            host.className = 'nav-session-host';

            var actions = nav.querySelector('.nav-site-actions');
            var toggle = nav.querySelector('#nav-toggle, .nav-toggle');
            if (actions) {
                if (toggle && toggle.parentNode === actions) {
                    actions.insertBefore(host, toggle);
                } else {
                    actions.appendChild(host);
                }
            } else if (toggle && toggle.parentNode) {
                toggle.parentNode.insertBefore(host, toggle);
            } else {
                var row = nav.querySelector('.max-w-6xl') || nav;
                row.appendChild(host);
            }
        }

        var slot = host.querySelector('[data-nav-session]');
        if (!slot) {
            slot = document.createElement('span');
            slot.setAttribute('data-nav-session', '');
            slot.setAttribute('data-nav-login', '');
            slot.className = 'nav-session';
            host.appendChild(slot);
        } else {
            slot.setAttribute('data-nav-login', '');
        }
        return slot;
    }

    var slot = ensureBarSlot();
    if (!slot) return;

    function renderLoggedIn(email) {
        slot.textContent = '';
        var mail = document.createElement('span');
        mail.className = 'nav-session-email';
        mail.textContent = email || '';
        var link = document.createElement('a');
        link.href = '/auth/logout.php?redirect=' + redirect;
        link.className = 'nav-session-logout';
        link.textContent = logoutLabel;
        slot.appendChild(mail);
        slot.appendChild(link);
        slot.classList.add('is-visible');
    }

    function renderLoggedOut() {
        slot.textContent = '';
        var link = document.createElement('a');
        link.href = '/auth/login.php?redirect=' + redirect;
        link.className = 'nav-session-login';
        link.textContent = loginLabel;
        slot.appendChild(link);
        slot.classList.add('is-visible');
    }

    fetch('/auth/me.php', { credentials: 'same-origin', cache: 'no-store' })
        .then(function (r) {
            if (!r.ok) throw new Error('AUTH ' + r.status);
            return r.json();
        })
        .then(function (user) {
            if (user && user.logged_in) renderLoggedIn(user.email);
            else renderLoggedOut();
        })
        .catch(function () {
            renderLoggedOut();
        });
})();
