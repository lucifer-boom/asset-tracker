<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelModel extends Model
{
    protected $table      = 'asset_models';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id',
        'sub_category_id',
        'name',
        'description',
        'created_at'
    ];

    public function getAssetModelsWithCategories()
    {
        return $this->select(
            'asset_models.*, 
             asset_categories.name as category_name, 
             asset_sub_categories.name as sub_category_name, 
             asset_sub_categories.sub_category_code as sub_category_code'
        )
            ->join('asset_categories', 'asset_models.category_id = asset_categories.id', 'left')
            ->join('asset_sub_categories', 'asset_models.sub_category_id = asset_sub_categories.id', 'left')
            ->findAll();
    }

    public function getAssetModelsWithImages()
{
    return $this->select(
        'asset_models.*, 
         asset_categories.name as category_name, 
         asset_sub_categories.name as sub_category_name, 
         asset_sub_categories.sub_category_code as sub_category_code, 
         asset_images.image_path'
    )
    ->join('asset_categories', 'asset_models.category_id = asset_categories.id', 'left')
    ->join('asset_sub_categories', 'asset_models.sub_category_id = asset_sub_categories.id', 'left')
    ->join('asset_images', 'asset_models.id = asset_images.model_id', 'left')
    ->findAll();
}

}
