<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<?php
$confidenceVal = round((float)($prediction['confidence'] ?? 0), 1);
$isHealthy = !empty($prediction['is_healthy']);

// Prepare Voice Readout Narrative
$voiceNarrative = "Diagnosis for " . ($prediction['crop_name'] ?? 'Crop') . ". Identified condition: " . ($prediction['predicted_disease'] ?? 'Unknown') . " with " . $confidenceVal . " percent confidence. ";
if (!empty($disease['symptoms'])) {
    $voiceNarrative .= "Observed symptoms: " . strip_tags($disease['symptoms']) . ". ";
}
if (!empty($disease['management'])) {
    $voiceNarrative .= "Recommended management: " . strip_tags($disease['management']) . ". ";
}
if (!empty($disease['prevention'])) {
    $voiceNarrative .= "Prevention measures: " . strip_tags($disease['prevention']) . ". ";
}
?>

<div class="container py-4">
    <!-- Breadcrumb & Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="index.php?route=dashboard" class="text-success text-decoration-none"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php?route=history" class="text-success text-decoration-none">History</a></li>
                <li class="breadcrumb-item active" aria-current="page">Advisory Result</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <!-- Voice Readout Button -->
            <button type="button" class="btn btn-sm btn-plantdoc-primary d-flex align-items-center gap-2" id="btnListenAdvisory" 
                    data-advisory-text="<?= htmlspecialchars($voiceNarrative, ENT_QUOTES, 'UTF-8') ?>"
                    data-lang="<?= e(TranslationService::getLanguage()) ?>">
                <i class="fa-solid fa-volume-high"></i>
                <span id="voiceBtnLabel">Listen Advisory</span>
                <span class="audio-visualizer ms-1" id="audioVisualizer" style="display: none;">
                    <span class="audio-bar"></span>
                    <span class="audio-bar"></span>
                    <span class="audio-bar"></span>
                    <span class="audio-bar"></span>
                </span>
            </button>

            <!-- WhatsApp Share -->
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="shareOnWhatsApp('<?= e(addslashes($prediction['crop_name'])) ?>', '<?= e(addslashes($prediction['predicted_disease'])) ?>', '<?= $confidenceVal ?>', '<?= e(addslashes(substr(strip_tags($disease['symptoms'] ?? ''), 0, 100))) ?>')">
                <i class="fa-brands fa-whatsapp text-success me-1"></i> Share
            </button>

            <!-- Print / PDF -->
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Honest Uncertainty Banner if flagged -->
    <?php if (!empty($prediction['uncertainty_flag']) || !empty($prediction['uncertainty_message']) || $confidenceVal < 65): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center rounded-3" role="alert">
            <i class="fa-solid fa-circle-exclamation fs-3 text-warning me-3"></i>
            <div>
                <h6 class="fw-bold mb-1">Honest Uncertainty Alert (Confidence: <?= $confidenceVal ?>%)</h6>
                <p class="small mb-0"><?= e($prediction['uncertainty_message'] ?? 'Model statistical confidence is moderate. Foliar symptoms may overlap with related pathogens or nutritional deficiencies. We strongly advise cross-verification with an agricultural officer.') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left Column: Leaf Photo & Probable Matches -->
        <div class="col-lg-5">
            <!-- Image Card -->
            <div class="card card-custom border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-forest text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-image me-2 text-leaf-light"></i>Uploaded Foliar Photo</h6>
                    <span class="badge bg-white text-forest"><?= e($prediction['crop_name']) ?></span>
                </div>
                <div class="p-3 text-center bg-black bg-opacity-10">
                    <img src="<?= e($prediction['image_path'] ?? 'img/placeholder.png') ?>" alt="Diagnosed Leaf" class="img-fluid rounded-3 shadow-sm" style="max-height: 320px; object-fit: contain;">
                </div>
                <div class="card-body p-3 bg-light border-top">
                    <div class="row text-center extra-small text-muted">
                        <div class="col-6">
                            <strong>Crop Variety:</strong> <?= e($prediction['crop_name']) ?>
                        </div>
                        <div class="col-6">
                            <strong>Scan Date:</strong> <?= date('M d, Y H:i', strtotime($prediction['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top-3 Predictions Ranking -->
            <?php if (!empty($prediction['top_3'])): ?>
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-forest mb-0"><i class="fa-solid fa-ranking-star me-2 text-warning"></i>Probable Diagnosis Candidates</h6>
                        <span class="badge bg-light text-muted border">Top 3</span>
                    </div>
                    <div class="card-body p-3">
                        <?php foreach ($prediction['top_3'] as $idx => $cand): ?>
                            <div class="mb-3 <?= $idx === 0 ? 'p-2 bg-light-success rounded-3 border border-success-subtle' : '' ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-dark">
                                        <span class="badge <?= $idx === 0 ? 'bg-success' : 'bg-secondary' ?> me-1">#<?= $idx + 1 ?></span>
                                        <?= e($cand['disease_name']) ?>
                                    </span>
                                    <span class="small fw-bold text-success"><?= round((float)$cand['confidence'], 1) ?>%</span>
                                </div>
                                <div class="progress" style="height: 7px; border-radius: 4px;">
                                    <div class="progress-bar <?= $idx === 0 ? 'bg-success' : 'bg-warning' ?>" style="width: <?= (float)$cand['confidence'] ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Farmer Feedback Card -->
            <div class="card card-custom border-0 shadow-sm" id="feedbackSection">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-forest mb-0"><i class="fa-solid fa-comment-dots me-2 text-primary"></i><?= __('feedback_title') ?></h6>
                </div>
                <div class="card-body p-3">
                    <form action="index.php?route=submit_feedback" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <input type="hidden" name="prediction_id" value="<?= e($prediction['_id']) ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Field Accuracy Assessment</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="feedback_status" id="fb_correct" value="correct" checked>
                                    <label class="form-check-label small" for="fb_correct">✅ <?= __('feedback_status_correct') ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="feedback_status" id="fb_incorrect" value="incorrect">
                                    <label class="form-check-label small" for="fb_incorrect">❌ <?= __('feedback_status_incorrect') ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="feedback_status" id="fb_uncertain" value="uncertain">
                                    <label class="form-check-label small" for="fb_uncertain">❓ <?= __('feedback_status_uncertain') ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="suggested_disease" class="form-label small fw-semibold text-muted">If incorrect, actual verified condition (Optional)</label>
                            <input type="text" class="form-control form-control-sm" id="suggested_disease" name="suggested_disease" placeholder="e.g. Potassium Deficiency, Thrips Damage">
                        </div>

                        <div class="mb-3">
                            <label for="comments" class="form-label small fw-semibold text-muted"><?= __('feedback_comments') ?></label>
                            <textarea class="form-control form-control-sm" id="comments" name="comments" rows="2" placeholder="Field symptoms observed..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-plantdoc-primary w-100 justify-content-center">
                            <i class="fa-solid fa-paper-plane me-2"></i> <?= __('btn_submit_feedback') ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Primary Diagnosis & Organic/Chemical Treatment Tabs -->
        <div class="col-lg-7">
            <!-- Main Diagnosis Banner -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%); color: #ffffff; border-radius: 12px 12px 0 0;">
                    <div>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 mb-2" style="color: #1b4332 !important;">
                            <?= $isHealthy ? '🌿 Healthy Foliage' : '⚠️ Foliar Condition Detected' ?>
                        </span>
                        <h3 class="mb-0 text-white" style="font-family: 'Playfair Display', Georgia, serif; font-weight: 700;"><?= e($prediction['predicted_disease']) ?></h3>
                        <span class="extra-small text-white text-opacity-75 d-block mt-1">Verified with trained Neural Diagnostic Architecture</span>
                    </div>
                    <div class="text-end">
                        <div class="confidence-gauge-container d-inline-block">
                            <svg viewBox="0 0 36 36" style="width: 86px; height: 86px;" class="confidence-gauge-circle">
                                <path stroke="#ffffff33" stroke-width="3" fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#a7f3d0" stroke-dasharray="<?= $confidenceVal ?>, 100" stroke-width="3.5" stroke-linecap="round" fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="confidence-text-inside text-white">
                                <span class="fw-bold fs-5 d-block lh-1"><?= $confidenceVal ?>%</span>
                                <span style="font-size: 9px; opacity: 0.85;">Confidence</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Key Pathology Indicators -->
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="p-3 bg-light rounded-3 text-center h-100">
                                <div class="extra-small text-muted fw-bold">CROP TYPE</div>
                                <div class="fw-bold text-dark mt-1"><?= e($prediction['crop_name']) ?></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded-3 text-center h-100">
                                <div class="extra-small text-muted fw-bold"><?= __('pathogen_type') ?></div>
                                <div class="fw-bold text-dark mt-1"><?= e($disease['pathogen_type'] ?? 'Foliar Condition') ?></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded-3 text-center h-100">
                                <div class="extra-small text-muted fw-bold"><?= __('severity_label') ?></div>
                                <div class="fw-bold <?= ($disease['severity'] ?? '') === 'Critical' ? 'text-danger' : 'text-warning' ?> mt-1">
                                    <?= e($disease['severity'] ?? 'Moderate') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($disease['description'])): ?>
                        <div class="mb-4">
                            <p class="text-muted leading-relaxed"><?= e($disease['description']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Visible Symptoms -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-forest d-flex align-items-center mb-2">
                            <i class="fa-solid fa-eye me-2 text-success"></i> <?= __('symptoms_title') ?>
                        </h6>
                        <div class="bg-light-success p-3 rounded-3 text-dark small border-start border-success border-4">
                            <?= nl2br(e($disease['symptoms'] ?? 'No symptom records available.')) ?>
                        </div>
                    </div>

                    <!-- Causes -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-forest d-flex align-items-center mb-2">
                            <i class="fa-solid fa-circle-nodes me-2 text-warning"></i> <?= __('causes_title') ?>
                        </h6>
                        <div class="bg-light-warning p-3 rounded-3 text-dark small border-start border-warning border-4">
                            <?= nl2br(e($disease['causes'] ?? 'Environmental moisture and spore splashing.')) ?>
                        </div>
                    </div>

                    <!-- PlantDoc Structured Treatment Guidance -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-forest mb-0 d-flex align-items-center">
                                <i class="fa-solid fa-notes-medical me-2 text-success"></i> Actionable Treatment Advisory
                            </h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Dual Recommendations</span>
                        </div>

                        <!-- 1. Organic Treatment Card (PlantDoc Hallmark) -->
                        <div class="treatment-card-organic shadow-sm">
                            <div class="treatment-title-organic">
                                <i class="fa-solid fa-seedling text-success fs-6"></i>
                                <span>🌿 Organic Treatment & Biological Control (Recommended)</span>
                            </div>
                            <p class="small text-dark mb-2">
                                <?= nl2br(e($disease['organic_remedies'] ?? $disease['management'] ?? 'Maintain proper row spacing, prune bottom infected foliage, apply neem oil (5ml/L) or Trichoderma viride bio-fungicide.')) ?>
                            </p>
                            <div class="extra-small text-muted border-top border-success-subtle pt-2 mt-2">
                                <strong>Eco-Friendly Note:</strong> Non-toxic to pollinators, safe for soil biome and groundwater.
                            </div>
                        </div>

                        <!-- 2. Chemical Treatment Card -->
                        <div class="treatment-card-chemical shadow-sm">
                            <div class="treatment-title-chemical">
                                <i class="fa-solid fa-flask-vial text-primary fs-6"></i>
                                <span>🧪 Chemical Treatment & Intervention</span>
                            </div>
                            <p class="small text-dark mb-2">
                                <?= nl2br(e($disease['chemical_remedies'] ?? 'If infection exceeds 20% economic threshold, consult local agricultural extension specialists. Apply certified copper oxychloride or targeted fungicide. Adhere strictly to pre-harvest intervals (PHI).')) ?>
                            </p>
                            <div class="extra-small text-muted border-top border-info-subtle pt-2 mt-2">
                                <i class="fa-solid fa-shield-halved text-info me-1"></i> Always wear protective eyewear & gloves. Do not spray during high winds.
                            </div>
                        </div>

                        <!-- 3. Preventive Measures Card -->
                        <div class="treatment-card-prevention shadow-sm">
                            <div class="treatment-title-prevention">
                                <i class="fa-solid fa-shield text-warning fs-6"></i>
                                <span>🛡️ Preventive Cultural Measures</span>
                            </div>
                            <p class="small text-dark mb-0">
                                <?= nl2br(e($disease['prevention'] ?? 'Crop rotation with non-host species, certified disease-free seedlings, drip irrigation to avoid leaf wetting.')) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Safety Notice -->
                    <div class="alert alert-warning border-0 p-3 mb-4 rounded-3">
                        <div class="d-flex align-items-start">
                            <i class="fa-solid fa-shield-cat text-warning fs-5 me-2 mt-1"></i>
                            <div>
                                <strong class="d-block text-dark mb-1"><?= __('disclaimer_title') ?></strong>
                                <span class="small text-muted"><?= __('disclaimer_text') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                        <a href="index.php?route=identify" class="btn btn-plantdoc-primary">
                            <i class="fa-solid fa-camera-retro me-2"></i> <?= __('btn_try_another') ?>
                        </a>
                        <a href="index.php?route=history" class="btn btn-plantdoc-outline">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i> <?= __('nav_history') ?>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
