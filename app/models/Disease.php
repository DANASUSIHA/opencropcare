<?php
/**
 * OpenCropCare - Disease Model
 */

require_once __DIR__ . '/../services/CouchDBService.php';

class Disease {
    private CouchDBService $couch;

    public function __construct() {
        $this->couch = new CouchDBService();
    }

    public function getAll(int $limit = 100): array {
        return $this->couch->findByType('disease', [], $limit);
    }

    public function findById(string $id): ?array {
        $doc = $this->couch->getDoc($id);
        return ($doc && ($doc['type'] ?? '') === 'disease') ? $doc : null;
    }

    public function findByName(string $name): ?array {
        $docs = $this->couch->findByType('disease', ['disease_name' => $name], 1);
        return $docs[0] ?? null;
    }

    public function findByCrop(string $cropName): array {
        return $this->couch->findByType('disease', ['crop_name' => $cropName], 50);
    }

    public function matchPrediction(string $predictedClass, ?string $cropHint = null): ?array {
        // Direct match
        $all = $this->getAll();
        $predictedClean = strtolower(str_replace(['_', '-'], ' ', $predictedClass));

        foreach ($all as $d) {
            $dNameClean = strtolower($d['disease_name']);
            if (strpos($predictedClean, $dNameClean) !== false || strpos($dNameClean, $predictedClean) !== false) {
                return $d;
            }
        }

        // Fuzzy match with crop hint
        if ($cropHint) {
            $cropDiseases = $this->findByCrop($cropHint);
            if (!empty($cropDiseases)) {
                return $cropDiseases[0];
            }
        }

        // Return generic healthy entry or first entry
        return $this->findById('disease_healthy_leaf') ?? ($all[0] ?? null);
    }

    public function save(array $data): array {
        $id = $data['_id'] ?? ('disease_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $data['disease_name'])));
        $existing = $this->couch->getDoc($id);

        $diseaseDoc = [
            '_id' => $id,
            'type' => 'disease',
            'crop_name' => trim($data['crop_name']),
            'disease_name' => trim($data['disease_name']),
            'pathogen_type' => trim($data['pathogen_type'] ?? 'Unknown'),
            'severity' => trim($data['severity'] ?? 'Moderate'),
            'description' => trim($data['description'] ?? ''),
            'symptoms' => trim($data['symptoms'] ?? ''),
            'causes' => trim($data['causes'] ?? ''),
            'prevention' => trim($data['prevention'] ?? ''),
            'management' => trim($data['management'] ?? ''),
            'references' => trim($data['references'] ?? ''),
            'last_reviewed' => $data['last_reviewed'] ?? date('Y-m-d')
        ];

        if ($existing) {
            $diseaseDoc['_rev'] = $existing['_rev'];
        }

        return $this->couch->saveDoc($diseaseDoc);
    }

    public function delete(string $id): bool {
        $disease = $this->findById($id);
        if (!$disease) return false;
        return $this->couch->deleteDoc($id, $disease['_rev'] ?? null);
    }
}
