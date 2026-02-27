<main class="admin container">

    <div class="page-header">
        <div>
            <h1>Site Settings</h1>
            <p class="muted">Controls your public site name, SEO defaults, and social links.</p>
        </div>
        <a href="/admin" class="btn">← Dashboard</a>
    </div>

    <form method="post" action="/admin/settings" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="card">
            <h3 style="margin-bottom:1.2rem">Identity</h3>

            <div class="form-row two">
                <div class="form-group">
                    <label class="label" for="site_name">Site Name</label>
                    <input class="input" type="text" id="site_name" name="site_name"
                           value="<?= htmlspecialchars($settings['site_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Rares Bana">
                </div>
                <div class="form-group">
                    <label class="label" for="site_tagline">Tagline</label>
                    <input class="input" type="text" id="site_tagline" name="site_tagline"
                           value="<?= htmlspecialchars($settings['site_tagline'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Full-Stack Developer">
                </div>
            </div>

            <div class="form-group" style="margin-top:1rem">
                <label class="label" for="canonical_base_url">Canonical Base URL</label>
                <input class="input" type="url" id="canonical_base_url" name="canonical_base_url"
                       value="<?= htmlspecialchars($settings['canonical_base_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="https://yourdomain.com">
                <span class="label" style="margin-top:.3rem;text-transform:none;letter-spacing:0">No trailing slash. Used for canonical links and OG URLs.</span>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:1.2rem">SEO Defaults</h3>
            <p class="muted" style="margin-bottom:1rem">Used as fallback when a project or page doesn't set its own SEO fields.</p>

            <div class="form-group">
                <label class="label" for="default_meta_title">Default Meta Title</label>
                <input class="input" type="text" id="default_meta_title" name="default_meta_title"
                       value="<?= htmlspecialchars($settings['default_meta_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Rares Bana | Full-Stack Developer">
            </div>

            <div class="form-group" style="margin-top:1rem">
                <label class="label" for="default_meta_description">Default Meta Description</label>
                <textarea class="textarea" id="default_meta_description" name="default_meta_description"
                          rows="3" placeholder="Building elegant digital systems with performance and security in mind."><?= htmlspecialchars($settings['default_meta_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group" style="margin-top:1rem">
                <label class="label" for="default_keywords">Default Keywords</label>
                <input class="input" type="text" id="default_keywords" name="default_keywords"
                       value="<?= htmlspecialchars($settings['default_keywords'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="php, mysql, laravel, javascript">
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:1.2rem">Contact &amp; Social</h3>

            <div class="form-group">
                <label class="label" for="email_public">Public Email</label>
                <input class="input" type="email" id="email_public" name="email_public"
                       value="<?= htmlspecialchars($settings['email_public'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="you@example.com">
            </div>

            <div class="form-row two" style="margin-top:1rem">
                <div class="form-group">
                    <label class="label" for="linkedin_url">LinkedIn URL</label>
                    <input class="input" type="url" id="linkedin_url" name="linkedin_url"
                           value="<?= htmlspecialchars($settings['linkedin_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="https://www.linkedin.com/in/yourprofile/">
                </div>
                <div class="form-group">
                    <label class="label" for="github_url">GitHub URL</label>
                    <input class="input" type="url" id="github_url" name="github_url"
                           value="<?= htmlspecialchars($settings['github_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="https://github.com/yourusername/">
                </div>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>

</main>
