<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asset Transfer Form</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .section-header { margin-top: 20px; font-weight: bold; }
        .signature { margin-top: 40px; }
        .signature div { display: inline-block; width: 24%; text-align: center; }
        .note { font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>

<h2>ASSET TRANSFER FORM</h2>

<div class="section-header">Transferor Section</div>
<p>Date: <?= esc($transfer['transfer_date']) ?> &nbsp;&nbsp;&nbsp; Division: <?= esc($transfer['from_location_name']) ?></p>

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
        <td><?= esc($transfer['asset_code']) ?></td>
        <td><?= esc($transfer['asset_name']) ?></td>
        <td><?= esc($transfer['reason_for_transfer']) ?></td>
    </tr>
    <!-- Additional rows can be left blank -->
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
        <?= esc($transfer['custodian_name'] ?? '') ?><br>
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

<div class="section-header">Transferee Section</div>
<p>Date of accepting asset: ___________________ &nbsp;&nbsp; Division: ___________________ &nbsp;&nbsp; Location: ___________________</p>

<div class="signature">
    <div>Asset Accepting HOD<br>___________________</div>
    <div>Mgr. Administration<br>___________________</div>
    <div>Updated the FAR<br>☐</div>
    <div>Date: __________________<br>Signature: __________________</div>
</div>

</body>
</html>
