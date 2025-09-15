<h2 style="margin-left: 30px; color:blue;">Asset Sub Categories</h2>

<!-- Button to trigger Add Category Modal -->
<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSubCategory">
        Add Sub Category
    </button>
</div>

<div class="modal fade" id="modalAddSubCategory" tabindex="-1" aria-labelledby="modalAddSubCategory" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add a New Sub Category</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/assets/sub_categories/add" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="main_category_id" class="form-control" required>
                            <option value="">Select Main Category</option>
                            <?php foreach ($categories as $subcategory): ?>
                                <option value="<?= $subcategory['id'] ?>"><?= $subcategory['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Sub Category Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="sub_category_code" placeholder="Sub Category Code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Sub Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Asset Sub Categories</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Main Category</th>
                            <th>Sub Category</th>
                            <th>Sub Category Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subcategories as $subcategory): ?>
                            <tr>
                                <td><?= $subcategory['id'] ?></td>
                                <td><?= $subcategory['main_category_name'] ?></td>
                                <td><?= $subcategory['name'] ?></td>
                                <td><?= $subcategory['sub_category_code'] ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditSubCategory<?= $subcategory['id'] ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal<?= $subcategory['id'] ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                       <div class="modal fade" id="modalEditSubCategory<?= $subcategory['id'] ?>" tabindex="-1" aria-labelledby="modalEditSubCategory" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sub Category</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/assets/sub_categories/update/<?= $subcategory['id'] ?>" method="post">
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <select name="main_category_id" class="form-control" required>
                            <option value="">Select Main Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"
                                    <?= ($subcategory['name'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <input type="text" class="form-control" name="name" 
                               value="<?= esc($subcategory['name']) ?>" 
                               placeholder="Sub Category Name" required>
                    </div>
                    <div class="form-group mb-2">
                        <input type="text" class="form-control" name="sub_category_code" 
                               value="<?= esc($subcategory['sub_category_code']) ?>" 
                               placeholder="Sub Category Code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Sub Category</button>
                </div>
            </form>
        </div>
    </div>
</div>


                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteCategoryModal<?= $subcategory['id'] ?>" tabindex="-1" aria-labelledby="deleteCategoryModalLabel<?= $subcategory['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteCategoryModalLabel<?= $subcategory['id'] ?>">Delete Category</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete "<strong><?= $subcategory['name'] ?></strong>"?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <a href="/assets/sub_categories/delete/<?= $subcategory['id'] ?>" class="btn btn-danger">Yes, Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>