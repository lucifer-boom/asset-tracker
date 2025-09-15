<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'asset_categories';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'description', 'created_at' ];

}
