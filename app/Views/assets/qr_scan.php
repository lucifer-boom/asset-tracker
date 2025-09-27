<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Asset QR Scanner</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#007bff">

<style>
body { background: #f8f9fa; font-family: Arial,sans-serif; padding: 20px; }
.scanner-container { display: flex; justify-content: center; margin-bottom: 20px; }
#reader { width: 100%; max-width: 350px; border: 2px dashed #6c757d; border-radius: 12px; padding: 10px; background: #fff; transition: 0.3s all; }
#reader.loading { border-color: #007bff; box-shadow: 0 0 10px rgba(0,123,255,0.5); }
#loadingSpinner { max-width: 600px; margin: 0 auto 20px auto; }
.asset-info { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px; display: none; }
.asset-info h3 { margin-bottom: 20px; color: #007bff; text-align: center; }
.asset-info .row { margin-bottom: 10px; }
.asset-info .label { font-weight: bold; color: #495057; }
.asset-info .value { color: #212529; }
.alert { max-width: 600px; margin: 10px auto; display: none; }
</style>
</head>
<body>

<div class="scanner-container">
    <div id="reader"></div>
</div>

<div class="text-center my-3" id="loadingSpinner" style="display:none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <div>Fetching asset details...</div>
</div>

<div class="alert alert-danger" id="errorAlert"></div>

<div class="asset-info" id="assetDetails">
    <h3>Asset Details</h3>
    <div class="row"><span class="label col-5">Asset ID:</span><span class="value col-7" id="assetId"></span></div>
    <div class="row"><span class="label col-5">Model:</span><span class="value col-7" id="modelName"></span></div>
    <div class="row"><span class="label col-5">Category:</span><span class="value col-7" id="categoryName"></span></div>
    <div class="row"><span class="label col-5">Serial Number:</span><span class="value col-7" id="serialNumber"></span></div>
    <div class="row"><span class="label col-5">Asset Code:</span><span class="value col-7" id="assetCode"></span></div>
    <div class="row"><span class="label col-5">Purchase Date:</span><span class="value col-7" id="purchaseDate"></span></div>
    <div class="row"><span class="label col-5">Supplier:</span><span class="value col-7" id="supplierName"></span></div>
    <div class="row"><span class="label col-5">Value:</span><span class="value col-7" id="value"></span></div>
</div>

<!-- HTML5 QR Code -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const readerElement = document.getElementById('reader');
const spinner = document.getElementById('loadingSpinner');
const errorAlert = document.getElementById('errorAlert');
const assetDetails = document.getElementById('assetDetails');

// Fetch asset details from server
function fetchAsset(assetId) {
    spinner.style.display = 'block';
    errorAlert.style.display = 'none';
    assetDetails.style.display = 'none';

    fetch(`/assets/view/${assetId}`)
        .then(res => res.json())
        .then(data => {
            spinner.style.display = 'none';
            if(data.error){
                errorAlert.innerText = data.error;
                errorAlert.style.display = 'block';
                return;
            }
            assetDetails.style.display = 'block';
            document.getElementById('assetId').innerText = data.id || '-';
            document.getElementById('modelName').innerText = data.model_name || '-';
            document.getElementById('categoryName').innerText = data.category_name || '-';
            document.getElementById('serialNumber').innerText = data.serial_number || '-';
            document.getElementById('assetCode').innerText = data.asset_code || '-';
            document.getElementById('purchaseDate').innerText = data.purchase_date || '-';
            document.getElementById('supplierName').innerText = data.supplier_name || '-';
            document.getElementById('value').innerText = data.value || '-';
        })
        .catch(err => {
            spinner.style.display = 'none';
            errorAlert.innerText = 'Failed to fetch asset details.';
            errorAlert.style.display = 'block';
        });
}

// QR Code scan success
function onScanSuccess(decodedText) {
    readerElement.classList.add('loading');
    const assetId = decodedText.split('/').pop();
    fetchAsset(assetId);
    setTimeout(() => readerElement.classList.remove('loading'), 500);
}

// Initialize scanner
const html5QrCode = new Html5Qrcode("reader");
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    onScanSuccess
);

// Register service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then(() => console.log('Service Worker registered'));
}
</script>

</body>
</html>
