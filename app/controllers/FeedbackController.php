<?php
/**
 * OpenCropCare - Feedback Controller
 */

require_once __DIR__ . '/../models/Feedback.php';
require_once __DIR__ . '/../models/Prediction.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class FeedbackController {
    private Feedback $feedbackModel;
    private Prediction $predictionModel;

    public function __construct() {
        $this->feedbackModel = new Feedback();
        $this->predictionModel = new Prediction();
    }

    public function submit(): void {
        AuthMiddleware::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Security token mismatch.";
            header('Location: index.php?route=history');
            exit();
        }

        $predictionId = trim($_POST['prediction_id'] ?? '');
        $feedbackStatus = trim($_POST['feedback_status'] ?? 'correct');
        $suggested = trim($_POST['suggested_disease'] ?? '');
        $comments = trim($_POST['comments'] ?? '');

        $prediction = $this->predictionModel->findById($predictionId);
        if (!$prediction) {
            $_SESSION['flash_error'] = "Prediction record not found.";
            header('Location: index.php?route=history');
            exit();
        }

        $user = AuthMiddleware::user();

        try {
            $this->feedbackModel->create([
                'prediction_id' => $predictionId,
                'user_id' => $user['_id'],
                'user_name' => $user['name'] ?? 'Farmer',
                'crop_name' => $prediction['crop_name'] ?? 'Unknown',
                'predicted_disease' => $prediction['predicted_disease'] ?? 'Unknown',
                'feedback_status' => $feedbackStatus,
                'suggested_disease' => $suggested,
                'comments' => $comments
            ]);

            $_SESSION['flash_success'] = "Thank you! Your feedback has been recorded for expert review and model evaluation.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Failed to record feedback: " . $e->getMessage();
        }

        header('Location: index.php?route=result&id=' . urlencode($predictionId));
        exit();
    }
}
