<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($seo['title'] ?? 'Rares Portfolio', ENT_QUOTES, 'UTF-8') ?></title>
    <?php if (!empty($seo['description'])): ?>
        <meta name="description" content="<?= htmlspecialchars($seo['description'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($seo['keywords'])): ?>
        <meta name="keywords" content="<?= htmlspecialchars($seo['keywords'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($seo['canonical'])): ?>
        <link rel="canonical" href="<?= htmlspecialchars($seo['canonical'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <!-- OpenGraph -->
    <meta property="og:type"  content="<?= htmlspecialchars($seo['og_type'] ?? 'website', ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($seo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <?php if (!empty($seo['description'])): ?>
        <meta property="og:description" content="<?= htmlspecialchars($seo['description'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($seo['canonical'])): ?>
        <meta property="og:url" content="<?= htmlspecialchars($seo['canonical'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($seo['og_image'])): ?>
        <meta property="og:image" content="<?= htmlspecialchars($seo['og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?= !empty($seo['og_image']) ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= htmlspecialchars($seo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <?php if (!empty($seo['description'])): ?>
        <meta name="twitter:description" content="<?= htmlspecialchars($seo['description'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($seo['og_image'])): ?>
        <meta name="twitter:image" content="<?= htmlspecialchars($seo['og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="/assets/css/app.css">
</head>

<body>
    <div class="wrap">
        <header class="site-header">
            <div class="container header-inner">
                <a href="/" class="logo">RB<span>.</span></a>
                    
                <nav class="nav" id="site-nav">
                    <a href="/#about">About</a>
                    <a href="/#projects">Projects</a>
                    <a href="/#contact">Contact</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/admin">Admin</a>
                    <?php endif; ?>
                </nav>

                <button class="nav-burger" id="nav-burger" aria-label="Toggle menu" aria-expanded="false" aria-controls="site-nav">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </header>

        <main class="site-main">
            <?php require $viewFile; ?>
        </main>

        <footer class="site-footer">
            <div class="container footer-inner">
                <div class="footer-copy">
                    <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? 'Rares Bana', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="footer-hosted">Hosted free on <a href="https://anybusiness.ro" target="_blank" rel="noopener noreferrer">Anybusiness.ro</a></span>
                </div>

                <div class="footer-links">
                    <?php if (!empty($settings['github_url'])): ?>
                        <a href="<?= htmlspecialchars($settings['github_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
                    <?php endif; ?>
                    <?php if (!empty($settings['linkedin_url'])): ?>
                        <a href="<?= htmlspecialchars($settings['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                    <?php endif; ?>
                    <?php if (!empty($settings['email_public'])): ?>
                        <a href="mailto:<?= htmlspecialchars($settings['email_public'], ENT_QUOTES, 'UTF-8') ?>">Email</a>
                    <?php endif; ?>
                </div>
            </div>
        </footer>
    </div>

    <script src="/assets/js/app.js"></script>

    <!-- Cursor elements (pointer devices only) -->
    <div id="cursor-glow" aria-hidden="true"></div>
    <div id="cursor-dot"  aria-hidden="true"></div>
    <div id="cursor-ring" aria-hidden="true"></div>
</body>

</html>