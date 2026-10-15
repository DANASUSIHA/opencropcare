<?php
/**
 * OpenCropCare - Crop Model
 */

require_once __DIR__ . '/../services/CouchDBService.php';

class Crop {
    private CouchDBService $couch;

    public function __construct() {
        $this->couch = new CouchDBService();
    }

    public function getAll(): array {
        return $this->couch->findByType('crop', [], 50);
    }

    public function findById(string $id): ?array {
        $doc = $this->couch->getDoc($id);
        return ($doc && ($doc['type'] ?? '') === 'crop') ? $doc : null;
    }

    public function findByName(string $name): ?array {
        $docs = $this->couch->findByType('crop', ['crop_name' => $name], 1);
        return $docs[0] ?? null;
    }

    public function save(array $data): array {
        $id = $data['_id'] ?? ('crop_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $data['crop_name'])));
        $existing = $this->couch->getDoc($id);

        $cropDoc = [
            '_id' => $id,
            'type' => 'crop',
            'crop_name' => trim($data['crop_name']),
            'scientific_name' => trim($data['scientific_name'] ?? ''),
            'icon' => $data['icon'] ?? '🌱',
            'description' => trim($data['description'] ?? ''),
            'supported_diseases' => is_array($data['supported_diseases'] ?? null) ? $data['supported_diseases'] : []
        ];

        if ($existing) {
            $cropDoc['_rev'] = $existing['_rev'];
        }

        return $this->couch->saveDoc($cropDoc);
    }

    public function delete(string $id): bool {
        $crop = $this->findById($id);
        if (!$crop) return false;
        return $this->couch->deleteDoc($id, $crop['_rev'] ?? null);
    }
}
