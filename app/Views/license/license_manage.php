
<div class="container mt-4">
    <h2><?= isset($license) ? 'Edit License' : 'Add New License' ?></h2>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= isset($license) ? base_url('licenses/update/'.$license['id']) : base_url('licenses/store') ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="license_name" class="form-label">License Name</label>
            <input type="text" name="license_name" id="license_name" class="form-control" required
                value="<?= isset($license) ? esc($license['license_name']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="license_type" class="form-label">License Type</label>
            <input type="text" name="license_type" id="license_type" class="form-control" required
                value="<?= isset($license) ? esc($license['license_type']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="version" class="form-label">Version</label>
            <input type="text" name="version" id="version" class="form-control"
                value="<?= isset($license) ? esc($license['version']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-select" required>
                <option value="">Select Supplier</option>
                <?php foreach($suppliers as $supplier): ?>
                    <option value="<?= $supplier['id'] ?>" <?= isset($license) && $license['supplier_id']==$supplier['id'] ? 'selected' : '' ?>>
                        <?= esc($supplier['supplier_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="purchase_date" class="form-label">Purchase Date</label>
            <input type="date" name="purchase_date" id="purchase_date" class="form-control" required
                value="<?= isset($license) ? esc($license['purchase_date']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control"
                value="<?= isset($license) ? esc($license['expiry_date']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="renewal_date" class="form-label">Renewal Date</label>
            <input type="date" name="renewal_date" id="renewal_date" class="form-control"
                value="<?= isset($license) ? esc($license['renewal_date']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="cost" class="form-label">Cost</label>
            <input type="number" step="0.01" name="cost" id="cost" class="form-control" required
                value="<?= isset($license) ? esc($license['cost']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="currency" class="form-label">Currency</label>
            <select name="currency" id="currency" class="form-select" required>
                <option value="LKR" <?= isset($license) && $license['currency']=='LKR' ? 'selected' : '' ?>>LKR</option>
                <option value="USD" <?= isset($license) && $license['currency']=='USD' ? 'selected' : '' ?>>USD</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="license_status" class="form-label">License Status</label>
            <select name="license_status" id="license_status" class="form-select" required>
                <option value="Active" <?= isset($license) && $license['license_status']=='Active' ? 'selected' : '' ?>>Active</option>
                <option value="Expired" <?= isset($license) && $license['license_status']=='Expired' ? 'selected' : '' ?>>Expired</option>
                <option value="Revoked" <?= isset($license) && $license['license_status']=='Revoked' ? 'selected' : '' ?>>Revoked</option>
                <option value="Pending" <?= isset($license) && $license['license_status']=='Pending' ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="purchase_order_number" class="form-label">Purchase Order Number</label>
            <input type="text" name="purchase_order_number" id="purchase_order_number" class="form-control"
                value="<?= isset($license) ? esc($license['purchase_order_number']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="invoice_number" class="form-label">Invoice Number</label>
            <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                value="<?= isset($license) ? esc($license['invoice_number']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <input type="text" name="payment_method" id="payment_method" class="form-control"
                value="<?= isset($license) ? esc($license['payment_method']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="purchase_approved_by" class="form-label">Approved By</label>
            <input type="text" name="purchase_approved_by" id="purchase_approved_by" class="form-control"
                value="<?= isset($license) ? esc($license['purchase_approved_by']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control" rows="3"><?= isset($license) ? esc($license['remarks']) : '' ?></textarea>
        </div>

        <div class="mb-3">
            <label for="attachments" class="form-label">Attachments</label>
            <input type="file" name="attachments" id="attachments" class="form-control">
            <?php if(isset($license) && $license['attachments']): ?>
                <small>Current File: <?= esc($license['attachments']) ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary"><?= isset($license) ? 'Update License' : 'Save License' ?></button>
    </form>
</div>

