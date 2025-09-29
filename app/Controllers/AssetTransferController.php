<?php

namespace App\Controllers;

use App\Models\AssetModel;
use App\Models\AssetTransferModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use CodeIgniter\Controller;
use Dompdf\Dompdf;

class AssetTransferController extends Controller
{
    /**
     * Show asset transfer form
     */
   public function create()
{
    $session = session();
    $userId = $session->get('user_id');

    $assetModel = new AssetModel();
    $userModel  = new UserModel();
    $deptModel  = new DepartmentModel();
    $assetTransferModel = new AssetTransferModel();

    // Fetch assets with details
    $assets = $assetModel->getAssetsWithDetails();

    // Fetch departments
    $departments = $deptModel->findAll();

    // Get logged-in user details
    $user = $userModel->find($userId);

    // Get all transfers (join with assets to get name/code if needed)
    $transfers = $assetTransferModel->getAllTransfers(); // create this method in model as suggested

    echo view('includes/sidebar');
    echo view('includes/topbar');
    echo view('assets/assets_transfers', [
        'assets'      => $assets,
        'departments' => $departments,
        'user'        => $user,
        'transfers'   => $transfers
    ]);
    echo view('includes/footer');
}


    /**
     * Store asset transfer request
     */
 public function store()
{
    $transferModel = new AssetTransferModel();
    $userModel     = new UserModel();
    $deptModel     = new DepartmentModel();

    $loggedInUserId = session()->get('user_id');
    $loggedInUser   = $userModel->find($loggedInUserId);

    $fromDeptId = $this->request->getPost('from_location');
    $toDeptId   = $this->request->getPost('to_location');

    $fromDept = $deptModel->find($fromDeptId);
    $toDept   = $deptModel->find($toDeptId);

    // Step 1: HOD of custodian
    $hod = $userModel->getHodByDepartment($loggedInUser['department_id']);
    $hodApproval = $hod ? $userModel->find($hod['id']) : null;

    // Step 2: Admin HOD
    $adminDept = $deptModel->where('name', 'Administration & Events')->first();
    $admin = $adminDept ? $userModel->getHodByDepartment($adminDept['id']) : null;
    $adminApproval = $admin ? $userModel->find($admin['id']) : null;

    // Step 3: CEO if external
    $ceoApproval = null;
    $ceoStatus   = null;
    if (isset($toDept['type']) && $toDept['type'] === 'external') {
        $ceoDept = $deptModel->where('name', 'CEO & Secretary')->first();
        $ceo = $ceoDept ? $userModel->getHodByDepartment($ceoDept['id']) : null;
        $ceoApproval = $ceo ? $userModel->find($ceo['id']) : null;
        $ceoStatus = $ceoApproval ? 'pending' : null;
    }

    // Generate unique approval token
    $token = bin2hex(random_bytes(16));

    // Insert transfer
    $transferId = $transferModel->insert([
        'asset_id'        => $this->request->getPost('asset_id'),
        'from_location'   => $fromDeptId,
        'to_location'     => $toDeptId,
        'transfer_date'   => date('Y-m-d'),
        'asset_custodian' => $loggedInUserId,
        'hod_approval'    => $hodApproval['id'] ?? null,
        'admin_approval'  => $adminApproval['id'] ?? null,
        'ceo_approval'    => $ceoApproval['id'] ?? null,
        'hod_status'      => $hodApproval ? 'pending' : null,
        'admin_status'    => $adminApproval ? 'pending' : null,
        'ceo_status'      => $ceoStatus,
        'approval_token'  => $token,
        'reason_for_transfer' => $this->request->getPost('reason_for_transfer'),
    ]);

    // Send email to first pending approver (HOD)
    if ($hodApproval && !empty($hodApproval['email'])) {
        $email = \Config\Services::email();
        $approveLink = base_url("assets/transfer/approve/{$transferId}/{$token}");
        $rejectLink  = base_url("assets/transfer/reject/{$transferId}/{$token}");

        log_message('info', "Approval Link: $approveLink");
    log_message('info', "Rejection Link: $rejectLink");

        $email->setTo($hodApproval['email']);
        $email->setSubject('New Asset Transfer Request Pending Your Approval');
        $email->setMessage("
            Dear {$hodApproval['username']},<br><br>
            A new asset transfer request has been submitted by <b>{$loggedInUser['username']}</b>.<br><br>
            <b>From Department:</b> {$fromDept['name']}<br>
            <b>To Department:</b> {$toDept['name']}<br>
            <b>Reason:</b> {$this->request->getPost('reason_for_transfer')}<br><br>
            Please click below to approve or reject:<br>
            <a href='{$approveLink}' style='padding:10px 15px; background:green; color:white; text-decoration:none;'>Approve</a>
            <a href='{$rejectLink}' style='padding:10px 15px; background:red; color:white; text-decoration:none;'>Reject</a><br><br>
            Thank you.
        ");

        if (!$email->send()) {
            log_message('error', 'Email sending failed: ' . print_r($email->printDebugger(), true));
        }
    }

    return redirect()->to('/assets/assets_transfers')->with('success', 'Transfer request submitted and email sent to HOD.');
}




    /**
     * Show pending approvals for logged-in user
     */
   public function pending()
{
    $userId = session()->get('user_id');
    $transferModel = new \App\Models\AssetTransferModel();
    $assetModel = new \App\Models\AssetModel();
    $deptModel  = new \App\Models\DepartmentModel();
    $userModel  = new \App\Models\UserModel();

    // --- Pending Transfers ---
    $pendingTransfers = $transferModel->getPendingForUser($userId);

    // Add asset, department, and approval date details
    foreach ($pendingTransfers as &$transfer) {
        $asset = $assetModel->find($transfer['asset_id']);
        $transfer['asset_name'] = $asset['asset_code'] ?? '';

        $fromDept = $deptModel->find($transfer['from_location']);
        $transfer['from_name'] = $fromDept['name'] ?? '';

        $toDept = $deptModel->find($transfer['to_location']);
        $transfer['to_name'] = $toDept['name'] ?? '';

        // Add approval users and dates
        $transfer['hod_user']   = $userModel->find($transfer['hod_approval'])['username'] ?? 'N/A';
        $transfer['admin_user'] = $userModel->find($transfer['admin_approval'])['username'] ?? 'N/A';
        $transfer['ceo_user']   = $transfer['ceo_approval'] ? $userModel->find($transfer['ceo_approval'])['username'] : 'N/A';

        $transfer['hod_date']   = $transfer['hod_approval_date'] ?? 'Pending';
        $transfer['admin_date'] = $transfer['admin_approval_date'] ?? 'Pending';
        $transfer['ceo_date']   = $transfer['ceo_approval_date'] ?? 'Pending';
    }

    // --- Past Transfers (approved/rejected by this user) ---
    $builder = $transferModel->builder();
    $builder->groupStart()
        ->where('hod_approval', $userId)
        ->orWhere('admin_approval', $userId)
        ->orWhere('ceo_approval', $userId)
    ->groupEnd();

    $builder->groupStart()
        ->where('hod_status !=', 'pending')
        ->orWhere('admin_status !=', 'pending')
        ->orWhere('ceo_status !=', 'pending')
    ->groupEnd();

    $pastTransfers = $builder->get()->getResultArray();

    // Add asset, department, and approval date details for past transfers
    foreach ($pastTransfers as &$transfer) {
        $asset = $assetModel->find($transfer['asset_id']);
        $transfer['asset_name'] = $asset['asset_code'] ?? '';

        $fromDept = $deptModel->find($transfer['from_location']);
        $transfer['from_name'] = $fromDept['name'] ?? '';

        $toDept = $deptModel->find($transfer['to_location']);
        $transfer['to_name'] = $toDept['name'] ?? '';

        // Add approval users and dates
        $transfer['hod_user']   = $userModel->find($transfer['hod_approval'])['username'] ?? 'N/A';
        $transfer['admin_user'] = $userModel->find($transfer['admin_approval'])['username'] ?? 'N/A';
        $transfer['ceo_user']   = $transfer['ceo_approval'] ? $userModel->find($transfer['ceo_approval'])['username'] : 'N/A';

        $transfer['hod_date']   = $transfer['hod_approval_date'] ?? 'Pending';
        $transfer['admin_date'] = $transfer['admin_approval_date'] ?? 'Pending';
        $transfer['ceo_date']   = $transfer['ceo_approval_date'] ?? 'Pending';
    }

    echo view('includes/sidebar');
    echo view('includes/topbar');
    echo view('assets/pending_transfers', [
        'transfers'     => $pendingTransfers,
        'pastTransfers' => $pastTransfers
    ]);
    echo view('includes/footer');
}


    /**
     * Approve or reject transfer
     */
 public function approve($id)
{
    $action = $this->request->getPost('action');
    $userId = session()->get('user_id');

    $transferModel = new AssetTransferModel();
    $userModel     = new UserModel();
    $deptModel     = new DepartmentModel();

    $transfer = $transferModel->find($id);

    if (!$transfer) {
        return redirect()->back()->with('error', 'Transfer not found.');
    }

    $currentDateTime = date('Y-m-d H:i:s');
    $nextApprover = null;
    $data = [];

    // Determine approval step
    if ($transfer['hod_approval'] == $userId && $transfer['hod_status'] === 'pending') {
        $statusField = 'hod_status';
        $dateField   = 'hod_approval_date';
        $nextApprover = $transfer['admin_approval'] ? $userModel->find($transfer['admin_approval']) : null;
    } elseif ($transfer['admin_approval'] == $userId && $transfer['hod_status'] === 'approved' && $transfer['admin_status'] === 'pending') {
        $statusField = 'admin_status';
        $dateField   = 'admin_approval_date';
        $nextApprover = $transfer['ceo_approval'] ? $userModel->find($transfer['ceo_approval']) : null;
    } elseif ($transfer['ceo_approval'] == $userId && $transfer['admin_status'] === 'approved' && $transfer['ceo_status'] === 'pending') {
        $statusField = 'ceo_status';
        $dateField   = 'ceo_approval_date';
    } else {
        return redirect()->back()->with('error', 'You are not authorized to approve this request or it is not your turn.');
    }

    // Update status
    $data[$statusField] = ($action === 'approve') ? 'approved' : 'rejected';
    if ($action === 'approve') {
        $data[$dateField] = $currentDateTime;
    }

    // Preserve approval token
    $data['approval_token'] = $transfer['approval_token'];

    $transferModel->update($id, $data);

    // Notify next approver
    if ($action === 'approve' && $nextApprover && !empty($nextApprover['email']) && filter_var($nextApprover['email'], FILTER_VALIDATE_EMAIL)) {
        $fromDept = $deptModel->find($transfer['from_location']);
        $toDept   = $deptModel->find($transfer['to_location']);
        $email = \Config\Services::email();

        $approveLink = base_url("assets/transfer/approve/{$id}/{$transfer['approval_token']}");
        $rejectLink  = base_url("assets/transfer/reject/{$id}/{$transfer['approval_token']}");

        $email->setTo($nextApprover['email']);
        $email->setSubject('Asset Transfer Request Pending Your Approval');
        $email->setMessage("
            Dear {$nextApprover['username']},<br><br>
            An asset transfer request is now pending your approval.<br><br>
            <b>Asset ID:</b> {$transfer['asset_id']}<br>
            <b>From Department:</b> {$fromDept['name']}<br>
            <b>To Department:</b> {$toDept['name']}<br>
            <b>Reason:</b> {$transfer['reason_for_transfer']}<br><br>
            Please click below to take action:<br>
            <a href='{$approveLink}' style='padding:10px 15px; background:green; color:white; text-decoration:none;'>Approve</a>
            <a href='{$rejectLink}' style='padding:10px 15px; background:red; color:white; text-decoration:none;'>Reject</a><br><br>
            Thank you.
        ");
        $email->send();
    }

    // Check if fully approved
    $updatedTransfer = $transferModel->find($id);
    $allApproved = (
        ($updatedTransfer['hod_status'] === 'approved') &&
        ($updatedTransfer['admin_status'] === 'approved') &&
        (
            ($updatedTransfer['ceo_approval'] === null) || 
            ($updatedTransfer['ceo_status'] === 'approved')
        )
    );

    if ($allApproved) {
        $custodian = $userModel->find($updatedTransfer['asset_custodian']);
        $toDept    = $deptModel->find($updatedTransfer['to_location']);
        $fromDept  = $deptModel->find($updatedTransfer['from_location']);
        $toHod     = $userModel->getHodByDepartment($updatedTransfer['to_location']); // HOD of receiving department

        // --- Generate receive token if not exists ---
        if (empty($updatedTransfer['receive_token'])) {
            $receiveToken = bin2hex(random_bytes(16)); // 32-character token
            $transferModel->update($id, ['receive_token' => $receiveToken]);
            $updatedTransfer = $transferModel->find($id); // reload with token
        }

        $receiveLink = base_url("assets/transfer/receiveAsset/{$updatedTransfer['id']}/{$updatedTransfer['receive_token']}");

        // Prepare approval summary
        $hodUser   = $userModel->find($updatedTransfer['hod_approval']);
        $adminUser = $userModel->find($updatedTransfer['admin_approval']);
        $ceoUser   = $updatedTransfer['ceo_approval'] ? $userModel->find($updatedTransfer['ceo_approval']) : null;

        $hodDate   = $updatedTransfer['hod_approval_date'] ?? 'Not set';
        $adminDate = $updatedTransfer['admin_approval_date'] ?? 'Not set';
        $ceoDate   = $updatedTransfer['ceo_approval_date'] ?? 'Not set';

        $approvalSummary = "
            <b>Approval Summary:</b><br>
            HOD ({$hodUser['username']}) - {$hodDate}<br>
            Admin ({$adminUser['username']}) - {$adminDate}<br>
        ";
        if ($ceoUser) {
            $approvalSummary .= "CEO ({$ceoUser['username']}) - {$ceoDate}<br>";
        }

        // --- Email to Custodian ---
        if ($custodian && !empty($custodian['email']) && filter_var($custodian['email'], FILTER_VALIDATE_EMAIL)) {
            $email = \Config\Services::email();
            $email->setTo($custodian['email']);
            $email->setSubject('Your Asset Transfer Has Been Approved');
            $email->setMessage("
                Dear {$custodian['username']},<br><br>
                Your asset transfer request has been <b>approved by all required approvers</b>.<br><br>
                <b>Asset ID:</b> {$updatedTransfer['asset_id']}<br>
                <b>From Department:</b> {$fromDept['name']}<br>
                <b>To Department:</b> {$toDept['name']}<br>
                <b>Reason:</b> {$updatedTransfer['reason_for_transfer']}<br><br>
                {$approvalSummary}<br>
                Thank you.
            ");
            $email->send();
        }

        // --- Email to Receiving Department HOD with "Mark as Received" button ---
        if ($toHod && !empty($toHod['email']) && filter_var($toHod['email'], FILTER_VALIDATE_EMAIL)) {
            $email = \Config\Services::email();
            $email->setTo($toHod['email']);
            $email->setSubject('Asset Transfer Received Notification');
            $email->setMessage("
                Dear {$toHod['username']},<br><br>
                The asset <b>{$updatedTransfer['asset_id']}</b> has been transferred to your department (<b>{$toDept['name']}</b>).<br>
                Please acknowledge receipt by clicking the button below:<br><br>
                <a href='{$receiveLink}' style='padding:10px 15px; background:green; color:white; text-decoration:none;'>Mark as Received</a><br><br>
                Thank you,<br>
                <b>CA-Asset-Tracker</b>
            ");
            $email->send();
        }
    }

    return redirect()->back()->with('success', ucfirst($action) . 'd successfully.');
}


    public function approvals()
{
    $session = session();
    $userId = $session->get('user_id');

    $transferModel = new \App\Models\AssetTransferModel();

    // Pending approvals
    $pendingTransfers = $transferModel->getPendingForUser($userId);

    // Past approvals (already approved/rejected/returned)
    $pastTransfers = $transferModel->getPastApprovalsForUser($userId);

    return view('assets/approvals', [
        'pendingTransfers' => $pendingTransfers,
        'pastTransfers'    => $pastTransfers,
    ]);
}



  public function received()
{
    $session = session();
    $userId = $session->get('user_id');

    $transferModel = new AssetTransferModel();
    $assetModel    = new AssetModel();
    $deptModel     = new DepartmentModel();
    $userModel     = new UserModel();

    // ✅ Get transfers that are fully approved and pending receiving
    $transfers = $transferModel
        ->where('hod_status', 'approved')
        ->where('admin_status', 'approved')
        ->groupStart()
            ->where('ceo_status', 'approved')
            ->orWhere('ceo_status', null) // skip CEO if not required
        ->groupEnd()
        ->findAll();

    foreach ($transfers as &$transfer) {
        // Asset info
        $asset = $assetModel->find($transfer['asset_id']);
        $transfer['asset_name'] = $asset['asset_code'] ?? '';

        // From Department
        $fromDept = $deptModel->find($transfer['from_location']);
        $transfer['from_name'] = $fromDept['name'] ?? '';

        // To Department
        $toDept = $deptModel->find($transfer['to_location']);
        $transfer['to_name'] = $toDept['name'] ?? '';

        // HOD of receiving department
        $hod = $userModel->getHodByDepartment($transfer['to_location']);
        $transfer['to_hod_id'] = $hod['id'] ?? null;

      
    }

    // Load views
    echo view('includes/sidebar');
    echo view('includes/topbar');
    echo view('assets/received_transfers', [
        'transfers' => $transfers,
        'user_id'   => $userId
    ]);
    echo view('includes/footer');
}




public function receiveAsset($id, $token = null)
{
    $transferModel = new AssetTransferModel();
    $userModel     = new UserModel();
    $deptModel     = new DepartmentModel();

    // Find transfer
    $transfer = $transferModel->find($id);
    if (!$transfer) {
        return redirect()->back()->with('error', 'Transfer not found.');
    }

    // If token is required, verify it
    if ($token && $transfer['receive_token'] !== $token) {
        return redirect()->back()->with('error', 'Invalid or expired receive link.');
    }

    // Get HOD of receiving department
    $hod = $userModel->getHodByDepartment($transfer['to_location']);
    if (!$hod) {
        return redirect()->back()->with('error', 'Receiving HOD not found.');
    }

    // If HOD is required to be logged in
    $userId = session()->get('user_id');
    if ($userId && $userId != $hod['id']) {
        return redirect()->back()->with('error', 'You are not authorized to mark this asset as received.');
    }

    // Mark as received
    $transferModel->update($id, [
        'received_date' => date('Y-m-d H:i:s'),
        'received_by'   => $hod['id']
    ]);

    // Send email to asset custodian
    $custodian = $userModel->find($transfer['asset_custodian']);
    $toDept    = $deptModel->find($transfer['to_location']);
    $fromDept  = $deptModel->find($transfer['from_location']);

    if ($custodian && !empty($custodian['email']) && filter_var($custodian['email'], FILTER_VALIDATE_EMAIL)) {
        $email = \Config\Services::email();
        $email->setTo($custodian['email']);
        $email->setSubject('Asset Received by Receiving Department');
        $email->setMessage("
            Dear {$custodian['username']},<br><br>
            The asset <b>{$transfer['asset_id']}</b> has been <b>received by the HOD</b> of the receiving department (<b>{$toDept['name']}</b>).<br>
            <b>From Department:</b> {$fromDept['name']}<br>
            <b>Received Date:</b> " . date('Y-m-d H:i:s') . "<br><br>
            Thank you,<br>
            <b>CA-Asset-Tracker</b>
        ");
        $email->send();
    }

    return redirect()->back()->with('success', 'Asset marked as received and custodian notified.');
}
    
public function downloadTransferNote($id)
{
    $transferModel = new \App\Models\AssetTransferModel();
    $userModel     = new \App\Models\UserModel();

    // Fetch transfer with all other details
    $transfer = $transferModel->getTransferWithDetails($id);

    if (!$transfer) {
        return redirect()->back()->with('error', 'Transfer not found.');
    }

    // Get HOD of the receiving (to_location) department
    $receivingHod = $userModel->getHodByDepartment($transfer['to_location']);
    $transfer['to_hod_name'] = $receivingHod['username'] ?? '___________________';

    // Ensure received date is displayed if set
    $transfer['received_date'] = $transfer['received_date'] ?? '___________________';

    // Generate PDF
    $html = view('assets/transfer_note_pdf', ['transfer' => $transfer]);

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream('Asset_Transfer_Form_'.$transfer['asset_code'].'.pdf', ['Attachment' => true]);
}

public function emailApprove($id, $token)
{
    $transferModel = new AssetTransferModel();
    $userModel     = new UserModel();
    $deptModel     = new DepartmentModel();

    // Find transfer using token
    $transfer = $transferModel->where('id', $id)
                              ->where('approval_token', $token)
                              ->first();

    if (!$transfer) {
        return redirect()->to('/login')->with('error', 'Invalid or expired approval link.');
    }

    $currentDateTime = date('Y-m-d H:i:s');
    $nextApprover = null;
    $data = [];

    // Approve current pending step
    if ($transfer['hod_status'] === 'pending') {
        $data['hod_status'] = 'approved';
        $data['hod_approval_date'] = $currentDateTime;
        if ($transfer['admin_approval']) $nextApprover = $userModel->find($transfer['admin_approval']);
    } elseif ($transfer['admin_status'] === 'pending') {
        $data['admin_status'] = 'approved';
        $data['admin_approval_date'] = $currentDateTime;
        if ($transfer['ceo_approval']) $nextApprover = $userModel->find($transfer['ceo_approval']);
    } elseif ($transfer['ceo_status'] === 'pending') {
        $data['ceo_status'] = 'approved';
        $data['ceo_approval_date'] = $currentDateTime;
    } else {
        return redirect()->to('/login')->with('error', 'This transfer has already been approved.');
    }

    // Keep token intact
    $data['approval_token'] = $transfer['approval_token'];
    $transferModel->update($id, $data);

    // Send email to next approver if exists
    if ($nextApprover && !empty($nextApprover['email']) && filter_var($nextApprover['email'], FILTER_VALIDATE_EMAIL)) {
        $fromDept = $deptModel->find($transfer['from_location']);
        $toDept   = $deptModel->find($transfer['to_location']);
        $email = \Config\Services::email();

        $approveLink = base_url("assets/transfer/approve/{$id}/{$transfer['approval_token']}");
        $rejectLink  = base_url("assets/transfer/reject/{$id}/{$transfer['approval_token']}");

        $email->setTo($nextApprover['email']);
        $email->setSubject('Asset Transfer Request Pending Your Approval');
        $email->setMessage("
            Dear {$nextApprover['username']},<br><br>
            An asset transfer request is now pending your approval.<br><br>
            <b>Asset ID:</b> {$transfer['asset_id']}<br>
            <b>From Department:</b> {$fromDept['name']}<br>
            <b>To Department:</b> {$toDept['name']}<br>
            <b>Reason:</b> {$transfer['reason_for_transfer']}<br><br>
            <a href='{$approveLink}' style='padding:10px 15px; background:green; color:white; text-decoration:none;'>Approve</a>
            <a href='{$rejectLink}' style='padding:10px 15px; background:red; color:white; text-decoration:none;'>Reject</a><br><br>
            Thank you.
        ");
        $email->send();
    }

    // Send final summary email if fully approved
    $updatedTransfer = $transferModel->find($id);
    $allApproved = (
        $updatedTransfer['hod_status'] === 'approved' &&
        $updatedTransfer['admin_status'] === 'approved' &&
        (
            ($updatedTransfer['ceo_approval'] === null) || 
            ($updatedTransfer['ceo_status'] === 'approved')
        )
    );

    if ($allApproved) {
        $custodian = $userModel->find($updatedTransfer['asset_custodian']);
        $toDept    = $deptModel->find($updatedTransfer['to_location']);
        $fromDept  = $deptModel->find($updatedTransfer['from_location']);
        $toHod     = $userModel->getHodByDepartment($updatedTransfer['to_location']); // HOD of receiving department

        // Generate receive_token if missing
        if (empty($updatedTransfer['receive_token'])) {
            $receiveToken = bin2hex(random_bytes(16));
            $transferModel->update($id, ['receive_token' => $receiveToken]);
            $updatedTransfer['receive_token'] = $receiveToken;
        }

        // Prepare approval summary
        $hodUser   = $userModel->find($updatedTransfer['hod_approval']);
        $adminUser = $userModel->find($updatedTransfer['admin_approval']);
        $ceoUser   = $updatedTransfer['ceo_approval'] ? $userModel->find($updatedTransfer['ceo_approval']) : null;

        $hodDate   = $updatedTransfer['hod_approval_date'] ?? 'Not set';
        $adminDate = $updatedTransfer['admin_approval_date'] ?? 'Not set';
        $ceoDate   = $updatedTransfer['ceo_approval_date'] ?? 'Not set';

        $approvalSummary = "
            <b>Approval Summary:</b><br>
            HOD ({$hodUser['username']}) - {$hodDate}<br>
            Admin ({$adminUser['username']}) - {$adminDate}<br>
        ";
        if ($ceoUser) {
            $approvalSummary .= "CEO ({$ceoUser['username']}) - {$ceoDate}<br>";
        }

        // --- Email to Custodian ---
        if ($custodian && !empty($custodian['email']) && filter_var($custodian['email'], FILTER_VALIDATE_EMAIL)) {
            $email = \Config\Services::email();
            $email->setTo($custodian['email']);
            $email->setSubject('Your Asset Transfer Has Been Approved');
            $email->setMessage("
                Dear {$custodian['username']},<br><br>
                Your asset transfer request has been <b>approved by all required approvers</b>.<br><br>
                <b>Asset ID:</b> {$updatedTransfer['asset_id']}<br>
                <b>From Department:</b> {$fromDept['name']}<br>
                <b>To Department:</b> {$toDept['name']}<br>
                <b>Reason:</b> {$updatedTransfer['reason_for_transfer']}<br><br>
                {$approvalSummary}<br>
                Thank you.
            ");
            $email->send();
        }

        // --- Email to Receiving Department HOD ---
        if ($toHod && !empty($toHod['email']) && filter_var($toHod['email'], FILTER_VALIDATE_EMAIL)) {
            $receiveLink = base_url("assets/transfer/receiveAsset/{$updatedTransfer['id']}/{$updatedTransfer['receive_token']}");
            $email = \Config\Services::email();
            $email->setTo($toHod['email']);
            $email->setSubject('Asset Transfer Received Notification');
            $email->setMessage("
                Dear {$toHod['username']},<br><br>
                The asset <b>{$updatedTransfer['asset_id']}</b> has been transferred to your department (<b>{$toDept['name']}</b>).<br>
                Please acknowledge receipt by clicking the button below:<br><br>
                <a href='{$receiveLink}' style='padding:10px 15px; background:green; color:white; text-decoration:none;'>Mark as Received</a><br><br>
                Thank you,<br>
                <b>CA-Asset-Tracker</b>
            ");
            $email->send();
        }
    }

    return redirect()->to('/login')->with('success', 'Transfer approved successfully.');
}



public function emailReject($id, $token)
{
    $transferModel = new AssetTransferModel();
    $userModel     = new UserModel();

    $transfer = $transferModel->where('id', $id)->where('approval_token', $token)->first();

    if (!$transfer) {
        return redirect()->to('/login')->with('error', 'Invalid or expired rejection link.');
    }

    // Determine which step is pending and reject it
    if ($transfer['hod_status'] === 'pending') {
        $data = ['hod_status' => 'rejected'];
    } elseif ($transfer['admin_status'] === 'pending') {
        $data = ['admin_status' => 'rejected'];
    } elseif ($transfer['ceo_status'] === 'pending') {
        $data = ['ceo_status' => 'rejected'];
    } else {
        return redirect()->to('/login')->with('error', 'This transfer has already been processed.');
    }

    // Keep approval_token intact
    $data['approval_token'] = $transfer['approval_token'];
    $transferModel->update($id, $data);

    return redirect()->to('/login')->with('success', 'Transfer rejected successfully.');
}


}
