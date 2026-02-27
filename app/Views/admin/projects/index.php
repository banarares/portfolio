<main class="admin container">

    <div class="page-header">
        <h1>Projects</h1>
        <div class="actions">
            <a href="/admin" class="btn btn-secondary">Back</a>
            <a href="/admin/projects/create" class="btn btn-primary">New Project</a>
        </div>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td>
                            <a href="/admin/projects/<?= htmlspecialchars($project['id']) ?>/edit">
                                <?= htmlspecialchars($project['title']) ?>
                            </a>
                        </td>
                        <td class="text-right">
                            <a href="/project/<?= htmlspecialchars($project['slug']) ?>" target="_blank" class="btn btn-sm btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php include __DIR__ . '/../partials/pagination.php'; ?>

    </div>
</main>