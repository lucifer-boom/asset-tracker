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

    // Get selected departments
    $fromDeptId = $this->request->getPost('from_location');
    $toDeptId   = $this->request->getPost('to_location');

    $fromDept = $deptModel->find($fromDeptId);
    $toDept   = $deptModel->find($toDeptId);

    // Step 1: HOD of custodian department
    $hodApproval = $userModel->getHodByDepartment($loggedInUser['department_id']);

    // Step 2: HOD of Administration & Events department (always required)
    $adminDept = $deptModel->where('name', 'Administration & Events')->first();
    $adminApproval = $adminDept ? $userModel->getHodByDepartment($adminDept['id']) : null;

    // Step 3: CEO approval only if target department is external
    $ceoApproval = null;
    $ceoStatus   = null;
    if (isset($toDept['type']) && $toDept['type'] === 'external') {
        $ceoDept = $deptModel->where('name', 'CEO & Secretary')->first();
        $ceoApproval = $ceoDept ? $userModel->getHodByDepartment($ceoDept['id']) : null;
        $ceoStatus   = $ceoApproval ? 'pending' : null;
    }

    // Insert transfer request with status tracking
    $transferModel->insert([
        'asset_id'        => $this->request->getPost('asset_id'),
        'from_location'   => $fromDeptId,
        'to_location'     => $toDeptId,
        'transfer_date'   => date('Y-m-d'),
        'asset_custodian' => $loggedInUserId,
        'hod_approval'    => $hodApproval['id'] ?? null,
        'admin_approval'  => $adminApproval['id'] ?? null,
        'ceo_approval'    => $ceoApproval['id'] ?? null,   // null if internal
        'hod_status'      => $hodApproval ? 'pending' : null,
        'admin_status'    => $adminApproval ? 'pending' : null,
        'ceo_status'      => $ceoStatus,                   // only set if external
        'reason_for_transfer' => $this->request->getPost('reason_for_transfer'),
    ]);

    return redirect()->to('/assets/assets_transfers')->with('success', 'Transfer request submitted successfully');
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

    // --- Pending Transfers ---
    $pendingTransfers = $transferModel->getPendingForUser($userId);

    // Add asset and department details
    foreach ($pendingTransfers as &$transfer) {
        $asset = $assetModel->find($transfer['asset_id']);
        $transfer['asset_name'] = $asset['asset_code'] ?? '';

        $fromDept = $deptModel->find($transfer['from_location']);
        $transfer['from_name'] = $fromDept['name'] ?? '';

        $toDept = $deptModel->find($transfer['to_location']);
        $transfer['to_name'] = $toDept['name'] ?? '';
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

    // Add asset and department details for past transfers
    foreach ($pastTransfers as &$transfer) {
        $asset = $assetModel->find($transfer['asset_id']);
        $transfer['asset_name'] = $asset['asset_code'] ?? '';

        $fromDept = $deptModel->find($transfer['from_location']);
        $transfer['from_name'] = $fromDept['name'] ?? '';

        $toDept = $deptModel->find($transfer['to_location']);
        $transfer['to_name'] = $toDept['name'] ?? '';
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

        $transfer = $transferModel->find($id);

        if (!$transfer) {
            return redirect()->back()->with('error', 'Transfer not found.');
        }

        $field = null;
        $statusField = null;

        // Determine which approval step the user can act on
        if ($transfer['hod_approval'] == $userId && $transfer['hod_status'] == 'pending') {
            $field = 'hod_approval';
            $statusField = 'hod_status';
        } elseif ($transfer['admin_approval'] == $userId && $transfer['hod_status'] == 'approved' && $transfer['admin_status'] == 'pending') {
            $field = 'admin_approval';
            $statusField = 'admin_status';
        } elseif ($transfer['ceo_approval'] == $userId && $transfer['admin_status'] == 'approved' && $transfer['ceo_status'] == 'pending') {
            $field = 'ceo_approval';
            $statusField = 'ceo_status';
        } else {
            return redirect()->back()->with('error', 'You are not authorized to approve this request or it is not your turn.');
        }
if ($action === 'approve') {
    // Approve current step
    $data = [$statusField => 'approved'];

    // If HOD is approving, save timestamp
    if ($statusField === 'hod_status') {
        $data['hod_approval_date'] = date('Y-m-d H:i:s');
    }

    $transferModel->update($id, $data);

    return redirect()->back()->with('success', 'Approved successfully.');
}

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

    // Get transfers that are fully approved and pending receiving
    $transfers = $transferModel
        ->where('hod_status', 'approved')
        ->where('admin_status', 'approved')
        ->groupStart()
            ->where('ceo_status', 'approved')
            ->orWhere('ceo_status', null) // skip CEO if not required
        ->groupEnd()
        ->findAll();

    foreach ($transfers as &$transfer) {
        $asset = $assetModel->find($transfer['asset_id']);
        $transfer['asset_name'] = $asset['asset_code'] ?? '';

        $fromDept = $deptModel->find($transfer['from_location']);
        $transfer['from_name'] = $fromDept['name'] ?? '';

        $toDept = $deptModel->find($transfer['to_location']);
        $transfer['to_name'] = $toDept['name'] ?? '';

        // Add HOD of receiving department
        $hod = $userModel->getHodByDepartment($transfer['to_location']);
        $transfer['to_hod_id'] = $hod['id'] ?? null;
    }

    echo view('includes/sidebar');
    echo view('includes/topbar');
    echo view('assets/received_transfers', [
        'transfers' => $transfers,
        'user_id'   => $userId
    ]);
    echo view('includes/footer');
}

public function receiveAsset($id)
{
    $userId = session()->get('user_id');
    $transferModel = new AssetTransferModel();
    $userModel     = new UserModel();

    $transfer = $transferModel->find($id);
    if (!$transfer) {
        return redirect()->back()->with('error', 'Transfer not found.');
    }

    // Verify logged-in user is HOD of receiving dept
    $hod = $userModel->getHodByDepartment($transfer['to_location']);
    if (!$hod || $hod['id'] != $userId) {
        return redirect()->back()->with('error', 'You are not authorized to mark this asset as received.');
    }

    $transferModel->update($id, [
        'received_date' => date('Y-m-d'),
        'received_by'   => $userId
    ]);

    $transferModel->update($id, [
    'received_date' => date('Y-m-d'),
    'received_by'   => $userId
]);


    return redirect()->back()->with('success', 'Asset marked as received successfully.');
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


}
