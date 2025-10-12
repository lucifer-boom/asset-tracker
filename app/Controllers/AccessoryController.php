<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AccessoryModel;
use Endroid\QrCode\Builder\Builder; // requires endroid/qr-code
use Endroid\QrCode\Writer\PngWriter;

class AccessoryController extends BaseController
{
    protected $accessoryModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->accessoryModel = new AccessoryModel();
    }

    public function index()
    {
        $data['accessories'] = $this->accessoryModel->orderBy('created_at', 'DESC')->findAll();

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('accessories/manage', $data);
        echo view('includes/footer');
        // return view('accessories/manage', $data);
    }

    public function store()
{
    $post = $this->request->getPost();

    // Validation rules
    $rules = [
        'name' => 'required|min_length[2]|max_length[255]',
        'model' => 'permit_empty|max_length[255]',
        'brand' => 'permit_empty|max_length[255]',
        'total_qty' => 'required|integer|greater_than_equal_to[0]',
        'warranty_months' => 'permit_empty|integer|greater_than_equal_to[0]',
        'purchase_date' => 'permit_empty|valid_date[Y-m-d]',
        'supplier' => 'permit_empty|max_length[255]',
    ];

    if (! $this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $accessoryName = $post['name'];
    $purchaseYear = !empty($post['purchase_date'])
        ? date('Y', strtotime($post['purchase_date']))
        : date('Y');

    // Generate asset code based on accessory name + year
    $assetCode = $this->generateAssetCode($accessoryName, $purchaseYear);

    $totalQty = (int) $post['total_qty'];

    $data = [
        'asset_code' => $assetCode,
        'name' => esc($accessoryName),
        'model' => esc($post['model'] ?? null),
        'brand' => esc($post['brand'] ?? null),
        'total_qty' => $totalQty,
        'available_qty' => $totalQty,
        'warranty_months' => $post['warranty_months'] ?? 0,
        'purchase_date' => $post['purchase_date'] ?? null,
        'supplier' => esc($post['supplier'] ?? null),
        'notes' => esc($post['notes'] ?? null),
    ];

    // Insert accessory
    $insertId = $this->accessoryModel->insert($data);

    if ($insertId === false) {
        return redirect()->back()->with('error', 'Failed to save accessory.')->withInput();
    }

    // Generate QR Code
    try {
        $this->generateQr((int)$insertId);
    } catch (\Throwable $e) {
        log_message('error', 'QR generation failed: ' . $e->getMessage());
    }

    return redirect()->to(base_url('accessories/manage'))->with('success', 'Accessory added successfully.');
}

    protected function generateAssetCode($accessoryName, $purchaseYear)
{
    // Get first two letters of accessory name (uppercase)
    $prefix = strtoupper(substr($accessoryName, 0, 2));

    // Get database instance
    $db = \Config\Database::connect();

    // Count how many accessories exist for this accessory name
    $builder = $db->table('accessories');
    $builder->like('asset_code', "CA-{$purchaseYear}-{$prefix}", 'after');
    $count = $builder->countAllResults();

    // Reset the sequence for each accessory type (starts at 001)
    $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    // Final asset code
    $assetCode = "CA-{$purchaseYear}-{$prefix}-{$sequence}";

    return $assetCode;
}
    
    protected function generateQr(int $accessoryId)
    {
        $accessory = $this->accessoryModel->find($accessoryId);
        if (! $accessory) {
            throw new \RuntimeException("Accessory #{$accessoryId} not found.");
        }

        // URL that QR code will open (create view method later)
        $url = site_url("accessories/view/{$accessory['id']}");

        // Ensure QR folder exists
        $qrFolder = WRITEPATH . 'uploads/qr/';
        if (! is_dir($qrFolder)) {
            mkdir($qrFolder, 0755, true);
        }

        // Generate QR
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->size(300)
            ->margin(10)
            ->build();

        $filename = 'qr_' . $accessory['asset_code'] . '.png';
        $savePath = $qrFolder . $filename;
        $result->saveToFile($savePath);

        // Save accessible web path
        $webPath = base_url('writable/uploads/qr/' . $filename);
        $this->accessoryModel->update($accessoryId, ['qr_image_path' => $webPath]);
    }
}
