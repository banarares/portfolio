<?php

/**
 * Portfolio Demo Seed
 * -------------------
 * Populates the database with a demo admin user, settings,
 * one category, one tag, and one sample project.
 *
 * Usage (from project root):
 *   php database/seed.php
 *
 * ⚠  Change the admin password immediately after first login.
 */

declare(strict_types=1);

// --------------- Load .env manually (no autoloader needed) --------------------
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

function env(string $key, string $default = ''): string
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// --------------- DB connection ------------------------------------------------
$dsn  = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    env('DB_HOST', 'localhost'),
    env('DB_PORT', '3306'),
    env('DB_NAME')
);

try {
    $pdo = new PDO($dsn, env('DB_USER'), env('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    exit('❌  Database connection failed: ' . $e->getMessage() . PHP_EOL);
}

echo PHP_EOL . '🌱  Running seed…' . PHP_EOL;

// --------------- Admin password -----------------------------------------------
$defaultPassword = 'admin123';
echo '   Admin password will be set to: "' . $defaultPassword . '" — change it after login!' . PHP_EOL;
$hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT, ['cost' => 12]);

// --------------- Helpers -------------------------------------------------------
function insert(PDO $pdo, string $table, array $data): int
{
    $cols = implode(', ', array_map(fn($c) => "`$c`", array_keys($data)));
    $phs  = implode(', ', array_map(fn($c) => ":$c", array_keys($data)));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute($data);
    return (int) $pdo->lastInsertId();
}

function alreadyExists(PDO $pdo, string $table, string $col, mixed $val): bool
{
    $st = $pdo->prepare("SELECT 1 FROM `$table` WHERE `$col` = ? LIMIT 1");
    $st->execute([$val]);
    return (bool) $st->fetchColumn();
}

// --------------- 1. Admin user -------------------------------------------------
$adminEmail = 'admin@example.com';
if (alreadyExists($pdo, 'users', 'email', $adminEmail)) {
    $userId = (int) $pdo->query("SELECT id FROM users WHERE email = '$adminEmail' LIMIT 1")->fetchColumn();
    echo '   ✓ User already exists (id=' . $userId . '), skipping.' . PHP_EOL;
} else {
    $userId = insert($pdo, 'users', [
        'email'      => $adminEmail,
        'password'   => $hashedPassword,
        'role'       => 'admin',
        'created_at' => date('Y-m-d H:i:s'),
    ]);
    echo '   ✓ Created admin user  id=' . $userId . PHP_EOL;
}

// --------------- 2. Settings --------------------------------------------------
if (!alreadyExists($pdo, 'settings', 'user_id', $userId)) {
    insert($pdo, 'settings', [
        'user_id'                  => $userId,
        'site_name'                => 'Your Name',
        'site_tagline'             => 'Full-Stack Developer',
        'canonical_base_url'       => 'https://yourdomain.com',
        'default_meta_title'       => 'Your Name | Full-Stack Developer',
        'default_meta_description' => 'Building elegant digital systems with performance and security in mind.',
        'default_keywords'         => 'php, mysql, javascript, portfolio',
        'email_public'             => 'you@example.com',
        'linkedin_url'             => 'https://www.linkedin.com/in/yourprofile/',
        'github_url'               => 'https://github.com/yourusername/',
        'created_at'               => date('Y-m-d H:i:s'),
    ]);
    echo '   ✓ Created settings' . PHP_EOL;
} else {
    echo '   ✓ Settings already exist, skipping.' . PHP_EOL;
}

// --------------- 3. Category --------------------------------------------------
$catSlug = 'web-development';
if (alreadyExists($pdo, 'categories', 'slug', $catSlug)) {
    $categoryId = (int) $pdo->query("SELECT id FROM categories WHERE slug = '$catSlug' AND user_id = $userId LIMIT 1")->fetchColumn();
    echo '   ✓ Category already exists (id=' . $categoryId . '), skipping.' . PHP_EOL;
} else {
    $categoryId = insert($pdo, 'categories', [
        'user_id'          => $userId,
        'name'             => 'Web Development',
        'slug'             => $catSlug,
        'description'      => 'Full-stack web applications and APIs.',
        'meta_title'       => 'Web Development Projects',
        'meta_description' => 'A collection of web development projects.',
        'keywords'         => 'web development, php, javascript, api',
        'created_at'       => date('Y-m-d H:i:s'),
    ]);
    echo '   ✓ Created category    id=' . $categoryId . PHP_EOL;
}

// --------------- 4. Tag -------------------------------------------------------
$tagSlug = 'php';
if (alreadyExists($pdo, 'tags', 'slug', $tagSlug)) {
    $tagId = (int) $pdo->query("SELECT id FROM tags WHERE slug = '$tagSlug' AND user_id = $userId LIMIT 1")->fetchColumn();
    echo '   ✓ Tag already exists  (id=' . $tagId . '), skipping.' . PHP_EOL;
} else {
    $tagId = insert($pdo, 'tags', [
        'user_id'    => $userId,
        'name'       => 'PHP',
        'slug'       => $tagSlug,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
    echo '   ✓ Created tag         id=' . $tagId . PHP_EOL;
}

// --------------- Extra tags for demo ------------------------------------------
foreach ([['MySQL', 'mysql'], ['Vanilla JS', 'vanilla-js']] as [$name, $slug]) {
    if (!alreadyExists($pdo, 'tags', 'slug', $slug)) {
        insert($pdo, 'tags', [
            'user_id'    => $userId,
            'name'       => $name,
            'slug'       => $slug,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        echo '   ✓ Created tag         ' . $name . PHP_EOL;
    }
}

// --------------- 5. Sample project --------------------------------------------
$projSlug = 'sample-project';
if (alreadyExists($pdo, 'projects', 'slug', $projSlug)) {
    echo '   ✓ Project already exists, skipping.' . PHP_EOL;
    $projectId = (int) $pdo->query("SELECT id FROM projects WHERE slug = '$projSlug' AND user_id = $userId LIMIT 1")->fetchColumn();
} else {
    $projectId = insert($pdo, 'projects', [
        'user_id'          => $userId,
        'category_id'      => $categoryId,
        'title'            => 'Sample Portfolio Project',
        'slug'             => $projSlug,
        'summary'          => 'A showcase project demonstrating clean architecture, secure backend logic, and refined user experience.',
        'description'      => "This is a sample project description. Replace it with the full case study narrative.\n\nExplain what problem you solved, what technologies you used, and what the outcome was. Keep it concise but descriptive.\n\nHighlight architecture decisions, performance considerations, and any interesting technical challenges.",
        'live_url'         => 'https://example.com',
        'repo_url'         => 'https://github.com/youruser/yourrepo',
        'meta_title'       => 'Sample Project | Portfolio',
        'meta_description' => 'A showcase project built with PHP, MySQL and Vanilla JS.',
        'keywords'         => 'php, mysql, portfolio, case study',
        'is_featured'      => 1,
        'published_at'     => date('Y-m-d H:i:s'),
        'created_at'       => date('Y-m-d H:i:s'),
        'updated_at'       => date('Y-m-d H:i:s'),
    ]);
    echo '   ✓ Created project     id=' . $projectId . PHP_EOL;
}

// --------------- 6. Tag pivot -------------------------------------------------
$pivot = $pdo->prepare('SELECT 1 FROM projects_tags WHERE project_id = ? AND tag_id = ? LIMIT 1');
$pivot->execute([$projectId, $tagId]);
if (!$pivot->fetchColumn()) {
    $pdo->prepare('INSERT INTO projects_tags (project_id, tag_id) VALUES (?, ?)')->execute([$projectId, $tagId]);
    echo '   ✓ Linked tag to project' . PHP_EOL;
}

// --------------- Done! --------------------------------------------------------
echo PHP_EOL;
echo '✅  Seed complete!' . PHP_EOL;
echo PHP_EOL;
echo '   Next steps:' . PHP_EOL;
echo '   1. Visit /admin/login  →  ' . $adminEmail .  '  /  ' . $defaultPassword . PHP_EOL;
echo '   2. Update Settings with your real name, URLs and email.' . PHP_EOL;
echo '   3. Change your password immediately after login.' . PHP_EOL;
echo '   4. Add your real projects via /admin/projects.' . PHP_EOL;
echo PHP_EOL;
