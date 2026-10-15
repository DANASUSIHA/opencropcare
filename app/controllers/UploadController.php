<?php
/**
 * OpenCropCare - Image Upload & Disease Identification Controller
 */

require_once __DIR__ . '/../models/Crop.php';
require_once __DIR__ . '/../models/Disease.php';
require_once __DIR__ . '/../models/Prediction.php';
require_once __DIR__ . '/../services/MLService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class UploadController {
    private Crop $cropModel;
    private Disease $diseaseModel;
    private Prediction $predictionModel;
    private MLService $mlService;

    public function __construct() {
        $this->cropModel = new Crop();
        $this->diseaseModel = new Disease();
        $this->predictionModel = new Prediction();
        $this->mlService = new MLService();
    }

    public function showUpload(): void {
        if (empty($_SESSION['user'])) {
            $_SESSION['user'] = [
                '_id' => 'guest_farmer',
                'name' => 'Field Farmer',
                'email' => 'farmer@plantdoc.in',
                'role' => 'farmer',
                'district' => 'Salem'
            ];
        }

        $crops = $this->cropModel->getAll();
        $diseases = $this->diseaseModel->getAll();
        $pageTitle = "Analyze Plant";
        require APP_PATH . '/views/predict/upload.php';
    }

    public function handlePredict(): void {
        if (empty($_SESSION['user'])) {
            $_SESSION['user'] = [
                '_id' => 'guest_farmer',
                'name' => 'Field Farmer',
                'email' => 'farmer@plantdoc.in',
                'role' => 'farmer',
                'district' => 'Salem'
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Security validation failed. Please try again.";
            header('Location: index.php?route=identify');
            exit();
        }

        $cropName = trim($_POST['crop_name'] ?? '');
        if (empty($cropName)) {
            $_SESSION['flash_error'] = "Please select a crop type.";
            header('Location: index.php?route=identify');
            exit();
        }

        if (empty($_FILES['leaf_image']) || $_FILES['leaf_image']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "Please upload a valid crop leaf image file.";
            header('Location: index.php?route=identify');
            exit();
        }

        $file = $_FILES['leaf_image'];

        // 1. Validate File Size
        if ($file['size'] > MAX_UPLOAD_SIZE_BYTES) {
            $_SESSION['flash_error'] = "File size exceeds the maximum limit of 8MB.";
            header('Location: index.php?route=identify');
            exit();
        }

        // 2. Validate MIME Type & Extension
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($mime, ALLOWED_IMAGE_TYPES) || !in_array($ext, ALLOWED_IMAGE_EXTENSIONS)) {
            $_SESSION['flash_error'] = "Invalid image format. Allowed formats: JPG, JPEG, PNG, WebP.";
            header('Location: index.php?route=identify');
            exit();
        }

        // 3. Move to sanitized uploads directory with unique name
        $uniqueFilename = 'leaf_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $destinationPath = UPLOAD_DIR . '/' . $uniqueFilename;
        $relativeImagePath = 'uploads/' . $uniqueFilename;

        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            $_SESSION['flash_error'] = "Failed to store uploaded image on server. Please try again.";
            header('Location: index.php?route=identify');
            exit();
        }

        // 4. Run Machine Learning Inference
        try {
            $mlResult = $this->mlService->predict($destinationPath, $cropName);

            // 5. Look up Disease Knowledge Base in CouchDB
            $predictedDiseaseName = $mlResult['predicted_disease'];
            $diseaseInfo = $this->diseaseModel->matchPrediction($predictedDiseaseName, $cropName);

            // 6. Save Prediction Record in CouchDB
            $user = AuthMiddleware::user();
            $predictionRecord = $this->predictionModel->create([
                'user_id' => $user['_id'],
                'user_name' => $user['name'] ?? 'Farmer',
                'user_district' => $user['district'] ?? '',
                'crop_name' => $cropName,
                'image_path' => $relativeImagePath,
                'image_filename' => $uniqueFilename,
                'predicted_disease' => $predictedDiseaseName,
                'confidence' => $mlResult['confidence'] ?? 90.0,
                'top_3' => $mlResult['top_3'] ?? [],
                'is_healthy' => $mlResult['is_healthy'] ?? false,
                'uncertainty_flag' => $mlResult['uncertainty_flag'] ?? false,
                'uncertainty_message' => $mlResult['uncertainty_message'] ?? null,
                'disease_id' => $diseaseInfo['_id'] ?? null,
                'model_version' => $mlResult['model_version'] ?? 'NeuralVision-CropV1.0'
            ]);

            header('Location: index.php?route=result&id=' . urlencode($predictionRecord['_id']));
            exit();

        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Diagnosis Error: " . $e->getMessage();
            header('Location: index.php?route=identify');
            exit();
        }
    }

    public function showResult(): void {
        if (empty($_SESSION['user'])) {
            $_SESSION['user'] = [
                '_id' => 'guest_farmer',
                'name' => 'Field Farmer',
                'email' => 'farmer@plantdoc.in',
                'role' => 'farmer',
                'district' => 'Salem'
            ];
        }

        $id = trim($_GET['id'] ?? '');
        if (empty($id)) {
            header('Location: index.php?route=history');
            exit();
        }

        $prediction = $this->predictionModel->findById($id);
        if (!$prediction) {
            $_SESSION['flash_error'] = "Prediction record not found.";
            header('Location: index.php?route=history');
            exit();
        }

        // Retrieve disease knowledge base document
        $disease = null;
        if (!empty($prediction['disease_id'])) {
            $disease = $this->diseaseModel->findById($prediction['disease_id']);
        }
        if (!$disease) {
            $disease = $this->diseaseModel->matchPrediction($prediction['predicted_disease'], $prediction['crop_name']);
        }

        $pageTitle = __('result_title') . ' - ' . e($prediction['predicted_disease']);
        require APP_PATH . '/views/predict/result.php';
    }
}
