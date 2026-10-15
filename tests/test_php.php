<?php
/**
 * OpenCropCare - Automated PHP Test Suite
 * Tests:
 * 1. CouchDB Service CRUD & Query operations
 * 2. User Authentication (password_hash & verify)
 * 3. Crop & Disease Knowledge Base Lookups
 * 4. ML Service Integration & Fallback Mode
 * 5. Prediction Record Persistence & Stats Calculation
 * 6. Feedback Creation & Retrieval
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/services/TranslationService.php';
require_once __DIR__ . '/../app/services/CouchDBService.php';
require_once __DIR__ . '/../app/services/MLService.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Crop.php';
require_once __DIR__ . '/../app/models/Disease.php';
require_once __DIR__ . '/../app/models/Prediction.php';
require_once __DIR__ . '/../app/models/Feedback.php';

echo "==================================================\n";
echo "🌿 RUNNING OPENCROPCARE PHP TEST SUITE\n";
echo "==================================================\n\n";

$passCount = 0;
$failCount = 0;

function assert_test(string $name, bool $condition, string $msg = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo "  [PASS] $name\n";
    } else {
        $failCount++;
        echo "  [FAIL] $name: $msg\n";
    }
}

// 1. CouchDB / Document Store Test
echo "[1/6] Testing CouchDB NoSQL Service...\n";
$couch = new CouchDBService();
$testDoc = [
    '_id' => 'test_doc_001',
    'type' => 'test_item',
    'name' => 'Sample Sensor Node',
    'status' => 'active'
];
$saved = $couch->saveDoc($testDoc);
assert_test("Document Save", $saved['_id'] === 'test_doc_001');

$retrieved = $couch->getDoc('test_doc_001');
assert_test("Document Retrieval", ($retrieved['name'] ?? '') === 'Sample Sensor Node');

$deleted = $couch->deleteDoc('test_doc_001');
assert_test("Document Deletion", $deleted === true);

// 2. User Model & Authentication Test
echo "\n[2/6] Testing User Authentication & RBAC...\n";
$userModel = new User();
$farmer = $userModel->findByEmail('farmer@opencropcare.org');
assert_test("Find User By Email", $farmer !== null && $farmer['role'] === 'farmer');

$authSuccess = $userModel->authenticate('farmer@opencropcare.org', 'farmer123');
assert_test("Farmer Password Verification (Valid)", $authSuccess !== null);

$authFail = $userModel->authenticate('farmer@opencropcare.org', 'wrongpass');
assert_test("Farmer Password Verification (Invalid)", $authFail === null);

// 3. Crops & Disease Knowledge Base Test
echo "\n[3/6] Testing Crop & Disease Knowledge Base...\n";
$cropModel = new Crop();
$crops = $cropModel->getAll();
assert_test("Retrieve All Crops", count($crops) >= 5);

$diseaseModel = new Disease();
$diseases = $diseaseModel->getAll();
assert_test("Retrieve All Diseases", count($diseases) >= 6);

$matched = $diseaseModel->matchPrediction('Tomato Early Blight', 'Tomato');
assert_test("Fuzzy Match Prediction to Knowledge Base", $matched !== null && strpos($matched['disease_name'], 'Early Blight') !== false);
assert_test("Symptom & Management Content Present", !empty($matched['symptoms']) && !empty($matched['management']));

// 4. ML Service Test
echo "\n[4/6] Testing ML Service & Fallback Classifier...\n";
$mlService = new MLService();

// Create temporary synthetic test JPEG image without relying on GD extension
$tmpImgPath = __DIR__ . '/test_leaf.jpg';
$rawJpeg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAACAAIBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
file_put_contents($tmpImgPath, $rawJpeg);

$mlResult = $mlService->predict($tmpImgPath, 'Tomato');
assert_test("ML Inference / Fallback Result", !empty($mlResult['predicted_disease']) && isset($mlResult['confidence']));
assert_test("Top-3 Candidates Present", count($mlResult['top_3']) === 3);

// 5. Prediction Record Persistence & Stats
echo "\n[5/6] Testing Prediction Model & History...\n";
$predictionModel = new Prediction();
$predDoc = $predictionModel->create([
    'user_id' => $farmer['_id'],
    'user_name' => $farmer['name'],
    'crop_name' => 'Tomato',
    'image_path' => 'uploads/test_leaf.jpg',
    'image_filename' => 'test_leaf.jpg',
    'predicted_disease' => 'Tomato Early Blight',
    'confidence' => 91.5,
    'top_3' => $mlResult['top_3'],
    'is_healthy' => false
]);
assert_test("Prediction Record Created", !empty($predDoc['_id']));

$userHistory = $predictionModel->getByUser($farmer['_id']);
assert_test("Retrieve Farmer Scan History", count($userHistory) >= 1);

$stats = $predictionModel->getStats($farmer['_id']);
assert_test("Dashboard Statistics Generated", $stats['total_scans'] >= 1);

// 6. Feedback Model Test
echo "\n[6/6] Testing Feedback Model...\n";
$feedbackModel = new Feedback();
$fb = $feedbackModel->create([
    'prediction_id' => $predDoc['_id'],
    'user_id' => $farmer['_id'],
    'user_name' => $farmer['name'],
    'crop_name' => 'Tomato',
    'predicted_disease' => 'Tomato Early Blight',
    'feedback_status' => 'correct',
    'comments' => 'Clear match with field leaf symptoms.'
]);
assert_test("Feedback Recorded", !empty($fb['_id']));

$allFeedback = $feedbackModel->getAll();
assert_test("Retrieve All Feedback Submissions", count($allFeedback) >= 1);

// Cleanup temporary test image
@unlink($tmpImgPath);

echo "\n==================================================\n";
echo "SUMMARY: Passed: $passCount | Failed: $failCount\n";
if ($failCount === 0) {
    echo "🎉 ALL PHP INTEGRATION TESTS PASSED SUCCESSFULLY!\n";
}
echo "==================================================\n";
