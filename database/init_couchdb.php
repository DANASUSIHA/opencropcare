<?php
/**
 * OpenCropCare - Database Seeder Script
 * Run via CLI: php database/init_couchdb.php
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/services/CouchDBService.php';

echo "========================================\n";
echo "🌿 OpenCropCare Database Initializer\n";
echo "========================================\n\n";

$couch = new CouchDBService();
echo "Storage Engine: " . $couch->getStorageType() . "\n\n";

// 1. Seed Users (Farmer & Admin)
echo "[1/3] Seeding Initial Users...\n";
$users = [
    [
        '_id' => 'user_admin',
        'type' => 'user',
        'name' => 'System Administrator',
        'email' => 'admin@opencropcare.org',
        'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
        'phone' => '+91 98765 43210',
        'district' => 'Central Agriculture Directorate',
        'role' => 'admin',
        'created_at' => date('c')
    ],
    [
        '_id' => 'user_farmer',
        'type' => 'user',
        'name' => 'Murugan Swaminathan',
        'email' => 'farmer@opencropcare.org',
        'password_hash' => password_hash('farmer123', PASSWORD_BCRYPT),
        'phone' => '+91 94432 10987',
        'district' => 'Coimbatore, Tamil Nadu',
        'role' => 'farmer',
        'created_at' => date('c')
    ]
];

foreach ($users as $u) {
    $existing = $couch->getDoc($u['_id']);
    if ($existing) {
        $u['_rev'] = $existing['_rev'];
    }
    $couch->saveDoc($u);
    echo "  ✓ User seeded: {$u['name']} ({$u['email']}) - Role: {$u['role']}\n";
}

// 2. Seed Crops
echo "\n[2/3] Seeding Supported Crops...\n";
$cropsFile = __DIR__ . '/sample_data/crops.json';
if (file_exists($cropsFile)) {
    $crops = json_decode(file_get_contents($cropsFile), true);
    foreach ($crops as $c) {
        $existing = $couch->getDoc($c['_id']);
        if ($existing) {
            $c['_rev'] = $existing['_rev'];
        }
        $couch->saveDoc($c);
        echo "  ✓ Crop seeded: {$c['crop_name']} ({$c['scientific_name']})\n";
    }
}

// 3. Seed Diseases Knowledge Base
echo "\n[3/3] Seeding Diseases Knowledge Base...\n";
$diseasesFile = __DIR__ . '/sample_data/diseases.json';
if (file_exists($diseasesFile)) {
    $diseases = json_decode(file_get_contents($diseasesFile), true);
    foreach ($diseases as $d) {
        $existing = $couch->getDoc($d['_id']);
        if ($existing) {
            $d['_rev'] = $existing['_rev'];
        }
        $couch->saveDoc($d);
        echo "  ✓ Disease seeded: {$d['disease_name']} [{$d['crop_name']}]\n";
    }
}

echo "\n========================================\n";
echo "🎉 Database Initialized Successfully!\n";
echo "Default Accounts:\n";
echo "  👨‍🌾 Farmer:  farmer@opencropcare.org / farmer123\n";
echo "  🛡️ Admin:   admin@opencropcare.org  / admin123\n";
echo "========================================\n";
