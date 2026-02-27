<main class="admin container">

    <div class="page-header">
        <h1>Categories</h1>
        <div class="actions">
            <a href="/admin" class="btn btn-secondary">Back</a>
            <a href="/admin/categories/create" class="btn btn-primary">New Category</a>
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
                <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>
                                <a href="/admin/categories/<?= htmlspecialchars($category['id']) ?>/edit">
                                    <?= htmlspecialchars($category['name']) ?>
                                </a>
                            </td>
                            <td class="text-right">
                                <a href="/category/<?= htmlspecialchars($category['slug']) ?>" target="_blank" class="btn btn-sm btn-secondary">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include __DIR__ . '/../partials/pagination.php'; ?>

</main>