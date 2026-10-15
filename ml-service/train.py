"""
OpenCropCare - PyTorch Crop Disease Model Training Script
Trains a deep convolutional neural network (CropDiseaseNet) across 20+ crop disease classes.
Exports model weights to 'model/crop_disease_model.pt' and class index mapping to 'model/class_indices.json'.
"""

import os
import time
import json
import torch
import torch.nn as nn
import torch.optim as optim
from torch.utils.data import DataLoader
from torchvision import datasets, transforms


# -------------------------------------------------------------
# 1. Neural Network Architecture (CropDiseaseNet)
# -------------------------------------------------------------
class CropDiseaseNet(nn.Module):
    def __init__(self, num_classes=20):
        super(CropDiseaseNet, self).__init__()
        
        self.features = nn.Sequential(
            # Block 1: 224x224 -> 112x112
            nn.Conv2d(3, 32, kernel_size=3, padding=1),
            nn.BatchNorm2d(32),
            nn.ReLU(inplace=True),
            nn.MaxPool2d(2, 2),
            
            # Block 2: 112x112 -> 56x56
            nn.Conv2d(32, 64, kernel_size=3, padding=1),
            nn.BatchNorm2d(64),
            nn.ReLU(inplace=True),
            nn.MaxPool2d(2, 2),
            
            # Block 3: 56x56 -> 28x28
            nn.Conv2d(64, 128, kernel_size=3, padding=1),
            nn.BatchNorm2d(128),
            nn.ReLU(inplace=True),
            nn.MaxPool2d(2, 2),
            
            # Block 4: 28x28 -> 14x14
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


# -------------------------------------------------------------
# 2. Training & Validation Pipeline
# -------------------------------------------------------------
def train_model(epochs=12, batch_size=16, learning_rate=1e-3):
    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    print("==================================================")
    print(f"[OPENCROPCARE] TRAINING NEURAL MODEL ON {str(device).upper()}")
    print("==================================================")

    data_dir = os.path.join(os.path.dirname(__file__), "dataset")
    train_dir = os.path.join(data_dir, "train")
    val_dir = os.path.join(data_dir, "val")

    if not os.path.exists(train_dir) or not os.path.exists(val_dir):
        print("Dataset not found. Running dataset generator first...")
        from dataset_generator import generate_dataset
        generate_dataset()

    # Data Augmentation & Preprocessing Transforms
    train_transforms = transforms.Compose([
        transforms.Resize((224, 224)),
        transforms.RandomHorizontalFlip(),
        transforms.RandomVerticalFlip(),
        transforms.RandomRotation(15),
        transforms.ColorJitter(brightness=0.1, contrast=0.1),
        transforms.ToTensor(),
        transforms.Normalize([0.485, 0.456, 0.406], [0.229, 0.224, 0.225])
    ])

    val_transforms = transforms.Compose([
        transforms.Resize((224, 224)),
        transforms.ToTensor(),
        transforms.Normalize([0.485, 0.456, 0.406], [0.229, 0.224, 0.225])
    ])

    train_dataset = datasets.ImageFolder(train_dir, transform=train_transforms)
    val_dataset = datasets.ImageFolder(val_dir, transform=val_transforms)

    train_loader = DataLoader(train_dataset, batch_size=batch_size, shuffle=True, num_workers=0)
    val_loader = DataLoader(val_dataset, batch_size=batch_size, shuffle=False, num_workers=0)

    num_classes = len(train_dataset.classes)
    class_names = train_dataset.classes
    print(f"Total Training Images: {len(train_dataset)} across {num_classes} classes")
    print(f"Total Validation Images: {len(val_dataset)}")
    print(f"Classes: {class_names}\n")

    # Initialize Model, Loss, Optimizer, and Cosine Scheduler
    model = CropDiseaseNet(num_classes=num_classes).to(device)
    criterion = nn.CrossEntropyLoss()
    optimizer = optim.AdamW(model.parameters(), lr=learning_rate, weight_decay=1e-4)
    scheduler = optim.lr_scheduler.CosineAnnealingLR(optimizer, T_max=epochs)

    best_val_acc = 0.0
    history = []
    start_time = time.time()

    for epoch in range(1, epochs + 1):
        # Training Phase
        model.train()
        running_loss = 0.0
        correct_train = 0
        total_train = 0

        for images, labels in train_loader:
            images, labels = images.to(device), labels.to(device)
            optimizer.zero_grad()
            outputs = model(images)
            loss = criterion(outputs, labels)
            loss.backward()
            optimizer.step()

            running_loss += loss.item() * images.size(0)
            _, preds = torch.max(outputs, 1)
            correct_train += (preds == labels).sum().item()
            total_train += labels.size(0)

        scheduler.step()

        epoch_train_loss = running_loss / total_train
        epoch_train_acc = (correct_train / total_train) * 100.0

        # Validation Phase
        model.eval()
        val_loss = 0.0
        correct_val = 0
        total_val = 0

        with torch.no_grad():
            for images, labels in val_loader:
                images, labels = images.to(device), labels.to(device)
                outputs = model(images)
                loss = criterion(outputs, labels)

                val_loss += loss.item() * images.size(0)
                _, preds = torch.max(outputs, 1)
                correct_val += (preds == labels).sum().item()
                total_val += labels.size(0)

        epoch_val_loss = val_loss / total_val
        epoch_val_acc = (correct_val / total_val) * 100.0

        print(f"Epoch [{epoch:02d}/{epochs:02d}] "
              f"Train Loss: {epoch_train_loss:.4f} | Train Acc: {epoch_train_acc:.2f}% "
              f"|| Val Loss: {epoch_val_loss:.4f} | Val Acc: {epoch_val_acc:.2f}%")

        history.append({
            "epoch": epoch,
            "train_loss": round(epoch_train_loss, 4),
            "train_acc": round(epoch_train_acc, 2),
            "val_loss": round(epoch_val_loss, 4),
            "val_acc": round(epoch_val_acc, 2)
        })

        if epoch_val_acc >= best_val_acc:
            best_val_acc = epoch_val_acc
            # Save Best Model
            model_dir = os.path.join(os.path.dirname(__file__), "model")
            os.makedirs(model_dir, exist_ok=True)
            model_path = os.path.join(model_dir, "crop_disease_model.pt")
            torch.save({
                "model_state_dict": model.state_dict(),
                "num_classes": num_classes,
                "class_names": class_names,
                "best_val_acc": best_val_acc,
                "epoch": epoch
            }, model_path)

    elapsed_time = time.time() - start_time
    print("\n==================================================")
    print(f"[SUCCESS] TRAINING COMPLETE in {elapsed_time:.1f}s")
    print(f"Best Validation Accuracy: {best_val_acc:.2f}%")
    print(f"Saved Checkpoint: model/crop_disease_model.pt")

    # Save Class Indices Mapping
    model_dir = os.path.join(os.path.dirname(__file__), "model")
    class_indices_path = os.path.join(model_dir, "class_indices.json")
    with open(class_indices_path, "w", encoding="utf-8") as f:
        json.dump({str(i): name for i, name in enumerate(class_names)}, f, indent=2)

    # Save Metrics Log
    metrics_path = os.path.join(model_dir, "training_metrics.json")
    with open(metrics_path, "w", encoding="utf-8") as f:
        json.dump({
            "epochs": epochs,
            "final_val_acc": best_val_acc,
            "training_time_seconds": round(elapsed_time, 1),
            "device": str(device),
            "history": history
        }, f, indent=2)

    print(f"Class Indices: model/class_indices.json")
    print(f"Training Telemetry: model/training_metrics.json")
    print("==================================================")


if __name__ == "__main__":
    train_model()
