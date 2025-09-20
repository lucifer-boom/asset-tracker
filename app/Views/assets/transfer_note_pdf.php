<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asset Transfer Form</title>
    <style>
        /* Reset and body styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 40px;
            color: #333;
            background-color: #f9f9f9;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        header h2 {
            text-align: left;
            margin: 0;
            font-size: 20px;
            letter-spacing: 1px;
        }

        header img {
            height: 50px;
        }

        /* Section headers */
        .section-header {
            margin-top: 25px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
            color: #222;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #666;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        /* Signature section */
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            width: 23%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 8px;
            font-size: 12px;
        }

        /* Note styling */
        .note {
            font-size: 10px;
            color: #555;
            margin-top: 10px;
            font-style: italic;
        }

        /* Footer */
        footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 50px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        /* Asset acceptance section */
        .acceptance {
            margin-top: 30px;
            font-size: 12px;
        }

        .acceptance p {
            margin: 4px 0;
        }
    </style>
</head>
<body>

<header>
    <h2>ASSET TRANSFER FORM</h2>
    <img src="logo.png" alt="Company Logo">
</header>

<!-- Transferor Section -->
<div class="section-header">Transferor Section</div>
<p>
    Date: <?= esc($transfer['transfer_date'] ?? '___________________') ?> &nbsp;&nbsp;&nbsp;
    Division: <?= esc($transfer['from_location_name'] ?? '___________________') ?>
</p>

<div>Details of transferring assets:</div>
<table>
    <tr>
        <th>#</th>
        <th>Asset Code</th>
        <th>Description</th>
        <th>Reason for Transfer</th>
    </tr>
    <tr>
        <td>1</td>
        <td><?= esc($transfer['asset_code'] ?? '') ?></td>
        <td><?= esc($transfer['asset_name'] ?? '') ?></td>
        <td><?= esc($transfer['reason_for_transfer'] ?? '') ?></td>
    </tr>
    <?php for($i=2; $i<=6; $i++): ?>
        <tr>
            <td><?= $i ?></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php endfor; ?>
</table>

<p class="note">(If the number of assets to be transferred exceeds the space given, please attach a separate sheet.)</p>

<div class="signature">
    <div>
        <?= esc($transfer['custodian_name'] ?? 'N/A') ?><br>
        User/Custodian of Asset
    </div>
    <div>
        <?= esc($transfer['hod_name'] ?? 'N/A') ?><br>
        HOD
    </div>
    <div>
        <?= esc($transfer['admin_name'] ?? 'N/A') ?><br>
        Mgr. Administration
    </div>
    <div>
        <?= esc($transfer['ceo_name'] ?? 'N/A') ?><br>
        CEO (External Only)
    </div>
</div>

<p class="note">NOTE: CEO's approval is needed if the asset is transferring from H/O to a branch.</p>

<!-- Transferee Section -->
<div class="section-header">Transferee Section</div>
<div class="acceptance">
    <p>Date of accepting asset: <?= esc($transfer['received_date']) ?></p>
    <p>Asset Accepting HOD: <?= esc($transfer['to_hod_name']) ?></p>
    <p>Division: <?= esc($transfer['to_location_name']) ?></p>
</div>

<footer>
    System Generated
</footer>

</body>
</html>
