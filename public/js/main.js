/**
 * OpenCropCare / LeafWise - Advanced Farmer Interactive Script
 * Features:
 *  - Photo Quality Gate (Blur & Lighting analysis via Canvas)
 *  - Camera Video Stream & Snapshot Capture
 *  - Web Speech API Voice Advisory Readout (English & Tamil)
 *  - Visual Crop Picker Chips
 *  - Accessibility: High-Contrast Sunlight Mode & Font Scaling
 *  - Offline / Online Connectivity Monitor
 *  - Live Search Filters & WhatsApp Sharing
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('[OpenCropCare] Initializing Advanced Frontend UI Engine...');

    // Clear any stale Service Workers or PWA caches from localhost:8000
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function (registrations) {
            for (let reg of registrations) {
                reg.unregister();
            }
        });
    }

    initAccessibility();
    initNetworkMonitor();
    initCropPicker();
    initPhotoQualityGate();
    initCameraCapture();
    initVoiceReadout();
    initLiveSearch();
});

/* ==========================================================================
   1. Accessibility: Sunlight Contrast Mode & Font Scaling
   ========================================================================== */
function initAccessibility() {
    const contrastBtn = document.getElementById('btnToggleContrast');
    const fontIncBtn = document.getElementById('btnFontIncrease');
    const fontResetBtn = document.getElementById('btnFontReset');

    // Load saved preferences
    if (localStorage.getItem('occ_contrast') === 'true') {
        document.body.classList.add('high-contrast-mode');
        if (contrastBtn) contrastBtn.classList.add('active');
    }

    if (localStorage.getItem('occ_large_text') === 'true') {
        document.body.classList.add('large-text-mode');
    }

    if (contrastBtn) {
        contrastBtn.addEventListener('click', function () {
            const isContrast = document.body.classList.toggle('high-contrast-mode');
            localStorage.setItem('occ_contrast', isContrast);
            contrastBtn.classList.toggle('active', isContrast);
        });
    }

    if (fontIncBtn) {
        fontIncBtn.addEventListener('click', function () {
            const isLarge = document.body.classList.toggle('large-text-mode');
            localStorage.setItem('occ_large_text', isLarge);
        });
    }
}

/* ==========================================================================
   2. Offline / Online Connectivity Monitor
   ========================================================================== */
function initNetworkMonitor() {
    const badge = document.getElementById('networkStatusBadge');
    if (!badge) return;

    function updateStatus() {
        if (navigator.onLine) {
            badge.className = 'offline-status-pill bg-success-subtle text-success border border-success-subtle';
            badge.innerHTML = '<i class="fa-solid fa-circle text-success" style="font-size: 8px;"></i> Online';
        } else {
            badge.className = 'offline-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle';
            badge.innerHTML = '<i class="fa-solid fa-cloud-slash text-warning"></i> Offline (Queued)';
        }
    }

    window.addEventListener('online', updateStatus);
    window.addEventListener('offline', updateStatus);
    updateStatus();
}

/* ==========================================================================
   3. Interactive Crop Picker Chips
   ========================================================================== */
function initCropPicker() {
    const chips = document.querySelectorAll('.crop-chip-item');
    const selectElem = document.getElementById('crop_name');

    if (!chips.length || !selectElem) return;

    chips.forEach(chip => {
        chip.addEventListener('click', function () {
            chips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const cropVal = this.getAttribute('data-crop');
            selectElem.value = cropVal;

            // Trigger change event if needed
            const event = new Event('change');
            selectElem.dispatchEvent(event);
        });
    });

    selectElem.addEventListener('change', function () {
        chips.forEach(chip => {
            if (chip.getAttribute('data-crop') === selectElem.value) {
                chip.classList.add('active');
            } else {
                chip.classList.remove('active');
            }
        });
    });
}

/* ==========================================================================
   4. Photo Quality Gate & Canvas Pre-Check
   ========================================================================== */
function initPhotoQualityGate() {
    const fileInput = document.getElementById('leaf_image');
    const qualityResult = document.getElementById('qualityGateResult');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const dropZoneContent = document.getElementById('dropZoneContent');

    if (!fileInput || !qualityResult) return;

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const img = new Image();
            img.onload = function () {
                if (imagePreview) {
                    imagePreview.src = event.target.result;
                    if (previewContainer) previewContainer.style.display = 'block';
                    if (dropZoneContent) dropZoneContent.style.display = 'none';
                }
                analyzeImageQuality(img, file.size);
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function analyzeImageQuality(img, fileSizeBytes) {
    const container = document.getElementById('qualityGateResult');
    if (!container) return;

    // Use offscreen canvas to sample pixels
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const sampleWidth = 200;
    const sampleHeight = Math.round((img.height / img.width) * sampleWidth);
    canvas.width = sampleWidth;
    canvas.height = sampleHeight;

    ctx.drawImage(img, 0, 0, sampleWidth, sampleHeight);
    const imgData = ctx.getImageData(0, 0, sampleWidth, sampleHeight);
    const data = imgData.data;

    let totalLuminance = 0;
    let grayPixels = [];

    for (let i = 0; i < data.length; i += 4) {
        // Luminance formula
        const lum = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
        totalLuminance += lum;
        grayPixels.push(lum);
    }

    const avgLuminance = totalLuminance / grayPixels.length;

    // Standard deviation as a proxy for contrast/sharpness
    let variance = 0;
    for (let i = 0; i < grayPixels.length; i++) {
        variance += Math.pow(grayPixels[i] - avgLuminance, 2);
    }
    const stdDev = Math.sqrt(variance / grayPixels.length);

    // Assess Lighting
    let lightingBadge = '';
    if (avgLuminance < 60) {
        lightingBadge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation"></i> Dim Lighting</span>';
    } else if (avgLuminance > 210) {
        lightingBadge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-sun"></i> Overexposed Glare</span>';
    } else {
        lightingBadge = '<span class="badge bg-success"><i class="fa-solid fa-circle-check"></i> Good Lighting</span>';
    }

    // Assess Sharpness & Contrast
    let sharpnessBadge = '';
    if (stdDev < 28) {
        sharpnessBadge = '<span class="badge bg-danger"><i class="fa-solid fa-blur"></i> Blurry / Low Contrast</span>';
    } else {
        sharpnessBadge = '<span class="badge bg-success"><i class="fa-solid fa-eye"></i> Clear Focus</span>';
    }

    // Assess Resolution
    let resolutionBadge = '';
    if (img.width < 320 || img.height < 320) {
        resolutionBadge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-compress"></i> Low Resolution</span>';
    } else {
        resolutionBadge = `<span class="badge bg-info text-white"><i class="fa-solid fa-expand"></i> ${img.width}x${img.height}px</span>`;
    }

    container.innerHTML = `
        <div class="card border-0 bg-light p-3 rounded-3 mt-3 shadow-xs">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-bold text-forest"><i class="fa-solid fa-shield-check text-success me-1"></i> Photo Quality Gate</span>
                <span class="extra-small text-muted">${(fileSizeBytes / 1024).toFixed(0)} KB</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                ${lightingBadge}
                ${sharpnessBadge}
                ${resolutionBadge}
            </div>
            ${avgLuminance < 60 || stdDev < 28 ?
                '<div class="extra-small text-danger mt-2 fw-semibold"><i class="fa-solid fa-circle-info me-1"></i> Tip: Retake in daylight 10-15cm away for highest AI accuracy.</div>' :
                '<div class="extra-small text-success mt-2 fw-semibold"><i class="fa-solid fa-circle-check me-1"></i> Excellent leaf photo! Ready for AI analysis.</div>'
            }
        </div>
    `;
    container.style.display = 'block';
}

/* ==========================================================================
   5. Live Camera Capture via WebRTC
   ========================================================================== */
let cameraStream = null;

function initCameraCapture() {
    const btnOpenCam = document.getElementById('btnOpenCam');
    const modalElem = document.getElementById('cameraCaptureModal');
    const video = document.getElementById('cameraVideo');
    const btnSnap = document.getElementById('btnSnapPhoto');
    const fileInput = document.getElementById('leaf_image');

    if (!btnOpenCam || !video || !btnSnap) return;

    btnOpenCam.addEventListener('click', async function () {
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 } }
            });
            video.srcObject = cameraStream;
            const camModal = new bootstrap.Modal(modalElem);
            camModal.show();
        } catch (err) {
            console.warn('[Camera] Direct webcam error, triggering file camera picker:', err);
            // Fallback: trigger standard mobile capture input
            fileInput.setAttribute('capture', 'environment');
            fileInput.click();
        }
    });

    if (modalElem) {
        modalElem.addEventListener('hidden.bs.modal', function () {
            if (cameraStream) {
                cameraStream.getTracks().forEach(t => t.stop());
                cameraStream = null;
            }
        });
    }

    btnSnap.addEventListener('click', function () {
        if (!video.videoWidth) return;

        const snapCanvas = document.createElement('canvas');
        snapCanvas.width = video.videoWidth;
        snapCanvas.height = video.videoHeight;
        const ctx = snapCanvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        snapCanvas.toBlob(function (blob) {
            const capturedFile = new File([blob], "camera_leaf_" + Date.now() + ".jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(capturedFile);
            fileInput.files = dataTransfer.files;

            // Trigger change event on input
            const event = new Event('change');
            fileInput.dispatchEvent(event);

            const camModal = bootstrap.Modal.getInstance(modalElem);
            if (camModal) camModal.hide();
        }, 'image/jpeg', 0.92);
    });
}

/* ==========================================================================
   6. Web Speech API Voice Advisory Readout (English & Tamil)
   ========================================================================== */
let isSpeaking = false;

function initVoiceReadout() {
    const voiceBtn = document.getElementById('btnListenAdvisory');
    if (!voiceBtn) return;

    voiceBtn.addEventListener('click', function () {
        if (!('speechSynthesis' in window)) {
            alert('Voice audio is not supported in your browser.');
            return;
        }

        if (isSpeaking) {
            window.speechSynthesis.cancel();
            updateVoiceBtn(false);
            isSpeaking = false;
            return;
        }

        const advisoryText = voiceBtn.getAttribute('data-advisory-text') || '';
        const langCode = voiceBtn.getAttribute('data-lang') || 'en';

        if (!advisoryText.trim()) return;

        const utterance = new SpeechSynthesisUtterance(advisoryText);
        utterance.rate = 0.95; // slightly slower for better farmer clarity
        utterance.pitch = 1.0;

        if (langCode === 'ta') {
            utterance.lang = 'ta-IN';
        } else {
            utterance.lang = 'en-US';
        }

        utterance.onstart = function () {
            isSpeaking = true;
            updateVoiceBtn(true);
        };

        utterance.onend = function () {
            isSpeaking = false;
            updateVoiceBtn(false);
        };

        utterance.onerror = function () {
            isSpeaking = false;
            updateVoiceBtn(false);
        };

        window.speechSynthesis.speak(utterance);
    });
}

function updateVoiceBtn(playing) {
    const voiceBtn = document.getElementById('btnListenAdvisory');
    const visualizer = document.getElementById('audioVisualizer');
    const btnLabel = document.getElementById('voiceBtnLabel');

    if (!voiceBtn) return;

    if (playing) {
        if (visualizer) visualizer.style.display = 'inline-flex';
        if (btnLabel) btnLabel.innerText = 'Stop Audio';
        voiceBtn.classList.remove('btn-leaf');
        voiceBtn.classList.add('btn-danger');
    } else {
        if (visualizer) visualizer.style.display = 'none';
        if (btnLabel) btnLabel.innerText = 'Listen Advisory';
        voiceBtn.classList.add('btn-leaf');
        voiceBtn.classList.remove('btn-danger');
    }
}

/* ==========================================================================
   7. Live Instant Search Filters
   ========================================================================== */
function initLiveSearch() {
    // Disease Library instant search
    const diseaseInput = document.getElementById('diseaseLiveSearch');
    if (diseaseInput) {
        diseaseInput.addEventListener('keyup', function () {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.disease-card-item');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // History Table live search
    const historyInput = document.getElementById('historyLiveSearch');
    if (historyInput) {
        historyInput.addEventListener('keyup', function () {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.history-table-row');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
}

/* ==========================================================================
   8. WhatsApp Share Helper
   ========================================================================== */
function shareOnWhatsApp(crop, disease, confidence, symptoms) {
    const text = `🌿 *OpenCropCare Advisory Report*\n` +
        `• *Crop:* ${crop}\n` +
        `• *Identified Condition:* ${disease}\n` +
        `• *AI Confidence:* ${confidence}%\n` +
        `• *Key Symptoms:* ${symptoms}\n\n` +
        `*Advisory:* Verified preliminary foliar diagnosis via OpenCropCare. Consult local Krishi Vigyan Kendra (KVK) for physical verification.`;

    const url = `https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
}
