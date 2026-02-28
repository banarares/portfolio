<?php require __DIR__ . '/../about.php'; ?>

<section id="projects" class="section">
    <div class="container">

        <div class="page-head">
            <div class="section-kicker">Selected Work</div>
            <h2>Projects</h2>

            <div class="tech-stack">
                <?php foreach ($tags as $tag): ?>
                    <button class="tag-filter<?= ($tagSlug ?? '') === $tag['slug'] ? ' active' : '' ?>"
                        data-tag="<?= htmlspecialchars($tag['slug'], ENT_QUOTES, 'UTF-8') ?>">
                        #<?= htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>


        <?php if (empty($projects)): ?>
            <p>No projects found.</p>
        <?php else: ?>
            <div id="projects-list" class="projects-grid">
                <?php foreach ($projects as $project): ?>

                    <a href="/project/<?= htmlspecialchars($project['slug'], ENT_QUOTES, 'UTF-8') ?>" class="project-card reveal">
                        <?php if (!empty($project['image_path'])): ?>
                            <!-- Project image -->
                            <div class="project-image">
                                <img src="<?= htmlspecialchars($project['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" decoding="async">
                            </div>
                        <?php endif; ?>

                        <div class="project-content">
                            <div class="project-topline">
                                <!-- Project title -->
                                <h3><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <!-- Project category -->
                                <?php if (!empty($project['category_name'])): ?>
                                    <span class="badge"><?= htmlspecialchars($project['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                            <!-- Project summary -->
                            <p><?= nl2br(htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8')) ?></p>
                        </div>
                    </a>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require __DIR__ . '/../contact.php'; ?>