<?php

namespace App\Controllers;

use App\Models\AssetModel;
use App\Models\ModelModel;
use App\Models\CategoryModel;
use App\Models\DepartmentModel;
use App\Models\SupplierModel;
use CodeIgniter\Controller;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class AssetController extends Controller
{
    public function index($id = null)
    {
        $assetModel = new AssetModel();
        $modelModel = new ModelModel();
        $categoryModel = new CategoryModel();
        $supplierModel = new SupplierModel();
        $departmentModel = new DepartmentModel();

        $data['assets'] = $assetModel->getAssetsWithDetails();
        $data['models'] = $modelModel->getAssetModelsWithCategories();
        $data['departments'] = $departmentModel->findAll();
        $data['categories'] = $categoryModel->findAll();
        $data['suppliers'] = $supplierModel->findAll();
       


        // Fetch asset for edit
        $data['editAsset'] = [];
        if ($id) {
            $data['editAsset'] = $assetModel->find($id) ?? [];
        }

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/assets_manage', $data);
        echo view('includes/footer');
    }

   public function store()
{
    $assetModel      = new AssetModel();
    $modelModel      = new \App\Models\ModelModel();
    $categoryModel   = new \App\Models\CategoryModel();
    $departmentModel = new \App\Models\DepartmentModel();
    $subCategoryModel = new \App\Models\SubCategoryModel();

    // Get selected IDs from POST
    $department_id = $this->request->getPost('department_id');
    $category_id   = $this->request->getPost('category_id');
    $model_id      = $this->request->getPost('model_id');

    $data['departments'] = $departmentModel->findAll();


$department_id = $this->request->getPost('department_id');
$department    = $departmentModel->asArray()->find($department_id);

if (empty($department_id)) {
    throw new \Exception("Please select a department before saving the asset.");
}

$deptCode = !empty($department['code']) ? $department['code'] : 'NA';


    // Fetch category
    $category = $categoryModel->find($category_id);
    if (!$category) {
        throw new \Exception("Category not found!");
    }
    $catCode = $category['code'];

    // Fetch model
    $model = $modelModel->find($model_id);
    if (!$model) {
        throw new \Exception("Model not found!");
    }

    // Fetch sub-category for model
$subCategory = $subCategoryModel->find($model['sub_category_id']);
$subCatCode  = $subCategory['sub_category_code'] ?? 'NA'; // use code, fallback if not found

    // Get last asset sequence
    $lastAsset = $assetModel
        ->where('department_id', $department_id)
        ->where('category_id', $category_id)
        ->where('model_id', $model_id)
        ->orderBy('id', 'DESC')
        ->first();

    $sequence = 1;
    if ($lastAsset && isset($lastAsset['asset_code'])) {
        $lastCode = explode("/", $lastAsset['asset_code']);
        $sequence = intval(end($lastCode)) + 1;
    }

    // Generate Asset Code
    $assetCode = $deptCode . '/' . $catCode . '/' . $subCatCode  . '/' . $sequence;

    // Save Asset
    $assetModel->save([
        'model_id'         => $model_id,
        'category_id'      => $category_id,
        'serial_number'    => $this->request->getPost('serial_number'),
        'asset_code'       => $assetCode,
        'purchase_date'    => $this->request->getPost('purchase_date'),
        'warranty_years'   => $this->request->getPost('warranty_years'),
        'supplier_id'      => $this->request->getPost('supplier_id'),
        'value'            => $this->request->getPost('value'),
        'department_id'    => $department_id, // must match allowedFields
       
    ]);

    return redirect()->to('/assets/manage');
}

    public function update($id)
    {

        $assetModel = new AssetModel();

        $oldAsset = $assetModel->find($id);
        $assetModel->update($id, [
            'model_id'         => $this->request->getPost('model_id'),
            'category_id'      => $this->request->getPost('category_id'),
            'serial_number'    => $this->request->getPost('serial_number'),
            'asset_code'       => $oldAsset['asset_code'],
            'purchase_date'    => $this->request->getPost('purchase_date'),
            'warranty_years'   => $this->request->getPost('warranty_years'),
            'supplier_id'      => $this->request->getPost('supplier_id'),
            'value'            => $this->request->getPost('value'),
            'department'       => $this->request->getPost('department'),
           
        ]);

        return redirect()->to('/assets/manage');
    }


    public function delete($id)
    {
        $assetModel = new AssetModel();
        $assetModel->delete($id);
        return redirect()->to('/assets/manage');
    }

    public function qrList()
    {
        $assetModel = new AssetModel();
        $data['assets'] = $assetModel
            ->select('assets.*, asset_models.name as model_name')
            ->join('asset_models', 'asset_models.id = assets.model_id')
            ->findAll();

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/qr_list', $data);
        echo view('includes/footer');
    }

    // Generate QR Code for a specific asset
    public function generateQRCode($assetId)
    {
        $assetModel = new AssetModel();

        // Fetch asset with model name
        $asset = $assetModel
            ->select('assets.*, asset_models.name as model_name')
            ->join('asset_models', 'asset_models.id = assets.model_id')
            ->where('assets.id', $assetId)
            ->first();

        if (!$asset) {
            return 'Asset not found!';
        }

        // Make folder if missing
        $folder = WRITEPATH . 'qrcodes/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        // Content for QR (using model_name)
        $qrContent = "Asset ID: {$asset['id']}\nModel: {$asset['model_name']}\nCode: {$asset['asset_code']}";

        // Build QR Code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->build();

        // Save QR to file
        $fileName = $folder . 'asset_' . $asset['id'] . '.png';
        $result->saveToFile($fileName);

        // Return as image response
        return $this->response->setHeader('Content-Type', $result->getMimeType())
            ->setBody($result->getString());
    }

    public function qrScan()
    {
        return view('assets/qr_scan');
    }

    // Return asset details as JSON
    public function view($assetId)
    {
        // Make sure to instantiate the model
        $assetModel = new \App\Models\AssetModel();

        $asset = $assetModel
            ->select('assets.*, models.name as model_name, asset_categories.name as category_name, suppliers.name as supplier_name')
            ->join('models', 'models.id = assets.model_id')
            ->join('asset_categories', 'asset_categories.id = assets.category_id')
            ->join('suppliers', 'suppliers.id = assets.supplier_id', 'left')
            ->find($assetId);

        if (!$asset) {
            return $this->response->setStatusCode(404)->setBody('Asset not found!');
        }

        return $this->response->setContentType('application/json')
            ->setBody(json_encode($asset));
    }

    public function generateSticker($assetId)
    {
        $assetModel = new AssetModel();
        $asset = $assetModel
            ->select('assets.*, asset_models.name as model_name, asset_categories.name as category_name')
            ->join('asset_models', 'asset_models.id = assets.model_id', 'left')
            ->join('asset_categories', 'asset_categories.id = assets.category_id', 'left')
            ->find($assetId);

        if (!$asset) {
            // Instead of redirecting, return an error image or text
            return $this->response->setBody('Asset not found')->setStatusCode(404);
        }

        // QR Code content
        $qrContent = "Asset ID: {$asset['id']}\nModel: {$asset['model_name']}\nAsset Code: {$asset['asset_code']}";

        $qrResult = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->data($qrContent)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->size(200)
            ->margin(5)
            ->build();

        // Sticker dimensions
        $width = 600;
        $height = 300;

        $sticker = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($sticker, 255, 255, 255);
        $black = imagecolorallocate($sticker, 0, 0, 0);
        imagefilledrectangle($sticker, 0, 0, $width, $height, $white);

        // QR code
        $qrImg = imagecreatefromstring($qrResult->getString());
        imagecopy($sticker, $qrImg, 20, 50, 0, 0, 200, 200);

        $fontFile = FCPATH . 'assets/fonts/arial.ttf';
        imagettftext($sticker, 14, 0, 240, 80, $black, $fontFile, "Asset Code: {$asset['asset_code']}");
        imagettftext($sticker, 14, 0, 240, 110, $black, $fontFile, "Model: {$asset['model_name']}");
        imagettftext($sticker, 14, 0, 240, 140, $black, $fontFile, "Category: {$asset['category_name']}");

        // Logo
        $logo = imagecreatefrompng(FCPATH . 'assets/images/logo.png');
        imagecopyresampled($sticker, $logo, 450, 20, 0, 0, 100, 100, imagesx($logo), imagesy($logo));
        imagettftext($sticker, 10, 0, 450, 140, $black, $fontFile, "Company Name / Support Info");

        // Force output as image and stop further rendering
        ob_clean();
        header('Content-Type: image/png');
        imagepng($sticker);
        imagedestroy($sticker);
        exit(); // Important! Stop CodeIgniter from adding any layout or redirect
    }

    public function downloadSticker($assetId)
    {
        $assetModel = new AssetModel();
        $asset = $assetModel
            ->select('assets.*, asset_models.name AS model_name, asset_categories.name AS category_name')
            ->join('asset_models', 'asset_models.id = assets.model_id')
            ->join('asset_categories', 'asset_categories.id = assets.category_id')
            ->find($assetId);

        if (!$asset) {
            return redirect()->back()->with('error', 'Asset not found!');
        }

        // Create sticker
        $width = 600;
        $height = 250;
        $sticker = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($sticker, 255, 255, 255);
        $black = imagecolorallocate($sticker, 0, 0, 0);
        $gray  = imagecolorallocate($sticker, 128, 128, 128);

        imagefilledrectangle($sticker, 0, 0, $width, $height, $white);

        // Font
        $fontFile = FCPATH . 'assets/fonts/arial.ttf'; // Ensure this exists

        // Company logo
        $logoPath = FCPATH . 'assets/img/logo_fixed.png';
        if (file_exists($logoPath)) {
            $logo = imagecreatefrompng($logoPath);
            $logoWidth = 100;
            $logoHeight = 70;
            $logoY = 20;
            $logoX = 20;
            imagecopyresampled($sticker, $logo, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight, imagesx($logo), imagesy($logo));
            imagedestroy($logo);
        }

        // Asset details (left side)

        $startY = 110; // vertical starting point
        $lineHeight = 25;
        imagettftext($sticker, 14, 0, 20, $startY, $black, $fontFile, "Asset Code: {$asset['asset_code']}");
        imagettftext($sticker, 14, 0, 20, $startY + $lineHeight, $black, $fontFile, "Model: {$asset['model_name']}");
        imagettftext($sticker, 14, 0, 20, $startY + $lineHeight * 2, $black, $fontFile, "Category: {$asset['category_name']}");
        imagettftext($sticker, 14, 0, 20, $startY + $lineHeight * 3, $black, $fontFile, "Serial: {$asset['serial_number']}");

        // QR code (right side, below logo)
        $qrPath = WRITEPATH . 'qrcodes/asset_' . $asset['id'] . '.png';
        if (file_exists($qrPath)) {
            $qr = imagecreatefrompng($qrPath);
            $qrSize = 120;
            imagecopyresampled($sticker, $qr, $width - $qrSize - 20, $height - $qrSize - 20, 0, 0, $qrSize, $qrSize, imagesx($qr), imagesy($qr));
            imagedestroy($qr);
        }

        // Main title (larger font)
        imagettftext($sticker, 12, 0, 20, $height - 40, $gray, $fontFile, "This is system genrated");
        // Small text (existing)
        imagettftext($sticker, 10, 0, 20, $height - 20, $gray, $fontFile, "Developed By CA Sri Lanka ICT DIvision © 2025 All Rights Reserved");
        // Output headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="sticker_' . $asset['asset_code'] . '.png"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        imagepng($sticker);
        imagedestroy($sticker);
        exit;
    }
}
