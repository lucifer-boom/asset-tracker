<?php

namespace App\Controllers;
use App\Models\SupplierModel;
use CodeIgniter\Controller;

class SupplierController extends Controller
{
    public function index($id = null)
    {
        $model = new SupplierModel();

        // Fetch all suppliers
        $data['suppliers'] = $model->findAll();

        // If editing, fetch the supplier
        $data['editSupplier'] = [];
        if ($id) {
            $data['editSupplier'] = $model->find($id) ?? [];
        }

        // Load views
        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/suppliers', $data);
        echo view('includes/footer');
    }

    public function store()
    {
        $model = new SupplierModel();
        $model->save([
            'name'           => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email'          => $this->request->getPost('email'),
            'phone'          => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address')
        ]);

        return redirect()->to('/assets/suppliers');
    }

    public function update($id)
    {
        $model = new SupplierModel();
        $model->update($id, [
            'name'           => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email'          => $this->request->getPost('email'),
            'phone'          => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address')
        ]);

        return redirect()->to('/assets/suppliers');
    }

    public function delete($id)
    {
        $model = new SupplierModel();
        $model->delete($id);
        return redirect()->to('/assets/suppliers');
    }
}
