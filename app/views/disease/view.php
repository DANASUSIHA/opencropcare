<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<?php
$voiceAdvisory = "Disease profile for " . ($disease['disease_name'] ?? '') . " on " . ($disease['crop_name'] ?? '') . ". Symptoms: " . strip_tags($disease['symptoms'] ?? '') . ". Management: " . strip_tags($disease['management'] ?? '');
?>

<div class="container py-4">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="index.php?route=dashboard" class="text-success text-decoration-none"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php?route=diseases" class="text-success text-decoration-none">Disease Library</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($disease['disease_name']) ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <!-- Voice Readout Button -->
            <button type="button" class="btn btn-sm btn-plantdoc-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-3" id="btnListenAdvisory" 
                    data-advisory-text="<?= htmlspecialchars($voiceAdvisory, ENT_QUOTES, 'UTF-8') ?>"
                    data-lang="<?= e(TranslationService::getLanguage()) ?>">
                <i class="fa-solid fa-volume-high"></i>
                <span id="voiceBtnLabel">Listen Guide</span>
                <span class="audio-visualizer ms-1" id="audioVisualizer" style="display: none;">
                    <span class="audio-bar"></span>
                    <span class="audio-bar"></span>
                    <span class="audio-bar"></span>
                    <span class="audio-bar"></span>
                </span>
            </button>

            <!-- WhatsApp Share -->
            <button type="button" class="btn btn-sm btn-plantdoc-outline rounded-pill px-3" onclick="shareOnWhatsApp('<?= e(addslashes($disease['crop_name'])) ?>', '<?= e(addslashes($disease['disease_name'])) ?>', '100', '<?= e(addslashes(substr(strip_tags($disease['symptoms'] ?? ''), 0, 100))) ?>')">
                <i class="fa-brands fa-whatsapp text-success me-1"></i> Share
            </button>

            <!-- Print -->
            <button type="button" class="btn btn-sm btn-plantdoc-outline rounded-pill px-3" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold rounded-pill px-3 py-1"><?= e($disease['crop_name']) ?></span>
                            <span class="badge <?= ($disease['severity'] ?? '') === 'Critical' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?> rounded-pill px-3 py-1">
                                <?= e($disease['severity']) ?> Severity
                            </span>
                        </div>
                        <h2 class="plantdoc-why-heading text-start mb-1"><?= e($disease['disease_name']) ?></h2>
                        <div class="extra-small text-muted"><i class="fa-solid fa-microscope text-success me-1"></i> Pathogen: <strong><?= e($disease['pathogen_type']) ?></strong></div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?php if (!empty($disease['description'])): ?>
                        <div class="mb-4">
                            <h6 class="fw-bold text-forest">Disease Overview</h6>
                            <p class="text-muted leading-relaxed"><?= e($disease['description']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Symptoms -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-forest d-flex align-items-center mb-2">
                            <i class="fa-solid fa-eye me-2 text-success"></i> <?= __('symptoms_title') ?>
                        </h6>
                        <div class="bg-light-success p-3 rounded-3 text-dark small border-start border-success border-4">
                            <?= nl2br(e($disease['symptoms'])) ?>
                        </div>
                    </div>

                    <!-- Causes -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-forest d-flex align-items-center mb-2">
                            <i class="fa-solid fa-circle-nodes me-2 text-warning"></i> <?= __('causes_title') ?>
                        </h6>
                        <div class="bg-light-warning p-3 rounded-3 text-dark small border-start border-warning border-4">
                            <?= nl2br(e($disease['causes'])) ?>
                        </div>
                    </div>

                    <!-- PlantDoc Structured Control & Management Strategy -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-forest d-flex align-items-center mb-3">
                            <i class="fa-solid fa-notes-medical me-2 text-success"></i> Control & Management Strategy
                        </h6>
                        
                        <!-- 1. Organic Treatment Card (PlantDoc Hallmark) -->
                        <div class="treatment-card-organic shadow-sm mb-3">
                            <div class="treatment-title-organic">
                                <i class="fa-solid fa-seedling text-success fs-6"></i>
                                <span>🌿 Organic Treatment & Biological Control (Recommended)</span>
                            </div>
                            <p class="small text-dark mb-0">
                                <?= nl2br(e($disease['organic_remedies'] ?? $disease['management'] ?? 'Maintain proper row spacing, prune bottom infected foliage, apply neem oil (5ml/L) or Trichoderma viride bio-fungicide.')) ?>
                            </p>
                        </div>

                        <!-- 2. Chemical Treatment Card -->
                        <div class="treatment-card-chemical shadow-sm mb-3">
                            <div class="treatment-title-chemical">
                                <i class="fa-solid fa-flask-vial text-primary fs-6"></i>
                                <span>🧪 Chemical Treatment & Intervention</span>
                            </div>
                            <p class="small text-dark mb-0">
                                <?= nl2br(e($disease['chemical_remedies'] ?? 'Consult local agricultural extension service officers before deploying chemical fungicides. Ensure correct PPE and respect harvest safety intervals (PHI).')) ?>
                            </p>
                        </div>

                        <!-- 3. Preventive Cultural Practices -->
                        <div class="treatment-card-prevention shadow-sm mb-3">
                            <div class="treatment-title-prevention">
                                <i class="fa-solid fa-shield text-warning fs-6"></i>
                                <span>🛡️ Preventive Cultural Practices</span>
                            </div>
                            <p class="small text-dark mb-0">
                                <?= nl2br(e($disease['prevention'] ?? 'Crop rotation with non-host species, certified disease-free seedlings, and drip irrigation to prevent spore splash.')) ?>
                            </p>
                        </div>
                    </div>

                    <!-- References & Review Date -->
                    <div class="row pt-3 border-top small text-muted">
                        <div class="col-sm-6">
                            <strong>Reference Authority:</strong> <?= e($disease['references'] ?? 'State Agricultural University / Extension Bureau') ?>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <strong>Last Advisory Review:</strong> <?= e($disease['last_reviewed'] ?? date('Y-m-d')) ?>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-4 text-center">
                        <a href="index.php?route=diseases" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to Disease Library
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
