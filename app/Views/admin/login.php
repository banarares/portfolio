<main class="admin container">

    <div class="page-header">
        <h1 style="margin: 0 auto;">Admin Login</h1>
    </div>

    <div class="card" style="max-width: 540px; width: 100%; margin: 0 auto;">

        <form method="post" class="form" action="/admin/login">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

            <div class="form-group">
                <label for="email" class="label">Email address</label>
                <input type="email" class="input" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password" class="label">Password</label>
                <input type="password" class="input" id="password" name="password" required>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>

    </div>
</main>