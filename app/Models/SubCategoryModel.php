<?php

namespace App\Models;

use CodeIgniter\Model;

class SubCategoryModel extends Model
{
    protected $table = 'asset_sub_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'sub_category_code', 'created_at', 'main_category_id'];
    protected $useTimestamps = true; // automatically manage created_at and updated_at
    protected $createdField  = 'created_at';

    public function getSubcategoriesWithCategory()
     {
        return $this->select('asset_sub_categories.id, asset_sub_categories.name, asset_sub_categories.sub_category_code, asset_categories.name AS main_category_name')
                    ->join('asset_categories', 'asset_sub_categories.main_category_id = asset_categories.id', 'left')
                    ->findAll();
     }
}
