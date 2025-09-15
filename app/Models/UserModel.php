<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'email', 'department_id', 'role', 'is_password_changed'];
    

    public function getUsersWithRoles()
    {
        return $this->select('users.id, users.username, users.email, roles.name as role_name, roles.type as role_type')
                    ->join('user_roles', 'user_roles.user_id = users.id', 'left')
                    ->join('roles', 'roles.id = user_roles.role_id', 'left')
                    ->findAll();
    }

    public function getHodByDepartment($departmentId)
{
    return $this->select('users.id, users.username')
                ->join('user_roles', 'users.id = user_roles.user_id')
                ->join('roles', 'user_roles.role_id = roles.id')
                ->where('users.department_id', $departmentId)
                ->where('roles.name', 'hod')
                ->where('roles.type', 'department')
                ->get()
                ->getRowArray(); // return first HOD found
}

}
