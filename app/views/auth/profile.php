<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Plant Doc Profile Card -->
            <div class="card card-custom border-0 shadow-sm overflow-hidden bg-white">
                <div class="p-4 text-center border-bottom bg-light bg-opacity-50">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle fs-3 fw-bold mb-3 shadow-sm" style="width: 72px; height: 72px; background: var(--color-primary-light); color: var(--color-primary); border: 2px solid var(--color-primary);">
                        <?= e(strtoupper(substr($user['name'] ?? 'F', 0, 1))) ?>
                    </div>
                    <div class="plantdoc-hero-badge mb-2 d-inline-flex">
                        <i class="fa-solid fa-user-check text-success"></i>
                        <span><?= e(ucfirst($user['role'] ?? 'farmer')) ?> Account</span>
                    </div>
                    <h3 class="plantdoc-why-heading mb-1"><?= e($user['name'] ?? 'Farmer') ?></h3>
                    <p class="plantdoc-main-sub mb-0" style="font-size: 0.85rem;"><?= e($user['email'] ?? '') ?></p>
                </div>

                <div class="card-body p-4">
                    <?php if (!empty($_SESSION['flash_success'])): ?>
                        <div class="alert alert-success py-2 px-3 small rounded-3 border-0 d-flex align-items-center gap-2 mb-3">
                            <i class="fa-solid fa-circle-check"></i>
                            <div><?= e($_SESSION['flash_success']) ?></div>
                        </div>
                        <?php unset($_SESSION['flash_success']); ?>
                    <?php endif; ?>

                    <form action="index.php?route=handle_profile_update" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted"><?= __('full_name') ?></label>
                            <input type="text" class="form-control py-2" name="name" value="<?= e($user['name'] ?? '') ?>" required style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted"><?= __('email_label') ?></label>
                            <input type="email" class="form-control bg-light py-2" value="<?= e($user['email'] ?? '') ?>" readonly style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                            <div class="form-text extra-small text-muted">Email address is permanently linked to your diagnostic scans.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted"><?= __('phone_number') ?></label>
                                <input type="tel" class="form-control py-2" name="phone" value="<?= e($user['phone'] ?? '') ?>" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">District / State</label>
                                <input type="text" class="form-control py-2" name="district" value="<?= e($user['district'] ?? '') ?>" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-muted">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control py-2" name="new_password" placeholder="••••••••" minlength="6" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="index.php?route=dashboard" class="btn btn-plantdoc-outline" style="padding: 8px 20px;">
                                <i class="fa-solid fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-plantdoc-primary">
                                <span>Save Changes</span>
                                <i class="fa-solid fa-check ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
