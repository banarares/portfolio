<main class="admin container">

    <div class="page-header">
        <h1><?= ($mode ?? 'create') === 'edit' ? 'Edit Tag' : 'Create Tag' ?></h1>
        <a href="/admin/tags" class="btn">Back</a>
    </div>

    <div class="card">
        <form method="post" class="form" action="<?= ($mode ?? 'create') === 'edit' ? '/admin/tags/' . (int)$tag['id'] : '/admin/tags' ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label class="label" for="name">Name:</label><br>
                <input type="text" id="name" name="name" class="input" value="<?= htmlspecialchars($tag['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label class="label" for="slug">Slug (optional):</label><br>
                <input type="text" id="slug" name="slug" class="input" value="<?= htmlspecialchars($tag['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div>
                <button type="submit" class="btn btn-primary"><?= $mode === 'create' ? 'Create' : 'Update' ?></button>
            </div>
        </form>
    </div>
</main>