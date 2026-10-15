<?php
/**
 * OpenCropCare - Prediction Model
 */

require_once __DIR__ . '/../services/CouchDBService.php';

class Prediction {
    private CouchDBService $couch;

    public function __construct() {
        $this->couch = new CouchDBService();
    }

    public function create(array $data): array {
        $predictionDoc = [
            '_id' => 'pred_' . bin2hex(random_bytes(8)),
            'type' => 'prediction',
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'] ?? 'Farmer',
            'user_district' => $data['user_district'] ?? '',
            'crop_name' => $data['crop_name'],
            'image_path' => $data['image_path'],
            'image_filename' => $data['image_filename'],
            'predicted_disease' => $data['predicted_disease'],
            'confidence' => (float)$data['confidence'],
            'top_3' => $data['top_3'] ?? [],
            'is_healthy' => (bool)($data['is_healthy'] ?? false),
            'uncertainty_flag' => (bool)($data['uncertainty_flag'] ?? false),
            'uncertainty_message' => $data['uncertainty_message'] ?? null,
            'disease_id' => $data['disease_id'] ?? null,
            'model_version' => $data['model_version'] ?? 'NeuralVision-CropV1.0',
            'created_at' => date('c')
        ];

        return $this->couch->saveDoc($predictionDoc);
    }

    public function findById(string $id): ?array {
        $doc = $this->couch->getDoc($id);
        return ($doc && ($doc['type'] ?? '') === 'prediction') ? $doc : null;
    }

    public function getByUser(string $userId, int $limit = 100): array {
        return $this->couch->findByType('prediction', ['user_id' => $userId], $limit);
    }

    public function getAll(int $limit = 200): array {
        return $this->couch->findByType('prediction', [], $limit);
    }

    public function searchUserPredictions(string $userId, ?string $crop = null, ?string $query = null): array {
        $predictions = $this->getByUser($userId, 200);

        if ($crop && $crop !== 'all') {
            $predictions = array_filter($predictions, fn($p) => strcasecmp($p['crop_name'] ?? '', $crop) === 0);
        }

        if ($query) {
            $q = strtolower(trim($query));
            $predictions = array_filter($predictions, function($p) use ($q) {
                return (strpos(strtolower($p['predicted_disease'] ?? ''), $q) !== false) ||
                       (strpos(strtolower($p['crop_name'] ?? ''), $q) !== false);
            });
        }

        return array_values($predictions);
    }

    public function delete(string $id, string $userId): bool {
        $pred = $this->findById($id);
        if (!$pred) return false;

        // Ensure users cannot delete others' records (unless admin)
        if (($pred['user_id'] ?? '') !== $userId && ($_SESSION['user']['role'] ?? '') !== 'admin') {
            throw new Exception("Unauthorized attempt to delete prediction record.");
        }

        // Remove uploaded image file if exists
        $filePath = PUBLIC_PATH . '/' . ($pred['image_path'] ?? '');
        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }

        return $this->couch->deleteDoc($id, $pred['_rev'] ?? null);
    }

    public function getStats(?string $userId = null): array {
        $predictions = $userId ? $this->getByUser($userId, 500) : $this->getAll(500);

        $totalScans = count($predictions);
        $healthyCount = 0;
        $diseasedCount = 0;
        $cropCounts = [];
        $diseaseCounts = [];

        foreach ($predictions as $p) {
            if (!empty($p['is_healthy']) || stripos($p['predicted_disease'] ?? '', 'healthy') !== false) {
                $healthyCount++;
            } else {
                $diseasedCount++;
            }

            $c = $p['crop_name'] ?? 'Other';
            $cropCounts[$c] = ($cropCounts[$c] ?? 0) + 1;

            $d = $p['predicted_disease'] ?? 'Unknown';
            $diseaseCounts[$d] = ($diseaseCounts[$d] ?? 0) + 1;
        }

        arsort($cropCounts);
        arsort($diseaseCounts);

        return [
            'total_scans' => $totalScans,
            'healthy_count' => $healthyCount,
            'diseased_count' => $diseasedCount,
            'crop_distribution' => $cropCounts,
            'disease_distribution' => $diseaseCounts,
            'recent_scans' => array_slice($predictions, 0, 5)
        ];
    }
}
