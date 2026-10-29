-- Catalogue officiel des produits (formations, decks, soft)
-- Les achats restent dans user_products (user_id + product_slug → products.slug)
-- Importer quand le dashboard « Mes formations » sera codé

CREATE TABLE IF NOT EXISTS products (
    slug VARCHAR(64) NOT NULL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NOT NULL DEFAULT '',
    kind ENUM('soft', 'formation', 'deck_pack') NOT NULL DEFAULT 'formation',
    phase TINYINT UNSIGNED NULL DEFAULT NULL COMMENT 'GameDevReady 1–3, NULL si N/A',
    hub_url VARCHAR(512) NOT NULL DEFAULT '' COMMENT 'Page vitrine deck / formation',
    deck_download_path VARCHAR(512) NULL DEFAULT NULL COMMENT 'Chemin FTP relatif zip deck, ex. flashdev/decks/…',
    guide_product_slug VARCHAR(64) NULL DEFAULT NULL COMMENT 'Si guides = autre slug user_products, rarement',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exemples (adapter titres / chemins avant prod)
INSERT IGNORE INTO products (slug, title, subtitle, kind, phase, hub_url, deck_download_path, sort_order) VALUES
('flashdev-soft', 'FlashDev', 'Logiciel de révision gratuit', 'soft', NULL, '/telecharger.html', NULL, 0),
('gamedevready-bases-cpp', 'GameDevReady — Phase 1 Bases C++', 'Bootcamp 7 modules', 'formation', 1, '/gamedevready-bases-cpp.html', NULL, 10);
