<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Plant Doc Admin Header -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="plantdoc-hero-badge mb-2">
                    <i class="fa-solid fa-comments text-success"></i>
                    <span>Administrator Portal &bull; Feedback Moderation &bull; <?= date('l, d F Y') ?></span>
                </div>
                <h2 class="plantdoc-why-heading text-start mb-1">
                    Farmer Feedback Queue
                </h2>
                <p class="plantdoc-main-sub text-start mb-0" style="font-size: 0.88rem;">
                    Review submitted farmer feedback for diagnostic evaluation and model accuracy.
                </p>
            </div>
            <div class="text-md-end flex-shrink-0">
                <a href="index.php?route=admin_dashboard" class="btn btn-plantdoc-outline" style="padding: 7px 18px; font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    <span>Admin Overview</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Feedback Table Card -->
    <div class="card card-custom border-0 shadow-sm overflow-hidden bg-white">
        <div class="card-body p-0">
            <?php if (empty($feedbacks)): ?>
                <div class="p-5 text-center text-muted">
                    <div class="feature-icon-square mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <p class="mb-0 fw-semibold">No feedback submissions found yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3 py-3">Farmer</th>
                                <th class="py-3">Crop</th>
                                <th class="py-3">AI Predicted</th>
                                <th class="py-3">Farmer Evaluation</th>
                                <th class="py-3">Suggested Disease</th>
                                <th class="py-3">Field Comments</th>
                                <th class="py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedbacks as $fb): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-dark"><?= e($fb['user_name'] ?? 'Farmer') ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= e($fb['crop_name'] ?? 'Unknown') ?></span></td>
                                    <td><span class="small fw-semibold"><?= e($fb['predicted_disease'] ?? 'Unknown') ?></span></td>
                                    <td>
                                        <span class="badge <?= ($fb['feedback_status'] === 'correct') ? 'bg-success-subtle text-success' : (($fb['feedback_status'] === 'incorrect') ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') ?> fw-semibold">
                                            <?= e(ucfirst($fb['feedback_status'])) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted"><?= e($fb['suggested_disease'] ?: '—') ?></td>
                                    <td class="small text-muted"><?= e($fb['comments'] ?: '—') ?></td>
                                    <td class="small text-muted"><?= date('M d, Y', strtotime($fb['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
