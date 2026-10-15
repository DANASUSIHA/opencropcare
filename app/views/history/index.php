<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Plant Doc Header -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="plantdoc-hero-badge mb-2">
                    <i class="fa-solid fa-clock-rotate-left text-success"></i>
                    <span>Foliar Pathology Archive</span>
                </div>
                <h2 class="plantdoc-why-heading text-start mb-1">
                    Scan History
                </h2>
                <p class="plantdoc-main-sub text-start mb-0" style="font-size: 0.88rem;">
                    Browse and review all past foliar disease scans, neural model confidence scores, and verified treatments.
                </p>
            </div>
            <div class="flex-shrink-0">
                <a href="index.php?route=identify" class="btn btn-plantdoc-primary">
                    <span>Start Analysis</span>
                    <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Live Instant Search Bar -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <form action="index.php" method="GET" id="cropFilterForm">
                        <input type="hidden" name="route" value="history">
                        <select name="crop" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all">All Crops (<?= count($predictions) ?>)</option>
                            <?php foreach ($crops as $c): ?>
                                <option value="<?= e($c['crop_name']) ?>" <?= ($selectedCrop === $c['crop_name']) ? 'selected' : '' ?>>
                                    <?= e($c['crop_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 py-2" id="historyLiveSearch" placeholder="Type to instantly filter scan records by disease or crop...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Predictions History Table -->
    <div class="card card-custom border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <?php if (empty($predictions)): ?>
                <div class="p-5 text-center text-muted">
                    <span class="fs-1 d-block mb-2">📂</span>
                    <h5 class="fw-bold mb-1">No Scan History Found</h5>
                    <p class="small mb-3">No prediction records match your current filter criteria.</p>
                    <a href="index.php?route=identify" class="btn btn-sm btn-leaf text-white rounded-pill px-4"><?= __('btn_start_scan') ?></a>
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
                                <th><?= __('table_date') ?></th>
                                <th class="text-end pe-4"><?= __('table_actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($predictions as $scan): ?>
                                <tr class="history-table-row">
                                    <td class="ps-4">
                                        <a href="index.php?route=result&id=<?= urlencode($scan['_id']) ?>">
                                            <img src="<?= e($scan['image_path'] ?? 'img/placeholder.png') ?>" alt="Leaf" class="rounded-3 object-fit-cover shadow-xs" style="width: 52px; height: 52px;">
                                        </a>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= e($scan['crop_name']) ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($scan['is_healthy'])): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-check-circle me-1"></i>Healthy</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><?= e($scan['predicted_disease']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 70px;">
                                                <div class="progress-bar bg-success" style="width: <?= (float)$scan['confidence'] ?>%"></div>
                                            </div>
                                            <span class="small fw-bold text-muted"><?= round((float)$scan['confidence'], 1) ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="small text-muted"><?= date('M d, Y', strtotime($scan['created_at'])) ?></span>
                                        <div class="extra-small text-muted"><?= date('h:i A', strtotime($scan['created_at'])) ?></div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?route=result&id=<?= urlencode($scan['_id']) ?>" class="btn btn-outline-success rounded-pill px-3 me-1" title="<?= __('btn_view') ?>">
                                                <i class="fa-solid fa-eye me-1"></i> View
                                            </a>
                                            <button type="button" class="btn btn-outline-danger rounded-pill px-2" onclick="confirmDeleteScan('<?= e($scan['_id']) ?>')" title="<?= __('btn_delete') ?>">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteScanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title fw-bold">Delete Scan Record</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fa-solid fa-trash-can text-danger display-4 mb-2"></i>
                <p class="small text-muted mb-0"><?= __('confirm_delete') ?></p>
            </div>
            <div class="modal-footer p-2 justify-content-center bg-light">
                <form action="index.php?route=delete_history" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="id" id="deleteScanId" value="">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-semibold rounded-pill px-3">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteScan(id) {
    document.getElementById('deleteScanId').value = id;
    var modal = new bootstrap.Modal(document.getElementById('deleteScanModal'));
    modal.show();
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
