<?php
/**
 * OpenCropCare - User Model
 */

require_once __DIR__ . '/../services/CouchDBService.php';

class User {
    private CouchDBService $couch;

    public function __construct() {
        $this->couch = new CouchDBService();
    }

    private function normalizeUser(?array $user): ?array {
        if (!$user) {
            return null;
        }
        $user['name'] = (string)($user['name'] ?? '');
        $user['email'] = (string)($user['email'] ?? '');
        $user['phone'] = (string)($user['phone'] ?? '');
        $user['district'] = (string)($user['district'] ?? '');
        $user['role'] = (string)($user['role'] ?? 'farmer');
        $user['created_at'] = (string)($user['created_at'] ?? date('c'));
        return $user;
    }

    public function findByEmail(string $email): ?array {
        $docs = $this->couch->findByType('user', ['email' => strtolower(trim($email))], 1);
        return !empty($docs[0]) ? $this->normalizeUser($docs[0]) : null;
    }

    public function findById(string $id): ?array {
        $doc = $this->couch->getDoc($id);
        return ($doc && ($doc['type'] ?? '') === 'user') ? $this->normalizeUser($doc) : null;
    }

    public function authenticate(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }
        if (!empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public function create(array $data): array {
        $email = strtolower(trim($data['email']));
        if ($this->findByEmail($email)) {
            throw new Exception("An account with this email address already exists.");
        }

        $userDoc = [
            '_id' => 'user_' . bin2hex(random_bytes(8)),
            'type' => 'user',
            'name' => trim($data['name'] ?? ''),
            'email' => $email,
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'phone' => trim($data['phone'] ?? ''),
            'role' => in_array($data['role'] ?? '', ['admin', 'farmer', 'expert']) ? $data['role'] : 'farmer',
            'district' => trim($data['district'] ?? ''),
            'created_at' => date('c')
        ];

        $saved = $this->couch->saveDoc($userDoc);
        return $this->normalizeUser($saved);
    }

    public function update(string $id, array $data): array {
        $user = $this->findById($id);
        if (!$user) {
            throw new Exception("User not found.");
        }

        $user['name'] = trim($data['name'] ?? ($user['name'] ?? ''));
        $user['phone'] = trim($data['phone'] ?? ($user['phone'] ?? ''));
        $user['district'] = trim($data['district'] ?? ($user['district'] ?? ''));

        if (!empty($data['password'])) {
            $user['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (isset($data['role']) && in_array($data['role'], ['admin', 'farmer', 'expert'])) {
            $user['role'] = $data['role'];
        }

        $saved = $this->couch->saveDoc($user);
        return $this->normalizeUser($saved);
    }

    public function getAll(int $limit = 100): array {
        $users = $this->couch->findByType('user', [], $limit);
        return array_values(array_filter(array_map([$this, 'normalizeUser'], $users)));
    }

    public function delete(string $id): bool {
        $user = $this->findById($id);
        if (!$user) return false;
        return $this->couch->deleteDoc($id, $user['_rev'] ?? null);
    }
}
