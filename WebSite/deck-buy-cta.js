/**
 * Bouton acheter deck — état selon /auth/me.php (formation déjà achetée).
 */
(function () {
    var FORMATION_SLUG = 'gamedevready-bases-cpp';

    function apply(host, data) {
        var buyLink = host.querySelector('[data-deck-buy-link]');
        var ownedBlock = host.querySelector('[data-deck-owned]');
        if (!buyLink || !ownedBlock) return;

        var slug = host.getAttribute('data-product') || FORMATION_SLUG;
        var hasAccess = !!data.can_access_formation;
        if (data.formation_product_slug && data.formation_product_slug !== slug) {
            hasAccess = Array.isArray(data.products) && data.products.indexOf(slug) !== -1;
        }

        var buyUrl = data.boutique_acheter_url
            || ('/boutique/acheter.php?product=' + encodeURIComponent(slug));

        if (hasAccess) {
            buyLink.classList.add('hidden');
            buyLink.setAttribute('aria-hidden', 'true');
            ownedBlock.classList.remove('hidden');
            ownedBlock.removeAttribute('aria-hidden');
            var libLink = ownedBlock.querySelector('a');
            if (libLink && data.dashboard_url) {
                libLink.href = data.dashboard_url;
            }
        } else {
            buyLink.classList.remove('hidden');
            buyLink.removeAttribute('aria-hidden');
            buyLink.href = buyUrl;
            ownedBlock.classList.add('hidden');
            ownedBlock.setAttribute('aria-hidden', 'true');
        }
    }

    function init() {
        var hosts = document.querySelectorAll('[data-deck-buy-cta]');
        if (!hosts.length) return;

        fetch('/auth/me.php', { credentials: 'same-origin', cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                hosts.forEach(function (host) { apply(host, data); });
            })
            .catch(function () { /* garder le lien acheter par défaut */ });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
