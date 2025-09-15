<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends Controller
{
  public function index()
{
    $session = session();
    $systemRole = $session->get('system_role'); // role fetched from roles table

    // Admin dashboard for system admin roles
    if (in_array($systemRole, ['super admin', 'it admin', 'finance admin'])) {
        return redirect()->to('/dashboard/admin');
    }

    // User dashboard for non-admin system roles
    if (in_array($systemRole, ['dashboard viewer', 'verification user'])) {
        return redirect()->to('/dashboard/user');
    }

    // Fallback for unauthorized or unknown roles
    return redirect()->to('/login')->with('error', 'Unauthorized Access');
}

}
