<main class="admin container">

    <div class="page-header">
        <h1>Admin</h1>

        <form method="post" action="/admin/logout">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-secondary">Logout</button>
        </form>
    </div>

    <div class="card">
        <table class="table">
            <tr>
                <td colspan="2"><a href="/" class="btn">View Site</a></td>
            </tr>
            <tr>
                <td><a href="/admin/projects" class="btn btn-primary">Projects</a></td>
                <td class="text-right">
                    <div class="badge"><?= htmlspecialchars($projectsCount) ?></div>
                </td>
            </tr>
            <tr>
                <td><a href="/admin/categories" class="btn btn-primary">Categories</a></td>
                <td class="text-right">
                    <div class="badge"><?= htmlspecialchars($categoriesCount) ?></div>
                </td>
            </tr>
            <tr>
                <td><a href="/admin/tags" class="btn btn-primary">Tags</a></td>
                <td class="text-right">
                    <div class="badge"><?= htmlspecialchars($tagsCount) ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><a href="/admin/settings" class="btn">Settings</a></td>
            </tr>
        </table>
    </div>

</main>