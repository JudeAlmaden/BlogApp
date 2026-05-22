-- ============================================================
--  WriteSphere – Database Migration Script
--  Run this in MySQL Workbench against your MySQL server.
-- ============================================================

-- 1. Create & select the database
CREATE DATABASE IF NOT EXISTS `blog_app`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `blog_app`;

-- ============================================================
-- 2. Tables (in dependency order)
-- ============================================================

-- users
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    `name`          VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(180)    NOT NULL UNIQUE,
    `password`      VARCHAR(255)    NOT NULL,
    `privilege`     ENUM('user','moderator','admin') NOT NULL DEFAULT 'user',
    `bio`           TEXT            NULL,
    `gender`        ENUM('male','female','other')    NULL,
    `profile_image` VARCHAR(255)    NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- tags
CREATE TABLE IF NOT EXISTS `tags` (
    `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- blog_posts
CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT UNSIGNED NOT NULL,
    `title`        VARCHAR(255) NOT NULL,
    `content`      LONGTEXT     NOT NULL,
    `status`       ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft',
    `likes`        INT UNSIGNED NOT NULL DEFAULT 0,
    `scheduled_at` DATETIME     NULL DEFAULT NULL,
    `published_at` DATETIME     NULL DEFAULT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bp_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- blog_post_media
CREATE TABLE IF NOT EXISTS `blog_post_media` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `blog_post_id` INT UNSIGNED NOT NULL,
    `file_path`    VARCHAR(255) NOT NULL,
    `file_type`    ENUM('Image','Video','Document') NOT NULL DEFAULT 'Image',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bpm_post`
        FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- blog_post_category  (pivot)
CREATE TABLE IF NOT EXISTS `blog_post_category` (
    `blog_post_id` INT UNSIGNED NOT NULL,
    `category_id`  INT UNSIGNED NOT NULL,
    PRIMARY KEY (`blog_post_id`, `category_id`),
    CONSTRAINT `fk_bpc_post`
        FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`)  ON DELETE CASCADE,
    CONSTRAINT `fk_bpc_category`
        FOREIGN KEY (`category_id`)  REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- blog_post_tags  (pivot)
CREATE TABLE IF NOT EXISTS `blog_post_tags` (
    `blog_post_id` INT UNSIGNED NOT NULL,
    `tag_id`       INT UNSIGNED NOT NULL,
    PRIMARY KEY (`blog_post_id`, `tag_id`),
    CONSTRAINT `fk_bpt_post`
        FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bpt_tag`
        FOREIGN KEY (`tag_id`)       REFERENCES `tags`(`id`)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- blog_post_liked_by  (pivot)
CREATE TABLE IF NOT EXISTS `blog_post_liked_by` (
    `blog_post_id` INT UNSIGNED NOT NULL,
    `user_id`      INT UNSIGNED NOT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`blog_post_id`, `user_id`),
    CONSTRAINT `fk_bplb_post`
        FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bplb_user`
        FOREIGN KEY (`user_id`)      REFERENCES `users`(`id`)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- comments
CREATE TABLE IF NOT EXISTS `comments` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `blog_post_id` INT UNSIGNED NOT NULL,
    `user_id`      INT UNSIGNED NOT NULL,
    `content`      TEXT         NOT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_c_post`
        FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_c_user`
        FOREIGN KEY (`user_id`)      REFERENCES `users`(`id`)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- replies
CREATE TABLE IF NOT EXISTS `replies` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `comment_id` INT UNSIGNED NOT NULL,
    `user_id`    INT UNSIGNED NOT NULL,
    `content`    TEXT         NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_r_comment`
        FOREIGN KEY (`comment_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_r_user`
        FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Done. All 10 tables created.
-- ============================================================
