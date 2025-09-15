<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\UserRoleModel;
use App\Models\DepartmentModel;
use CodeIgniter\Controller;

class AdminController extends Controller
{
    protected $userModel;
    protected $roleModel;
    protected $userRoleModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
        $this->departmentModel = new DepartmentModel();
    }

    // List users
    public function index($id = null)
{
    // Join users with departments
    $builder = $this->userModel->builder();
    $builder->select('users.*, departments.name as department_name')
            ->join('departments', 'departments.id = users.department_id', 'left');
    $users = $builder->get()->getResultArray();

    // Load roles for each user
    foreach ($users as &$user) {
        $roles = $this->userRoleModel->where('user_id', $user['id'])->findAll();
        $user['roles'] = [];
        foreach ($roles as $r) {
            $roleData = $this->roleModel->find($r['role_id']);
            if ($roleData) {
                $user['roles'][] = $roleData;
            }
        }
    }

    $data['users'] = $users;

    $data['systemRoles'] = $this->roleModel->where('type', 'system')->findAll();
    $data['departmentRoles'] = $this->roleModel->where('type', 'department')->findAll();
    $data['departments'] = $this->departmentModel->findAll();

    $data['editUser'] = $id ? $this->userModel->find($id) : null;
    $data['userRoles'] = $id ? $this->userRoleModel->where('user_id', $id)->findAll() : [];

    echo view('includes/sidebar');
    echo view('includes/topbar');
    echo view('auth/user_manage', $data);
    echo view('includes/footer');
}

    // Create new user
    public function store()
    {
        $userId = $this->userModel->insert([
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'email' => $this->request->getPost('email'),
            'department_id' => $this->request->getPost('department_id'),
            'is_password_changed' => 0
        ]);

        // Assign system roles
        $systemRoles = $this->request->getPost('system_role') ?? [];
        foreach ($systemRoles as $roleId) {
            $this->userRoleModel->insert(['user_id' => $userId, 'role_id' => $roleId]);
        }

        // Assign department roles
        $departmentRoles = $this->request->getPost('department_role') ?? [];
        foreach ($departmentRoles as $roleId) {
            $this->userRoleModel->insert(['user_id' => $userId, 'role_id' => $roleId]);
        }

        return redirect()->to('/auth/users');
    }

    // Update user
    public function update($id)
    {
        $this->userModel->update($id, [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'department_id' => $this->request->getPost('department_id')
        ]);

        $systemRoles = $this->request->getPost('system_role') ?? [];
        $departmentRoles = $this->request->getPost('department_role') ?? [];

        // Remove old roles
        $currentRoles = $this->userRoleModel->where('user_id', $id)->findAll();
        foreach ($currentRoles as $role) {
            $this->userRoleModel->where('user_id', $id)->where('role_id', $role['role_id'])->delete();
        }

        // Insert new roles
        foreach ($systemRoles as $roleId) {
            $this->userRoleModel->insert(['user_id' => $id, 'role_id' => $roleId]);
        }
        foreach ($departmentRoles as $roleId) {
            $this->userRoleModel->insert(['user_id' => $id, 'role_id' => $roleId]);
        }

        return redirect()->to('/auth/users');
    }

    // Reset password
    public function resetPassword($id)
    {
        $this->userModel->update($id, [
            'password' => 'icasl@123',
            'is_password_changed' => 0
        ]);

        return redirect()->to('/auth/users');
    }

    // Delete user
    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/auth/users');
    }
}
