<?php

namespace App\Controllers;

use App\Models\LicenseModel;
use App\Models\SupplierModel;

class LicenseController extends BaseController
{
    public function create()
    {
        $supplierModel = new SupplierModel();
        $data['suppliers'] = $supplierModel->findAll();

        echo view('includes/header');
        echo view('includes/topbar');
        echo view('license/license_manage');
        echo view('includes/footer');
    }

    public function store()
    {
        $licenseModel = new LicenseModel();

        $licenseModel->save([
            'license_name' => $this->request->getPost('license_name'),
            'license_type' => $this->request->getPost('license_type'),
            'version' => $this->request->getPost('version'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'purchase_date' => $this->request->getPost('purchase_date'),
            'expiry_date' => $this->request->getPost('expiry_date'),
            'renewal_date' => $this->request->getPost('renewal_date'),
            'cost' => $this->request->getPost('cost'),
            'currency' => $this->request->getPost('currency'),
            'license_status' => $this->request->getPost('license_status'),
            'purchase_order_number' => $this->request->getPost('purchase_order_number'),
            'invoice_number' => $this->request->getPost('invoice_number'),
            'payment_method' => $this->request->getPost('payment_method'),
            'purchase_approved_by' => $this->request->getPost('purchase_approved_by'),
            'remarks' => $this->request->getPost('remarks'),
            'attachments' => $this->request->getPost('attachments'),
        ]);

        return redirect()->to('/licenses/license_manage')->with('success', 'License added successfully!');
    }
}
