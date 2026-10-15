<?php
/**
 * OpenCropCare - Help & Awareness Controller
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class HelpController {
    public function index(): void {
        AuthMiddleware::check();
        $pageTitle = __('help_title');
        require APP_PATH . '/views/help/index.php';
    }
}
