<?php
/**
 * OpenCropCare - Farmer Dashboard Controller
 */

require_once __DIR__ . '/../models/Prediction.php';
require_once __DIR__ . '/../models/Crop.php';
require_once __DIR__ . '/../models/Disease.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DashboardController {
    private Prediction $predictionModel;
    private Crop $cropModel;
    private Disease $diseaseModel;

    public function __construct() {
        $this->predictionModel = new Prediction();
        $this->cropModel = new Crop();
        $this->diseaseModel = new Disease();
    }

    public function index(): void {
        AuthMiddleware::check();

        $userId = AuthMiddleware::userId();
        $stats = $this->predictionModel->getStats($userId);
        $crops = $this->cropModel->getAll();
        $diseases = $this->diseaseModel->getAll();

        $pageTitle = __('nav_dashboard');
        require APP_PATH . '/views/dashboard/index.php';
    }
}
