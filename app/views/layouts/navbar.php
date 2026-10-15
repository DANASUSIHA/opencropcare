<?php
$isLoggedInUser = !empty($_SESSION['user']) && ($_SESSION['user']['_id'] ?? '') !== 'guest_farmer';
$currentRoute = $_GET['route'] ?? 'login';
$brandRoute = $isLoggedInUser
    ? (AuthMiddleware::isAdmin() ? 'admin_dashboard' : 'dashboard')
    : ($currentRoute === 'admin_login' ? 'admin_login' : 'login');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-2 border-bottom border-light shadow-xs" style="min-height: 64px;">
    <div class="container px-3 px-xl-4">
        <a class="navbar-brand d-flex align-items-center gap-2 py-0 text-decoration-none" href="index.php?route=<?= e($brandRoute) ?>">
            <span class="plantdoc-nav-emblem">
                <i class="fa-solid fa-leaf text-white"></i>
            </span>
            <span class="plantdoc-nav-title">OpenCropCare</span>
        </a>

        <?php if ($isLoggedInUser): ?>
            <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php endif; ?>

        <div class="collapse navbar-collapse" id="navbarMain">
            <?php if ($isLoggedInUser): ?>
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-3">
                    <?php if (AuthMiddleware::isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link px-2 fw-semibold <?= is_active_route('admin_dashboard') ?>" href="index.php?route=admin_dashboard">
                            <i class="fa-solid fa-chart-line me-1"></i> Admin Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 <?= is_active_route('admin_users') ?>" href="index.php?route=admin_users">
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 <?= is_active_route('admin_diseases') ?>" href="index.php?route=admin_diseases">
                            Knowledge Base
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 <?= is_active_route('admin_feedback') ?>" href="index.php?route=admin_feedback">
                            Feedback
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link px-2 <?= is_active_route('dashboard') ?>" href="index.php?route=dashboard">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 <?= is_active_route('history') ?>" href="index.php?route=history">
                            History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 <?= is_active_route('diseases') ?>" href="index.php?route=diseases">
                            Disease Library
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 <?= is_active_route('help') ?>" href="index.php?route=help">
                            Guide
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

            <!-- Right-Hand Actions (Farmer & Admin Login Access) -->
            <div class="d-flex align-items-center gap-2 gap-xl-3 ms-lg-auto mt-2 mt-lg-0">
                <?php if (!$isLoggedInUser): ?>
                    <?php if ($currentRoute !== 'admin_login'): ?>
                        <a href="index.php?route=register" class="btn btn-plantdoc-nav nav-create-account">
                            Create account
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Language Switcher -->
                    <div class="dropdown">
                        <button class="btn btn-sm nav-util-btn dropdown-toggle d-flex align-items-center gap-1 py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-globe text-muted"></i>
                            <span class="small fw-semibold"><?= TranslationService::getLanguage() === 'ta' ? 'தமிழ்' : 'EN' ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between <?= TranslationService::getLanguage() === 'en' ? 'active fw-bold' : '' ?>" href="index.php?route=set_lang&lang=en">
                                    <span>🇬🇧 English</span>
                                    <?php if (TranslationService::getLanguage() === 'en'): ?><i class="fa-solid fa-check small text-success ms-2"></i><?php endif; ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between <?= TranslationService::getLanguage() === 'ta' ? 'active fw-bold' : '' ?>" href="index.php?route=set_lang&lang=ta">
                                    <span>🇮🇳 தமிழ் (Tamil)</span>
                                    <?php if (TranslationService::getLanguage() === 'ta'): ?><i class="fa-solid fa-check small text-success ms-2"></i><?php endif; ?>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-leaf dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="user-avatar-badge"><?= e(strtoupper(substr($_SESSION['user']['name'] ?? 'F', 0, 1))) ?></span>
                            <span class="d-none d-sm-inline fw-semibold text-truncate" style="max-width: 110px;"><?= e($_SESSION['user']['role'] === 'admin' ? 'Admin' : (explode(' ', trim($_SESSION['user']['name'] ?? 'Farmer'))[0])) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="px-3 py-2 border-bottom bg-light rounded-top">
                                <div class="fw-bold text-dark"><?= e($_SESSION['user']['name']) ?></div>
                                <div class="small text-muted"><?= e($_SESSION['user']['email']) ?></div>
                            </li>
                            <li><a class="dropdown-item py-2" href="index.php?route=<?= AuthMiddleware::isAdmin() ? 'admin_dashboard' : 'dashboard' ?>"><i class="fa-solid fa-gauge me-2 text-success"></i><?= AuthMiddleware::isAdmin() ? 'Admin Overview' : 'Dashboard' ?></a></li>
                            <li><a class="dropdown-item py-2" href="index.php?route=profile"><i class="fa-solid fa-id-card me-2 text-success"></i>Profile</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item text-danger py-2" href="index.php?route=logout"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Global Flash Messages Notification Area -->
<div class="container mt-3">
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center rounded-3" role="alert">
            <i class="fa-solid fa-circle-check fs-5 text-success me-2"></i>
            <div class="fw-medium"><?= e($_SESSION['flash_success']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center rounded-3" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-5 text-danger me-2"></i>
            <div class="fw-medium"><?= e($_SESSION['flash_error']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
</div>
