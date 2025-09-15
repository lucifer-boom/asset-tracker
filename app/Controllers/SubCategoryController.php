<?php

namespace App\Controllers;

use App\Models\SubCategoryModel;
use App\Models\CategoryModel;

class SubCategoryController extends BaseController
{
    public function index($id = null)
    {
        $categoryModel = new CategoryModel();
        $subcategoryModel = new SubCategoryModel();
        $data['categories'] = $categoryModel->findAll();
        $data['subcategories'] = $subcategoryModel->getSubcategoriesWithCategory();
        

        $data['sub_categories'] = null;
        if ($id) {
            $data['sub_categories'] = $subcategoryModel->find($id);
        }


        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/sub_categories', $data);
        echo view('includes/footer');
    }

    public function store()
    {
        $subcategoryModel = new SubCategoryModel();

        // Debug POST data if needed
        // dd($this->request->getPost());

        $subcategoryModel->save([
            'name' => $this->request->getPost('name'),
            'sub_category_code' => $this->request->getPost('sub_category_code'),
            'main_category_id' => $this->request->getPost('main_category_id')
        ]);

        return redirect()->to('/assets/sub_categories');
    }

    public function update($id) {

        $subcategoryModel = new SubCategoryModel();
        $subcategoryModel->update($id, [
            'name' => $this->request->getPost('name'),
            'sub_category_code' => $this->request->getPost('sub_category_code'),
            'main_category_id' => $this->request->getPost('main_category_id')
        ]);
        
        return redirect()->to('/assets/sub_categories');
    }

    public function delete($id){
        $subcategoryModel = new SubCategoryModel();
        $subcategoryModel->delete($id);

        return redirect()->to('/assets/sub_categories');
    }
}
