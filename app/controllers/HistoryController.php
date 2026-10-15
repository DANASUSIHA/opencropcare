<?php
/**
 * OpenCropCare - Scan History Controller
 */

require_once __DIR__ . '/../models/Prediction.php';
require_once __DIR__ . '/../models/Crop.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class HistoryController {
    private Prediction $predictionModel;
    private Crop $cropModel;

    public function __construct() {
        $this->predictionModel = new Prediction();
        $this->cropModel = new Crop();
    }

    public function index(): void {
        AuthMiddleware::check();

        $userId = AuthMiddleware::userId();
        $selectedCrop = trim($_GET['crop'] ?? 'all');
        $searchQuery = trim($_GET['q'] ?? '');

        $predictions = $this->predictionModel->searchUserPredictions($userId, $selectedCrop, $searchQuery);
        $crops = $this->cropModel->getAll();

        $pageTitle = __('history_title');
        require APP_PATH . '/views/history/index.php';
    }

    public function delete(): void {
        AuthMiddleware::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Security token validation failed.";
            header('Location: index.php?route=history');
            exit();
        }

        $id = trim($_POST['id'] ?? '');
        $userId = AuthMiddleware::userId();

        try {
            $deleted = $this->predictionModel->delete($id, $userId);
            if ($deleted) {
                $_SESSION['flash_success'] = "Prediction history record removed successfully.";
            } else {
                $_SESSION['flash_error'] = "Could not delete the record.";
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: index.php?route=history');
        exit();
    }
}
