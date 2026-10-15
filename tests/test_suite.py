"""
OpenCropCare - Automated Python ML Microservice Test Suite
Tests:
1. OpenCV Image Preprocessing
2. PyTorch / Feature Extraction Pipeline
3. Top-3 Confidence Ranking
4. FastAPI HTTP Endpoints (/health, /predict)
"""

import sys
import io
import os
import cv2
import numpy as np
from fastapi.testclient import TestClient

# Add ml-service to path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "ml-service"))

from predictor import predictor
from app import app


def generate_synthetic_leaf(disease="early_blight") -> bytes:
    # 224x224 green image with simulated necrotic lesions
    img = np.full((224, 224, 3), (34, 139, 34), dtype=np.uint8)

    # Add dark brown target spots
    cv2.circle(img, (112, 112), 30, (19, 69, 139), -1)
    cv2.circle(img, (112, 112), 18, (30, 105, 210), 2)
    # Add yellow halos
    cv2.circle(img, (112, 112), 45, (0, 215, 255), 2)

    _, encoded = cv2.imencode(".jpg", img)
    return encoded.tobytes()


def run_tests():
    print("==================================================")
    print("[OPENCROPCARE] PYTHON ML VERIFICATION SUITE")
    print("==================================================")

    # 1. Test Preprocessing
    print("\n[1/3] Testing OpenCV Preprocessing...")
    leaf_bytes = generate_synthetic_leaf()
    norm_img = predictor.preprocess(leaf_bytes)
    assert norm_img.shape == (224, 224, 3), "Shape must be 224x224x3"
    assert norm_img.dtype == np.float32, "Type must be float32"
    print("  [PASS] OpenCV Preprocessing verified (224x224 RGB normalized)")

    # 2. Test Predictor
    print("\n[2/3] Testing Predictor Feature Extraction & Inference...")
    res = predictor.predict(leaf_bytes, crop_hint="Tomato")
    assert "predicted_disease" in res, "Result must contain predicted_disease"
    assert "confidence" in res, "Result must contain confidence"
    assert len(res["top_3"]) == 3, "Top-3 predictions must be returned"
    print(f"  [PASS] Top-1 Match: {res['predicted_disease']} ({res['confidence']}%)")
    print(f"  [PASS] Top-3 Classes: {[c['disease_name'] for c in res['top_3']]}")

    # 3. Test FastAPI Endpoints
    print("\n[3/3] Testing FastAPI Microservice Endpoints...")
    client = TestClient(app)

    # Health
    res_h = client.get("/health")
    assert res_h.status_code == 200
    assert res_h.json()["status"] == "online"
    print("  [PASS] GET /health response status 200 OK")

    # Predict
    res_p = client.post(
        "/predict",
        files={"image": ("test_leaf.jpg", leaf_bytes, "image/jpeg")},
        data={"crop_hint": "Tomato"}
    )
    assert res_p.status_code == 200
    pred_data = res_p.json()
    assert "predicted_disease" in pred_data
    print(f"  [PASS] POST /predict HTTP 200 OK -> Predicted: {pred_data['predicted_disease']}")

    print("\n==================================================")
    print("[SUCCESS] ALL ML MICROSERVICE TESTS PASSED!")
    print("==================================================")


if __name__ == "__main__":
    run_tests()
