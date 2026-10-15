<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-4">
    <!-- Plant Doc Admin Header -->
    <div class="card card-custom border-0 shadow-sm p-4 mb-4 bg-white position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="plantdoc-hero-badge mb-2">
                    <i class="fa-solid fa-users text-success"></i>
                    <span>Administrator Portal &bull; User Directory &bull; <?= date('l, d F Y') ?></span>
                </div>
                <h2 class="plantdoc-why-heading text-start mb-1">
                    User Management
                </h2>
                <p class="plantdoc-main-sub text-start mb-0" style="font-size: 0.88rem;">
                    Manage registered farmers, agricultural experts, and system administrators.
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

    <!-- Users Table Card -->
    <div class="card card-custom border-0 shadow-sm overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3 py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">District</th>
                            <th class="py-3">Joined Date</th>
                            <th class="text-end pe-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <?= e($u['name']) ?>
                                    <?php if ($u['_id'] === AuthMiddleware::userId()): ?>
                                        <span class="badge bg-success-subtle text-success ms-1">You</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($u['email']) ?></td>
                                <td>
                                    <span class="badge <?= ($u['role'] === 'admin') ? 'bg-danger-subtle text-danger' : (($u['role'] === 'expert') ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success') ?> fw-semibold">
                                        <?= e(ucfirst($u['role'])) ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= e(!empty($u['phone']) ? $u['phone'] : '—') ?></td>
                                <td class="small text-muted">
                                    <?php if (!empty($u['district'])): ?>
                                        <span class="badge bg-light text-dark border"><i class="fa-solid fa-location-dot text-danger me-1"></i><?= e($u['district']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= !empty($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—' ?></td>
                                <td class="text-end pe-3">
                                    <?php if ($u['_id'] !== AuthMiddleware::userId()): ?>
                                        <form action="index.php?route=admin_delete_user" method="POST" onsubmit="return confirm('Remove user account <?= e($u['name']) ?>?');" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="user_id" value="<?= e($u['_id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User" style="border-radius: 6px;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
