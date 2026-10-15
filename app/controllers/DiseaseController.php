<?php
/**
 * OpenCropCare - Disease Knowledge Base Controller
 */

require_once __DIR__ . '/../models/Disease.php';
require_once __DIR__ . '/../models/Crop.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DiseaseController {
    private Disease $diseaseModel;
    private Crop $cropModel;

    public function __construct() {
        $this->diseaseModel = new Disease();
        $this->cropModel = new Crop();
    }

    public function index(): void {
        AuthMiddleware::check();

        $selectedCrop = trim($_GET['crop'] ?? '');
        $diseases = $selectedCrop ? $this->diseaseModel->findByCrop($selectedCrop) : $this->diseaseModel->getAll();
        $crops = $this->cropModel->getAll();

        $pageTitle = __('nav_diseases');
        require APP_PATH . '/views/disease/list.php';
    }

    public function view(): void {
        AuthMiddleware::check();

        $id = trim($_GET['id'] ?? '');
        $disease = $this->diseaseModel->findById($id);

        if (!$disease) {
            $_SESSION['flash_error'] = "Disease entry not found.";
            header('Location: index.php?route=diseases');
            exit();
        }

        $pageTitle = e($disease['disease_name']) . ' - ' . __('nav_diseases');
        require APP_PATH . '/views/disease/view.php';
    }
}
