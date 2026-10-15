<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<main class="login-hero-container auth-page py-5">
    <div class="auth-layout auth-layout-admin">
        <aside class="auth-showcase auth-showcase-admin">
            <a class="auth-home-link" href="index.php?route=home">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to home
            </a>
            <div class="auth-showcase-content">
                <span class="auth-showcase-mark"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
                <span class="auth-showcase-label">OPEN CROPCARE / SYSTEM ACCESS</span>
                <h1>Stewardship<br>for a more<br><em>resilient field.</em></h1>
                <p>Manage the knowledge and tools that help growers make more informed decisions for their crops.</p>
                <div class="auth-showcase-bottom">
                    <span class="showcase-line"></span>
                    <span>Secure administrator access</span>
                </div>
            </div>
            <span class="auth-showcase-number">02 — ADMIN PORTAL</span>
        </aside>

        <section class="login-card-luxury">
        <div class="login-card-top pb-3">
            <div class="login-emblem"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></div>
            <div class="plantdoc-hero-badge admin-login-badge mb-2 d-inline-flex">
                <i class="fa-solid fa-lock text-primary"></i>
                <span>Administrator Control Panel</span>
            </div>
            <h2 class="login-title mb-1">Admin <span style="color: var(--color-primary);">Sign In</span></h2>
            <p class="login-subtitle">Central Platform Administration & System Controls</p>
        </div>

        <div class="card-body pt-0 px-4 pb-4">
            <!-- Flash Alerts -->
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger py-2 px-3 small rounded-3 border-0 d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <div><?= e($_SESSION['flash_error']) ?></div>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success py-2 px-3 small rounded-3 border-0 d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                    <div><?= e($_SESSION['flash_success']) ?></div>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <!-- Admin Login Form -->
            <form action="index.php?route=handle_admin_login" method="POST" id="adminLoginForm">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                <!-- Email Input -->
                <div class="mb-3 text-start">
                    <label for="admin_email" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                        Administrator Email Address
                    </label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-user-shield text-primary"></i>
                        <input type="email" class="form-control form-control-sleek" id="admin_email" name="email" placeholder="admin@example.com" required autocomplete="username">
                    </div>
                </div>

                <!-- Password Input with Reveal Toggle -->
                <div class="mb-3 text-start">
                    <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                        <label for="admin_password" class="form-label small fw-bold text-muted text-uppercase mb-0 text-nowrap" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                            Administrator Password
                        </label>
                        <button type="button" class="btn btn-link p-0 text-success text-decoration-none extra-small fw-semibold text-nowrap flex-shrink-0" onclick="togglePasswordVisibility('admin_password', 'adminEyeIcon', 'adminEyeText')">
                            <i class="fa-solid fa-eye me-1" id="adminEyeIcon"></i> <span id="adminEyeText">Show</span>
                        </button>
                    </div>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-key text-primary"></i>
                        <input type="password" class="form-control form-control-sleek" id="admin_password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                    </div>
                </div>

                <!-- Primary Submit Button -->
                <button type="submit" class="btn btn-plantdoc-primary w-100 justify-content-center shadow-sm mb-2 py-2" id="btnAdminSubmit">
                    <i class="fa-solid fa-shield-halved me-2"></i>
                    <span>Sign In to Admin Panel</span>
                </button>

            </form>

            <!-- Security & Audit Note -->
            <div class="mt-3 pt-3 border-top text-center text-muted admin-security-note">
                <i class="fa-solid fa-lock text-success me-1"></i> Restricted Area &bull; Authorized Personnel Only &bull; Encrypted Session
            </div>
        </div>
        </section>
    </div>
</main>

<script>
function togglePasswordVisibility(inputId, iconId, textId) {
    var pass = document.getElementById(inputId);
    var eye = document.getElementById(iconId);
    var text = document.getElementById(textId);
    if (pass.type === 'password') {
        pass.type = 'text';
        eye.className = 'fa-solid fa-eye-slash me-1';
        text.innerText = 'Hide';
    } else {
        pass.type = 'password';
        eye.className = 'fa-solid fa-eye me-1';
        text.innerText = 'Show';
    }
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
