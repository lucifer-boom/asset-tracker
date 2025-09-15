<?php

use App\Models\UserRoleModel;

if (!function_exists('has_role')) {
    function has_role($roleName)
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) return false;

        $userRoleModel = new UserRoleModel();
        $roles = $userRoleModel
                    ->select('roles.name')
                    ->join('roles', 'roles.id = user_roles.role_id')
                    ->where('user_roles.user_id', $userId)
                    ->findAll();

        $roleNames = array_column($roles, 'name');

        return in_array($roleName, $roleNames);
    }
}
