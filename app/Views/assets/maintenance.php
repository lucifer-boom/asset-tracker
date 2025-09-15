<h2 style="margin-left: 30px; color:blue;">Asset Maintenance</h2>


<div class="d-flex justify-content-end form-group" style="margin-right: 30px;">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Maintenancemodal">
        New Maintenance
    </button>
</div>

<div class="modal fade" id="Maintenancemodal" tabindex="-1" aria-labelledby="Maintenancemodal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Maintenancemodal">Add a New Model</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/assets/maintenance/store" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="asset_id" class="form-control" required>
                            <option value="">-- Select Asset --</option>
                            <?php foreach ($assets as $asset): ?>
                                <option value="<?= $asset['id'] ?>"><?= $asset['asset_code'] ?> - <?= $asset['computer_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="date" name="maintenance_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <select name="maintenance_type" class="form-control" required>
                            <option value="select">--Select Maintenance Type--</option>
                            <option value="Repair">Repair</option>
                            <option value="Upgrade">Upgrade</option>
                            <option value="Warranty">Warranty</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <textarea name="description" class="form-control" rows="3" placeholder="Maintenance Description"></textarea>
                    </div>

                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="select">--Select Status--</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="number" step="0.01" name="cost" class="form-control" placeholder="Cost">
                    </div>

                    <!-- Optional hardware updates -->
                    <div class="form-group">
                        <input type="text" name="ram" class="form-control" placeholder="RAM">
                    </div>
                    <div class="form-group">
                        <input type="text" name="hdd_capacity" class="form-control" placeholder="HDD Capacity">
                    </div>
                    <div class="form-group">
                        <select name="hdd_type" class="form-control">
                            <option value="">-- Select Type --</option>
                            <option value="SATA">SATA</option>
                            <option value="M.2">M.2</option>
                            <option value="NVMe">NVMe</option>
                        </select>
                    </div>
                    <div class="form-group">
<select name="operating_system" class="form-control">
        <?php 
            $osOptions = [
                "Windows 7 Pro",
                "Windows 8 Pro",
                "Windows 8.1 Pro",
                "Windows 10 Pro",
                "Windows 11 Pro"
            ];
        ?>
        <option value="">-- Select Operating System --</option>
        <?php foreach($osOptions as $os): ?>
            <option value="<?= $os ?>" <?= isset($maintenance['operating_system']) && $maintenance['operating_system'] == $os ? 'selected' : '' ?>>
                <?= $os ?>
            </option>
        <?php endforeach; ?>
    </select>                       </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add This Maintenance</button>
                    </div>
            </form>
        </div>

    </div>
</div>
</div>


<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Asset Maintenance Records</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Asset Code</th>
                            <th>Model</th>
                            <th>Maintenance Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maintenanceRecords as $m): ?>
                            <tr>
                                <td><?= $m['asset_code'] ?></td>
                                <td><?= $m['model_id'] ?></td>
                                <td><?= $m['maintenance_date'] ?></td>
                                <td><?= $m['maintenance_type'] ?></td>
                                <td><?= $m['description'] ?? '-' ?></td>
                                <td><?= $m['status'] ?></td>
                                <td><?= $m['cost'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>