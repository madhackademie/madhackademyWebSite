-- Appareil de confiance — accès formation (guides) lié au 1er navigateur utilisé
-- Importer après schema.sql sur l'hébergeur (phpMyAdmin ou mysql CLI)

CREATE TABLE IF NOT EXISTS user_trusted_devices (
    user_id INT UNSIGNED NOT NULL PRIMARY KEY,
    device_token_hash CHAR(64) NOT NULL,
    bound_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_seen_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_trusted_devices_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
