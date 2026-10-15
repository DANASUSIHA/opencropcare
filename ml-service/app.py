"""
OpenCropCare - Python ML Microservice API (FastAPI)
Exposes endpoints for image preprocessing and PyTorch disease classification.
"""

from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from predictor import predictor

app = FastAPI(
    title="OpenCropCare ML Service",
    description="Crop disease identification inference service",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/health")
def health_check():
    return {
        "status": "online",
        "service": "OpenCropCare ML Microservice",
        "model_version": predictor.model_version
    }


@app.post("/predict")
async def predict_disease(
    image: UploadFile = File(...),
    crop_hint: str = Form("Tomato")
):
    try:
        contents = await image.read()
        if not contents:
            raise HTTPException(status_code=400, detail="Empty image payload received.")

        result = predictor.predict(contents, crop_hint=crop_hint)
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Inference error: {str(e)}")


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=5000)
