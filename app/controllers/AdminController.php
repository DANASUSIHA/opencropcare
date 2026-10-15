<?php
/**
 * OpenCropCare - Admin Management Controller
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Crop.php';
require_once __DIR__ . '/../models/Disease.php';
require_once __DIR__ . '/../models/Prediction.php';
require_once __DIR__ . '/../models/Feedback.php';
require_once __DIR__ . '/../middleware/AdminMiddleware.php';

class AdminController {
    private User $userModel;
    private Crop $cropModel;
    private Disease $diseaseModel;
    private Prediction $predictionModel;
    private Feedback $feedbackModel;

    public function __construct() {
        $this->userModel = new User();
        $this->cropModel = new Crop();
        $this->diseaseModel = new Disease();
        $this->predictionModel = new Prediction();
        $this->feedbackModel = new Feedback();
    }

    public function dashboard(): void {
        AdminMiddleware::check();

        $stats = $this->predictionModel->getStats();
        $users = $this->userModel->getAll();
        $crops = $this->cropModel->getAll();
        $diseases = $this->diseaseModel->getAll();
        $feedbackList = $this->feedbackModel->getAll(10);

        $pageTitle = 'Admin Dashboard - OpenCropCare';
        require APP_PATH . '/views/admin/dashboard.php';
    }

    public function users(): void {
        AdminMiddleware::check();

        $users = $this->userModel->getAll(100);
        $pageTitle = 'Manage Users - Admin';
        require APP_PATH . '/views/admin/users.php';
    }

    public function deleteUser(): void {
        AdminMiddleware::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Invalid CSRF token.";
            header('Location: index.php?route=admin_users');
            exit();
        }

        $id = trim($_POST['user_id'] ?? '');
        if ($id === AuthMiddleware::userId()) {
            $_SESSION['flash_error'] = "You cannot delete your own admin account.";
            header('Location: index.php?route=admin_users');
            exit();
        }

        $this->userModel->delete($id);
        $_SESSION['flash_success'] = "User account removed.";
        header('Location: index.php?route=admin_users');
        exit();
    }

    public function diseases(): void {
        AdminMiddleware::check();

        $diseases = $this->diseaseModel->getAll(100);
        $crops = $this->cropModel->getAll();
        $pageTitle = 'Manage Disease Knowledge Base - Admin';
        require APP_PATH . '/views/admin/diseases.php';
    }

    public function saveDisease(): void {
        AdminMiddleware::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Invalid CSRF token.";
            header('Location: index.php?route=admin_diseases');
            exit();
        }

        $data = [
            '_id' => !empty($_POST['disease_id']) ? $_POST['disease_id'] : null,
            'crop_name' => trim($_POST['crop_name'] ?? ''),
            'disease_name' => trim($_POST['disease_name'] ?? ''),
            'pathogen_type' => trim($_POST['pathogen_type'] ?? 'Fungal'),
            'severity' => trim($_POST['severity'] ?? 'Moderate'),
            'description' => trim($_POST['description'] ?? ''),
            'symptoms' => trim($_POST['symptoms'] ?? ''),
            'causes' => trim($_POST['causes'] ?? ''),
            'prevention' => trim($_POST['prevention'] ?? ''),
            'management' => trim($_POST['management'] ?? ''),
            'references' => trim($_POST['references'] ?? '')
        ];

        try {
            $this->diseaseModel->save($data);
            $_SESSION['flash_success'] = "Disease knowledge base updated successfully.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: index.php?route=admin_diseases');
        exit();
    }

    public function feedback(): void {
        AdminMiddleware::check();

        $feedbacks = $this->feedbackModel->getAll(100);
        $pageTitle = 'Farmer Feedback Queue - Admin';
        require APP_PATH . '/views/admin/feedback.php';
    }
}
