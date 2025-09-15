<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetAssignmentModel extends Model
{
    protected $table = 'assets_assignments';
    protected $primaryKey = 'id';

    protected $allowedFields = ['asset_id', 'assigned_date', 'returned_date', 'status', 'remarks', 'user_id'];

    protected $useTimestamps = false;
}
