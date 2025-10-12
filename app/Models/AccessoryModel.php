<?php namespace App\Models;

use CodeIgniter\Model;

class AccessoryModel extends Model
{
    protected $table      = 'accessories';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'asset_code','name','model','brand','total_qty','available_qty',
        'warranty_months','purchase_date','supplier','qr_image_path'
    ];
    protected $useTimestamps = true;
}
