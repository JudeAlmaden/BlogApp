<?php
/**
 * WriteSphere Database Migration Tool
 * Run from the command line: php migrate.php
 * Or visit in browser: http://localhost/WriteSphere/migrate.php
 */

// ─── Config ──────────────────────────────────────────────────────────────────
$servername = "localhost:3306";
$username   = "root";
$password   = "root";
$db         = "blog_app";

// ─── CLI vs Browser output helpers ───────────────────────────────────────────
$isCli = php_sapi_name() === 'cli';

function out(string $msg, string $type = 'info'): void {
    global $isCli;
    $colors = ['info' => "\033[0;36m", 'success' => "\033[0;32m", 'error' => "\033[0;31m", 'warn' => "\033[0;33m", 'reset' => "\033[0m"];
    $icons  = ['info' => 'ℹ', 'success' => '✔', 'error' => '✘', 'warn' => '⚠'];
    $htmlClass = ['info' => 'info', 'success' => 'success', 'error' => 'error', 'warn' => 'warn'];

    if ($isCli) {
        echo ($colors[$type] ?? '') . ($icons[$type] ?? '') . "  " . $msg . ($colors['reset']) . PHP_EOL;
    } else {
        echo '<p class="' . ($htmlClass[$type] ?? 'info') . '">' . ($icons[$type] ?? '') . '&nbsp;&nbsp;' . htmlspecialchars($msg) . '</p>' . PHP_EOL;
    }
}

function section(string $title): void {
    global $isCli;
    if ($isCli) {
        echo PHP_EOL . "\033[1;37m── " . $title . " ──\033[0m" . PHP_EOL;
    } else {
        echo '<h2>' . htmlspecialchars($title) . '</h2>' . PHP_EOL;
    }
}

// ─── HTML wrapper (browser only) ─────────────────────────────────────────────
if (!$isCli): ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WriteSphere – Database Migration</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', system-ui, sans-serif; background: #0f172a; color: #e2e8f0; padding: 2rem; }
  h1   { font-size: 1.6rem; margin-bottom: 1.5rem; color: #f8fafc; border-bottom: 1px solid #334155; padding-bottom: .75rem; }
  h2   { font-size: 1rem; font-weight: 600; color: #94a3b8; margin: 1.5rem 0 .5rem; text-transform: uppercase; letter-spacing: .05em; }
  p    { padding: .35rem .6rem; border-radius: 4px; font-size: .9rem; margin-bottom: .25rem; display: flex; align-items: center; gap: .4rem; }
  .info    { background: #1e3a5f; color: #93c5fd; }
  .success { background: #14532d; color: #86efac; }
  .error   { background: #450a0a; color: #fca5a5; }
  .warn    { background: #451a03; color: #fdba74; }
  .container { max-width: 860px; margin: 0 auto; }
  .badge { display: inline-block; padding: .15rem .5rem; border-radius: 9999px; font-size: .75rem; font-weight: 700; margin-left: .5rem; }
  .badge-ok  { background:#166534; color:#bbf7d0; }
  .badge-err { background:#7f1d1d; color:#fecaca; }
</style>
</head>
<body>
<div class="container">
<h1>🛠 WriteSphere – Database Migration</h1>
<?php endif;

// ─── Step 1: Create database if it doesn't exist ─────────────────────────────
section("Step 1 – Database");

try {
    // Connect without selecting a DB first
    $pdo = new PDO("mysql:host=$servername", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    out("Database `$db` is ready.", 'success');

    // Now select it
    $pdo->exec("USE `$db`");
} catch (PDOException $e) {
    out("Connection failed: " . $e->getMessage(), 'error');
    if (!$isCli) { echo '</div></body></html>'; }
    exit(1);
}

// ─── Migration definitions ────────────────────────────────────────────────────
// Each entry: [ 'table' => string, 'sql' => string ]
$migrations = [

    // 1. users
    [
        'table' => 'users',
        'sql'   => "CREATE TABLE IF NOT EXISTS `users` (
            `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name`          VARCHAR(100)  NOT NULL,
            `email`         VARCHAR(180)  NOT NULL UNIQUE,
            `password`      VARCHAR(255)  NOT NULL,
            `privilege`     ENUM('user','moderator','admin') NOT NULL DEFAULT 'user',
            `bio`           TEXT          NULL,
            `gender`        ENUM('male','female','other') NULL,
            `profile_image` VARCHAR(255)  NULL,
            `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 2. categories
    [
        'table' => 'categories',
        'sql'   => "CREATE TABLE IF NOT EXISTS `categories` (
            `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 3. tags
    [
        'table' => 'tags',
        'sql'   => "CREATE TABLE IF NOT EXISTS `tags` (
            `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 4. blog_posts
    [
        'table' => 'blog_posts',
        'sql'   => "CREATE TABLE IF NOT EXISTS `blog_posts` (
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
            CONSTRAINT `fk_bp_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 5. blog_post_media
    [
        'table' => 'blog_post_media',
        'sql'   => "CREATE TABLE IF NOT EXISTS `blog_post_media` (
            `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `blog_post_id` INT UNSIGNED NOT NULL,
            `file_path`    VARCHAR(255) NOT NULL,
            `file_type`    ENUM('Image','Video','Document') NOT NULL DEFAULT 'Image',
            `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_bpm_post` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 6. blog_post_category  (pivot)
    [
        'table' => 'blog_post_category',
        'sql'   => "CREATE TABLE IF NOT EXISTS `blog_post_category` (
            `blog_post_id` INT UNSIGNED NOT NULL,
            `category_id`  INT UNSIGNED NOT NULL,
            PRIMARY KEY (`blog_post_id`, `category_id`),
            CONSTRAINT `fk_bpc_post`     FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`)  ON DELETE CASCADE,
            CONSTRAINT `fk_bpc_category` FOREIGN KEY (`category_id`)  REFERENCES `categories`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 7. blog_post_tags  (pivot)
    [
        'table' => 'blog_post_tags',
        'sql'   => "CREATE TABLE IF NOT EXISTS `blog_post_tags` (
            `blog_post_id` INT UNSIGNED NOT NULL,
            `tag_id`       INT UNSIGNED NOT NULL,
            PRIMARY KEY (`blog_post_id`, `tag_id`),
            CONSTRAINT `fk_bpt_post` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_bpt_tag`  FOREIGN KEY (`tag_id`)       REFERENCES `tags`(`id`)       ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 8. blog_post_liked_by  (pivot)
    [
        'table' => 'blog_post_liked_by',
        'sql'   => "CREATE TABLE IF NOT EXISTS `blog_post_liked_by` (
            `blog_post_id` INT UNSIGNED NOT NULL,
            `user_id`      INT UNSIGNED NOT NULL,
            `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`blog_post_id`, `user_id`),
            CONSTRAINT `fk_bplb_post` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_bplb_user` FOREIGN KEY (`user_id`)      REFERENCES `users`(`id`)      ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 9. comments
    [
        'table' => 'comments',
        'sql'   => "CREATE TABLE IF NOT EXISTS `comments` (
            `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `blog_post_id` INT UNSIGNED NOT NULL,
            `user_id`      INT UNSIGNED NOT NULL,
            `content`      TEXT NOT NULL,
            `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_c_post` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_c_user` FOREIGN KEY (`user_id`)      REFERENCES `users`(`id`)      ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],

    // 10. replies
    [
        'table' => 'replies',
        'sql'   => "CREATE TABLE IF NOT EXISTS `replies` (
            `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `comment_id` INT UNSIGNED NOT NULL,
            `user_id`    INT UNSIGNED NOT NULL,
            `content`    TEXT NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_r_comment` FOREIGN KEY (`comment_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_r_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],
];

// ─── Step 2: Run migrations ───────────────────────────────────────────────────
section("Step 2 – Tables");

$created = 0;
$skipped = 0;
$errors  = 0;

foreach ($migrations as $m) {
    $table = $m['table'];

    // Check if table already exists
    $exists = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount() > 0;

    if ($exists) {
        out("Table `$table` already exists – skipped.", 'warn');
        $skipped++;
        continue;
    }

    try {
        $pdo->exec($m['sql']);
        out("Table `$table` created.", 'success');
        $created++;
    } catch (PDOException $e) {
        out("Failed to create `$table`: " . $e->getMessage(), 'error');
        $errors++;
    }
}

// ─── Step 3: Verify all tables ───────────────────────────────────────────────
section("Step 3 – Verification");

$expected = array_column($migrations, 'table');
$existing = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($expected as $table) {
    if (in_array($table, $existing)) {
        out("$table  ✓", 'success');
    } else {
        out("$table  ✗  MISSING", 'error');
    }
}

// ─── Summary ─────────────────────────────────────────────────────────────────
section("Summary");
out("Created : $created table(s)", $created  > 0 ? 'success' : 'info');
out("Skipped : $skipped table(s)", $skipped  > 0 ? 'warn'    : 'info');
out("Errors  : $errors  table(s)", $errors   > 0 ? 'error'   : 'success');

if ($errors === 0) {
    out("Migration completed successfully. Your database is ready.", 'success');
} else {
    out("Migration finished with errors. Review the messages above.", 'error');
}

// ─── Close HTML (browser only) ───────────────────────────────────────────────
if (!$isCli): ?>
</div>
</body>
</html>
<?php endif;
