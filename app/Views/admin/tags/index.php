<main class="admin container">

    <div class="page-header">
        <h1>Tags</h1>
        <div class="actions">
            <a href="/admin" class="btn btn-secondary">Back</a>
            <a href="/admin/tags/create" class="btn btn-primary">New Tag</a>
        </div>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tags as $tag): ?>
                    <tr>
                        <td>
                            <a href="/admin/tags/<?= htmlspecialchars($tag['id']) ?>/edit">
                                <?= htmlspecialchars($tag['name']) ?>
                            </a>
                        </td>
                        <td class="text-right">
                            <a href="/tag/<?= htmlspecialchars($tag['slug']) ?>" target="_blank" class="btn btn-sm btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php include __DIR__ . '/../partials/pagination.php'; ?>

    </div>
</main>