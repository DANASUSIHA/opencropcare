<?php
/**
 * OpenCropCare - Authentication Controller
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Dedicated Farmer Login Page
     */
    public function showLogin(): void {
        if (!empty($_SESSION['user'])) {
            if (($_SESSION['user']['role'] ?? '') === 'admin') {
                header('Location: index.php?route=admin_dashboard');
            } else {
                header('Location: index.php?route=dashboard');
            }
            exit();
        }
        $pageTitle = 'Farmer Sign In';
        require APP_PATH . '/views/auth/farmer_login.php';
    }

    /**
     * Dedicated Administrator Login Page
     */
    public function showAdminLogin(): void {
        if (!empty($_SESSION['user'])) {
            if (($_SESSION['user']['role'] ?? '') === 'admin') {
                header('Location: index.php?route=admin_dashboard');
            } else {
                header('Location: index.php?route=dashboard');
            }
            exit();
        }
        $pageTitle = 'Administrator Sign In';
        require APP_PATH . '/views/auth/admin_login.php';
    }

    /**
     * Farmer Login Handler
     */
    public function handleLogin(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=login');
            exit();
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Invalid or expired session security token. Please try again.";
            header('Location: index.php?route=login');
            exit();
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = "Please provide both email and password.";
            header('Location: index.php?route=login');
            exit();
        }

        $user = $this->userModel->authenticate($email, $password);
        if ($user) {
            session_regenerate_id(true);
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            $_SESSION['flash_success'] = "Welcome back, " . e($user['name']) . "!";

            if (($user['role'] ?? '') === 'admin') {
                header('Location: index.php?route=admin_dashboard');
            } else {
                header('Location: index.php?route=dashboard');
            }
            exit();
        } else {
            $_SESSION['flash_error'] = "Invalid email or password. Please try again.";
            header('Location: index.php?route=login');
            exit();
        }
    }

    /**
     * Administrator Login Handler
     */
    public function handleAdminLogin(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=admin_login');
            exit();
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Invalid or expired session security token. Please try again.";
            header('Location: index.php?route=admin_login');
            exit();
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = "Please provide administrator email and password.";
            header('Location: index.php?route=admin_login');
            exit();
        }

        $user = $this->userModel->authenticate($email, $password);
        if ($user) {
            if (($user['role'] ?? '') !== 'admin') {
                $_SESSION['flash_error'] = "Access denied: Account (" . e($user['email']) . ") is not an Administrator. Please use Farmer Login.";
                header('Location: index.php?route=admin_login');
                exit();
            }

            session_regenerate_id(true);
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            $_SESSION['flash_success'] = "Administrator session active. Welcome back, " . e($user['name']) . "!";
            header('Location: index.php?route=admin_dashboard');
            exit();
        } else {
            $_SESSION['flash_error'] = "Invalid administrator credentials. Please check your email and password.";
            header('Location: index.php?route=admin_login');
            exit();
        }
    }

    /**
     * Instant 1-Click Administrator Access (Guaranteed reliability)
     */
    public function instantAdminLogin(): void {
        $user = $this->userModel->authenticate('admin@opencropcare.org', 'admin123');
        if ($user) {
            session_regenerate_id(true);
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            $_SESSION['flash_success'] = "Administrator session active. Welcome, " . e($user['name']) . "!";
            header('Location: index.php?route=admin_dashboard');
            exit();
        }
        $_SESSION['flash_error'] = "Unable to start administrator demo session.";
        header('Location: index.php?route=admin_login');
        exit();
    }

    /**
     * Instant 1-Click Farmer Access (Guaranteed reliability)
     */
    public function instantFarmerLogin(): void {
        $user = $this->userModel->authenticate('farmer@opencropcare.org', 'farmer123');
        if ($user) {
            session_regenerate_id(true);
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            $_SESSION['flash_success'] = "Welcome back, " . e($user['name']) . "!";
            header('Location: index.php?route=dashboard');
            exit();
        }
        $_SESSION['flash_error'] = "Unable to start farmer demo session.";
        header('Location: index.php?route=login');
        exit();
    }

    public function showRegister(): void {
        if (!empty($_SESSION['user'])) {
            if (($_SESSION['user']['role'] ?? '') === 'admin') {
                header('Location: index.php?route=admin_dashboard');
            } else {
                header('Location: index.php?route=dashboard');
            }
            exit();
        }
        $pageTitle = __('register_title');
        require APP_PATH . '/views/auth/register.php';
    }

    public function handleRegister(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=register');
            exit();
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Security token mismatch. Please try again.";
            header('Location: index.php?route=register');
            exit();
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $role = trim($_POST['role'] ?? 'farmer');
        $district = trim($_POST['district'] ?? '');

        if (strlen($name) < 2) {
            $_SESSION['flash_error'] = "Please provide your full name.";
            header('Location: index.php?route=register');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Please enter a valid email address.";
            header('Location: index.php?route=register');
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = "Password must be at least 6 characters.";
            header('Location: index.php?route=register');
            exit();
        }

        try {
            $user = $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'role' => $role,
                'district' => $district
            ]);

            session_regenerate_id(true);
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            $_SESSION['flash_success'] = "Account successfully created! Welcome to " . APP_NAME . ", " . e($user['name']) . "!";
            header('Location: index.php?route=dashboard');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?route=register');
            exit();
        }
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        session_start();
        $_SESSION['flash_success'] = "You have been logged out successfully.";
        header('Location: index.php?route=login');
        exit();
    }

    public function showProfile(): void {
        AuthMiddleware::check();
        $user = $this->userModel->findById(AuthMiddleware::userId());
        if (!$user) {
            header('Location: index.php?route=logout');
            exit();
        }
        $pageTitle = 'Profile Settings';
        require APP_PATH . '/views/auth/profile.php';
    }

    public function handleProfileUpdate(): void {
        AuthMiddleware::check();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash_error'] = "Invalid session token.";
            header('Location: index.php?route=profile');
            exit();
        }

        $userId = AuthMiddleware::userId();
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';

        $updateData = [
            'name' => $name,
            'phone' => $phone,
            'district' => $district
        ];

        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                $_SESSION['flash_error'] = "New password must be at least 6 characters.";
                header('Location: index.php?route=profile');
                exit();
            }
            $updateData['password'] = $newPassword;
        }

        try {
            $updated = $this->userModel->update($userId, $updateData);
            $_SESSION['user']['name'] = $updated['name'];
            $_SESSION['user']['phone'] = $updated['phone'];
            $_SESSION['user']['district'] = $updated['district'];
            $_SESSION['flash_success'] = "Profile updated successfully.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: index.php?route=profile');
        exit();
    }
}
