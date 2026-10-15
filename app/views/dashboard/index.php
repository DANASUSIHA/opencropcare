<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Plant Doc Welcome Banner -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="plantdoc-hero-badge mb-2">
                    <i class="fa-solid fa-leaf text-success"></i>
                    <span>Farmer Portal &bull; <?= date('l, d F Y') ?><?php if (!empty($_SESSION['user']['district'])): ?> &bull; <i class="fa-solid fa-location-dot text-danger"></i> <?= e($_SESSION['user']['district']) ?><?php endif; ?></span>
                </div>
                <h2 class="plantdoc-why-heading text-start mb-1">
                    <?= __('welcome_back') ?>, <?= e($_SESSION['user']['name']) ?>!
                </h2>
                <p class="plantdoc-main-sub text-start mb-0" style="font-size: 0.88rem;">
                    <?= __('dashboard_subtitle') ?>
                </p>
            </div>
            <div class="text-md-end flex-shrink-0">
                <a href="index.php?route=identify" class="btn btn-plantdoc-primary">
                    <span>Start Analysis</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Two Quick Action Cards (Matching Screenshot) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <a href="index.php?route=identify&mode=upload" class="plantdoc-action-card">
                <div class="action-icon-box">
                    <i class="fa-regular fa-image"></i>
                </div>
                <div class="text-start">
                    <div class="action-title">Image Analysis</div>
                    <div class="action-desc">Upload leaf photos for instant diagnosis</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6">
            <a href="index.php?route=identify&mode=symptoms" class="plantdoc-action-card">
                <div class="action-icon-box">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <div class="text-start">
                    <div class="action-title">Symptom Check</div>
                    <div class="action-desc">Describe symptoms for AI analysis</div>
                </div>
            </a>
        </div>
    </div>

    <!-- 4 Plant Doc Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="feature-card-box p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="feature-icon-square mb-0" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill extra-small">Live</span>
                </div>
                <div class="text-muted extra-small fw-semibold text-uppercase"><?= __('stat_total_scans') ?></div>
                <h3 class="fw-bold mb-0 text-dark mt-1"><?= $stats['total_scans'] ?></h3>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="feature-card-box p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="feature-icon-square mb-0" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="fa-solid fa-shield-heart text-success"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill extra-small">Safe</span>
                </div>
                <div class="text-muted extra-small fw-semibold text-uppercase"><?= __('stat_healthy_leaves') ?></div>
                <h3 class="fw-bold mb-0 text-success mt-1"><?= $stats['healthy_count'] ?></h3>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="feature-card-box p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="feature-icon-square mb-0 text-danger" style="width: 36px; height: 36px; font-size: 1rem; background: #fee2e2;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill extra-small">Advisory</span>
                </div>
                <div class="text-muted extra-small fw-semibold text-uppercase"><?= __('stat_diseased_leaves') ?></div>
                <h3 class="fw-bold mb-0 text-danger mt-1"><?= $stats['diseased_count'] ?></h3>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="feature-card-box p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="feature-icon-square mb-0 text-warning" style="width: 36px; height: 36px; font-size: 1rem; background: #fef3c7;">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill extra-small">Crops</span>
                </div>
                <div class="text-muted extra-small fw-semibold text-uppercase"><?= __('stat_crops_monitored') ?></div>
                <h3 class="fw-bold mb-0 text-dark mt-1"><?= count($stats['crop_distribution']) ?></h3>
            </div>
        </div>
    </div>

    <!-- Platform Modules Hub -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="fw-bold text-dark mb-0">Platform Modules & Services</h4>
                <span class="extra-small text-muted">Select any module below to access platform features</span>
            </div>
            <span class="badge bg-light text-muted border">Quick Navigation</span>
        </div>

        <div class="row g-3">
            <!-- 1. Leaf Disease Diagnosis -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-box p-3 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="feature-icon-square mb-0" style="width: 34px; height: 34px; font-size: 0.95rem;">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0"><?= __('nav_identify') ?></h6>
                    </div>
                    <p class="extra-small text-muted flex-grow-1 mb-3">
                        Upload or capture a leaf photo. Runs automated quality validation and AI disease predictions.
                    </p>
                    <a href="index.php?route=identify" class="btn btn-sm btn-plantdoc-primary w-100">
                        Scan Leaf Now
                    </a>
                </div>
            </div>

            <!-- 2. Scan History -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-box p-3 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="feature-icon-square mb-0" style="width: 34px; height: 34px; font-size: 0.95rem;">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0"><?= __('nav_history') ?></h6>
                    </div>
                    <p class="extra-small text-muted flex-grow-1 mb-3">
                        Browse all past foliar disease scans, model confidence scores, dates, and farmer feedback.
                    </p>
                    <a href="index.php?route=history" class="btn btn-sm btn-plantdoc-outline w-100">
                        View Scan History
                    </a>
                </div>
            </div>

            <!-- 3. Disease Library -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-box p-3 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="feature-icon-square mb-0" style="width: 34px; height: 34px; font-size: 0.95rem;">
                            <i class="fa-solid fa-book-medical"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0"><?= __('nav_diseases') ?></h6>
                    </div>
                    <p class="extra-small text-muted flex-grow-1 mb-3">
                        Explore 20+ crop disease profiles across Tomato, Potato, Corn, Apple, Grape, and Pepper with remedies.
                    </p>
                    <a href="index.php?route=diseases" class="btn btn-sm btn-plantdoc-outline w-100">
                        Browse Library
                    </a>
                </div>
            </div>

            <!-- 4. Farmer Guide -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-box p-3 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="feature-icon-square mb-0" style="width: 34px; height: 34px; font-size: 0.95rem;">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0"><?= __('nav_help') ?></h6>
                    </div>
                    <p class="extra-small text-muted flex-grow-1 mb-3">
                        Best practices for field leaf photography, understanding confidence levels, and Kisan helpline support.
                    </p>
                    <a href="index.php?route=help" class="btn btn-sm btn-plantdoc-outline w-100">
                        Open Guide
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Scans & Analytics Charts -->
    <div class="row g-4 mb-4">
        <!-- Recent Scans Table -->
        <div class="col-lg-7">
            <div class="card card-custom border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-success"></i>
                        <span><?= __('recent_scans') ?></span>
                    </h5>
                    <a href="index.php?route=history" class="btn btn-sm btn-plantdoc-outline py-1 px-3 extra-small"><?= __('view_all') ?></a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($stats['recent_scans'])): ?>
                        <div class="p-5 text-center text-muted">
                            <span class="fs-1 d-block mb-2">📸</span>
                            <h6 class="fw-bold mb-1">No Scans Recorded Yet</h6>
                            <p class="small mb-3"><?= __('no_scans_yet') ?></p>
                            <a href="index.php?route=identify" class="btn btn-sm btn-plantdoc-primary"><?= __('btn_start_scan') ?></a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="ps-4"><?= __('table_image') ?></th>
                                        <th><?= __('table_crop') ?></th>
                                        <th><?= __('table_prediction') ?></th>
                                        <th><?= __('table_confidence') ?></th>
                                        <th class="text-end pe-4"><?= __('table_actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['recent_scans'] as $scan): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <img src="<?= e($scan['image_path'] ?? 'img/placeholder.png') ?>" alt="Leaf" class="rounded-3 object-fit-cover shadow-xs" style="width: 44px; height: 44px;">
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark small"><?= e($scan['crop_name']) ?></span>
                                                <div class="text-muted extra-small"><?= date('M d, H:i', strtotime($scan['created_at'])) ?></div>
                                            </td>
                                            <td>
                                                <?php if (!empty($scan['is_healthy'])): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small">Healthy</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small"><?= e($scan['predicted_disease']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 5px; width: 55px;">
                                                        <div class="progress-bar bg-success" style="width: <?= (float)$scan['confidence'] ?>%"></div>
                                                    </div>
                                                    <span class="extra-small fw-bold text-muted"><?= round((float)$scan['confidence'], 1) ?>%</span>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="index.php?route=result&id=<?= urlencode($scan['_id']) ?>" class="btn btn-sm btn-plantdoc-outline py-1 px-2 extra-small" title="View Diagnosis">
                                                    <i class="fa-solid fa-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Analytics Chart -->
        <div class="col-lg-5">
            <div class="card card-custom border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-success"></i>
                        <span><?= __('frequent_diseases') ?></span>
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <?php if ($stats['total_scans'] === 0): ?>
                        <div class="text-center text-muted py-4">
                            <span class="fs-1 d-block mb-2">📊</span>
                            <p class="small mb-0">Charts will automatically render after your first crop diagnosis scan.</p>
                        </div>
                    <?php else: ?>
                        <div style="position: relative; height: 230px;">
                            <canvas id="diseaseBreakdownChart"></canvas>
                        </div>
                        <div class="extra-small text-muted text-center mt-3">
                            <em>AI disease telemetry &bull; OpenCropCare Diagnostic Intelligence</em>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- OpenCropCare feature grid -->
    <div class="plantdoc-why-section pt-3">
        <h2 class="plantdoc-why-heading">Why Choose OpenCropCare?</h2>
        <p class="plantdoc-why-sub">
            A comprehensive solution designed for farmers, agriculture students, and gardening enthusiasts to detect and manage plant diseases effectively.
        </p>

        <div class="row g-4 text-start">
            <div class="col-sm-6 col-lg-3">
                <div class="feature-card-box">
                    <div class="feature-icon-square">
                        <i class="fa-solid fa-microscope"></i>
                    </div>
                    <h4 class="feature-card-title">Accurate Detection</h4>
                    <p class="feature-card-desc">
                        Our AI model is trained on thousands of plant disease images for precise identification.
                    </p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="feature-card-box">
                    <div class="feature-icon-square">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h4 class="feature-card-title">Instant Results</h4>
                    <p class="feature-card-desc">
                        Get disease diagnosis and treatment recommendations within seconds.
                    </p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="feature-card-box">
                    <div class="feature-icon-square">
                        <i class="fa-solid fa-shield"></i>
                    </div>
                    <h4 class="feature-card-title">Prevention Tips</h4>
                    <p class="feature-card-desc">
                        Learn how to prevent diseases and maintain healthy crops year-round.
                    </p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="feature-card-box">
                    <div class="feature-icon-square">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <h4 class="feature-card-title">Educational Resource</h4>
                    <p class="feature-card-desc">
                        Detailed information about plant diseases for students and researchers.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('diseaseBreakdownChart');
    if (ctx && window.Chart) {
        var labels = <?= json_encode(array_keys($stats['disease_distribution'])) ?>;
        var data = <?= json_encode(array_values($stats['disease_distribution'])) ?>;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#2d6a4f', '#40916c', '#52b788', '#f59e0b', '#ef4444', '#0284c7', '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 11, family: 'Inter' }, padding: 12 }
                    }
                }
            }
        });
    }
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
