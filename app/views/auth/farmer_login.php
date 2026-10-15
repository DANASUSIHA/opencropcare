<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<main class="login-hero-container auth-page py-5">
    <div class="auth-layout">
        <aside class="auth-showcase">
            <div class="auth-showcase-content">
                <span class="auth-showcase-mark"><i class="fa-solid fa-seedling" aria-hidden="true"></i></span>
                <span class="auth-showcase-label">OPEN CROPCARE / FIELD NOTES 01</span>
                <h1>Good care<br>starts with<br><em>knowing.</em></h1>
                <p>Understand what your crops are telling you. Get thoughtful, practical plant-health insights in a few simple steps.</p>
                <div class="auth-showcase-bottom">
                    <span class="showcase-line"></span>
                    <span>Better insight. Healthier crops.</span>
                </div>
            </div>
            <span class="auth-showcase-number">GROW WITH CONFIDENCE</span>
        </aside>

        <section class="login-card-luxury">
            <div class="login-card-top">
                <div class="login-emblem"><i class="fa-solid fa-leaf" aria-hidden="true"></i></div>
                <span class="auth-showcase-label login-overline">YOUR FIELD, IN FOCUS</span>
                <h1 class="login-title mb-1">Welcome back</h1>
                <p class="login-subtitle">Sign in to continue to your crop health workspace.</p>
            </div>

            <div class="card-body">
                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger py-2 px-3 small rounded-3 border-0 d-flex align-items-center gap-2 mb-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation flex-shrink-0" aria-hidden="true"></i>
                        <div><?= e($_SESSION['flash_error']) ?></div>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success py-2 px-3 small rounded-3 border-0 d-flex align-items-center gap-2 mb-3" role="status">
                        <i class="fa-solid fa-circle-check flex-shrink-0" aria-hidden="true"></i>
                        <div><?= e($_SESSION['flash_success']) ?></div>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <form action="index.php?route=handle_login" method="POST" id="farmerLoginForm">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                    <div class="mb-3 text-start">
                        <label for="farmer_email" class="form-label small fw-semibold">Email address</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                            <input type="email" class="form-control form-control-sleek" id="farmer_email" name="email" placeholder="you@example.com" required autocomplete="username">
                        </div>
                    </div>

                    <div class="mb-4 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="farmer_password" class="form-label small fw-semibold mb-0">Password</label>
                            <button type="button" class="auth-password-toggle" onclick="togglePasswordVisibility('farmer_password', 'farmerEyeIcon', 'farmerEyeText')" aria-label="Show password">
                                <i class="fa-solid fa-eye me-1" id="farmerEyeIcon" aria-hidden="true"></i><span id="farmerEyeText">Show</span>
                            </button>
                        </div>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-lock" aria-hidden="true"></i>
                            <input type="password" class="form-control form-control-sleek" id="farmer_password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-plantdoc-primary w-100 justify-content-center auth-submit" id="btnFarmerSubmit">
                        <span>Sign in</span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <div class="auth-create-account">
                    <span>New to OpenCropCare?</span>
                    <a href="index.php?route=register">Create a new account <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                </div>
                <div class="auth-admin-access">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                    <span>Administrator?</span>
                    <a href="index.php?route=admin_login">Admin sign in</a>
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
