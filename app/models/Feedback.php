<?php
/**
 * OpenCropCare - Feedback Model
 */

require_once __DIR__ . '/../services/CouchDBService.php';

class Feedback {
    private CouchDBService $couch;

    public function __construct() {
        $this->couch = new CouchDBService();
    }

    public function create(array $data): array {
        $feedbackDoc = [
            '_id' => 'fb_' . bin2hex(random_bytes(8)),
            'type' => 'feedback',
            'prediction_id' => $data['prediction_id'],
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'] ?? 'Farmer',
            'crop_name' => $data['crop_name'] ?? 'Unknown',
            'predicted_disease' => $data['predicted_disease'] ?? 'Unknown',
            'feedback_status' => in_array($data['feedback_status'], ['correct', 'incorrect', 'uncertain']) ? $data['feedback_status'] : 'correct',
            'suggested_disease' => trim($data['suggested_disease'] ?? ''),
            'comments' => trim($data['comments'] ?? ''),
            'status' => 'pending_expert_review',
            'created_at' => date('c')
        ];

        return $this->couch->saveDoc($feedbackDoc);
    }

    public function getAll(int $limit = 100): array {
        return $this->couch->findByType('feedback', [], $limit);
    }

    public function findByPrediction(string $predictionId): ?array {
        $docs = $this->couch->findByType('feedback', ['prediction_id' => $predictionId], 1);
        return $docs[0] ?? null;
    }

    public function delete(string $id): bool {
        $doc = $this->couch->getDoc($id);
        if (!$doc) return false;
        return $this->couch->deleteDoc($id, $doc['_rev'] ?? null);
    }
}
