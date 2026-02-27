<section id="contact" class="section">
    <div class="container">
        <div class="contact-card reveal">
            <div>
                <h2>Let's build something exceptional.</h2>
                <p class="muted">
                    For architectures, integrations, performance work or techincal leadership.<br>
                    I'm open to new opportunities - reach out anytime.
                </p>
            </div>

            <div class="contact-row">
                <div></div>
                <div>
                    <?php if (!empty($settings['email_public'])): ?>
                        <a class="btn btn-primary" href="mailto:<?= htmlspecialchars($settings['email_public'], ENT_QUOTES, 'UTF-8') ?>">Email me</a>
                    <?php endif; ?>
                    <?php if (!empty($settings['linkedin_url'])): ?>
                        <a class="btn" href="<?= htmlspecialchars($settings['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>