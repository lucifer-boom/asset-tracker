<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset QR Scanner</title>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
<meta name="theme-color" content="#4361ee">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --danger: #e63946;
            --warning: #fca311;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        
        .app-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px 0;
        }
        
        .header h1 {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 5px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .header p {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .scanner-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .scanner-container {
            position: relative;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            aspect-ratio: 1/1;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        #reader {
            width: 100%;
            height: 100%;
        }
        
        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }
        
        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }
        
        .scanner-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            border: 3px solid var(--primary);
            border-radius: 10px;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.3);
        }
        
        .scanner-line {
            position: absolute;
            top: 20%;
            left: 10%;
            width: 80%;
            height: 3px;
            background: var(--primary);
            animation: scan 2s infinite linear;
            border-radius: 3px;
        }
        
        @keyframes scan {
            0% { top: 20%; }
            50% { top: 80%; }
            100% { top: 20%; }
        }
        
        .scanner-instructions {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .scanner-instructions i {
            margin-right: 5px;
            color: var(--primary);
        }
        
        .status-section {
            margin: 20px 0;
        }
        
        #loadingSpinner {
            text-align: center;
            padding: 20px;
            display: none;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            color: var(--primary);
        }
        
        #errorAlert {
            display: none;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .asset-info {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: none;
        }
        
        .asset-info h3 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .label {
            font-weight: 600;
            color: #495057;
            flex: 1;
        }
        
        .value {
            font-weight: 500;
            color: var(--dark);
            flex: 1;
            text-align: right;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }
        
        .footer {
            text-align: center;
            padding: 15px;
            color: #6c757d;
            font-size: 0.8rem;
            margin-top: auto;
        }
        
        /* Camera controls */
        .camera-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }
        
        .camera-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .camera-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        
        .camera-btn:active {
            transform: scale(0.95);
        }
       
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .app-container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .scanner-section, .asset-info {
                padding: 15px;
            }
            
            .scanner-container {
                max-width: 100%;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .value {
                text-align: left;
                margin-top: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
        
        @media (min-width: 768px) {
            .app-container {
                max-width: 700px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
                color: #e2e8f0;
            }
            
            .scanner-section, .asset-info {
                background: #2d3748;
                color: #e2e8f0;
            }
            
            .label {
                color: #cbd5e0;
            }
            
            .value {
                color: #e2e8f0;
            }
            
            .scanner-instructions {
                color: #a0aec0;
            }
        }
    </style>
</head>
<body>
    <div class="text-center my-3" id="installPromptContainer" style="display:none;">
    <button id="installBtn" class="btn btn-primary">
        <i class="fas fa-download"></i> Install App
    </button>
</div>
    <div class="app-container">

        <div class="header">
            <h1><i class="fas fa-qrcode"></i> Asset QR Scanner</h1>
            <p>Scan QR codes to retrieve asset information</p>
        </div>
        
        <div class="scanner-section">
            <div class="scanner-container">
                <div id="reader"></div>
                <div class="scanner-overlay">
                    <div class="scanner-frame"></div>
                    <div class="scanner-line"></div>
                </div>
            </div>
            
            <div class="scanner-instructions">
                <p><i class="fas fa-lightbulb"></i> Point your camera at the QR code to scan</p>
            </div>
            
            <div class="camera-controls">
                <button id="switchCamera" class="camera-btn" title="Switch Camera">
                    <i class="fas fa-camera-retro"></i>
                </button>
             
            </div>
            
            <div class="status-section">
                <!-- Loading Spinner -->
                <div class="text-center my-3" id="loadingSpinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-3">Fetching asset details...</div>
                </div>

                <!-- Error Message -->
                <div class="alert alert-danger" id="errorAlert"></div>
            </div>
        </div>
        
        <!-- Asset Details Card -->
        <div class="asset-info" id="assetDetails">
            <h3><i class="fas fa-cube"></i> Asset Details</h3>
            <div class="info-row">
                <span class="label">Asset ID:</span>
                <span class="value" id="assetId">-</span>
            </div>
            <div class="info-row">
                <span class="label">Model:</span>
                <span class="value" id="modelName">-</span>
            </div>
            <div class="info-row">
                <span class="label">Category:</span>
                <span class="value" id="categoryName">-</span>
            </div>
            <div class="info-row">
                <span class="label">Serial Number:</span>
                <span class="value" id="serialNumber">-</span>
            </div>
            <div class="info-row">
                <span class="label">Asset Code:</span>
                <span class="value" id="assetCode">-</span>
            </div>
            <div class="info-row">
                <span class="label">Purchase Date:</span>
                <span class="value" id="purchaseDate">-</span>
            </div>
            <div class="info-row">
                <span class="label">Supplier:</span>
                <span class="value" id="supplierName">-</span>
            </div>
            <div class="info-row">
                <span class="label">Value:</span>
                <span class="value" id="value">-</span>
            </div>
            
            <div class="action-buttons">
                <button id="scanAgainBtn" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Scan Another QR
                </button>
                <button id="shareBtn" class="btn btn-outline-primary">
                    <i class="fas fa-share-alt"></i> Share Details
                </button>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2025</p>
            <p>Desined & Developed By CA Sri Lanka - ICT Division</p>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
        .then((reg) => console.log('Service Worker Registered', reg))
        .catch((err) => console.error('SW registration failed:', err));
}

</script>
    <script>

        let deferredPrompt;
const installPromptContainer = document.getElementById('installPromptContainer');
const installBtn = document.getElementById('installBtn');

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    e.prompt(); // <-- shows install popup immediately
});

// Handle install button click
installBtn.addEventListener('click', async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    if (outcome === 'accepted') {
        console.log('User accepted the install prompt');
    } else {
        console.log('User dismissed the install prompt');
    }
    // Hide the button after prompting
    installPromptContainer.style.display = 'none';
    deferredPrompt = null;
});

        const readerElement = document.getElementById('reader');
        const spinner = document.getElementById('loadingSpinner');
        const errorAlert = document.getElementById('errorAlert');
        const assetDetails = document.getElementById('assetDetails');
        const scanAgainBtn = document.getElementById('scanAgainBtn');
        const shareBtn = document.getElementById('shareBtn');
        const switchCameraBtn = document.getElementById('switchCamera');
        
        let html5QrCode;
        let currentCameraId = null;
        let cameras = [];
        let currentCameraIndex = 0;
        
        function fetchAsset(assetId) {
            spinner.style.display = 'block';
            errorAlert.style.display = 'none';
            assetDetails.style.display = 'none';
            
            // Fetch asset data from your API
            fetch(`/assets/view/${assetId}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(data => {
                    spinner.style.display = 'none';
                    if(data.error){
                        errorAlert.innerText = data.error;
                        errorAlert.style.display = 'block';
                        // Restart scanner after error
                        setTimeout(() => {
                            initScanner();
                        }, 3000);
                        return;
                    }
                    displayAssetData(data);
                })
                .catch(err => {
                    spinner.style.display = 'none';
                    errorAlert.innerText = 'Failed to fetch asset details. Please check your connection and try again.';
                    errorAlert.style.display = 'block';
                    console.error('Error fetching asset:', err);
                    // Restart scanner after error
                    setTimeout(() => {
                        initScanner();
                    }, 3000);
                });
        }
        
        function displayAssetData(data) {
            assetDetails.style.display = 'block';
            document.getElementById('assetId').innerText = data.id || '-';
            document.getElementById('modelName').innerText = data.model_name || '-';
            document.getElementById('categoryName').innerText = data.category_name || '-';
            document.getElementById('serialNumber').innerText = data.serial_number || '-';
            document.getElementById('assetCode').innerText = data.asset_code || '-';
            document.getElementById('purchaseDate').innerText = data.purchase_date || '-';
            document.getElementById('supplierName').innerText = data.supplier_name || '-';
            document.getElementById('value').innerText = data.value || '-';
            
            // Scroll to asset details for better UX
            assetDetails.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        function showError(message) {
            errorAlert.innerText = message;
            errorAlert.style.display = 'block';
        }
        
        function onScanSuccess(decodedText) {
            // Stop the scanner to prevent multiple scans
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    console.log("QR Code scanning stopped.");
                }).catch(err => {
                    console.error("Failed to stop scanning.", err);
                });
            }
            
            // Extract asset ID from URL if needed
            const assetId = decodedText.split('/').pop();
            fetchAsset(assetId);
        }
        
        function onScanFailure(error) {
            // Handle scan failure, but ignore most errors to keep the scanner running
            console.warn(`QR error = ${error}`);
        }
        
        // Initialize scanner
        function initScanner() {
            // Clear any previous scanner instance
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    console.log("Previous scanner stopped.");
                }).catch(err => {
                    console.error("Error stopping previous scanner:", err);
                });
            }
            
            html5QrCode = new Html5Qrcode("reader");
            
            const config = { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };
            
            // Get available cameras
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameras = devices;
                    
                    // Try to start with the environment (rear) camera first
                    let cameraId = cameras[0].id;
                    for (let i = 0; i < cameras.length; i++) {
                        if (cameras[i].label.toLowerCase().includes('back') || 
                            cameras[i].label.toLowerCase().includes('rear')) {
                            cameraId = cameras[i].id;
                            currentCameraIndex = i;
                            break;
                        }
                    }
                    
                    currentCameraId = cameraId;
                    
                    html5QrCode.start(
                        cameraId,
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).then(() => {
                        console.log("QR Scanner started successfully");
                    }).catch(err => {
                        console.error("Unable to start QR scanner", err);
                        showError("Unable to access camera. Please check permissions.");
                    });
                } else {
                    showError("No camera found on this device.");
                }
            }).catch(err => {
                console.error("Unable to get camera list", err);
                showError("Camera access is required for scanning. Please enable camera permissions.");
            });
        }
        
        // Switch between cameras
        function switchCamera() {
            if (cameras.length < 2) {
                showError("Only one camera available on this device.");
                return;
            }
            
            // Stop current scanner
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    // Switch to next camera
                    currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
                    currentCameraId = cameras[currentCameraIndex].id;
                    
                    // Restart with new camera
                    const config = { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    };
                    
                    html5QrCode.start(
                        currentCameraId,
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).then(() => {
                        console.log("Switched to camera: " + cameras[currentCameraIndex].label);
                    }).catch(err => {
                        console.error("Error switching camera:", err);
                        showError("Failed to switch camera.");
                    });
                }).catch(err => {
                    console.error("Error stopping scanner:", err);
                });
            }
        }
        
       
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initScanner();
        });
        
        // Scan Another QR button
        scanAgainBtn.addEventListener('click', () => {
            assetDetails.style.display = 'none';
            errorAlert.style.display = 'none';
            initScanner();
        });
        
        // Share button functionality
        shareBtn.addEventListener('click', () => {
            if (navigator.share) {
                const assetText = `Asset Details:
ID: ${document.getElementById('assetId').innerText}
Model: ${document.getElementById('modelName').innerText}
Category: ${document.getElementById('categoryName').innerText}
Serial: ${document.getElementById('serialNumber').innerText}`;
                
                navigator.share({
                    title: 'Asset Information',
                    text: assetText,
                    url: window.location.href
                }).then(() => {
                    console.log('Shared successfully');
                }).catch(err => {
                    console.error('Error sharing:', err);
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                alert('Share functionality not supported in this browser. You can copy the details manually.');
            }
        });
        
        // Camera control buttons
        switchCameraBtn.addEventListener('click', switchCamera);
    </script>
</body>
</html>