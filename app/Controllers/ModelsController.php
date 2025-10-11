<?php

namespace App\Controllers;

use App\Models\ModelModel;
use App\Models\CategoryModel;
use App\Models\SubCategoryModel;
use App\Models\AssetImageModel;
use CodeIgniter\Controller;

class ModelsController extends Controller
{
    public function index($id = null)
    {
        $modelModel = new ModelModel();
        $categoryModel = new CategoryModel();
        $subCategoryModel = new SubCategoryModel();

        $data['categories'] = $categoryModel->findAll();
        $data['subcategories'] = $subCategoryModel->findAll();
        $data['models'] = $modelModel->getAssetModelsWithCategories();
        $data['models'] = $modelModel->getAssetModelsWithImages();


        // Fetch model to edit
        $data['editModel'] = [];
        if ($id) {
            $data['editModel'] = $categoryModel->find($id);
        }

        echo view('includes/sidebar');
        echo view('includes/topbar');
        echo view('assets/models', $data);
        echo view('includes/footer');
    }

    public function store()
    {
        $model = new ModelModel();
        $imageModel = new AssetImageModel();
        $subCategoryModel = new SubCategoryModel();

        $sub_category_id = $this->request->getPost('sub_category_id');

        // Validate subcategory
        if (!$subCategoryModel->find($sub_category_id)) {
            return redirect()->back()->with('error', 'Invalid Sub Category selected');
        }

        // Save model data
        $model->save([
            'category_id'     => $this->request->getPost('category_id'),
            'sub_category_id' => $sub_category_id,
            'name'            => $this->request->getPost('name'),
            'description'     => $this->request->getPost('description')
        ]);

        $modelId = $model->getInsertID();

        // Handle image upload
        $file = $this->request->getFile('model_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {

            // Generate a random filename
            $newName = $file->getRandomName();

            $uploadPath = FCPATH . 'uploads/models/';
            $file->move($uploadPath, $newName);
            $imageModel->save([
                'model_id' => $modelId,
                'image_path' => 'uploads/models/' . $newName
            ]);
        }

        return redirect()->to('/assets/models')->with('success', 'Model created successfully');
    }

    public function update($id)
    {
        $model = new ModelModel();
        $subCategoryModel = new SubCategoryModel();

        $sub_category_id = $this->request->getPost('sub_category_id');

        if (!$subCategoryModel->find($sub_category_id)) {
            return redirect()->back()->with('error', 'Invalid Sub Category selected');
        }

        $model->update($id, [
            'category_id'    => $this->request->getPost('category_id'),
            'sub_category_id' => $sub_category_id,
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
