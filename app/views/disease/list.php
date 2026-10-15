<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Plant Doc Header -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="plantdoc-hero-badge mb-2">
                    <i class="fa-solid fa-book-open text-success"></i>
                    <span>Educational Resource & Knowledge Base</span>
                </div>
                <h2 class="plantdoc-why-heading text-start mb-1">
                    Plant Disease Library
                </h2>
                <p class="plantdoc-main-sub text-start mb-0" style="font-size: 0.88rem;">
                    Detailed information about crop diseases, symptoms, and organic remedies for farmers, students, and researchers.
                </p>
            </div>
            <div style="min-width: 260px;" class="flex-shrink-0">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 py-2" id="diseaseLiveSearch" placeholder="Search diseases or symptoms...">
                </div>
            </div>
        </div>
    </div>

    <!-- Crop Filter Pills -->
    <div class="mb-4 d-flex gap-2 flex-wrap">
        <a href="index.php?route=diseases" class="btn btn-sm rounded-pill px-3 <?= empty($selectedCrop) ? 'btn-plantdoc-primary' : 'btn-plantdoc-outline' ?>">
            All Crops (<?= count($diseases) ?>)
        </a>
        <?php foreach ($crops as $c): ?>
            <a href="index.php?route=diseases&crop=<?= urlencode($c['crop_name']) ?>" class="btn btn-sm rounded-pill px-3 <?= ($selectedCrop === $c['crop_name']) ? 'btn-plantdoc-primary' : 'btn-plantdoc-outline' ?>">
                <?= e($c['icon'] ?? '🌱') ?> <?= e($c['crop_name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Diseases Grid -->
    <div class="row g-4" id="diseaseGrid">
        <?php if (empty($diseases)): ?>
            <div class="col-12 text-center py-5 text-muted">
                <span class="fs-1 d-block mb-2">🌿</span>
                <h5 class="fw-bold">No Disease Profiles Found</h5>
                <p class="small">Try resetting your crop filter or searching with different keywords.</p>
                <a href="index.php?route=diseases" class="btn btn-sm btn-plantdoc-primary px-4">Show All Diseases</a>
            </div>
        <?php else: ?>
            <?php foreach ($diseases as $d): ?>
                <div class="col-md-6 col-lg-4 disease-card-item">
                    <div class="feature-card-box p-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="feature-icon-square mb-0" style="width: 38px; height: 38px;">
                                <i class="fa-solid fa-microscope"></i>
                            </div>
                            <div class="d-flex gap-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small px-2 py-1"><?= e($d['crop_name']) ?></span>
                                <span class="badge <?= ($d['severity'] ?? '') === 'Critical' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?> rounded-pill extra-small px-2 py-1">
                                    <?= e($d['severity'] ?? 'Moderate') ?>
                                </span>
                            </div>
                        </div>

                        <h4 class="feature-card-title text-start mb-1"><?= e($d['disease_name']) ?></h4>
                        <div class="extra-small text-muted mb-2">
                            <i class="fa-solid fa-dna text-success me-1"></i> <?= e($d['pathogen_type'] ?? 'Pathogen') ?>
                        </div>
                        <p class="feature-card-desc flex-grow-1 mb-3">
                            <?= e(substr($d['description'] ?? '', 0, 115)) ?>...
                        </p>
                        <a href="index.php?route=view_disease&id=<?= urlencode($d['_id']) ?>" class="btn btn-sm btn-plantdoc-outline w-100 mt-auto">
                            <i class="fa-solid fa-book-open me-1"></i> View Advisory Guide
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('diseaseLiveSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('.disease-card-item');
            items.forEach(function(item) {
                var text = item.innerText.toLowerCase();
                item.style.display = text.includes(q) ? 'block' : 'none';
            });
        });
    }
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
