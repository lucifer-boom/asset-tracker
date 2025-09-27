<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Asset QR Scanner</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 20px;
}

h1 {
    text-align: center;
    color: #007bff;
    margin-bottom: 20px;
}

/* Scanner Box */
.scanner-container {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

#reader-wrapper {
    width: 90%;
    max-width: 300px;  /* smaller scanner */
    aspect-ratio: 4/3;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

#reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}

/* Loading Spinner */
#loadingSpinner {
    max-width: 400px;
    margin: 20px auto;
    text-align: center;
}

/* Error Alert */
.alert {
    max-width: 400px;
    margin: 10px auto 20px auto;
    font-weight: 500;
}

/* Asset Details Card */
.asset-info {
    max-width: 500px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 20px;
    display: none;
}

.asset-info h3 {
    margin-bottom: 20px;
    color: #007bff;
    text-align: center;
}

.asset-info .row {
    margin-bottom: 10px;
}

.asset-info .label {
    font-weight: 600;
}

.asset-info .value {
    font-weight: 500;
}

/* Responsive */
@media (max-width: 576px) {
    #reader-wrapper {
        max-width: 100%;
    }
    .asset-info {
        padding: 15px;
    }
}
</style>
</head>
<body>

<h1>Asset QR Scanner</h1>

<!-- Scanner -->
<div class="scanner-container">
    <div id="reader-wrapper">
        <div id="reader"></div>
    </div>
</div>

<!-- Loading Spinner -->
<div class="text-center my-3" id="loadingSpinner" style="display:none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <div class="mt-2">Fetching asset details...</div>
</div>

<!-- Error Message -->
<div class="alert alert-danger" id="errorAlert"></div>

<!-- Asset Details Card -->
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

function onScanSuccess(decodedText) {
    const assetId = decodedText.split('/').pop();
    fetchAsset(assetId);
}

const html5QrCode = new Html5Qrcode("reader");
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 200 },
    onScanSuccess
);
</script>

</body>
</html>
