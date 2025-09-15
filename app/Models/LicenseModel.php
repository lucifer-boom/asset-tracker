<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseModel extends Model
{
    protected $table = 'licenses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'license_name',
        'license_type',
        'version',
        'supplier_id',
        'purchase_date',
        'expiry_date',
        'renewal_date',
        'cost',
        'currency',
        'license_status',
        'purchase_order_number',
        'invoice_number',
        'payment_method',
        'purchase_approved_by',
        'remarks',
        'attachments',
    ];

    protected $useTimestamps = true;
}
