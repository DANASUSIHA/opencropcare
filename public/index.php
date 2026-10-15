<?php
/**
 * OpenCropCare - Front Controller & Application Router
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/services/TranslationService.php';

// Route Parameter - Start visitors at the sign-in page
$route = $_GET['route'] ?? 'login';

// Language Switch Route
if ($route === 'set_lang') {
    $lang = $_GET['lang'] ?? 'en';
    TranslationService::setLanguage($lang);
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=login';
    header('Location: ' . $referer);
    exit();
}

// Route Dispatching
switch ($route) {
    case 'home':
        require_once APP_PATH . '/controllers/HomeController.php';
        (new HomeController())->index();
        break;

    // Farmer Auth Routes
    case 'login':
    case 'farmer_login':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->showLogin();
        break;

    case 'handle_login':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->handleLogin();
        break;

    case 'farmer_instant':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->instantFarmerLogin();
        break;

    // Administrator Auth Routes
    case 'admin_login':
    case 'admin':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->showAdminLogin();
        break;

    case 'handle_admin_login':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->handleAdminLogin();
        break;

    case 'admin_instant':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->instantAdminLogin();
        break;

    case 'register':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->showRegister();
        break;

    case 'handle_register':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->handleRegister();
        break;

    case 'logout':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case 'profile':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->showProfile();
        break;

    case 'handle_profile_update':
        require_once APP_PATH . '/controllers/AuthController.php';
        (new AuthController())->handleProfileUpdate();
        break;

    // Farmer Dashboard
    case 'dashboard':
        require_once APP_PATH . '/controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    // Image Upload & ML Diagnosis
    case 'identify':
        require_once APP_PATH . '/controllers/UploadController.php';
        (new UploadController())->showUpload();
        break;

    case 'handle_predict':
        require_once APP_PATH . '/controllers/UploadController.php';
        (new UploadController())->handlePredict();
        break;

    case 'result':
        require_once APP_PATH . '/controllers/UploadController.php';
        (new UploadController())->showResult();
        break;

    // Scan History
    case 'history':
        require_once APP_PATH . '/controllers/HistoryController.php';
        (new HistoryController())->index();
        break;

    case 'delete_history':
        require_once APP_PATH . '/controllers/HistoryController.php';
        (new HistoryController())->delete();
        break;

    // Disease Knowledge Base
    case 'diseases':
        require_once APP_PATH . '/controllers/DiseaseController.php';
        (new DiseaseController())->index();
        break;

    case 'view_disease':
        require_once APP_PATH . '/controllers/DiseaseController.php';
        (new DiseaseController())->view();
        break;

    // Feedback
    case 'submit_feedback':
        require_once APP_PATH . '/controllers/FeedbackController.php';
        (new FeedbackController())->submit();
        break;

    // Admin Routes
    case 'admin_dashboard':
        require_once APP_PATH . '/controllers/AdminController.php';
        (new AdminController())->dashboard();
        break;

    case 'admin_users':
        require_once APP_PATH . '/controllers/AdminController.php';
        (new AdminController())->users();
        break;

    case 'admin_delete_user':
        require_once APP_PATH . '/controllers/AdminController.php';
        (new AdminController())->deleteUser();
        break;

    case 'admin_diseases':
        require_once APP_PATH . '/controllers/AdminController.php';
        (new AdminController())->diseases();
        break;

    case 'admin_save_disease':
        require_once APP_PATH . '/controllers/AdminController.php';
        (new AdminController())->saveDisease();
        break;

    case 'admin_feedback':
        require_once APP_PATH . '/controllers/AdminController.php';
        (new AdminController())->feedback();
        break;

    // Help & Guide
    case 'help':
        require_once APP_PATH . '/controllers/HelpController.php';
        (new HelpController())->index();
        break;

    default:
        header('Location: index.php?route=dashboard');
        exit();
}
