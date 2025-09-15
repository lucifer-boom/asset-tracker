<?php

namespace App\Controllers;

use App\Models\ModelModel;     // model for asset_models
use App\Models\CategoryModel;  // to fetch categories
use App\Models\SubCategoryModel;  // to fetch sub categories
use CodeIgniter\Controller;


class ModelsController extends Controller
{
    public function index($id = null)
    {
        $modelModel = new ModelModel();
        $categoryModel = new CategoryModel();
        $subCategoryModel = new SubCategoryModel();

        $data['categories'] = $categoryModel->findAll();
        $data['subcategories'] = $subCategoryModel->findAll();   // ✅ Add this

        $data['models'] = $modelModel->getAssetModelsWithCategories();

        // Fetch model to edit
        $data['editModel'] = [];
        if ($id) {
            $data['editModel'] = $categoryModel->find($id);  // Make sure this returns an array
        }

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/models', $data);
        echo view('includes/footer');
    }



    public function store()
    {
        $model = new ModelModel();

        $sub_category_id = $this->request->getPost('sub_category_id');

        // Optional validation: make sure the subcategory exists
        $subCategoryModel = new SubCategoryModel();
        if (!$subCategoryModel->find($sub_category_id)) {
            return redirect()->back()->with('error', 'Invalid Sub Category selected');
        }
        $model->save([
            'category_id' => $this->request->getPost('category_id'),
            'sub_category_id' => $sub_category_id,  // ✅ Add this

            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);
        return redirect()->to('/assets/models');
    }

    public function update($id)
{
    $model = new ModelModel();
    $sub_category_id = $this->request->getPost('sub_category_id');

    // Optional: validate the subcategory exists
    $subCategoryModel = new SubCategoryModel();
    if (!$subCategoryModel->find($sub_category_id)) {
        return redirect()->back()->with('error', 'Invalid Sub Category selected');
    }

    $model->update($id, [
        'category_id'    => $this->request->getPost('category_id'),
        'sub_category_id'=> $sub_category_id,   // ✅ add this
        'name'           => $this->request->getPost('name'),
        'description'    => $this->request->getPost('description')
    ]);

    return redirect()->to('/assets/models')->with('success', 'Model updated successfully');
}


    public function delete($id)
    {
        $model = new ModelModel();
        $model->delete($id);
        return redirect()->to('/assets/models');
    }
}
