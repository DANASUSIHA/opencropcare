<?php
/**
 * OpenCropCare - Bilingual Translation Service (English & Tamil)
 */

class TranslationService {
    private static array $dictionary = [
        'en' => [
            'app_title' => 'OpenCropCare',
            'app_tagline' => 'AI-Powered Plant Disease Detection System',
            'nav_dashboard' => 'Dashboard',
            'nav_identify' => 'Identify Disease',
            'nav_history' => 'Scan History',
            'nav_diseases' => 'Disease Library',
            'nav_help' => 'Help & Guide',
            'nav_admin' => 'Admin Panel',
            'nav_login' => 'Login',
            'nav_register' => 'Register',
            'nav_logout' => 'Logout',
            'nav_profile' => 'My Profile',
            
            // Auth
            'login_title' => 'Sign In to Your Account',
            'login_subtitle' => 'Access your crop health scans and personalized advisory',
            'email_label' => 'Email Address',
            'password_label' => 'Password',
            'btn_login' => 'Sign In',
            'no_account' => "Don't have an account?",
            'register_link' => 'Register here',
            'register_title' => 'Create Farmer Account',
            'register_subtitle' => 'Join OpenCropCare to diagnose and track crop diseases',
            'full_name' => 'Full Name',
            'phone_number' => 'Phone Number (Optional)',
            'role_label' => 'User Role',
            'role_farmer' => 'Farmer / Cultivator',
            'role_expert' => 'Agricultural Expert',
            'btn_register' => 'Create Account',
            'have_account' => 'Already have an account?',
            'login_link' => 'Sign in here',
            
            // Dashboard
            'welcome_back' => 'Welcome back',
            'dashboard_subtitle' => 'Here is the summary of your crop leaf health scans',
            'stat_total_scans' => 'Total Scans',
            'stat_healthy_leaves' => 'Healthy Foliage',
            'stat_diseased_leaves' => 'Diseased Cases',
            'stat_crops_monitored' => 'Crops Monitored',
            'btn_start_scan' => 'Diagnose New Leaf Image',
            'recent_scans' => 'Recent Scan Records',
            'frequent_diseases' => 'Frequent Diagnoses Distribution',
            'view_all' => 'View All',
            'no_scans_yet' => 'No scan history recorded yet. Upload your first crop leaf to get started!',
            
            // Upload & Predict
            'upload_title' => 'Crop Leaf Disease Identification',
            'upload_subtitle' => 'Select your crop, upload a clear photo of the leaf, and receive ML-based analysis',
            'select_crop' => 'Select Crop Type',
            'choose_crop_placeholder' => '-- Choose Crop --',
            'drop_zone_text' => 'Click or drag leaf photo here to upload',
            'drop_zone_hint' => 'Supported formats: JPG, JPEG, PNG, WebP (Max 8MB)',
            'image_preview' => 'Leaf Image Preview',
            'btn_diagnose' => 'Analyze Leaf Health',
            'analyzing' => 'Analyzing leaf image with AI Diagnostic Model...',
            
            // Results
            'result_title' => 'Diagnosis & Disease Advisory',
            'prediction_label' => 'Identified Condition',
            'confidence_label' => 'Model Confidence',
            'severity_label' => 'Severity Level',
            'pathogen_type' => 'Pathogen Type',
            'symptoms_title' => 'Visible Symptoms',
            'causes_title' => 'Possible Causes',
            'prevention_title' => 'Cultural Prevention Measures',
            'management_title' => 'General Disease Management',
            'disclaimer_title' => 'Preliminary Identification Notice',
            'disclaimer_text' => 'OpenCropCare provides preliminary AI-assisted identification for educational and advisory purposes. Consult your local agricultural extension officer or agronomist before applying chemical treatments in critical cases.',
            'btn_try_another' => 'Scan Another Leaf',
            
            // Feedback
            'feedback_title' => 'Was this diagnosis helpful?',
            'feedback_status_correct' => 'Accurate / Correct',
            'feedback_status_incorrect' => 'Incorrect Disease',
            'feedback_status_uncertain' => 'Uncertain / Need Verification',
            'feedback_comments' => 'Farmer Observations / Notes (Optional)',
            'btn_submit_feedback' => 'Submit Feedback',
            
            // History
            'history_title' => 'Your Scan History',
            'history_subtitle' => 'Browse and filter your past leaf disease analyses',
            'filter_crop' => 'Filter by Crop',
            'search_placeholder' => 'Search by disease or crop...',
            'table_date' => 'Date & Time',
            'table_crop' => 'Crop',
            'table_image' => 'Leaf Image',
            'table_prediction' => 'Predicted Disease',
            'table_confidence' => 'Confidence',
            'table_actions' => 'Actions',
            'btn_view' => 'View Details',
            'btn_delete' => 'Delete Record',
            'confirm_delete' => 'Are you sure you want to delete this scan record?',
            
            // Help
            'help_title' => 'Farmer Help & Awareness Guide',
            'help_subtitle' => 'Best practices for accurate crop leaf photography and disease understanding',
            'photo_tips_title' => 'Photography Best Practices',
            'photo_tip_1' => 'Focus on the infected leaf area holding the camera 10–15 cm away.',
            'photo_tip_2' => 'Take photos in natural diffused daylight to avoid harsh glare or dark shadows.',
            'photo_tip_3' => 'Ensure the leaf fills at least 50% of the camera frame without fingers or soil in front.'
        ],

        'ta' => [
            'app_title' => 'ஓபன்கிராப்கேர்',
            'app_tagline' => 'திறந்த மூல பயிர் நோய் கண்டறியும் தளம்',
            'nav_dashboard' => 'முகப்பு பலகை',
            'nav_identify' => 'நோய் கண்டறிதல்',
            'nav_history' => 'பரிசோதனை வரலாறு',
            'nav_diseases' => 'பயிர் நோய்கள் நூலகம்',
            'nav_help' => 'உதவி & வழிகாட்டி',
            'nav_admin' => 'நிர்வாக பகுதி',
            'nav_login' => 'உள்நுழைவு',
            'nav_register' => 'பதிவு செய்',
            'nav_logout' => 'வெளியேறு',
            'nav_profile' => 'என் சுயவிவரம்',
            
            // Auth
            'login_title' => 'உங்கள் கணக்கில் உள்நுழையவும்',
            'login_subtitle' => 'பயிர் பரிசோதனைகள் மற்றும் ஆலோசனைகளைப் பெறவும்',
            'email_label' => 'மின்னஞ்சல் முகவரி',
            'password_label' => 'கடவுச்சொல்',
            'btn_login' => 'உள்நுழைக',
            'no_account' => 'கணக்கு இல்லையா?',
            'register_link' => 'இங்கே பதிவு செய்க',
            'register_title' => 'விவசாயி கணக்கு உருவாக்கவும்',
            'register_subtitle' => 'பயிர் நோய்களைக் கண்டறிய ஓபன்கிராப்கேரில் இணையுங்கள்',
            'full_name' => 'முழு பெயர்',
            'phone_number' => 'தொலைபேசி எண் (விருப்பத்தேர்வு)',
            'role_label' => 'பயனர் பங்கு',
            'role_farmer' => 'விவசாயி / பயிர் வளர்ப்பாளர்',
            'role_expert' => 'வேளாண் நிபுணர்',
            'btn_register' => 'பதிவு செய்க',
            'have_account' => 'ஏற்கனவே கணக்கு உள்ளதா?',
            'login_link' => 'இங்கே உள்நுழையவும்',
            
            // Dashboard
            'welcome_back' => 'மீண்டும் வருக',
            'dashboard_subtitle' => 'உங்கள் பயிர் இலை பரிசோதனைகளின் சுருக்கம்',
            'stat_total_scans' => 'மொத்த பரிசோதனைகள்',
            'stat_healthy_leaves' => 'ஆரோக்கியமான இலைகள்',
            'stat_diseased_leaves' => 'நோய் பாதித்த இலைகள்',
            'stat_crops_monitored' => 'கண்காணிக்கப்படும் பயிர்கள்',
            'btn_start_scan' => 'புதிய இலையை பரிசோதிக்கவும்',
            'recent_scans' => 'சமீபத்திய பரிசோதனைகள்',
            'frequent_diseases' => 'அடிக்கடி கண்டறியப்பட்ட நோய்கள்',
            'view_all' => 'அனைத்தையும் காண்க',
            'no_scans_yet' => 'பரிசோதனை வரலாறு ஏதுமில்லை. முதல் இலையைப் பதிவேற்றித் தொடங்குங்கள்!',
            
            // Upload & Predict
            'upload_title' => 'பயிர் இலை நோய் கண்டறிதல்',
            'upload_subtitle' => 'பயிரைத் தேர்வுசெய்து, பாதிக்கப்பட்ட இலையின் தெளிவான புகைப்படத்தைப் பதிவேற்றவும்',
            'select_crop' => 'பயிர் வகையைத் தேர்ந்தெடுக்கவும்',
            'choose_crop_placeholder' => '-- பயிரைத் தேர்வுசெய்க --',
            'drop_zone_text' => 'புகைப்படத்தை இங்கே பதிவேற்ற கிளிக் செய்யவும்',
            'drop_zone_hint' => 'அனுமதிக்கப்பட்ட வடிவங்கள்: JPG, JPEG, PNG, WebP (அதிகபட்சம் 8MB)',
            'image_preview' => 'இலை புகைப்படத்தின் முன்னோட்டம்',
            'btn_diagnose' => 'இலையை ஆய்வு செய்',
            'analyzing' => 'படத்தை மெஷின் லேர்னிங் மூலம் ஆய்வு செய்கிறது...',
            
            // Results
            'result_title' => 'நோய் கண்டறிதல் & வழிகாட்டல்',
            'prediction_label' => 'கண்டறியப்பட்ட நோய்',
            'confidence_label' => 'துல்லிய சதவீதம்',
            'severity_label' => 'தீவிரத்தன்மை',
            'pathogen_type' => 'நோய்க்கிருமி வகை',
            'symptoms_title' => 'காணக்கூடிய அறிகுறிகள்',
            'causes_title' => 'சாத்தியமான காரணங்கள்',
            'prevention_title' => 'தடுப்பு நடவடிக்கைகள்',
            'management_title' => 'மேலாண்மை மற்றும் தீர்வுகள்',
            'disclaimer_title' => 'முக்கிய எச்சரிக்கை குறிப்பு',
            'disclaimer_text' => 'இந்த முடிவு முதற்கட்ட செயற்கை நுண்ணறிவு வழிகாட்டுதல் மட்டுமே. தீவிர பாதிப்புகளில் வேளாண்மை அலுவலர் அல்லது நிபுணரை அணுகவும்.',
            'btn_try_another' => 'மற்றொரு இலையை சோதிக்கவும்',
            
            // Feedback
            'feedback_title' => 'இந்த முடிவு பயனுள்ளதாக இருந்ததா?',
            'feedback_status_correct' => 'சரியானது',
            'feedback_status_incorrect' => 'தவறான நோய்',
            'feedback_status_uncertain' => 'உறுதியாக தெரியவில்லை',
            'feedback_comments' => 'உங்கள் களக் கருத்துக்கள் / குறிப்புகள்',
            'btn_submit_feedback' => 'கருத்தைச் சமர்ப்பிக்கவும்',
            
            // History
            'history_title' => 'உங்கள் பரிசோதனை வரலாறு',
            'history_subtitle' => 'முந்தைய பரிசோதனை முடிவுகளைத் தேடிப் பார்க்கவும்',
            'filter_crop' => 'பயிரின்படி வடிகட்டுக',
            'search_placeholder' => 'நோய் அல்லது பயிரைத் தேடுக...',
            'table_date' => 'நாள் & நேரம்',
            'table_crop' => 'பயிர்',
            'table_image' => 'இலை படம்',
            'table_prediction' => 'கண்டறியப்பட்ட நோய்',
            'table_confidence' => 'துல்லியம்',
            'table_actions' => 'செயல்கள்',
            'btn_view' => 'விவரங்கள்',
            'btn_delete' => 'நீக்கு',
            'confirm_delete' => 'இந்த பதிவை நிச்சயமாக நீக்க விரும்புகிறீர்களா?',
            
            // Help
            'help_title' => 'விவசாயிகள் உதவி & வழிகாட்டுதல்',
            'help_subtitle' => 'துல்லியமான நோயறிதலுக்கு புகைப்படங்களை எடுப்பதற்கான வழிகாட்டி',
            'photo_tips_title' => 'புகைப்படம் எடுப்பதற்கான குறிப்புகள்',
            'photo_tip_1' => 'பாதிக்கப்பட்ட பகுதியை 10-15 செ.மீ தொலைவில் வைத்து தெளிவாக படம் எடுக்கவும்.',
            'photo_tip_2' => 'நேரடி சூரிய ஒளி அல்லது அதிக இருளைத் தவிர்த்து இயற்கை வெளிச்சத்தில் படம் எடுக்கவும்.',
            'photo_tip_3' => 'இலை புகைப்படத்தின் 50% இடத்தை நிரப்ப வேண்டும்.'
        ]
    ];

    public static function getLanguage(): string {
        if (!isset($_SESSION['lang'])) {
            $_SESSION['lang'] = 'en';
        }
        return $_SESSION['lang'];
    }

    public static function setLanguage(string $lang): void {
        if (in_array($lang, ['en', 'ta'])) {
            $_SESSION['lang'] = $lang;
        }
    }

    public static function translate(string $key): string {
        $lang = self::getLanguage();
        return self::$dictionary[$lang][$key] ?? self::$dictionary['en'][$key] ?? $key;
    }
}

// Global translation shortcut function
function __(string $key): string {
    return TranslationService::translate($key);
}
