<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<?php
$initialMode = ($_GET['mode'] ?? 'upload') === 'symptoms' ? 'symptoms' : 'upload';
?>

<div class="container py-4">
    <!-- PlantDoc Center Hero Banner -->
    <div class="plantdoc-hero mb-4">
        <div class="plantdoc-badge">
            <i class="fa-solid fa-leaf text-success"></i>
            <span>AI-Powered Plant Health Analysis</span>
        </div>
        <h1 class="plantdoc-hero-title">Plant Disease Detection System</h1>
        <p class="plantdoc-hero-sub">
            Upload a plant leaf image or describe symptoms to get instant disease diagnosis and eco-friendly treatment recommendations.
        </p>

        <!-- PlantDoc Dual-Mode Switcher -->
        <div class="plantdoc-tab-switcher">
            <button type="button" class="plantdoc-tab-btn <?= $initialMode === 'upload' ? 'active' : '' ?>" id="tabBtnUpload" onclick="switchPlantDocMode('upload')">
                <i class="fa-solid fa-camera"></i>
                <span>Upload Leaf Photo</span>
            </button>
            <button type="button" class="plantdoc-tab-btn <?= $initialMode === 'symptoms' ? 'active' : '' ?>" id="tabBtnSymptoms" onclick="switchPlantDocMode('symptoms')">
                <i class="fa-solid fa-stethoscope"></i>
                <span>Describe Symptoms</span>
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">

            <!-- MODE 1: Upload Leaf Photo -->
            <div id="plantdocModeUpload" style="<?= $initialMode === 'upload' ? '' : 'display: none;' ?>">
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="stat-icon-circle bg-light-success text-success" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="fa-solid fa-camera"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Leaf Photo Diagnosis</h5>
                                <span class="extra-small text-muted">Neural deep learning vision analysis</span>
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                            <i class="fa-solid fa-bolt text-warning me-1"></i> Instant AI
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <form action="index.php?route=handle_predict" method="POST" enctype="multipart/form-data" id="predictForm">
                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                            <!-- 1. Visual Crop Selection Chips -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold text-dark mb-0">
                                        <span class="badge bg-success me-1">1</span> Select Crop Type *
                                    </label>
                                    <span class="extra-small text-muted">Tap to select</span>
                                </div>

                                <div class="crop-chip-grid mb-3">
                                    <?php foreach ($crops as $c): ?>
                                        <div class="crop-chip-item" data-crop="<?= e($c['crop_name']) ?>">
                                            <span class="crop-icon"><?= e($c['icon'] ?? '🌱') ?></span>
                                            <span class="small d-block fw-semibold text-truncate"><?= e($c['crop_name']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Hidden input for form submission -->
                                <select class="form-select d-none" id="crop_name" name="crop_name" required>
                                    <option value="" disabled selected><?= __('choose_crop_placeholder') ?></option>
                                    <?php foreach ($crops as $c): ?>
                                        <option value="<?= e($c['crop_name']) ?>"><?= e($c['crop_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- 2. Photo Upload / Camera Area -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold text-dark mb-0">
                                        <span class="badge bg-success me-1">2</span> Upload or Snap Diseased Leaf *
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-leaf fw-semibold py-1 px-3" id="btnOpenCam">
                                        <i class="fa-solid fa-camera me-1"></i> Use Camera
                                    </button>
                                </div>

                                <!-- PlantDoc Dashed Dropzone -->
                                <div class="upload-dropzone p-4 text-center position-relative" id="dropZone">
                                    <input type="file" class="upload-file-input" id="leaf_image" name="leaf_image" accept="image/jpeg,image/png,image/webp" required>
                                    <div id="dropZoneContent" class="py-3">
                                        <div class="mb-2">
                                            <i class="fa-solid fa-cloud-arrow-up text-success display-5"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Click to upload or drag & drop leaf photo</h6>
                                        <p class="text-muted extra-small mb-3">Supports JPG, PNG, or WebP (High resolution, Max 8MB)</p>
                                        <span class="btn btn-sm btn-outline-leaf px-3">
                                            <i class="fa-solid fa-folder-open me-1"></i> Browse Files
                                        </span>
                                    </div>
                                </div>

                                <!-- Preview with Laser Scanner Beam -->
                                <div id="previewContainer" class="mt-3 text-center" style="display: none;">
                                    <div class="position-relative d-inline-block shadow-sm rounded-3 overflow-hidden leaf-scan-container" id="scanPreviewWrapper">
                                        <div class="leaf-scanner-beam"></div>
                                        <img id="imagePreview" src="#" alt="Leaf Preview" class="img-fluid rounded-3" style="max-height: 300px; min-height: 180px; object-fit: contain; background: #000;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle shadow" id="btnRemoveImage" title="Remove Photo">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>

                                <div id="qualityGateResult" style="display: none;"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-leaf text-white fw-bold shadow-sm py-2 px-4 rounded-pill" id="btnSubmit">
                                    <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Analyze Plant
                                </button>
                                
                                <div id="submitLoader" class="card border-0 p-3 rounded-3 text-center mt-2 shadow-xs" style="display: none; background: #e9f5ec;">
                                    <div class="spinner-grow text-success mb-2" role="status">
                                        <span class="visually-hidden">Analyzing...</span>
                                    </div>
                                    <h6 class="fw-bold text-forest mb-1" id="analysisStatusHeader">Analyzing Plant Foliage...</h6>
                                    <p class="extra-small text-muted mb-0" id="analysisStatusSub">Neural feature extraction & foliar pathogen classification in progress...</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODE 2: Describe Symptoms (Interactive Symptom Triage like PlantDoc) -->
            <div id="plantdocModeSymptoms" style="<?= $initialMode === 'symptoms' ? 'display: block;' : 'display: none;' ?>">
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="stat-icon-circle bg-light-warning text-warning" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="fa-solid fa-stethoscope"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Describe Symptoms</h5>
                                <span class="extra-small text-muted">Rule-based botanical pathology matching</span>
                            </div>
                        </div>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">
                            <i class="fa-solid fa-brain me-1"></i> Pathology Engine
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <!-- Select Plant -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">1. Select Plant / Crop</label>
                            <select class="form-select" id="symptomCropSelect">
                                <option value="">Choose Crop...</option>
                                <?php foreach ($crops as $c): ?>
                                    <option value="<?= e($c['crop_name']) ?>"><?= e($c['icon'] ?? '🌱') ?> <?= e($c['crop_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Symptom Chips -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark d-block">2. Observed Symptoms (Select all that apply)</label>
                            <div class="symptom-tag-cloud" id="symptomTagCloud">
                                <span class="symptom-tag-chip" data-tag="yellow">🍂 Yellowing / Chlorosis</span>
                                <span class="symptom-tag-chip" data-tag="brown spots">🟤 Brown Necrotic Spots</span>
                                <span class="symptom-tag-chip" data-tag="blight">🥀 Blight / Wilting</span>
                                <span class="symptom-tag-chip" data-tag="rings">🎯 Concentric Target Rings</span>
                                <span class="symptom-tag-chip" data-tag="powdery">⚪ White Powdery Coating</span>
                                <span class="symptom-tag-chip" data-tag="curl">🌀 Leaf Curling / Puckering</span>
                                <span class="symptom-tag-chip" data-tag="water soaked">💧 Water-Soaked Lesions</span>
                                <span class="symptom-tag-chip" data-tag="pustules">🔴 Rust Pustules</span>
                                <span class="symptom-tag-chip" data-tag="fruit rot">🍅 Sunken Fruit Lesions</span>
                            </div>
                        </div>

                        <!-- Custom Symptom Description -->
                        <div class="mb-3">
                            <label for="symptomTextInput" class="form-label small fw-bold text-dark">3. Additional Field Notes / Observations</label>
                            <textarea class="form-control" id="symptomTextInput" rows="2" placeholder="e.g. Lower leaves turning yellow with brown spots, humid rainy weather..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-leaf text-white fw-bold py-2 px-4 rounded-pill shadow-sm" onclick="analyzeSymptoms()">
                                <i class="fa-solid fa-magnifying-glass me-2"></i> Analyze Symptoms
                            </button>
                        </div>

                        <!-- Symptom Match Results Box -->
                        <div id="symptomResultArea" class="mt-4" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- Helpful Photography Tips Card -->
            <div class="card card-custom border-0 shadow-sm bg-white p-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="stat-icon-circle bg-light-success text-success me-3" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fa-solid fa-lightbulb"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold text-forest mb-0">Field Photography Recommendations</h6>
                        <span class="extra-small text-muted">Follow these 3 simple rules for 95%+ accurate diagnosis</span>
                    </div>
                </div>
                <div class="row g-2 text-start">
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded-3 h-100">
                            <span class="small fw-bold text-dark d-block">1. Target Disease</span>
                            <span class="extra-small text-muted">Center the lens directly on the infected foliage patch.</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded-3 h-100">
                            <span class="small fw-bold text-dark d-block">2. Diffused Daylight</span>
                            <span class="extra-small text-muted">Capture under morning or afternoon shade. Avoid harsh midday glare.</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded-3 h-100">
                            <span class="small fw-bold text-dark d-block">3. Fill 50%+ Frame</span>
                            <span class="extra-small text-muted">Ensure leaf fills at least half of the frame with sharp focus.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OpenCropCare feature grid -->
            <div class="plantdoc-features-section">
                <div class="text-center mb-3">
                    <h3 class="fw-bold plantdoc-title mb-1">Why Choose OpenCropCare?</h3>
                    <p class="extra-small text-muted">Intelligent AI agriculture designed for practical field conditions</p>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="plantdoc-feature-card">
                            <div class="feature-icon-circle">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <div class="plantdoc-feature-title">Accurate Detection</div>
                            <p class="plantdoc-feature-desc">Deep learning computer vision trained on thousands of crop disease samples.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="plantdoc-feature-card">
                            <div class="feature-icon-circle">
                                <i class="fa-solid fa-bolt text-warning"></i>
                            </div>
                            <div class="plantdoc-feature-title">Instant Results</div>
                            <p class="plantdoc-feature-desc">Receive diagnosis and localized treatment recommendations within seconds.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="plantdoc-feature-card">
                            <div class="feature-icon-circle">
                                <i class="fa-solid fa-seedling text-success"></i>
                            </div>
                            <div class="plantdoc-feature-title">Dual Remedies</div>
                            <p class="plantdoc-feature-desc">Organic cultural remedies alongside safe chemical intervention guidelines.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="plantdoc-feature-card">
                            <div class="feature-icon-circle">
                                <i class="fa-solid fa-sun text-warning"></i>
                            </div>
                            <div class="plantdoc-feature-title">Field Ready</div>
                            <p class="plantdoc-feature-desc">High-contrast outdoor sunlight mode with multilingual support for farmers.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Camera WebRTC Capture Modal -->
<div class="modal fade" id="cameraCaptureModal" tabindex="-1" aria-labelledby="camModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-forest text-white py-2">
                <h6 class="modal-title fw-bold" id="camModalLabel"><i class="fa-solid fa-camera me-2 text-leaf-light"></i>Live Leaf Camera</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-black">
                <div class="camera-view-container position-relative">
                    <video id="cameraVideo" autoplay playsinline style="width: 100%; max-height: 380px; object-fit: cover;"></video>
                </div>
            </div>
            <div class="modal-footer justify-content-center p-3 bg-light">
                <button type="button" class="btn btn-leaf text-white fw-bold py-2 px-4 rounded-pill shadow-sm" id="btnSnapPhoto">
                    <i class="fa-solid fa-circle-dot me-2"></i> Snap Leaf Photo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Embedded Disease Knowledge for Instant Symptom Matching -->
<script>
var PLANTDOC_DISEASES = <?= json_encode($diseases ?? []) ?>;

function switchPlantDocMode(mode) {
    var upPane = document.getElementById('plantdocModeUpload');
    var symPane = document.getElementById('plantdocModeSymptoms');
    var upBtn = document.getElementById('tabBtnUpload');
    var symBtn = document.getElementById('tabBtnSymptoms');

    if (mode === 'upload') {
        upPane.style.display = 'block';
        symPane.style.display = 'none';
        upBtn.classList.add('active');
        symBtn.classList.remove('active');
    } else {
        upPane.style.display = 'none';
        symPane.style.display = 'block';
        symBtn.classList.add('active');
        upBtn.classList.remove('active');
    }
}

// Symptom Chip Toggle
document.addEventListener('DOMContentLoaded', function() {
    var chips = document.querySelectorAll('.symptom-tag-chip');
    chips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            this.classList.toggle('selected');
        });
    });

    // Crop Chip Selection Sync
    var cropChips = document.querySelectorAll('.crop-chip-item');
    var cropSelect = document.getElementById('crop_name');
    cropChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            cropChips.forEach(function(c) { c.classList.remove('active'); });
            this.classList.add('active');
            var val = this.getAttribute('data-crop');
            if (cropSelect) {
                cropSelect.value = val;
            }
        });
    });

    // File Preview & Upload Logic
    var fileInput = document.getElementById('leaf_image');
    var dropContent = document.getElementById('dropZoneContent');
    var previewContainer = document.getElementById('previewContainer');
    var imagePreview = document.getElementById('imagePreview');
    var btnRemove = document.getElementById('btnRemoveImage');
    var form = document.getElementById('predictForm');
    var btnSubmit = document.getElementById('btnSubmit');
    var submitLoader = document.getElementById('submitLoader');
    var scanWrapper = document.getElementById('scanPreviewWrapper');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(evt) {
                    imagePreview.src = evt.target.result;
                    previewContainer.style.display = 'block';
                    dropContent.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (btnRemove) {
        btnRemove.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (fileInput) fileInput.value = '';
            previewContainer.style.display = 'none';
            dropContent.style.display = 'block';
        });
    }

    if (form) {
        form.addEventListener('submit', function() {
            btnSubmit.style.display = 'none';
            submitLoader.style.display = 'block';
            if (scanWrapper) scanWrapper.classList.add('is-scanning');

            var steps = [
                'Analyzing color histograms & contrast...',
                'Detecting foliar necrotic lesions...',
                'Querying trained Neural Diagnostic Model...',
                'Synthesizing localized treatment advisory...'
            ];
            var stepIndex = 0;
            var subMsg = document.getElementById('analysisStatusSub');
            setInterval(function() {
                if (stepIndex < steps.length && subMsg) {
                    subMsg.innerText = steps[stepIndex];
                    stepIndex++;
                }
            }, 600);
        });
    }
});

// Interactive Symptom Pathology Analyzer
function analyzeSymptoms() {
    var crop = document.getElementById('symptomCropSelect').value.toLowerCase();
    var notes = (document.getElementById('symptomTextInput').value || '').toLowerCase();
    var selectedTags = [];
    document.querySelectorAll('.symptom-tag-chip.selected').forEach(function(el) {
        selectedTags.push(el.getAttribute('data-tag'));
    });

    var resultArea = document.getElementById('symptomResultArea');
    resultArea.style.display = 'block';

    if (!crop && selectedTags.length === 0 && !notes) {
        resultArea.innerHTML = '<div class="alert alert-warning border-0 small rounded-3"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please select a crop or at least one observed symptom.</div>';
        return;
    }

    // Match against database diseases
    var matches = [];
    PLANTDOC_DISEASES.forEach(function(d) {
        var score = 0;
        var dCrop = (d.crop_name || '').toLowerCase();
        var dName = (d.disease_name || '').toLowerCase();
        var dSym = (d.symptoms || '').toLowerCase();
        var dCauses = (d.causes || '').toLowerCase();

        if (crop && dCrop.includes(crop)) {
            score += 35;
        }

        selectedTags.forEach(function(tag) {
            if (dSym.includes(tag) || dName.includes(tag) || dCauses.includes(tag)) {
                score += 20;
            }
        });

        if (notes) {
            var words = notes.split(/\s+/);
            words.forEach(function(w) {
                if (w.length > 3 && (dSym.includes(w) || dName.includes(w))) {
                    score += 10;
                }
            });
        }

        if (score > 15) {
            matches.push({ disease: d, score: Math.min(score, 98) });
        }
    });

    matches.sort(function(a, b) { return b.score - a.score; });

    if (matches.length === 0) {
        resultArea.innerHTML = '<div class="alert alert-info border-0 small rounded-3"><i class="fa-solid fa-circle-info me-1"></i> No exact foliar condition matched these specific symptoms. Try adding more observations or scan a leaf photo above.</div>';
        return;
    }

    var top = matches[0].disease;
    var conf = matches[0].score;

    var html = '<div class="card border border-success-subtle shadow-sm rounded-3 overflow-hidden">';
    html += '<div class="card-header bg-gradient-leaf text-white py-2 px-3 d-flex justify-content-between align-items-center">';
    html += '<span class="fw-bold"><i class="fa-solid fa-circle-check me-2 text-warning"></i> Probable Match: ' + (top.disease_name || 'Condition') + '</span>';
    html += '<span class="badge bg-white text-forest fw-bold rounded-pill">' + conf + '% Match</span>';
    html += '</div>';
    html += '<div class="card-body p-3">';
    html += '<div class="row g-2 mb-3 text-muted extra-small">';
    html += '<div class="col-6"><strong>Crop:</strong> ' + (top.crop_name || 'General') + '</div>';
    html += '<div class="col-6"><strong>Pathogen:</strong> ' + (top.pathogen_type || 'Foliar') + '</div>';
    html += '</div>';
    
    html += '<div class="mb-3">';
    html += '<div class="fw-bold text-dark small mb-1"><i class="fa-solid fa-eye text-success me-1"></i> Observed Symptoms:</div>';
    html += '<p class="extra-small text-muted mb-0">' + (top.symptoms || 'Symptoms recorded in knowledge base.') + '</p>';
    html += '</div>';

    html += '<div class="treatment-card-organic p-2 mb-2 rounded-2">';
    html += '<div class="treatment-title-organic small"><i class="fa-solid fa-leaf"></i> 🌿 Organic Treatment (Recommended):</div>';
    html += '<p class="extra-small text-dark mb-0">' + (top.organic_remedies || top.management || 'Apply neem oil formulation, maintain aeration, remove diseased bottom leaves.') + '</p>';
    html += '</div>';

    html += '<div class="treatment-card-chemical p-2 rounded-2">';
    html += '<div class="treatment-title-chemical small"><i class="fa-solid fa-flask"></i> 🧪 Chemical Treatment:</div>';
    html += '<p class="extra-small text-dark mb-0">' + (top.chemical_remedies || 'If severity exceeds 20%, apply targeted copper-based fungicide or bactericide following safety PHI instructions.') + '</p>';
    html += '</div>';

    html += '<div class="text-end mt-2">';
    html += '<a href="index.php?route=view_disease&id=' + encodeURIComponent(top._id || '') + '" class="btn btn-sm btn-outline-leaf rounded-pill px-3 extra-small">View Full Pathology Details <i class="fa-solid fa-arrow-right ms-1"></i></a>';
    html += '</div>';

    html += '</div></div>';

    resultArea.innerHTML = html;
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
