-- ============================================================
--  Portfolio Schema
--  Run this first: mysql -u USER -p DBNAME < database/schema.sql
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- Users -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(255)    NOT NULL UNIQUE,
    `password`   VARCHAR(255)    NOT NULL,
    `role`       VARCHAR(50)     NOT NULL DEFAULT 'admin',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` DATETIME                 DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`                 INT UNSIGNED NOT NULL,
    `site_name`               VARCHAR(255)          DEFAULT NULL,
    `site_tagline`            VARCHAR(500)          DEFAULT NULL,
    `canonical_base_url`      VARCHAR(500)          DEFAULT NULL,
    `default_meta_title`      VARCHAR(255)          DEFAULT NULL,
    `default_meta_description`TEXT                  DEFAULT NULL,
    `default_keywords`        TEXT                  DEFAULT NULL,
    `email_public`            VARCHAR(255)          DEFAULT NULL,
    `linkedin_url`            VARCHAR(500)          DEFAULT NULL,
    `github_url`              VARCHAR(500)          DEFAULT NULL,
    `created_at`              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `settings_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories --------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`          INT UNSIGNED NOT NULL,
    `name`             VARCHAR(255) NOT NULL,
    `slug`             VARCHAR(255) NOT NULL,
    `description`      TEXT                  DEFAULT NULL,
    `image_path`       VARCHAR(500)          DEFAULT NULL,
    `meta_title`       VARCHAR(255)          DEFAULT NULL,
    `meta_description` TEXT                  DEFAULT NULL,
    `keywords`         TEXT                  DEFAULT NULL,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME              DEFAULT NULL,
    `deleted_at`       DATETIME              DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_user_slug` (`user_id`, `slug`),
    KEY `categories_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tags --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tags` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `name`       VARCHAR(255) NOT NULL,
    `slug`       VARCHAR(255) NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME              DEFAULT NULL,
    `deleted_at` DATETIME              DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `tags_user_slug` (`user_id`, `slug`),
    KEY `tags_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`          INT UNSIGNED NOT NULL,
    `category_id`      INT UNSIGNED          DEFAULT NULL,
    `title`            VARCHAR(255) NOT NULL,
    `slug`             VARCHAR(255) NOT NULL,
    `summary`          TEXT                  DEFAULT NULL,
    `description`      LONGTEXT              DEFAULT NULL,
    `image_path`       VARCHAR(500)          DEFAULT NULL,
    `live_url`         VARCHAR(500)          DEFAULT NULL,
    `repo_url`         VARCHAR(500)          DEFAULT NULL,
    `meta_title`       VARCHAR(255)          DEFAULT NULL,
    `meta_description` TEXT                  DEFAULT NULL,
    `keywords`         TEXT                  DEFAULT NULL,
    `is_featured`      TINYINT(1)   NOT NULL DEFAULT 0,
    `published_at`     DATETIME              DEFAULT NULL,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME              DEFAULT NULL,
    `deleted_at`       DATETIME              DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `projects_user_slug` (`user_id`, `slug`),
    KEY `projects_user_id` (`user_id`),
    KEY `projects_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects ↔ Tags pivot --------------------------------------
CREATE TABLE IF NOT EXISTS `projects_tags` (
    `project_id` INT UNSIGNED NOT NULL,
    `tag_id`     INT UNSIGNED NOT NULL,
    PRIMARY KEY (`project_id`, `tag_id`),
    KEY `projects_tags_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
