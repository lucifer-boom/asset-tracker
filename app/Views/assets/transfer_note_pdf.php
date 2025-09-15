<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asset Transfer Note</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Asset Transfer Note</h2>

    <table>
        <tr>
            <th>Asset</th>
            <td><?= esc($transfer['asset_name']) ?></td>
        </tr>
        <tr>
            <th>From Department</th>
            <td><?= esc($transfer['from_name']) ?></td>
        </tr>
        <tr>
            <th>To Department</th>
            <td><?= esc($transfer['to_name']) ?></td>
        </tr>
        <tr>
            <th>Asset Custodian</th>
            <td><?= esc($transfer['asset_custodian']) ?></td>
        </tr>
        <tr>
            <th>Transfer Date</th>
            <td><?= esc($transfer['transfer_date']) ?></td>
        </tr>
        <tr>
            <th>Reason</th>
            <td><?= esc($transfer['reason_for_transfer']) ?></td>
        </tr>
    </table>

    <p style="margin-top: 50px;">Approved By HOD: _____________________</p>
    <p>Approved By Admin: _____________________</p>
    <p>Approved By CEO (if external): _____________________</p>
</body>
</html>
