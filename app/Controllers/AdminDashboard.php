<?php

namespace App\Controllers;

class AdminDashboard extends BaseController
{
    public function index()
    {

          $db = \Config\Database::connect();

    // Get count of assets per category
    $builder = $db->table('assets');
    $builder->select('asset_categories.name AS category_name, COUNT(assets.id) AS total');
    $builder->join('asset_categories', 'asset_categories.id = assets.category_id');
    $builder->groupBy('asset_categories.id');

    $query = $builder->get();
    $data['categoryCounts'] = $query->getResultArray();

    // Optional: total assets
    $data['totalAssets'] = $db->table('assets')->countAllResults();
    
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $role = $session->get('role');


        //load files for dashboard
        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('admin/dashboard', $data);
        echo view('includes/footer');

    }

    

}
