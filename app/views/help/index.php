<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Plant Doc Header -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white text-center">
        <div class="plantdoc-hero-badge mb-2 mx-auto">
            <i class="fa-solid fa-lightbulb text-success"></i>
            <span>Field Guide & Farmer Support</span>
        </div>
        <h2 class="plantdoc-why-heading mb-1">
            Plant Photography & Advisory Guide
        </h2>
        <p class="plantdoc-why-sub mb-0">
            Practical guidelines on capturing diagnostic leaf photos, understanding AI confidence scores, and accessing extension services.
        </p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- 1. Photography Best Practices -->
        <div class="col-lg-8">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-camera me-2 text-success"></i><?= __('photo_tips_title') ?></h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <span class="fs-1 d-block mb-2 text-success">🎯</span>
                                <h6 class="fw-bold text-dark">Focus on the Leaf</h6>
                                <p class="small text-muted mb-0"><?= __('photo_tip_1') ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <span class="fs-1 d-block mb-2 text-warning">☀️</span>
                                <h6 class="fw-bold text-dark">Natural Daylight</h6>
                                <p class="small text-muted mb-0"><?= __('photo_tip_2') ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <span class="fs-1 d-block mb-2 text-primary">🌿</span>
                                <h6 class="fw-bold text-dark">Fill the Frame</h6>
                                <p class="small text-muted mb-0"><?= __('photo_tip_3') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Understanding Model Confidence -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-forest mb-0"><i class="fa-solid fa-gauge-high me-2 text-primary"></i>Understanding Model Confidence & Uncertainty</h5>
                </div>
                <div class="card-body p-4 text-muted small leading-relaxed">
                    <p>OpenCropCare's neural network calculates statistical confidence percentages by cross-referencing symptomatic lesion patterns with thousands of verified foliar datasets:</p>
                    <div class="row g-3 my-2">
                        <div class="col-md-4">
                            <div class="p-3 bg-light-success rounded-3 border-start border-success border-4 h-100">
                                <strong class="text-success d-block mb-1">&gt; 85% Confidence</strong>
                                High statistical certainty. Characteristic necrotic or chlorotic lesion patterns clearly match trained classes.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light-warning rounded-3 border-start border-warning border-4 h-100">
                                <strong class="text-warning-emphasis d-block mb-1">60% – 85% Confidence</strong>
                                Moderate certainty. Early lesion stages or minor symptom overlap. Check physical underside of leaf.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light-danger rounded-3 border-start border-danger border-4 h-100">
                                <strong class="text-danger d-block mb-1">&lt; 60% Confidence</strong>
                                Honest uncertainty triggered. Retake photo in diffused daylight or consult local agricultural officer.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Important Safety & Agricultural Advisory -->
            <div class="card card-custom border-0 shadow-sm border-start border-warning border-4 p-2">
                <div class="card-body p-3">
                    <h5 class="fw-bold text-forest d-flex align-items-center mb-2">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Integrated Pest Management & Safety Notice
                    </h5>
                    <p class="small text-muted mb-2">
                        <?= __('disclaimer_text') ?>
                    </p>
                    <p class="small text-muted mb-0">
                        Always adopt an <strong>Organic & Cultural First</strong> strategy: ensure weed sanitation, certified seeds, crop rotation, and bio-fungicides (e.g., <em>Trichoderma</em>, neem formulations) before considering chemical sprays.
                    </p>
                </div>
            </div>

            <!-- 4. Kisan Helpline Callout -->
            <div class="card card-custom border-0 shadow-sm bg-forest text-white p-4 mt-4 text-center rounded-4">
                <span class="fs-2 mb-2 d-block">📞</span>
                <h5 class="fw-bold mb-1">Toll-Free Kisan Call Center Support</h5>
                <p class="small text-leaf-light mb-3">Dial <strong>1800-180-1551</strong> (6:00 AM to 10:00 PM, All 7 Days) for free farmer consultation with agricultural university scientists in your regional language.</p>
                <div>
                    <a href="tel:18001801551" class="btn btn-light text-forest fw-bold rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-phone me-2 text-success"></i> Call 1800-180-1551
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
