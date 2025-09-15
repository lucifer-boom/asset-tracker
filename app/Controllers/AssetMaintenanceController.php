<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssetModel;
use App\Models\AssetMaintenanceModel;

class AssetMaintenanceController extends BaseController
{
   public function index()
{
    $assetModel = new \App\Models\AssetModel(); // define asset model
    $maintenanceModel = new \App\Models\AssetMaintenanceModel();

    // Fetch all assets for the dropdown
    $data['assets'] = $assetModel->findAll();

    // Fetch all maintenance records with asset info
    $data['maintenanceRecords'] = $maintenanceModel
        ->select('asset_maintenance.*, assets.asset_code, assets.model_id')
        ->join('assets', 'assets.id = asset_maintenance.asset_id')
        ->orderBy('maintenance_date', 'DESC')
        ->findAll();

    // Load views with data
    echo view('includes/sidebar');
    echo view('includes/topbar');
    echo view('assets/maintenance', $data);
    echo view('includes/footer');
}


    public function create()
    {
        $assetModel = new AssetModel();
        $data['assets'] = $assetModel->findAll();
        return view('assets/maintanance', $data);
    }

    public function store()
{
    $maintenanceModel = new \App\Models\AssetMaintenanceModel();
    $assetModel = new \App\Models\AssetModel();

    $assetId = $this->request->getPost('asset_id');

    // Save maintenance record
    $maintenanceModel->save([
        'asset_id' => $assetId,
        'maintenance_date' => $this->request->getPost('maintenance_date'),
        'maintenance_type' => $this->request->getPost('maintenance_type'),
        'description' => $this->request->getPost('description'),
        'status' => $this->request->getPost('status'),
        'cost' => $this->request->getPost('cost'),
        'ram' => $this->request->getPost('ram'),
        'hdd_capacity' => $this->request->getPost('hdd_capacity'),
        'hdd_type' => $this->request->getPost('hdd_type'),
        'operating_system' => $this->request->getPost('operating_system'),
    ]);

    // Update assets table automatically if hardware is updated
    $assetData = [];
    if ($this->request->getPost('ram')) {
        $assetData['ram'] = $this->request->getPost('ram');
    }
    if ($this->request->getPost('hdd_capacity')) {
        $assetData['hdd_capacity'] = $this->request->getPost('hdd_capacity');
    }
    if ($this->request->getPost('hdd_type')) {
        $assetData['hdd_type'] = $this->request->getPost('hdd_type');
    }
    if ($this->request->getPost('operating_system')) {
        $assetData['operating_system'] = $this->request->getPost('operating_system');
    }

    if (!empty($assetData)) {
        $assetModel->update($assetId, $assetData);
    }

    return redirect()->to('/assets/maintenance');
}

}
