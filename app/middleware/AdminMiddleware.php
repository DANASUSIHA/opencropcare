<?php
/**
 * OpenCropCare - Admin Middleware
 */

require_once __DIR__ . '/AuthMiddleware.php';

class AdminMiddleware {
    public static function check(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['_id'])) {
            $_SESSION['flash_error'] = "Administrator sign-in required to access the Admin Panel.";
            header('Location: index.php?route=admin_login');
            exit();
        }
        if (!AuthMiddleware::isAdmin()) {
            $_SESSION['flash_error'] = "Administrator privileges required. Please sign in with an administrator account.";
            header('Location: index.php?route=admin_login');
            exit();
        }
    }
}
