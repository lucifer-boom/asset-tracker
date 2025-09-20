<h2 style="margin-left: 30px; color:blue;">Assets Information</h2>

<!-- Add Asset Button -->
<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddAsset">
        Add Asset
    </button>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="modalAddAsset" tabindex="-1" aria-labelledby="modalAddAsset" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add a New Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/assets/store" method="post">
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <select class="form-control" name="model_id" id="model_id" required>
                            <option value="">-- Select Model --</option>
                            <?php if (!empty($models)): ?>
                                <?php foreach ($models as $model): ?>
                                    <option value="<?= esc($model['id']) ?>">
                                        <?= esc($model['name']) ?>
                                        (<?= esc($model['sub_category_code'] ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                    </div>



                    <div class="form-group mb-2">
                        <select class="form-control" name="category_id" id="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= esc($cat['id']) ?>">
                                    <?= esc($cat['name']) ?> (<?= esc($cat['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <input type="text" class="form-control" name="serial_number" placeholder="Serial Number" required>
                    </div>

                    <input type="hidden" name="asset_code" id="asset_code">


                    <div class="form-group mb-2">
                        <input type="date" class="form-control" name="purchase_date" max="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group mb-2">
                        <input type="number" class="form-control" name="warranty_years" placeholder="Warranty Years" min="0" required>
                    </div>

                    <div class="form-group mb-2">
                        <select name="supplier_id" class="form-control">
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <div class="input-group">
                            <span class="input-group-text">LKR</span>
                            <input type="number" class="form-control" name="value" step="0.01" placeholder="Value" required>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <select class="form-control" name="department_id" id="department_id" required>
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept['id']) ?>">
                                    <?= esc($dept['name']) ?> (<?= esc($dept['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>




                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add This Asset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assets Table -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Assets</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Model</th>
                        <th>Category</th>
                        <th>Serial Number</th>
                        <th>Asset Code</th>
                        <th>Purchase Date</th>
                        <th>Warranty Years</th>
                        <th>Supplier</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $a): ?>
                        <tr>
                            <td><?= $a['id'] ?></td>
                            <td><?= $a['model_name'] ?></td>
                            <td><?= $a['category_name'] ?></td>
                            <td><?= $a['serial_number'] ?></td>
                            <td><?= $a['asset_code'] ?></td>
                            <td><?= $a['purchase_date'] ?></td>
                            <td><?= $a['warranty_years'] ?></td>
                            <td><?= $a['supplier_name'] ?? '' ?></td>
                            <td><?= $a['value'] ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAssetModal<?= $a['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAssetModal<?= $a['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>

                        <!-- Edit Asset Modal -->
                        <div class="modal fade" id="editAssetModal<?= $a['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Asset</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="/assets/update/<?= $a['id'] ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group mb-2">
                                                <select name="model_id" class="form-control" required>
                                                    <option value="">Select Model</option>
                                                    <?php foreach ($models as $m): ?>
                                                        <option value="<?= $m['id'] ?>" <?= ($a['model_id'] == $m['id']) ? 'selected' : '' ?>><?= $m['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-2">
                                                <select name="category_id" class="form-control" required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $c): ?>
                                                        <option value="<?= $c['id'] ?>" <?= ($a['category_id'] == $c['id']) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-2">
                                                <input type="text" class="form-control" name="serial_number" value="<?= $a['serial_number'] ?>" required>
                                            </div>

                                            <div class="form-group mb-2">
                                                <input type="text" class="form-control" name="asset_code" value="<?= $a['asset_code'] ?>" readonly>
                                            </div>

                                            <div class="form-group mb-2">
                                                <input type="date" class="form-control" name="purchase_date" value="<?= $a['purchase_date'] ?>" max="<?= date('Y-m-d') ?>" required>
                                            </div>

                                            <div class="form-group mb-2">
                                                <input type="number" class="form-control" name="warranty_years" value="<?= $a['warranty_years'] ?>" min="0" required>
                                            </div>

                                            <div class="form-group mb-2">
                                                <select name="supplier_id" class="form-control">
                                                    <option value="">Select Supplier</option>
                                                    <?php foreach ($suppliers as $s): ?>
                                                        <option value="<?= $s['id'] ?>" <?= ($a['supplier_id'] == $s['id']) ? 'selected' : '' ?>><?= $s['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-2">
                                                <div class="input-group">
                                                    <span class="input-group-text">LKR</span>
                                                    <input type="number" class="form-control" name="value" step="0.01" value="<?= $a['value'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group mb-2">
                                                <select class="form-control" name="department" required>
                                                    <option value="">Select Department</option>
                                                    <?php foreach ($departments as $d): ?>
                                                        <option value="<?= esc($d['name']) ?>"
                                                            <?= isset($editAsset['department']) && $editAsset['department'] == $d['name'] ? 'selected' : '' ?>>
                                                            <?= esc($d['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

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

                        <!-- Delete Asset Modal -->
                        <div class="modal fade" id="deleteAssetModal<?= $a['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Asset</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete "<strong><?= $a['asset_code'] ?></strong>"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <a href="/assets/delete/<?= $a['id'] ?>" class="btn btn-danger">Yes, Delete</a>
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