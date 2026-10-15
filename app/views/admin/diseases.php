<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Plant Doc Admin Header -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="plantdoc-hero-badge mb-2">
                    <i class="fa-solid fa-book-medical text-success"></i>
                    <span>Administrator Portal &bull; Knowledge Base &bull; <?= date('l, d F Y') ?></span>
                </div>
                <h2 class="plantdoc-why-heading text-start mb-1">
                    Manage Disease Knowledge Base
                </h2>
                <p class="plantdoc-main-sub text-start mb-0" style="font-size: 0.88rem;">
                    Create, update, and manage symptoms, treatments, and prevention guidance in Knowledge Base.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-md-end flex-shrink-0">
                <button class="btn btn-plantdoc-primary" data-bs-toggle="modal" data-bs-target="#diseaseModal" onclick="clearDiseaseModal()" style="padding: 7px 18px; font-size: 0.85rem;">
                    <i class="fa-solid fa-plus me-1"></i>
                    <span>Add Disease</span>
                </button>
                <a href="index.php?route=admin_dashboard" class="btn btn-plantdoc-outline" style="padding: 7px 18px; font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    <span>Admin Overview</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Diseases Table Card -->
    <div class="card card-custom border-0 shadow-sm overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3 py-3">Crop</th>
                            <th class="py-3">Disease Name</th>
                            <th class="py-3">Pathogen</th>
                            <th class="py-3">Severity</th>
                            <th class="py-3">Last Reviewed</th>
                            <th class="text-end pe-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diseases as $d): ?>
                            <tr>
                                <td class="ps-3"><span class="badge bg-success-subtle text-success fw-bold"><?= e($d['crop_name']) ?></span></td>
                                <td class="fw-bold text-dark"><?= e($d['disease_name']) ?></td>
                                <td class="small text-muted"><?= e($d['pathogen_type']) ?></td>
                                <td>
                                    <span class="badge <?= ($d['severity'] === 'Critical') ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' ?> fw-semibold">
                                        <?= e($d['severity']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= e($d['last_reviewed'] ?? '2026-08-01') ?></td>
                                <td class="text-end pe-3">
                                    <a href="index.php?route=view_disease&id=<?= urlencode($d['_id']) ?>" class="btn btn-sm btn-plantdoc-outline" title="Preview Guide" style="padding: 4px 10px; font-size: 0.78rem;">
                                        <i class="fa-solid fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add / Edit Disease Modal -->
<div class="modal fade" id="diseaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-3" style="background: var(--color-primary); color: #ffffff;">
                <h5 class="modal-title fw-bold" id="modalTitle">Add Disease Knowledge Base Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=admin_save_disease" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="disease_id" id="modalDiseaseId" value="">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Crop Name *</label>
                            <select name="crop_name" class="form-select form-select-sm" required style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                                <?php foreach ($crops as $c): ?>
                                    <option value="<?= e($c['crop_name']) ?>"><?= e($c['crop_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Disease Name *</label>
                            <input type="text" name="disease_name" class="form-control form-control-sm" placeholder="e.g. Tomato Early Blight" required style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Pathogen Type</label>
                            <input type="text" name="pathogen_type" class="form-control form-control-sm" placeholder="e.g. Fungal (Alternaria solani)" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Severity</label>
                            <select name="severity" class="form-select form-select-sm" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                                <option value="Low">Low</option>
                                <option value="Moderate" selected>Moderate</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Overview Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Brief disease summary..." style="border-radius: var(--radius-sm); border-color: var(--color-border);"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Visible Symptoms</label>
                        <textarea name="symptoms" class="form-control form-control-sm" rows="2" placeholder="Describe spots, lesions, yellowing..." style="border-radius: var(--radius-sm); border-color: var(--color-border);"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Possible Causes</label>
                        <textarea name="causes" class="form-control form-control-sm" rows="2" placeholder="Environmental causes, vector insects, moisture..." style="border-radius: var(--radius-sm); border-color: var(--color-border);"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">General Management Practices</label>
                        <textarea name="management" class="form-control form-control-sm" rows="2" placeholder="Organic remedies, bio-fungicides, pruning..." style="border-radius: var(--radius-sm); border-color: var(--color-border);"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Cultural Prevention Measures</label>
                        <textarea name="prevention" class="form-control form-control-sm" rows="2" placeholder="Crop rotation, drip irrigation, resistant varieties..." style="border-radius: var(--radius-sm); border-color: var(--color-border);"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">References</label>
                        <input type="text" name="references" class="form-control form-control-sm" placeholder="e.g. ICAR / Extension Agricultural Advisory" style="border-radius: var(--radius-sm); border-color: var(--color-border);">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-plantdoc-outline" data-bs-dismiss="modal" style="padding: 6px 16px;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-plantdoc-primary" style="padding: 6px 18px;">Save to Knowledge Base</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearDiseaseModal() {
    document.getElementById('modalDiseaseId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Disease Knowledge Base Entry';
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
