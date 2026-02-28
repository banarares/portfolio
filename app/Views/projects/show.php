<section class="section project-view">
    <div class="case">

        <a href="/#projects" class="breadcrumb">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M7.5 10L3.5 6L7.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            All Projects
        </a>

        <!-- HERO -->
        <header class="case-hero reveal">
            <?php if (!empty($project['image_path'])): ?>
                <div class="case-cover">
                    <img src="<?= htmlspecialchars($project['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>"
                         decoding="async">
                </div>
            <?php endif; ?>

            <div class="case-head">
                <h1 class="case-title"><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></h1>

                <?php if (!empty($project['summary'])): ?>
                    <p class="case-summary"><?= nl2br(htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8')) ?></p>
                <?php endif; ?>

                <div class="case-meta">
                    <?php if (!empty($project['category_slug'])): ?>
                        <a class="badge" href="/category/<?= htmlspecialchars($project['category_slug'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($project['category_name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($project['published_at'])): ?>
                        <span class="meta-item"><?= date('M Y', strtotime($project['published_at'])) ?></span>
                    <?php endif; ?>

                    <?php if (!empty($project['tags'])): ?>
                        <span class="meta-sep">&middot;</span>
                        <div class="case-tags">
                            <?php foreach ($project['tags'] as $tag): ?>
                                <a class="tag-pill" href="/tag/<?= htmlspecialchars($tag['slug'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($project['live_url']) || !empty($project['repo_url'])): ?>
                    <div class="case-actions">
                        <?php if (!empty($project['live_url'])): ?>
                            <a class="btn btn-primary" href="<?= htmlspecialchars($project['live_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Live</a>
                        <?php endif; ?>
                        <?php if (!empty($project['repo_url'])): ?>
                            <a class="btn" href="<?= htmlspecialchars($project['repo_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Repo</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- BODY -->
        <?php if (!empty($project['description'])): ?>
            <div class="case-body prose reveal">
                <?php
                $paragraphs = preg_split('/\r?\n\r?\n/', $project['description']);
                foreach ($paragraphs as $para) {
                    $para = trim($para);
                    if ($para !== '') {
                        echo '<p>' . nl2br(htmlspecialchars($para, ENT_QUOTES, 'UTF-8')) . '</p>';
                    }
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- NAV -->
        <nav class="case-nav reveal">
            <?php if (!empty($previousProject)): ?>
                <a class="case-nav-link" href="/project/<?= htmlspecialchars($previousProject['slug'], ENT_QUOTES, 'UTF-8') ?>">
                    <span class="case-nav-kicker">&larr; Previous</span>
                    <span class="case-nav-title"><?= htmlspecialchars($previousProject['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>

            <?php if (!empty($nextProject)): ?>
                <a class="case-nav-link case-nav-link--next" href="/project/<?= htmlspecialchars($nextProject['slug'], ENT_QUOTES, 'UTF-8') ?>">
                    <span class="case-nav-kicker">Next &rarr;</span>
                    <span class="case-nav-title"><?= htmlspecialchars($nextProject['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>
        </nav>

    </div>
</section>
