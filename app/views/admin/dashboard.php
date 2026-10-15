<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Administrator Hero Banner (Plant Doc Design Language) -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="plantdoc-hero-badge mb-2">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Administrator Portal &bull; <?= date('l, d F Y') ?><?php if (!empty($_SESSION['user']['district'])): ?> &bull; <i class="fa-solid fa-location-dot text-danger"></i> <?= e($_SESSION['user']['district']) ?><?php endif; ?></span>
                </div>
                <h2 class="plantdoc-why-heading text-start mb-1">
                    <?= __('welcome_back') ?>, <?= e($_SESSION['user']['name']) ?>!
                </h2>
                <p class="plantdoc-main-sub text-start mb-0" style="font-size: 0.88rem;">
                    Central platform administration, user directory, disease catalog, and system controls.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-md-end flex-shrink-0">
                <a href="index.php?route=admin_users" class="btn btn-plantdoc-outline" style="padding: 7px 16px; font-size: 0.85rem;">
                    <i class="fa-solid fa-users me-1 text-success"></i>
                    <span>Users (<?= count($users) ?>)</span>
                </a>
                <a href="index.php?route=admin_diseases" class="btn btn-plantdoc-outline" style="padding: 7px 16px; font-size: 0.85rem;">
                    <i class="fa-solid fa-database me-1 text-success"></i>
                    <span>Diseases (<?= count($diseases) ?>)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Admin overview statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="feature-card-box p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="feature-icon-square mb-0" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success extra-small fw-bold">Sitewide</span>
                </div>
                <div class="stat-number fs-3 fw-bold text-dark"><?= $stats['total_scans'] ?></div>
                <div class="stat-label text-muted small">Total Scans Performed</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="feature-card-box p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="feature-icon-square mb-0" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary extra-small fw-bold">Active</span>
                </div>
                <div class="stat-number fs-3 fw-bold text-dark"><?= count($users) ?></div>
                <div class="stat-label text-muted small">Registered Accounts</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="feature-card-box p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="feature-icon-square mb-0" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="fa-solid fa-book-medical"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning extra-small fw-bold">Catalog</span>
                </div>
                <div class="stat-number fs-3 fw-bold text-dark"><?= count($diseases) ?></div>
                <div class="stat-label text-muted small">Diseases Cataloged</div>
            </div>
        </div>
    </div>

    <!-- Recent farmer feedback -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card card-custom border-0 shadow-sm mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-comments text-success"></i>
                        <span>Recent Farmer Feedback</span>
                    </h5>
                    <a href="index.php?route=admin_feedback" class="btn btn-sm btn-plantdoc-outline" style="padding: 4px 12px; font-size: 0.78rem;">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($feedbackList)): ?>
                        <div class="p-4 text-center text-muted small">No feedback recorded yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="ps-3">Farmer</th>
                                        <th>Crop</th>
                                        <th>Predicted</th>
                                        <th>Accuracy</th>
                                        <th>Suggested / Comments</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($feedbackList, 0, 5) as $fb): ?>
                                        <tr>
                                            <td class="ps-3 fw-semibold text-dark"><?= e($fb['user_name'] ?? 'Farmer') ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= e($fb['crop_name'] ?? 'Unknown') ?></span></td>
                                            <td class="small fw-semibold"><?= e($fb['predicted_disease'] ?? 'Unknown') ?></td>
                                            <td>
                                                <span class="badge <?= ($fb['feedback_status'] === 'correct') ? 'bg-success' : (($fb['feedback_status'] === 'incorrect') ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                                    <?= e(ucfirst($fb['feedback_status'])) ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted">
                                                <?= e($fb['suggested_disease'] ?: ($fb['comments'] ?: '—')) ?>
                                            </td>
                                            <td class="small text-muted"><?= date('M d', strtotime($fb['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
