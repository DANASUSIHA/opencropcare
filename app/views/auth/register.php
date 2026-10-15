<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card card-custom border-0 shadow-sm overflow-hidden bg-white">
                <div class="p-4 text-center border-bottom bg-light bg-opacity-50">
                    <div class="plantdoc-hero-badge mb-2 d-inline-flex">
                        <i class="fa-solid fa-leaf text-success"></i>
                        <span>Start Free Diagnosis</span>
                    </div>
                    <h2 class="plantdoc-why-heading mb-1"><?= __('register_title') ?></h2>
                    <p class="plantdoc-main-sub mb-0" style="font-size: 0.88rem;"><?= __('register_subtitle') ?></p>
                </div>

                <div class="card-body p-4">
                    <?php if (!empty($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger py-2 px-3 small rounded-3 border-0 d-flex align-items-center gap-2 mb-3">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <div><?= e($_SESSION['flash_error']) ?></div>
                        </div>
                        <?php unset($_SESSION['flash_error']); ?>
                    <?php endif; ?>

                    <form action="index.php?route=handle_register" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold small text-muted"><?= __('full_name') ?> *</label>
                                <input type="text" class="form-control py-2" id="name" name="name" placeholder="e.g. Ramesh Patel" required style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label fw-semibold small text-muted"><?= __('role_label') ?></label>
                                <select class="form-select py-2" id="role" name="role" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                                    <option value="farmer"><?= __('role_farmer') ?></option>
                                    <option value="expert"><?= __('role_expert') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-muted"><?= __('email_label') ?> *</label>
                            <input type="email" class="form-control py-2" id="email" name="email" placeholder="farmer@opencropcare.org" required style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold small text-muted"><?= __('phone_number') ?></label>
                                <input type="tel" class="form-control py-2" id="phone" name="phone" placeholder="+91 98765 43210" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                            </div>
                            <div class="col-md-6">
                                <label for="district" class="form-label fw-semibold small text-muted">District / State</label>
                                <input type="text" class="form-control py-2" id="district" name="district" placeholder="e.g. Coimbatore, Tamil Nadu" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold small text-muted"><?= __('password_label') ?> *</label>
                            <input type="password" class="form-control py-2" id="password" name="password" placeholder="At least 6 characters" minlength="6" required style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                        </div>

                        <button type="submit" class="btn btn-plantdoc-primary w-100 justify-content-center py-2 mb-3">
                            <i class="fa-solid fa-user-plus me-2"></i>
                            <span><?= __('btn_register') ?></span>
                        </button>

                        <div class="text-center small text-muted">
                            <?= __('have_account') ?> 
                            <a href="index.php?route=login" class="fw-bold text-success text-decoration-none"><?= __('login_link') ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
