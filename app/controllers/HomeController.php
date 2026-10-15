<?php
/**
 * OpenCropCare / PlantDoc - Landing Page Controller
 */

require_once __DIR__ . '/../models/Crop.php';
require_once __DIR__ . '/../models/Disease.php';

class HomeController {
    private Crop $cropModel;
    private Disease $diseaseModel;

    public function __construct() {
        $this->cropModel = new Crop();
        $this->diseaseModel = new Disease();
    }

    public function index(): void {
        $crops = $this->cropModel->getAll();
        $diseases = $this->diseaseModel->getAll();
        $pageTitle = 'Plant Disease Detection';
        require APP_PATH . '/views/home/index.php';
    }
}
