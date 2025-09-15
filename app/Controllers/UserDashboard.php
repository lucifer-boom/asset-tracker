<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class UserDashboard extends BaseController
{
    public function user_assigned()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $db = \Config\Database::connect();

        $userId = $session->get('user_id');

        // Assets assigned to the logged-in user
        $builder = $db->table('assets_assignments');
        $builder->select('assets.id AS asset_id, asset_models.name AS asset_name, assets.asset_code,assets.serial_number, assets_assignments.assigned_date, asset_categories.name AS asset_category, assets_assignments.remarks');
        $builder->join('assets', 'assets.id = assets_assignments.asset_id');
        $builder->join('asset_models', 'asset_models.id = assets.model_id');
        $builder->join('asset_categories', 'asset_categories.id = assets.category_id');
        $builder->where('assets_assignments.user_id', $userId);
        $data['assignedAssets'] = $builder->get()->getResultArray();

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('user/user_assigned', $data);   // show assigned assets
        echo view('includes/footer');
    }

    public function department_assigned()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $db = \Config\Database::connect();

        $userDept = $session->get('department');
        // Assets in the user's department
        // Assets in the user's department
$builder = $db->table('assets');
$builder->select('
    assets.id AS asset_id, 
    asset_models.name AS asset_name, 
    assets.asset_code, 
    assets.serial_number, 
    asset_categories.name AS asset_category, 
    users.username AS user, 
    assets.status,
    assets_assignments.assigned_date,
    assets_assignments.remarks
');
$builder->join('asset_models', 'asset_models.id = assets.model_id');
$builder->join('asset_categories', 'asset_categories.id = assets.category_id');

// ✅ First join assignments with assets
$builder->join('assets_assignments', 'assets_assignments.asset_id = assets.id');

// ✅ Then join users using the assignments table
$builder->join('users', 'users.id = assets_assignments.user_id');

        $builder->where('assets.department', $userDept);
        $data['departmentAssets'] = $builder->get()->getResultArray();

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('user/department_assigned', $data);
        echo view('includes/footer'); // show department assets echo view('includes/footer'); }
    }
}
