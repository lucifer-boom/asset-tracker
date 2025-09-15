<div class="container-fluid mt-4">
    <h2 class="mb-4">Welcome, <?= session()->get('username') ?></h2>

<!-- Assets in User's Division -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Assets in Your Division (<?= session()->get('department') ?>)</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Asset Category</th>
                        <th>Asset Model</th>
                        <th>Asset Code</th>
                        <th>Serial Number</th>
                        <th>Assigned User</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($departmentAssets)): ?>
                        <?php foreach($departmentAssets as $asset): ?>
                        <tr>
                            <td><?= $asset['asset_category'] ?></td>
                            <td><?= $asset['asset_name'] ?></td>
                            <td><?= $asset['asset_code'] ?></td>
                            <td><?= $asset['serial_number'] ?></td>
                            <td><?= $asset['user'] ?></td>
                            <td><?= $asset['status'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center">No assets in your division.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>