<h2 style="margin-left: 30px; color:blue;">Suppliers</h2>


<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSupplier">
        Add Supplier
    </button>
</div>

<div class="modal fade" id="modalAddSupplier" tabindex="-1" aria-labelledby="modalAddSupplier" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddSupplier">Add a New Supplier</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/assets/suppliers/add" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Supplier Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="contact_person" placeholder="Contect Person">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Email Address">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="phone" placeholder="Phone Number">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="address" placeholder="Address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add This Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Supplier Information</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td><?= $s['name'] ?></td>
                                <td><?= $s['contact_person'] ?></td>
                                <td><?= $s['email'] ?></td>
                                <td><?= $s['phone'] ?></td>
                                <td><?= $s['address'] ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editSupplierModal<?= $s['id'] ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSupplierModal<?= $s['id'] ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>


                    <!-- Edit Modal -->
                    <div class="modal fade" id="editSupplierModal<?= $s['id'] ?>" tabindex="-1" aria-labelledby="editSupplierModal<?= $s['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editSupplierModal<?= $s['id'] ?>">Edit Category</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="/assets/suppliers/update/<?= $s['id'] ?>" method="post">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" value="<?= $s['name'] ?>" placeholder="Name">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="contact_person" value="<?= $s['contact_person'] ?>" placeholder="Contact Person">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="email" value="<?= $s['email'] ?>" placeholder="Email Address">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="phone" value="<?= $s['phone'] ?>" placeholder="Phone Number">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="address" value="<?= $s['address'] ?>" placeholder="Address">
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
                    <div class="modal fade" id="deleteSupplierModal<?= $s['id'] ?>" tabindex="-1" aria-labelledby="deleteSupplierModal<?= $s['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteSupplierModal<?= $s['id'] ?>">Delete Category</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete "<strong><?= $s['name'] ?></strong>"?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <a href="/assets/suppliers/delete/<?= $s['id'] ?>" class="btn btn-danger">Yes, Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </table>
            </div>
        </div>
    </div>
</div>
</div>