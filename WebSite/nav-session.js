/**
 * Version cache nav : v=7 (MiniPoulpe + menu profil)
 * Charger APRÈS nav-mobile.js
 */
(function () {
    var isEn = (document.documentElement.lang || '').toLowerCase().indexOf('en') === 0;
    var logoutLabel = isEn ? 'Log out' : 'Déconnexion';
    var loginLabel = isEn ? 'Log in' : 'Connexion';
    var libraryLabel = isEn ? 'My library' : 'Ma bibliothèque';
    var minipoulpeSrc = '/Image/MiniPoulpeDicord.png';
    var minipoulpeFallback = '/Image/MiniPoupleMiniMarioTransparence.png';
    var redirect = encodeURIComponent(window.location.pathname + window.location.search);
    var openMenu = null;

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

    function closeProfileMenu() {
        if (!openMenu) return;
        openMenu.menu.classList.remove('is-open');
        openMenu.menu.setAttribute('aria-hidden', 'true');
        openMenu.trigger.setAttribute('aria-expanded', 'false');
        openMenu = null;
    }

    function bindProfileMenu(wrap, trigger, menu) {
        var closeHoverTimer = null;

        function openMenuNow() {
            if (openMenu && openMenu.menu === menu) return;
            closeProfileMenu();
            menu.classList.add('is-open');
            menu.setAttribute('aria-hidden', 'false');
            trigger.setAttribute('aria-expanded', 'true');
            openMenu = { wrap: wrap, trigger: trigger, menu: menu };
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (openMenu && openMenu.menu === menu) {
                closeProfileMenu();
                return;
            }
            openMenuNow();
        });

        wrap.addEventListener('mouseenter', function () {
            if (window.matchMedia('(hover: hover) and (min-width: 768px)').matches) {
                clearTimeout(closeHoverTimer);
                openMenuNow();
            }
        });

        wrap.addEventListener('mouseleave', function () {
            if (!window.matchMedia('(hover: hover) and (min-width: 768px)').matches) return;
            closeHoverTimer = setTimeout(function () {
                if (openMenu && openMenu.menu === menu) closeProfileMenu();
            }, 280);
        });

        menu.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                closeProfileMenu();
            });
        });
    }

    if (!window.__mhaNavProfileBound) {
        window.__mhaNavProfileBound = true;
        document.addEventListener('click', function () {
            closeProfileMenu();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeProfileMenu();
        });
    }

    var slot = ensureBarSlot();
    if (!slot) return;

    function renderLoggedIn(email) {
        slot.textContent = '';
        var wrap = document.createElement('div');
        wrap.className = 'nav-profile-wrap';

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'nav-profile-trigger';
        trigger.setAttribute('aria-haspopup', 'true');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.setAttribute('aria-controls', 'nav-profile-menu');
        trigger.setAttribute(
            'aria-label',
            (isEn ? 'Account menu, ' : 'Menu compte, ') + (email || '')
        );
        trigger.title = email || '';

        var img = document.createElement('img');
        img.className = 'nav-profile-minipoulpe';
        img.src = minipoulpeSrc;
        img.alt = isEn ? 'My account' : 'Mon compte';
        img.width = 40;
        img.height = 40;
        img.decoding = 'async';
        img.addEventListener('error', function onErr() {
            img.removeEventListener('error', onErr);
            img.src = minipoulpeFallback;
        });
        trigger.appendChild(img);

        var menu = document.createElement('div');
        menu.id = 'nav-profile-menu';
        menu.className = 'nav-profile-menu';
        menu.setAttribute('role', 'menu');
        menu.setAttribute('aria-hidden', 'true');

        var menuHead = document.createElement('p');
        menuHead.className = 'nav-profile-menu-head';
        menuHead.textContent = email || '';

        var lib = document.createElement('a');
        lib.href = '/dashboard/';
        lib.className = 'nav-profile-menu-item nav-profile-menu-library';
        lib.setAttribute('role', 'menuitem');
        lib.textContent = libraryLabel;

        var logout = document.createElement('a');
        logout.href = '/auth/logout.php?redirect=' + redirect;
        logout.className = 'nav-profile-menu-item nav-profile-menu-logout';
        logout.setAttribute('role', 'menuitem');
        logout.textContent = logoutLabel;

        menu.appendChild(menuHead);
        menu.appendChild(lib);
        menu.appendChild(logout);
        menu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        wrap.appendChild(trigger);
        wrap.appendChild(menu);
        slot.appendChild(wrap);
        slot.classList.add('is-visible');

        bindProfileMenu(wrap, trigger, menu);
    }

    function renderLoggedOut() {
        closeProfileMenu();
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
