<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetMaintenanceModel extends Model
{
    protected $table = 'asset_maintenance';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'asset_id', 
        'maintenance_date', 
        'maintenance_type',
        'description', 
        'status', 
        'cost',
        'ram',
        'hdd_capacity',
        'hdd_type',
        'operating_system'
    ];
    
    protected $useTimestamps = true;
}
