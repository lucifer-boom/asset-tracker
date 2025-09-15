<?php

namespace App\Controllers;
use App\Models\AssetModel;

class AssetMovementController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Fetch assets with model names
       $db = \Config\Database::connect();

// Fetch unique, active assets for transfer/disposal
$builder = $db->table('assets');
$builder->select('assets.id, assets.asset_code, asset_models.name AS model_name, assets.current_location, assets.status');
$builder->join('asset_models', 'asset_models.id = assets.model_id', 'left');
$builder->where('assets.status !=', 'disposed');
$builder->groupBy('assets.id'); // ensure uniqueness
$data['assets'] = $builder->get()->getResultArray();


        // Fetch transfer history
        $data['transfers'] = $db->table('asset_transfers')
            ->join('assets', 'assets.id = asset_transfers.asset_id')
            ->select('asset_transfers.*, assets.asset_code')
            ->orderBy('asset_transfers.id', 'DESC')
            ->get()->getResultArray();

        // Fetch disposal history
        $data['disposals'] = $db->table('asset_disposals')
            ->join('assets', 'assets.id = asset_disposals.asset_id')
            ->select('asset_disposals.*, assets.asset_code')
            ->orderBy('asset_disposals.id', 'DESC')
            ->get()->getResultArray();

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/movements', $data);
        echo view('includes/footer');
    }

    public function storeTransfer()
    {
        $db = \Config\Database::connect();

        $data = [
            'asset_id'      => $this->request->getPost('asset_id'),
            'from_location' => $this->request->getPost('from_location'),
            'to_location'   => $this->request->getPost('to_location'),
            'transfer_date' => date('Y-m-d'),
            'remarks'       => $this->request->getPost('remarks'),
        ];

        $db->table('asset_transfers')->insert($data);

        // Update current location
        $db->table('assets')->where('id', $data['asset_id'])->update(['current_location' => $data['to_location']]);

        return redirect()->to('/assets/movements')->with('success', 'Asset transferred successfully!');
    }

    public function storeDisposal()
    {
        $db = \Config\Database::connect();

        $data = [
            'asset_id'      => $this->request->getPost('asset_id'),
            'disposal_date' => $this->request->getPost('disposal_date'),
            'reason'        => $this->request->getPost('reason'),
            'remarks'       => $this->request->getPost('remarks'),
        ];

        $db->table('asset_disposals')->insert($data);

        // Mark asset as disposed
        $db->table('assets')->where('id', $data['asset_id'])->update(['status' => 'disposed']);

        return redirect()->to('/assets/movements')->with('success', 'Asset disposed successfully!');
    }
}
