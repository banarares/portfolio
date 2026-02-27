<main class="admin container">

    <div class="page-header">
        <h1><?= $mode === 'create' ? 'Create Project' : 'Edit Project' ?></h1>
        <a href="/admin/projects" class="btn">Back</a>
    </div>

    <div class="card">
        <form method="post" class="form" enctype="multipart/form-data" action="<?= $mode === 'edit' ? '/admin/projects/' . (int)$project['id'] : '/admin/projects' ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label class="label" for="title">Title:</label>
                <input type="text" id="title" name="title" class="input" value="<?= htmlspecialchars($project['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <!-- Optional -->
            <div class="form-group">
                <label class="label" for="slug">Slug:</label>
                <input type="text" id="slug" name="slug" class="input" value="<?= htmlspecialchars($project['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label class="label" for="summary">Summary:</label>
                <input type="text" id="summary" name="summary" class="input" value="<?= htmlspecialchars($project['summary'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label class="label" for="description">Description:</label>
                <textarea id="description" name="description" class="textarea" rows="5"><?= htmlspecialchars($project['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label class="label" for="live_url">Live URL:</label>
                <input type="url" id="live_url" name="live_url" class="input" value="<?= htmlspecialchars($project['live_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label class="label" for="repo_url">Repository URL:</label>
                <input type="url" id="repo_url" name="repo_url" class="input" value="<?= htmlspecialchars($project['repo_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label class="label" for="category_id">Category:</label>
                <select id="category_id" name="category_id" class="select">
                    <option value="">-- None --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (isset($project['category_id']) && $project['category_id'] == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" name="is_published" value="1" <?= (isset($project['published_at']) && $project['published_at']) ? 'checked' : '' ?> />
                    Published
                </label>
            </div>

            <div class="form-group">
                <label class="label" for="tags">Tags:</label>
                <select id="tags" name="tag_ids[]" multiple size="8" class="select">
                    <?php foreach ($tags as $tag): ?>
                        <?php $tid = (int)$tag['id']; ?>
                        <option value="<?= $tid ?>" <?= in_array($tid, $selectedTagIds) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group file-input">
                <label class="label" for="image">Image (optional):</label><br>
                <input type="file" id="image" name="image" accept="image/*">
                <?php
                $img = $project['image_path'] ?? '';
                $img = $img ? '/' . ltrim($img, '/') : '';
                ?>
                <?php if ($img): ?>
                    <div>
                        <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="Project Image" style="max-width: 200px; margin-top: 10px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= (isset($project['is_featured']) && $project['is_featured']) ? 'checked' : '' ?>>
                    Featured
                </label>
            </div>

            <hr style="border:none; border-top:1px solid var(--border-subtle); margin: 2rem 0;">
            <h3>SEO</h3>

            <div class="form-group">
                <label class="label" for="meta_title">Meta Title:</label><br>
                <input type="text" id="meta_title" name="meta_title" class="input" value="<?= htmlspecialchars($project['meta_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label class="label" for="meta_description">Meta Description:</label><br>
                <textarea id="meta_description" name="meta_description" rows="3" class="input"><?= htmlspecialchars($project['meta_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label class="label" for="keywords">Keywords (comma-separated):</label><br>
                <input type="text" id="keywords" name="keywords" class="input" value="<?= htmlspecialchars($project['keywords'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div>
                <button type="submit" class="btn btn-primary"><?= $mode === 'create' ? 'Create' : 'Update' ?></button>
            </div>
        </form>
    </div>
</main>