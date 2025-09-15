<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table = 'assets';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'model_id',
        'category_id',
        'serial_number',
        'asset_code',
        'purchase_date',
        'warranty_years',
        'supplier_id',
        'value',
        'department_id',
        'created_at',
        'model_id',
        'category_id',
        'serial_number',
        'asset_code',
        'purchase_date',
        'warranty_years',
        'supplier_id',
        'value',
    ];

     public function getAssetsByDepartment($department)
    {
        
        return $this->where('department_id', $department)->findAll();
    }

    public function getAssetsWithDetails()
{
    return $this->select('assets.*, asset_models.name as model_name, asset_categories.name as category_name, suppliers.name as supplier_name')
                ->join('asset_models', 'assets.model_id = asset_models.id')
                ->join('asset_categories', 'assets.category_id = asset_categories.id')
                ->join('suppliers', 'assets.supplier_id = suppliers.id', 'left')
                ->findAll();
}

}
