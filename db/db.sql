-- Device Diagnostic Dashboard database schema
-- Compatible with MySQL 8+

CREATE DATABASE IF NOT EXISTS `device-test`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `device-test`;

CREATE TABLE IF NOT EXISTS `key_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(64) NOT NULL,
  `key_code` VARCHAR(64) NOT NULL,
  `key_value` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_key_code` (`key_code`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_session_created` (`session_id`, `created_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
