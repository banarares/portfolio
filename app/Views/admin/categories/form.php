<main class="admin container">

    <div class="page-header">
        <h1><?= $mode === 'create' ? 'Create Category' : 'Edit Category' ?></h1>
        <a href="/admin/categories" class="btn">Back</a>
    </div>

    <div class="card">
        <form method="POST" class="form" action="<?= $mode === 'edit' ? '/admin/categories/' . (int)$category['id'] : '/admin/categories' ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label class="label" for="name">Name:</label><br>
                <input type="text" id="name" name="name" class="input" value="<?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label class="label" for="slug">Slug (optional):</label><br>
                <input type="text" id="slug" name="slug" class="input" value="<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <hr style="border:none; border-top:1px solid var(--border-subtle); margin: 2rem 0;">
            <h3>SEO</h3>

            <div class="form-group">
                <label class="label" for="meta_title">Meta Title:</label><br>
                <input type="text" id="meta_title" name="meta_title" class="input" value="<?= htmlspecialchars($category['meta_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label class="label" for="meta_description">Meta Description:</label><br>
                <textarea id="meta_description" name="meta_description" class="textarea" rows="3"><?= htmlspecialchars($category['meta_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label class="label" for="meta_keywords">Meta Keywords (comma-separated):</label><br>
                <input type="text" id="meta_keywords" name="meta_keywords" class="input" value="<?= htmlspecialchars($category['meta_keywords'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div>
                <button type="submit" class="btn btn-primary"><?= $mode === 'create' ? 'Create' : 'Update' ?></button>
            </div>

        </form>
    </div>
</main>