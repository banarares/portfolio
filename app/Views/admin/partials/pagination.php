<?php
if (!isset($totalPages) || $totalPages <= 1) return;

$query = $_GET;
?>

<nav class="admin-pagination">
    <ul>
        <?php if ($page > 1): ?>
            <?php $query['page'] = $page - 1; ?>
            <li>
                <a href="?<?= http_build_query($query) ?>">← Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $query['page'] = $i; ?>
            <li>
                <a href="?<?= http_build_query($query) ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <?php $query['page'] = $page + 1; ?>
            <li>
                <a href="?<?= http_build_query($query) ?>">Next →</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>