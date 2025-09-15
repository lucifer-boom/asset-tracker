<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Asset QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .scanner-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        #reader {
            width: 100%;
            max-width: 350px;
            border: 2px dashed #6c757d;
            border-radius: 12px;
            padding: 10px;
            background: #fff;
        }
        .asset-info {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            display: none; /* Hide initially */
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
            font-weight: bold;
            color: #495057;
        }
        .asset-info .value {
            color: #212529;
        }
        .alert {
            max-width: 600px;
            margin: 10px auto;
            display: none;
        }
    </style>
</head>
<body>

<div class="scanner-container">
    <div id="reader"></div>
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

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function fetchAsset(assetId) {
    fetch(`/assets/view/${assetId}`)
        .then(response => response.json())
        .then(data => {
            if(data.error){
                document.getElementById('errorAlert').innerText = data.error;
                document.getElementById('errorAlert').style.display = 'block';
                document.getElementById('assetDetails').style.display = 'none';
                return;
            }
            document.getElementById('errorAlert').style.display = 'none';
            document.getElementById('assetDetails').style.display = 'block';
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
            document.getElementById('errorAlert').innerText = 'Failed to fetch asset details.';
            document.getElementById('errorAlert').style.display = 'block';
            document.getElementById('assetDetails').style.display = 'none';
        });
}

function onScanSuccess(decodedText, decodedResult) {
    // Extract the ID from URL
    const assetId = decodedText.split('/').pop();
    fetchAsset(assetId);
}

const html5QrCode = new Html5Qrcode("reader");
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    onScanSuccess
);
</script>
</body>
</html>
