<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class CategoryController extends BaseController
{
    public function index($id = null)
    {
         

        $model = new CategoryModel();
        $data['categories'] = $model->findAll();

        // If editing, fetch the record
        $data['editCategory'] = null;
        if ($id) {
            $data['editCategory'] = $model->find($id);
        }

        echo view('includes/sidebar');
         echo view('includes/topbar.php');
         echo view('assets/categories', $data);
         echo view('includes/footer.php');

    }

     public function store()
    {
        $model = new CategoryModel();
        $model->save([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);
        return redirect()->to('/assets/categories');
    }

    

    public function update($id)
    {
        $model = new CategoryModel();
        $model->update($id, [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);
        return redirect()->to('/assets/categories');
    }

    public function delete($id)
    {
        $model = new CategoryModel();
        $model->delete($id);
        return redirect()->to('/assets/categories');
    }
}

