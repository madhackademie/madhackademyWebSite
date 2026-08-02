-- Migration — comptes FlashDev (opt-in → set password)
-- Exécuter une fois sur la base prod existante (phpMyAdmin).
-- Si « Duplicate column name 'password_initialized' » : la colonne existe déjà, passer à la CREATE TABLE.

ALTER TABLE users
    ADD COLUMN password_initialized TINYINT(1) NOT NULL DEFAULT 1
    AFTER is_active;

CREATE TABLE IF NOT EXISTS password_setup_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_password_setup_tokens_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_password_setup_tokens_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
