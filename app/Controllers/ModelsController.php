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
    $model = new \App\Models\ModelModel();
    $imageModel = new \App\Models\AssetImageModel();
    $subCategoryModel = new \App\Models\SubCategoryModel();

    $sub_category_id = $this->request->getPost('sub_category_id');

    // Validate subcategory
    if (!$subCategoryModel->find($sub_category_id)) {
        return redirect()->back()->with('error', 'Invalid Sub Category selected');
    }

    // ✅ Update model details
    $model->update($id, [
        'category_id'     => $this->request->getPost('category_id'),
        'sub_category_id' => $sub_category_id,
        'name'            => $this->request->getPost('name'),
        'description'     => $this->request->getPost('description'),
    ]);

    // ✅ Handle new image upload
    $file = $this->request->getFile('model_image');
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $uploadPath = FCPATH . 'uploads/models/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        // Check if old image exists for this model
        $existingImage = $imageModel->where('model_id', $id)->first();

        if ($existingImage) {
            // Delete old image file (optional)
            $oldImagePath = FCPATH . $existingImage['image_path'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Update existing image record
            $imageModel->update($existingImage['id'], [
                'image_path' => 'uploads/models/' . $newName
            ]);
        } else {
            // Insert new image record
            $imageModel->save([
                'model_id'   => $id,
                'image_path' => 'uploads/models/' . $newName
            ]);
        }
    }

    return redirect()->to('/assets/models')->with('success', 'Model updated successfully');
}


    public function delete($id)
    {
        $model = new ModelModel();
        $model->delete($id);
        return redirect()->to('/assets/models');
    }
}
