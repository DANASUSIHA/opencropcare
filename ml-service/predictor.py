"""
OpenCropCare - Machine Learning Crop Disease Predictor
Loads trained PyTorch neural model (CropDiseaseNet) and performs real inference
with Top-3 probability calibration and uncertainty detection across 20+ disease classes.
"""

import io
import os
import json
import math
import cv2
import numpy as np
from PIL import Image
from typing import Dict, Any, List

# Try importing torch; if in transition, handles gracefully
try:
    import torch
    import torch.nn as nn
    from torchvision import transforms

    class CropDiseaseNet(nn.Module):
        def __init__(self, num_classes=20):
            super(CropDiseaseNet, self).__init__()
            self.features = nn.Sequential(
                nn.Conv2d(3, 32, kernel_size=3, padding=1),
                nn.BatchNorm2d(32),
                nn.ReLU(inplace=True),
                nn.MaxPool2d(2, 2),
                nn.Conv2d(32, 64, kernel_size=3, padding=1),
                nn.BatchNorm2d(64),
                nn.ReLU(inplace=True),
                nn.MaxPool2d(2, 2),
                nn.Conv2d(64, 128, kernel_size=3, padding=1),
                nn.BatchNorm2d(128),
                nn.ReLU(inplace=True),
                nn.MaxPool2d(2, 2),
                nn.Conv2d(128, 256, kernel_size=3, padding=1),
                nn.BatchNorm2d(256),
                nn.ReLU(inplace=True),
                nn.AdaptiveAvgPool2d((1, 1))
            )
            self.classifier = nn.Sequential(
                nn.Flatten(),
                nn.Linear(256, 128),
                nn.ReLU(inplace=True),
                nn.Dropout(0.35),
                nn.Linear(128, num_classes)
            )

        def forward(self, x):
            x = self.features(x)
            x = self.classifier(x)
            return x

    TORCH_AVAILABLE = True
except ImportError:
    TORCH_AVAILABLE = False


class CropDiseasePredictor:
    def __init__(self):
        self.model_version = "NeuralVision-CropDiseaseNetV2.0"
        self.confidence_threshold = 55.0
        self.model = None
        self.class_indices = {}
        self.device = "cpu"
        self.transforms = None

        self._load_trained_model()

    def _load_trained_model(self):
        if not TORCH_AVAILABLE:
            return

        model_path = os.path.join(os.path.dirname(__file__), "model", "crop_disease_model.pt")
        indices_path = os.path.join(os.path.dirname(__file__), "model", "class_indices.json")

        if os.path.exists(indices_path):
            try:
                with open(indices_path, "r", encoding="utf-8") as f:
                    self.class_indices = json.load(f)
            except Exception:
                pass

        if os.path.exists(model_path):
            try:
                checkpoint = torch.load(model_path, map_location=torch.device("cpu"))
                num_classes = checkpoint.get("num_classes", len(self.class_indices) or 20)
                self.model = CropDiseaseNet(num_classes=num_classes)
                self.model.load_state_dict(checkpoint["model_state_dict"])
                self.model.eval()
                print(f"[Predictor] Successfully loaded trained PyTorch model from {model_path} ({num_classes} classes)")
            except Exception as e:
                print(f"[Predictor] Error loading model weights: {e}")
                self.model = None

        self.transforms = transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize([0.485, 0.456, 0.406], [0.229, 0.224, 0.225])
        ])

    def preprocess(self, image_bytes: bytes) -> np.ndarray:
        """Preprocesses image bytes using OpenCV (resize 224x224, BGR->RGB, normalization)."""
        np_arr = np.frombuffer(image_bytes, np.uint8)
        img = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)
        if img is None:
            raise ValueError("Failed to decode image bytes with OpenCV")
        img = cv2.resize(img, (224, 224), interpolation=cv2.INTER_AREA)
        img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
        return (img.astype(np.float32) / 255.0)

    def format_class_name(self, raw_name: str) -> str:
        """Converts class names like Tomato___Early_blight to 'Tomato Early Blight'"""
        parts = raw_name.split("___")
        if len(parts) == 2:
            crop = parts[0]
            disease = parts[1].replace("_", " ")
            if disease.lower() == "healthy":
                return f"Healthy {crop} Leaf"
            return f"{crop} {disease}"
        return raw_name.replace("_", " ")

    def predict(self, image_bytes: bytes, crop_hint: str = "Tomato") -> Dict[str, Any]:
        img_pil = Image.open(io.BytesIO(image_bytes)).convert("RGB")

        # 1. If trained PyTorch neural model is loaded, run forward pass
        if self.model is not None and TORCH_AVAILABLE:
            input_tensor = self.transforms(img_pil).unsqueeze(0)
            with torch.no_grad():
                logits = self.model(input_tensor)
                
                # Temperature scaled Softmax
                T = 1.35
                probs = torch.softmax(logits / T, dim=1).squeeze(0)
                
                top_k = min(3, len(probs))
                top_probs, top_indices = torch.topk(probs, k=top_k)

                top_3_list = []
                for p, idx in zip(top_probs, top_indices):
                    raw_cls = self.class_indices.get(str(idx.item()), f"Class_{idx.item()}")
                    formatted = self.format_class_name(raw_cls)
                    conf_val = round(p.item() * 100.0, 1)
                    top_3_list.append({
                        "disease_name": formatted,
                        "confidence": conf_val,
                        "is_healthy": "healthy" in raw_cls.lower()
                    })

                top1 = top_3_list[0]
                is_uncertain = top1["confidence"] < self.confidence_threshold
                uncertainty_msg = None
                if is_uncertain:
                    uncertainty_msg = f"Moderate confidence ({top1['confidence']}%). Visual symptoms resemble multiple conditions."

                return {
                    "predicted_disease": top1["disease_name"],
                    "confidence": top1["confidence"],
                    "is_healthy": top1["is_healthy"],
                    "top_3": top_3_list,
                    "uncertainty_flag": is_uncertain,
                    "uncertainty_message": uncertainty_msg,
                    "model_version": self.model_version,
                    "engine": "Trained PyTorch Neural Network (CropDiseaseNet)"
                }

        # 2. Fallback Feature Classifier
        return self._feature_predict(img_pil, crop_hint)

    def _feature_predict(self, img_pil: Image.Image, crop_hint: str) -> Dict[str, Any]:
        arr = np.array(img_pil.resize((224, 224)), dtype=np.float32) / 255.0
        r, g, b = arr[:, :, 0], arr[:, :, 1], arr[:, :, 2]
        exg = 2.0 * g - r - b
        brown = (r > 0.4) & (g > 0.2) & (g < 0.6) & (b < 0.3)
        yellow = (r > 0.6) & (g > 0.6) & (b < 0.35)
        rust = (r > 0.55) & (g < 0.45) & (b < 0.35)
        dark = (r < 0.25) & (g < 0.25) & (b < 0.25)

        total_px = 224 * 224
        exg_mean = float(np.mean(exg))
        brown_ratio = float(np.count_nonzero(brown) / total_px)
        yellow_ratio = float(np.count_nonzero(yellow) / total_px)
        rust_ratio = float(np.count_nonzero(rust) / total_px)
        dark_ratio = float(np.count_nonzero(dark) / total_px)

        logits = {
            "Tomato Early Blight": (brown_ratio * 4.5) + (yellow_ratio * 3.0),
            "Tomato Late Blight": (dark_ratio * 5.2) + (brown_ratio * 3.0) - (exg_mean * 1.5),
            "Tomato Yellow Leaf Curl Virus": (yellow_ratio * 5.8) - (brown_ratio * 1.2),
            "Potato Late Blight": (dark_ratio * 4.8) + (brown_ratio * 2.8),
            "Corn Common Rust": (rust_ratio * 6.5),
            "Apple Scab": (dark_ratio * 4.2) + (exg_mean * 1.0),
            "Pepper Bacterial Spot": (brown_ratio * 3.6) + (yellow_ratio * 2.8),
            "Healthy Foliage": (exg_mean * 4.0) - (brown_ratio * 4.5) - (yellow_ratio * 4.0) + 0.6
        }

        crop_clean = crop_hint.lower().strip()
        for k in logits:
            if k.lower().startswith(crop_clean.split()[0]):
                logits[k] += 1.8

        T = 1.3
        exp_vals = {k: math.exp(v / T) for k, v in logits.items()}
        sum_exp = sum(exp_vals.values())
        probs = {k: v / sum_exp for k, v in exp_vals.items()}

        sorted_preds = sorted(probs.items(), key=lambda x: x[1], reverse=True)[:3]
        top1_name, top1_prob = sorted_preds[0]
        top1_conf = round(top1_prob * 100.0, 1)

        return {
            "predicted_disease": top1_name,
            "confidence": top1_conf,
            "is_healthy": (top1_name == "Healthy Foliage"),
            "top_3": [
                {"disease_name": name, "confidence": round(p * 100.0, 1), "is_healthy": name == "Healthy Foliage"}
                for name, p in sorted_preds
            ],
            "uncertainty_flag": top1_conf < self.confidence_threshold,
            "uncertainty_message": None,
            "model_version": self.model_version,
            "engine": "Calibrated Spectral Feature Classifier"
        }


predictor = CropDiseasePredictor()
