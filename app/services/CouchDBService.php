<?php
/**
 * OpenCropCare - Apache CouchDB NoSQL Service Layer
 * Supports full CouchDB HTTP REST API + Embedded Fallback Engine for offline/local standalone development
 */

class CouchDBService {
    private string $baseUrl;
    private string $dbName;
    private ?string $authHeader;
    private bool $isCouchAvailable = false;
    private string $fallbackFile;

    public function __construct() {
        $this->dbName = COUCHDB_DB;
        $this->baseUrl = 'http://' . COUCHDB_HOST . ':' . COUCHDB_PORT;
        $this->authHeader = 'Basic ' . base64_encode(COUCHDB_USER . ':' . COUCHDB_PASS);
        $this->fallbackFile = DATA_DIR . '/couch_store.json';

        $this->checkConnectivity();
        if (!$this->isCouchAvailable) {
            $this->initFallbackStore();
        }
    }

    private function checkConnectivity(): void {
        $ch = curl_init($this->baseUrl . '/_session');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->authHeader,
                'Accept: application/json'
            ]
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->isCouchAvailable = ($httpCode === 200 && $response !== false);
        if ($this->isCouchAvailable) {
            $this->ensureDatabase();
        }
    }

    private function ensureDatabase(): void {
        $ch = curl_init($this->baseUrl . '/' . $this->dbName);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->authHeader,
                'Content-Type: application/json'
            ]
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    private function initFallbackStore(): void {
        if (!file_exists($this->fallbackFile)) {
            $initial = [
                'docs' => []
            ];
            file_put_contents($this->fallbackFile, json_encode($initial, JSON_PRETTY_PRINT));
        }
    }

    public function isConnected(): bool {
        return $this->isCouchAvailable;
    }

    public function getStorageType(): string {
        return $this->isCouchAvailable ? 'NoSQL Document Store' : 'Local Document Engine';
    }

    /**
     * Get document by _id
     */
    public function getDoc(string $id): ?array {
        if ($this->isCouchAvailable) {
            $ch = curl_init($this->baseUrl . '/' . $this->dbName . '/' . urlencode($id));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->authHeader,
                    'Accept: application/json'
                ]
            ]);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $res) {
                return json_decode($res, true);
            }
            return null;
        }

        // Fallback store
        $store = $this->readFallbackStore();
        return $store['docs'][$id] ?? null;
    }

    /**
     * Create or update document
     */
    public function saveDoc(array $doc): array {
        if (empty($doc['_id'])) {
            $doc['_id'] = $this->generateUuid();
        }
        if (!isset($doc['created_at'])) {
            $doc['created_at'] = date('c');
        }
        $doc['updated_at'] = date('c');

        if ($this->isCouchAvailable) {
            $id = $doc['_id'];
            $ch = curl_init($this->baseUrl . '/' . $this->dbName . '/' . urlencode($id));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($doc),
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->authHeader,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]
            ]);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if (($code === 201 || $code === 200) && $res) {
                $respData = json_decode($res, true);
                $doc['_rev'] = $respData['rev'] ?? ($doc['_rev'] ?? '1-rev');
                return $doc;
            }
            throw new Exception("CouchDB Error: Failed to save document ($code) - " . ($res ?: 'No response'));
        }

        // Fallback store
        $store = $this->readFallbackStore();
        $doc['_rev'] = '1-' . md5(json_encode($doc) . microtime());
        $store['docs'][$doc['_id']] = $doc;
        $this->writeFallbackStore($store);
        return $doc;
    }

    /**
     * Delete document by _id and _rev
     */
    public function deleteDoc(string $id, ?string $rev = null): bool {
        if ($this->isCouchAvailable) {
            if (!$rev) {
                $existing = $this->getDoc($id);
                $rev = $existing['_rev'] ?? null;
            }
            if (!$rev) return false;

            $ch = curl_init($this->baseUrl . '/' . $this->dbName . '/' . urlencode($id) . '?rev=' . urlencode($rev));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->authHeader,
                    'Accept: application/json'
                ]
            ]);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($code === 200 || $code === 202);
        }

        // Fallback store
        $store = $this->readFallbackStore();
        if (isset($store['docs'][$id])) {
            unset($store['docs'][$id]);
            $this->writeFallbackStore($store);
            return true;
        }
        return false;
    }

    /**
     * Mango query find
     */
    public function find(array $selector = [], array $sort = [], int $limit = 100): array {
        if ($this->isCouchAvailable) {
            $payload = ['selector' => $selector, 'limit' => $limit];
            if (!empty($sort)) {
                $payload['sort'] = $sort;
            }

            $ch = curl_init($this->baseUrl . '/' . $this->dbName . '/_find');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->authHeader,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]
            ]);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $res) {
                $data = json_decode($res, true);
                return $data['docs'] ?? [];
            }
        }

        // Fallback in-memory search
        $store = $this->readFallbackStore();
        $results = [];

        foreach ($store['docs'] as $doc) {
            $match = true;
            foreach ($selector as $field => $val) {
                if (is_array($val)) {
                    if (isset($val['$eq']) && ($doc[$field] ?? null) !== $val['$eq']) {
                        $match = false;
                        break;
                    }
                } else {
                    if (($doc[$field] ?? null) !== $val) {
                        $match = false;
                        break;
                    }
                }
            }
            if ($match) {
                $results[] = $doc;
            }
        }

        // Sort descending by created_at by default
        usort($results, function ($a, $b) {
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });

        return array_slice($results, 0, $limit);
    }

    public function findByType(string $type, array $extraSelector = [], int $limit = 100): array {
        $selector = array_merge(['type' => $type], $extraSelector);
        return $this->find($selector, [], $limit);
    }

    private function readFallbackStore(): array {
        if (!file_exists($this->fallbackFile)) {
            $this->initFallbackStore();
        }
        $data = file_get_contents($this->fallbackFile);
        $json = json_decode($data, true);
        return is_array($json) ? $json : ['docs' => []];
    }

    private function writeFallbackStore(array $data): void {
        file_put_contents($this->fallbackFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    private function generateUuid(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
