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

        // Assets & Users for Assign Asset modal
        $data['assets'] = $assetModel
            ->whereNotIn('id', function ($builder) {
                $builder->select('asset_id')
                    ->from('assets_assignments')
                    ->where('status', 'assigned');
            })
            ->findAll();

        $data['users'] = $userModel
    ->select('users.id, users.username, departments.name as department_name')
    ->join('departments', 'departments.id = users.department_id', 'left')
    ->orderBy('users.username', 'ASC')
    ->findAll();


      $data['activeAssignments'] = $assignmentModel
    ->select('assets_assignments.id, assets.asset_code, users.username, departments.name as department_name')
    ->join('assets', 'assets.id = assets_assignments.asset_id')
    ->join('users', 'users.id = assets_assignments.user_id')
    ->join('departments', 'departments.id = users.department_id', 'left')
    ->where('assets_assignments.status', 'assigned')
    ->findAll();




        // Assignment History
      $data['history'] = $assignmentModel
    ->select('assets_assignments.id, assets.asset_code, users.username, departments.name as department_name, assets_assignments.assigned_date, assets_assignments.returned_date, assets_assignments.remarks, assets_assignments.status')
    ->join('assets', 'assets.id = assets_assignments.asset_id')
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

        $assignmentModel->insert([
            'asset_id' => $this->request->getPost('asset_id'),
            'user_id' => $this->request->getPost('user_id'),
            'assigned_date' => $this->request->getPost('assigned_date'),
            'remarks' => $this->request->getPost('remarks'),
            'status' => 'assigned'
        ]);

        return redirect()->to('/assets/assignments');
    }

    public function return()
    {
        $assignmentModel = new AssetAssignmentModel();
        $id = $this->request->getPost('assignment_id');

        $assignmentModel->update($id, [
            'returned_date' => $this->request->getPost('return_date'),
            'remarks' => $this->request->getPost('remarks'),
            'status' => 'returned'
        ]);

        return redirect()->to('/assets/assignments');
    }
}
