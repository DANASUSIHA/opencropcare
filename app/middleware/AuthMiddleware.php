<?php
/**
 * OpenCropCare - Auth Middleware
 */

class AuthMiddleware {
    public static function check(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['_id'])) {
            $_SESSION['flash_error'] = "Please sign in to access this feature.";
            header('Location: index.php?route=login');
            exit();
        }
    }

    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function userId(): ?string {
        return $_SESSION['user']['_id'] ?? null;
    }

    public static function isAdmin(): bool {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }
}
