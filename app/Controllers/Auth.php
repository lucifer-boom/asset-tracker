<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\UserRoleModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $userRoleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
    }

    public function login()
    {
        return view('auth/login');
    }

    public function doLogin()
    {
        $session = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Plain-text password check
        if ($password !== $user['password']) {
            return redirect()->back()->with('error', 'Invalid password.');
        }

        // Fetch roles
        $roles = $this->userRoleModel
            ->select('roles.name, roles.type')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $user['id'])
            ->findAll();

        $systemRole = null;
        $departmentRole = null;

        foreach ($roles as $role) {
            if ($role['type'] === 'system') {
                $systemRole = $role['name'];
            } elseif ($role['type'] === 'department') {
                $departmentRole = $role['name'];
            }
        }

        // Set session data
        $session->set([
    'user_id' => $user['id'],
    'username' => $user['username'],
    'system_role' => $systemRole,
    'department_role' => $departmentRole,
    'department_id' => $user['department_id'],
    'isLoggedIn' => true
]);

        // First login → redirect to password change page
        if ($user['is_password_changed'] == 0) {
            return redirect()->to('/change-password');
        }

        // Redirect based on system role
        switch ($systemRole) {
            case 'super admin':
            case 'it admin':
            case 'finance admin':
                return redirect()->to('/dashboard/admin');
            case 'dashboard viewer':
            case 'verification user':
            default:
                return redirect()->to('/dashboard/user');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function changePassword()
    {
        return view('auth/change_password');
    }

    public function updatePassword()
    {
        $session = session();
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $userId = $session->get('user_id');

        // Update password and mark as changed
        $this->userModel->update($userId, [
            'password' => $newPassword,
            'is_password_changed' => 1
        ]);

        // Fetch updated user data
        $user = $this->userModel->find($userId);

        // Fetch roles again
        $roles = $this->userRoleModel
            ->select('roles.name, roles.type')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $user['id'])
            ->findAll();

        $systemRole = null;
        $departmentRole = null;

        foreach ($roles as $role) {
            if ($role['type'] === 'system') $systemRole = $role['name'];
            if ($role['type'] === 'department') $departmentRole = $role['name'];
        }

        // Update session
        $session->set([
    'user_id' => $user['id'],
    'username' => $user['username'],
    'system_role' => $systemRole,
    'department_role' => $departmentRole,
    'department_id' => $user['department_id'],
    'isLoggedIn' => true
]);

        // Redirect based on system role
        switch ($systemRole) {
            case 'super admin':
            case 'it admin':
            case 'finance admin':
                return redirect()->to('/dashboard/admin')->with('success', 'Password updated successfully.');
            case 'dashboard viewer':
            case 'verification user':
            default:
                return redirect()->to('/dashboard/user')->with('success', 'Password updated successfully.');
        }
    }
}
