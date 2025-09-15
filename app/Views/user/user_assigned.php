<div class="container-fluid mt-4">
    <h2 class="mb-4">Welcome, <?= session()->get('username') ?></h2>

    <!-- Assets Assigned to User -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Assets Assigned to You</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Asset Category</th>
                        <th>Asset Model</th>
                        <th>Asset Code</th>
                        <th>Asset Serial Number</th>
                        <th>Assigned Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assignedAssets)): ?>
                        <?php foreach ($assignedAssets as $asset): ?>
                            <tr>
                                <td><?= $asset['asset_category'] ?></td>
                                <td><?= $asset['asset_name'] ?></td>
                                <td><?= $asset['asset_code'] ?></td>
                                <td><?= $asset['serial_number'] ?></td>
                                <td><?= $asset['assigned_date'] ?></td>
                                <td><?= $asset['remarks'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No assets assigned to you.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>