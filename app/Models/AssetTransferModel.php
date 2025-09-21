<?php

namespace App\Models;
use CodeIgniter\Model;

class AssetTransferModel extends Model
{
    protected $table = 'asset_transfers';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'asset_id', 'from_location', 'to_location', 'transfer_date',
        'asset_custodian', 'hod_approval', 'admin_approval', 'ceo_approval', 'reason_for_transfer', 
        'hod_status', 'admin_status', 'ceo_status', 'received_date', 'received_by', 'hod_approval_date', 'admin_approval_date', 'ceo_approval_date'
    ];

    /**
     * Get all pending approvals for the logged-in user
     */
    public function getPendingForUser($userId)
    {
        return $this->groupStart()
            // Pending HOD approvals
            ->where('hod_approval', $userId)
            ->where('hod_status', 'pending')
        ->groupEnd()
        ->orGroupStart()
            // Pending Admin approvals (only if HOD approved)
            ->where('admin_approval', $userId)
            ->where('hod_status', 'approved')
            ->where('admin_status', 'pending')
        ->groupEnd()
        ->orGroupStart()
            // Pending CEO approvals (only if Admin approved and external)
            ->where('ceo_approval', $userId)
            ->where('admin_status', 'approved')
            ->where('ceo_status', 'pending')
        ->groupEnd()
        ->findAll();
    }

    /**
     * Get all transfers with joined data
     */
   public function getAllTransfers()
{
    return $this->select('
                asset_transfers.*,
                asset_models.name AS asset_name,
                assets.asset_code,
                from_dept.name AS from_location_name,
                to_dept.name AS to_location_name,
                users.username AS custodian_name,
                u_hod.username AS hod_name,
                u_admin.username AS admin_name,
                u_ceo.username AS ceo_name
            ')
            ->join('assets', 'assets.id = asset_transfers.asset_id')
            ->join('asset_models', 'asset_models.id = assets.model_id')
            ->join('departments AS from_dept', 'from_dept.id = asset_transfers.from_location', 'left')
            ->join('departments AS to_dept', 'to_dept.id = asset_transfers.to_location', 'left')
            ->join('users', 'users.id = asset_transfers.asset_custodian', 'left')
            ->join('users AS u_hod', 'u_hod.id = asset_transfers.hod_approval', 'left')
            ->join('users AS u_admin', 'u_admin.id = asset_transfers.admin_approval', 'left')
            ->join('users AS u_ceo', 'u_ceo.id = asset_transfers.ceo_approval', 'left')
            ->orderBy('asset_transfers.transfer_date', 'DESC')
            ->findAll();
}


    /**
     * Get all past approvals or received assets
     */
    public function getPastTransfers()
    {
        return $this->select('
                    asset_transfers.*,
                    asset_models.name AS asset_name,
                    assets.asset_code,
                    from_dept.name AS from_location_name,
                    to_dept.name AS to_location_name,
                    users.username AS custodian_name,
                    u_received.username AS received_by_name
                ')
                ->join('assets', 'assets.id = asset_transfers.asset_id')
                ->join('asset_models', 'asset_models.id = assets.model_id')
                ->join('departments AS from_dept', 'from_dept.id = asset_transfers.from_location', 'left')
                ->join('departments AS to_dept', 'to_dept.id = asset_transfers.to_location', 'left')
                ->join('users', 'users.id = asset_transfers.asset_custodian', 'left')
                ->join('users AS u_received', 'u_received.id = asset_transfers.received_by', 'left')
                ->where('hod_status !=', 'pending')
                ->orWhere('admin_status !=', 'pending')
                ->orWhere('ceo_status !=', 'pending')
                ->orderBy('asset_transfers.transfer_date', 'DESC')
                ->findAll();
    }

   public function getTransferWithDetails($id)
{
    return $this->select('
                asset_transfers.*,
                asset_models.name AS asset_name,
                assets.asset_code,
                from_dept.name AS from_location_name,
                to_dept.name AS to_location_name,
                users.username AS custodian_name,
                u_hod.username AS hod_name,
                u_admin.username AS admin_name,
                u_ceo.username AS ceo_name,
                u_received.username AS received_by_name
            ')
            ->join('assets', 'assets.id = asset_transfers.asset_id')
            ->join('asset_models', 'asset_models.id = assets.model_id')
            ->join('departments AS from_dept', 'from_dept.id = asset_transfers.from_location', 'left')
            ->join('departments AS to_dept', 'to_dept.id = asset_transfers.to_location', 'left')
            ->join('users', 'users.id = asset_transfers.asset_custodian', 'left')
            ->join('users AS u_hod', 'u_hod.id = asset_transfers.hod_approval', 'left')
            ->join('users AS u_admin', 'u_admin.id = asset_transfers.admin_approval', 'left')
            ->join('users AS u_ceo', 'u_ceo.id = asset_transfers.ceo_approval', 'left')
            ->join('users AS u_received', 'u_received.id = asset_transfers.received_by', 'left')
            ->where('asset_transfers.id', $id)
            ->first();
}

}
