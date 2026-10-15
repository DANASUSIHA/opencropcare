# 🌿 OpenCropCare: Open-Source Crop Disease Identification System

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)
[![PHP 8.2](https://img.shields.io/badge/PHP-8.2+-777BB4.svg)](https://www.php.net)
[![FastAPI](https://img.shields.io/badge/FastAPI-0.141-009688.svg)](https://fastapi.tiangolo.com)
[![Apache CouchDB](https://img.shields.io/badge/Apache_CouchDB-NoSQL-E42528.svg)](https://couchdb.apache.org)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5.3-7952B3.svg)](https://getbootstrap.com)

**OpenCropCare** is a modular, open-source web application designed to help farmers and agricultural practitioners identify possible crop leaf diseases, evaluate model confidence, and access actionable disease-management guidance through a clean, bilingual (English & Tamil) interface.

---

## 1. System Architecture

```text
[Farmer / Administrator]
         │
         ▼
[Responsive Web Frontend (HTML5, CSS3, JS, Bootstrap 5, English & Tamil)]
         │
         ▼
[PHP 8+ Backend (MVC Controllers & Auth Sessions)]
   ├── Auth & User Management (password_hash BCRYPT)
   ├── Crop & Image Validation (MIME / Size / Unique UUIDs)
   ├── Prediction History & Feedback Moderation
   └── Disease Knowledge Base Lookups
         │
    ┌────┴─────────────────────────────┐
    ▼                                  ▼
[Apache CouchDB NoSQL]         [Python ML Microservice (FastAPI)]
 (Users, Crops, Diseases,       (OpenCV Preprocessing &
  Predictions, Feedback)         PyTorch Inference Pipeline)
```

---

## 2. Technology Stack & Open-Source Licenses

| Layer | Component | License |
|---|---|---|
| **Frontend** | HTML5, CSS3, JavaScript, Bootstrap 5, FontAwesome, Chart.js | MIT / Apache 2.0 |
| **Backend** | PHP 8.2+ (cURL, Session Management, MVC) | PHP License |
| **Database** | Apache CouchDB (HTTP REST API) + Resilient Local Fallback Store | Apache 2.0 |
| **ML Service** | Python 3, FastAPI, PyTorch / OpenCV, NumPy, Pillow, Uvicorn | BSD / Apache 2.0 |
| **Server** | Apache / XAMPP / PHP Built-in Web Server | Apache 2.0 / Open Source |

---

## 3. Main Modules

1. **User Registration & Authentication (`app/controllers/AuthController.php`)**:
   - Secure registration, login, and profile editing.
   - BCRYPT password hashing (`password_hash` / `password_verify`).
   - CSRF protection on all forms & session fixation protection.
   - Role-Based Access Control (`farmer`, `expert`, `admin`).
2. **Crop Selection & Leaf Image Upload (`app/controllers/UploadController.php`)**:
   - MIME type and file size validation (max 8MB).
   - Real-time client-side photo preview and unique UUID filename generation.
3. **ML Disease Identification (`ml-service/app.py`, `predictor.py`)**:
   - OpenCV image preprocessing (RGB normalization to 224x224).
   - Calibrated Top-3 candidate ranking with percentage confidence.
   - Automatic uncertainty flags and advisory messages for ambiguous or low-confidence photos.
4. **Disease Knowledge Base (`app/models/Disease.php`)**:
   - Rich descriptions, visible symptoms, possible causes, prevention checklists, and general management practices.
5. **Prediction History & Data Isolation (`app/controllers/HistoryController.php`)**:
   - Searchable, crop-filterable scan history.
   - Strict data isolation ensuring users only access their own records.
6. **Farmer Dashboard (`app/controllers/DashboardController.php`)**:
   - Summary telemetry (Total Scans, Healthy Leaves, Diseased Cases, Monitored Crops).
   - Visual distribution doughnut chart (Chart.js).
7. **User Feedback Loop (`app/controllers/FeedbackController.php`)**:
   - Accuracy rating (Correct, Incorrect, Uncertain) with farmer field notes.
8. **Admin Control Center (`app/controllers/AdminController.php`)**:
   - User account management, knowledge base editor, and feedback review queue.
9. **Help, Awareness & Localization (`app/controllers/HelpController.php`, `TranslationService.php`)**:
   - Photography guidelines and safety disclaimers in **English** and **Tamil (தமிழ்)**.

---

## 4. CouchDB Document Schema

### Users (`type: "user"`)
```json
{
  "_id": "user_farmer",
  "type": "user",
  "name": "Murugan Swaminathan",
  "email": "farmer@opencropcare.org",
  "password_hash": "$2y$10$...",
  "phone": "+91 94432 10987",
  "role": "farmer",
  "district": "Coimbatore, Tamil Nadu",
  "created_at": "2026-10-04T10:00:00Z"
}
```

### Diseases (`type: "disease"`)
```json
{
  "_id": "disease_tomato_early_blight",
  "type": "disease",
  "crop_name": "Tomato",
  "disease_name": "Tomato Early Blight",
  "pathogen_type": "Fungal (Alternaria solani)",
  "severity": "Moderate to High",
  "symptoms": "Concentric rings (target-board pattern) on older lower leaves...",
  "causes": "Fungal spores overwintering in soil and crop residue...",
  "management": "Prune bottom leaves within 30cm of soil. Apply bio-fungicides...",
  "prevention": "Crop rotation, base drip irrigation, proper plant spacing...",
  "references": "ICAR Central Horticultural Research Station"
}
```

### Predictions (`type: "prediction"`)
```json
{
  "_id": "pred_a1b2c3d4",
  "type": "prediction",
  "user_id": "user_farmer",
  "crop_name": "Tomato",
  "image_path": "uploads/leaf_20261004_120000_abc.jpg",
  "predicted_disease": "Tomato Early Blight",
  "confidence": 91.5,
  "top_3": [...],
  "model_version": "PyTorch-PlantNetV1.0",
  "created_at": "2026-10-04T10:15:00Z"
}
```

---

## 5. Quick Start & Installation

### Step 1: Initialize Database Documents
From the project root:
```powershell
php database/init_couchdb.php
```

### Step 2: Start the Python ML Microservice (Optional / Live Mode)
```powershell
cd ml-service
python app.py
```
*(The ML service will listen on `http://127.0.0.1:5000`)*

### Step 3: Start the PHP Web Application
```powershell
php -S 127.0.0.1:8000 -t public
```

Open your browser and navigate to:
👉 **`http://127.0.0.1:8000`**

---

## 6. Default Demo Credentials

| Role | Email | Password |
|---|---|---|
| 👨‍🌾 **Farmer Account** | `farmer@opencropcare.org` | `farmer123` |
| 🛡️ **Admin Account** | `admin@opencropcare.org` | `admin123` |

*(Quick 1-click login buttons are also provided on the login page for testing)*

---

## 7. Automated Test Verification

Run PHP Integration Tests:
```powershell
php tests/test_php.php
```

Run Python ML Microservice Tests:
```powershell
python tests/test_suite.py
```
