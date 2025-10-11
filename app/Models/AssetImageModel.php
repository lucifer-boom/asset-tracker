<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetImageModel extends Model
{
    protected $table = 'asset_images';
    protected $primaryKey = 'id';
    protected $allowedFields = ['model_id', 'image_path', 'created_at'];
}
