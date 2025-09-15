<h2 style="margin-left: 30px; color:blue;">Asset Categories</h2>

<!-- Button to trigger Add Category Modal -->
<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
        Add Category
    </button>
</div>


<!-- Add Category Modal -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" aria-labelledby="modalAddCategory" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddCategory">Add a New Category</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/assets/categories/add" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Category Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="description" placeholder="Description">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add This Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Asset Categories</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= $category['name'] ?></td>
                                <td><?= $category['description'] ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?= $category['id'] ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal<?= $category['id'] ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editCategoryModal<?= $category['id'] ?>" tabindex="-1" aria-labelledby="editCategoryModalLabel<?= $category['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCategoryModalLabel<?= $category['id'] ?>">Edit Category</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="/assets/categories/update/<?= $category['id'] ?>" method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="name" value="<?= $category['name'] ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="description" value="<?= $category['description'] ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteCategoryModal<?= $category['id'] ?>" tabindex="-1" aria-labelledby="deleteCategoryModalLabel<?= $category['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteCategoryModalLabel<?= $category['id'] ?>">Delete Category</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete "<strong><?= $category['name'] ?></strong>"?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <a href="/assets/categories/delete/<?= $category['id'] ?>" class="btn btn-danger">Yes, Delete</a>
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
