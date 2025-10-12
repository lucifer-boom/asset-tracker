<h2 style="margin-left: 30px; color:blue;">Accessories Information</h2>

<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddAccessory">
        Add Accessories
    </button>
</div>

<div class="modal fade" id="modalAddAccessory" tabindex="-1" aria-labelledby="modalAddAccessory" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add a New Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('accesories/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="name">Accessory Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="model">Model</label>
                        <input type="text" class="form-control" id="model" name="model" value="<?= old('model') ?>">
                    </div>

                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <input type="text" class="form-control" id="brand" name="brand" value="<?= old('brand') ?>">
                    </div>

                    <div class="form-group">
                        <label for="total_qty">Total Quantity</label>
                        <input type="number" min="0" class="form-control" id="total_qty" name="total_qty" value="<?= old('total_qty', 1) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="warranty_months">Warranty (months)</label>
                        <input type="number" min="0" class="form-control" id="warranty_months" name="warranty_months" value="<?= old('warranty_months', 0) ?>">
                    </div>

                    <div class="form-group">
                        <label for="purchase_date">Purchase Date (YYYY-MM-DD)</label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= old('purchase_date') ?>">
                    </div>

                    <div class="form-group">
                        <label for="supplier">Supplier</label>
                        <textarea class="form-control" id="supplier" name="supplier"><?= old('supplier') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Accessory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Assets</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Asset Code</th>
                        <th>Name</th>
                        <th>Model</th>
                        <th>Brand</th>
                        <th>Available Qty</th>
                        <th>Warranty (Months)</th>
                        <th>Purchase Date</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($accessories)): ?>
                        <?php foreach ($accessories as $index => $accessory): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= esc($accessory['asset_code']) ?></td>
                                <td><?= esc($accessory['name']) ?></td>
                                <td><?= esc($accessory['model']) ?></td>
                                <td><?= esc($accessory['brand']) ?></td>
                                <td><?= esc($accessory['available_qty']) ?></td>
                                <td><?= esc($accessory['warranty_months']) ?></td>
                                <td><?= esc($accessory['purchase_date']) ?></td>
                                <td><?= esc($accessory['supplier']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center">No accessories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>