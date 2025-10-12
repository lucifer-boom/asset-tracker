<h2 style="margin-left: 30px; color:blue;">Asset Models</h2>


<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddModel">
        Add Model
    </button>
</div>



<div class="modal fade" id="modalAddModel" tabindex="-1" aria-labelledby="modalAddModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddModel">Add a New Model</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/assets/models/add" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Model Name" required>
                    </div>
                    <div class="form-group">
                        <select name="sub_category_id" class="form-control" required>
                            <option value="">Select Sub Category</option>
                            <?php foreach ($subcategories as $subcategory): ?>
                                <option value="<?= $subcategory['id'] ?>"><?= $subcategory['name'] ?> (<?= $subcategory['sub_category_code'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                            </div>
                            <form action="/assets/models/add" method="post" enctype="multipart/form-data">
  

        <div class="form-group">
            <label for="model_image">Upload Model Image</label>
            <input type="file" class="form-control" name="model_image" accept="image/*">
        </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="description" placeholder="Description">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add This Model</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Asset Models</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Model Name</th>
                            <th>Sub Category & Code</th>
                            <th>Asset Image</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($models as $m): ?>
                            <tr>
                                <td><?= $m['id'] ?></td>
                                <td><?= $m['category_name'] ?></td>
                                <td><?= $m['name'] ?></td>
                                <td><?= $m['sub_category_code'] ?></td>
                                <td>
    <?php if (!empty($m['image_path'])): ?>
        <img src="<?= base_url($m['image_path']) ?>" alt="Model Image" style="width:80px; height:auto;">
    <?php else: ?>
        No Image
    <?php endif; ?>
</td>

                                <td><?= $m['description'] ?></td>

                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModelModal<?= $m['id'] ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModelsModal<?= $m['id'] ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModelModal<?= $m['id'] ?>" tabindex="-1" aria-labelledby="editModelModal<?= $m['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModelModal<?= $m['id'] ?>">Edit Category</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="/assets/models/update/<?= $m['id'] ?>" method="post" enctype="multipart/form-data">
    <div class="modal-body">
        <!-- Category -->
        <div class="form-group mb-3">
            <label for="category_id">Select Category</label>
            <select name="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" 
                        <?= ($m['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Sub Category -->
        <div class="form-group mb-3">
            <label for="sub_category_id">Select Sub Category</label>
            <select name="sub_category_id" class="form-control" required>
                <option value="">Select Sub Category</option>
                <?php foreach ($subcategories as $subcategory): ?>
                    <option value="<?= $subcategory['id'] ?>" 
                        <?= ($m['sub_category_id'] == $subcategory['id']) ? 'selected' : '' ?>>
                        <?= esc($subcategory['name']) ?> (<?= esc($subcategory['sub_category_code']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Model Name -->
        <div class="form-group mb-3">
            <label for="name">Model Name</label>
            <input type="text" class="form-control" name="name" 
                   value="<?= esc($m['name']) ?>" required>
        </div>

        <!-- Description -->
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <input type="text" class="form-control" name="description" 
                   value="<?= esc($m['description']) ?>">
        </div>

        <!-- Image Upload -->
        <div class="form-group mb-3">
            <label for="model_image">Upload New Image (optional)</label>
            <input type="file" class="form-control" name="model_image" accept="image/*">
        </div>

        <!-- Current Image Preview -->
        <?php if (!empty($m['image_path'])): ?>
            <div class="text-center mt-3">
                <label>Current Image:</label><br>
                <img src="<?= base_url($m['image_path']) ?>" 
                     alt="Model Image" width="120" height="120"
                     class="rounded border mt-1" style="object-fit:cover;">
            </div>
        <?php else: ?>
            <p class="text-muted text-center mt-2">No image uploaded</p>
        <?php endif; ?>
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
                            <div class="modal fade" id="deleteModelsModal<?= $m['id'] ?>" tabindex="-1" aria-labelledby="deleteModelsModal<?= $m['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModelsModal<?= $m['id'] ?>">Delete Category</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete "<strong><?= $m['name'] ?></strong>"?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <a href="/assets/models/delete/<?= $m['id'] ?>" class="btn btn-danger">Yes, Delete</a>
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