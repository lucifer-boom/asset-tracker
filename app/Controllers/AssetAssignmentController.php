<?php

namespace App\Controllers;

use App\Models\AssetModel;
use App\Models\AssetAssignmentModel;
use App\Models\UserModel;

class AssetAssignmentController extends BaseController
{
    public function index()
    {
        $assetModel = new AssetModel();
        $assignmentModel = new AssetAssignmentModel();
        $userModel = new UserModel();

        // Available assets
        $data['assets'] = $assetModel
            ->select('assets.id, assets.asset_code, asset_models.name as model_name')
            ->join('asset_models', 'asset_models.id = assets.model_id', 'left')
            ->whereNotIn('assets.id', function ($builder) {
                $builder->select('asset_id')
                    ->from('assets_assignments')
                    ->where('status', 'assigned');
            })
            ->findAll();

        // Users
        $data['users'] = $userModel
            ->select('users.id, users.username, departments.name as department_name')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->orderBy('users.username', 'ASC')
            ->findAll();

        // Active assignments
        $data['activeAssignments'] = $assignmentModel
            ->select('assets_assignments.id, assets.asset_code, asset_models.name as model_name, users.username, departments.name as department_name')
            ->join('assets', 'assets.id = assets_assignments.asset_id')
            ->join('asset_models', 'asset_models.id = assets.model_id', 'left')
            ->join('users', 'users.id = assets_assignments.user_id')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('assets_assignments.status', 'assigned')
            ->findAll();

        // Assignment history
        $data['history'] = $assignmentModel
            ->select('assets_assignments.id, assets.asset_code, asset_models.name as model_name, users.username, departments.name as department_name, assets_assignments.assigned_date, assets_assignments.returned_date, assets_assignments.remarks, assets_assignments.status')
            ->join('assets', 'assets.id = assets_assignments.asset_id')
            ->join('asset_models', 'asset_models.id = assets.model_id', 'left')
            ->join('users', 'users.id = assets_assignments.user_id')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->orderBy('assets_assignments.id', 'DESC')
            ->findAll();

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/assignment', $data);
        echo view('includes/footer');
    }

    public function store()
    {
        $assignmentModel = new AssetAssignmentModel();

        $assetId      = $this->request->getPost('asset_id');
        $userId       = $this->request->getPost('user_id');
        $assignedDate = $this->request->getPost('assigned_date');
        $remarks      = $this->request->getPost('remarks');

        // Insert assignment
        $assignmentModel->insert([
            'asset_id'      => $assetId,
            'user_id'       => $userId,
            'assigned_date' => $assignedDate,
            'remarks'       => $remarks,
            'status'        => 'assigned'
        ]);

        $db = \Config\Database::connect();
        $userModel = new UserModel();

        // Assigned user info
        $assignedUser = $userModel->find($userId);

        // Asset info
        $asset = $db->table('assets')
            ->select('assets.asset_code, asset_models.name as model_name')
            ->join('asset_models', 'asset_models.id = assets.model_id', 'left')
            ->where('assets.id', $assetId)
            ->get()
            ->getRowArray();

        // IT Admin & Super Admin emails
        $query = $db->table('user_roles')
            ->select('users.email')
            ->join('users', 'users.id = user_roles.user_id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->whereIn('roles.name', ['IT Admin', 'Super Admin'])
            ->get();

        $ccEmails = array_column($query->getResultArray(), 'email');

        // Prepare email
        $email = \Config\Services::email();

        $email->setFrom('sehanm234@gmail.com', 'CA-Asset-Tracker');
        $email->setTo($assignedUser['email']);
        if (!empty($ccEmails)) {
            $email->setCC($ccEmails);
        }
        $email->setSubject('New Asset Assigned');

        $displayRemarks = $remarks ? $remarks : 'No additional remarks';

        $message = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Asset Assignment Notification</title>
<style>
body { font-family: Arial, sans-serif; line-height: 1.6; color: #2c3e50; background: #f8f9fa; padding: 20px; }
.email-wrapper { max-width:600px; margin:auto; background:#fff; border-radius:8px; overflow:hidden; }
.header { background:#2c3e50; color:#fff; text-align:center; padding:30px 20px; }
.header h1 { font-size:1.8rem; margin-bottom:5px; }
.content { padding:35px 30px; }
.asset-details { background:#f8f9fa; border:1px solid #e9ecef; padding:25px; border-radius:6px; margin:25px 0; }
.detail-row { display:flex; margin-bottom:12px; }
.detail-label { font-weight:600; min-width:140px; }
.detail-value { flex:1; margin-left:15px; }
.footer { text-align:center; padding:20px; font-size:12px; color:#777; border-top:1px solid #e9ecef; }
</style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <h1>CA-Asset-Tracker</h1>
        <div class="subtitle">Asset Management System</div>
    </div>
    <div class="content">
        <p>Dear <strong>{$assignedUser['username']}</strong>,</p>
        <p>You have been assigned an asset. Please see the details below:</p>
        <div class="asset-details">
            <div class="detail-row"><div class="detail-label">Asset Code:</div><div class="detail-value">{$asset['asset_code']}</div></div>
            <div class="detail-row"><div class="detail-label">Model Name:</div><div class="detail-value">{$asset['model_name']}</div></div>
            <div class="detail-row"><div class="detail-label">Assigned Date:</div><div class="detail-value">{$assignedDate}</div></div>
            <div class="detail-row"><div class="detail-label">Remarks:</div><div class="detail-value">{$displayRemarks}</div></div>
        </div>
        <p>Please follow company guidelines for asset usage.</p>
        <p>Thank you,<br>CA Sri Lanka - ICT Division</p>
    </div>
    <div class="footer">This is a system-generated email. Please do not reply.<br></div>
</div>
</body>
</html>
EOD;


        $email->setMessage($message);

        if (!$email->send()) {
            log_message('error', $email->printDebugger(['headers']));
        }

        return redirect()->to('/assets/assignments');
    }

    public function return()
{
    $assignmentModel = new AssetAssignmentModel();
    $id = $this->request->getPost('assignment_id');

    $returnedDate = $this->request->getPost('return_date');
    $remarks = $this->request->getPost('remarks');

    // Update assignment
    $assignmentModel->update($id, [
        'returned_date' => $returnedDate,
        'remarks' => $remarks,
        'status' => 'returned'
    ]);

    $db = \Config\Database::connect();
    $assignment = $assignmentModel->find($id);

    $userModel = new \App\Models\UserModel();
    $assignedUser = $userModel->find($assignment['user_id']);

    $asset = $db->table('assets')
        ->select('assets.asset_code, asset_models.name as model_name')
        ->join('asset_models', 'asset_models.id = assets.model_id', 'left')
        ->where('assets.id', $assignment['asset_id'])
        ->get()
        ->getRowArray();

    // Get IT Admin and Super Admin emails
    $query   = $db->table('user_roles')
        ->select('users.email')
        ->join('users', 'users.id = user_roles.user_id')
        ->join('roles', 'roles.id = user_roles.role_id')
        ->whereIn('roles.name', ['IT Admin', 'Super Admin'])
        ->get();

    $ccEmails = array_column($query->getResultArray(), 'email');

    $displayRemarks = $remarks ?: 'No additional remarks';

    // Prepare email
    $email = \Config\Services::email();
    
    // Send to assigned user
    $email->setTo($assignedUser['email']);
    
    // CC IT Admin & Super Admin
    if (!empty($ccEmails)) {
        $email->setCC($ccEmails);
    }

    $email->setFrom('sehanm234@gmail.com', 'CA-Asset-Tracker');
    $email->setSubject('Asset Returned Notification');

    $message = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Asset Returned Notification</title>
<style>
body { font-family: Arial, sans-serif; line-height:1.6; color:#2c3e50; background:#f8f9fa; padding:20px; }
.email-wrapper { max-width:600px; margin:auto; background:#fff; border-radius:8px; overflow:hidden; }
.header { background:#2c3e50; color:#fff; text-align:center; padding:30px 20px; }
.header h1 { font-size:1.8rem; margin-bottom:5px; }
.content { padding:35px 30px; }
.asset-details { background:#f8f9fa; border:1px solid #e9ecef; padding:25px; border-radius:6px; margin:25px 0; }
.detail-row { display:flex; margin-bottom:12px; }
.detail-label { font-weight:600; min-width:140px; }
.detail-value { flex:1; margin-left:15px; }
.footer { text-align:center; padding:20px; font-size:12px; color:#777; border-top:1px solid #e9ecef; }
</style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <h1>CA-Asset-Tracker</h1>
        <div class="subtitle">Asset Management System</div>
    </div>
    <div class="content">
        <p>Dear <strong>{$assignedUser['username']}</strong>,</p>
        <p>The following asset has been returned successfully:</p>
        <div class="asset-details">
            <div class="detail-row"><div class="detail-label">Asset Code:</div><div class="detail-value">{$asset['asset_code']}</div></div>
            <div class="detail-row"><div class="detail-label">Model Name:</div><div class="detail-value">{$asset['model_name']}</div></div>
            <div class="detail-row"><div class="detail-label">Returned Date:</div><div class="detail-value">{$returnedDate}</div></div>
            <div class="detail-row"><div class="detail-label">Remarks:</div><div class="detail-value">{$displayRemarks}</div></div>
        </div>
        <p>Thank you for following the company asset return procedures.</p>
        <p>Regards,<br>CA Sri Lanka - ICT Division</p>
    </div>
    <div class="footer">This is a system-generated email. Please do not reply.</div>
</div>
</body>
</html>
EOD;

    $email->setMessage($message);

    if (!$email->send()) {
        log_message('error', $email->printDebugger(['headers']));
    }

    return redirect()->to('/assets/assignments');
}


}
