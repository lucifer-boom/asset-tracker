<h2 class="mb-3" style="margin-left: 30px; color:blue;">Assets Receiveds</h2>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>Past Approvals</th>
                            <th>Received</th>
                        </tr>

                    <tbody>
                        <?php foreach ($transfers as $transfer): ?>
                            <tr>
                                <td><?= esc($transfer['asset_name']) ?></td>
                                <td><?= esc($transfer['from_name']) ?></td>
                                <td><?= esc($transfer['to_name']) ?></td>
                                <td><?= esc($transfer['reason_for_transfer']) ?></td>

                                <!-- Past Approvals -->
                                <td>
                                    <?php
                                    $userModel = new \App\Models\UserModel();

                                    // HOD approval
                                    if (!empty($transfer['hod_approval'])) {
                                        $hod = $userModel->find($transfer['hod_approval']);
                                        echo 'HOD: ' . ($hod['username'] ?? 'Unknown') . ' - ' . ucfirst($transfer['hod_status']) . '<br>';
                                    }

                                    // Admin approval
                                    if (!empty($transfer['admin_approval'])) {
                                        $admin = $userModel->find($transfer['admin_approval']);
                                        echo 'Admin: ' . ($admin['username'] ?? 'Unknown') . ' - ' . ucfirst($transfer['admin_status']) . '<br>';
                                    }

                                    // CEO approval
                                    if (!empty($transfer['ceo_approval'])) {
                                        $ceo = $userModel->find($transfer['ceo_approval']);
                                        echo 'CEO: ' . ($ceo['username'] ?? 'Unknown') . ' - ' . ucfirst($transfer['ceo_status']) . '<br>';
                                    }
                                    ?>
                                </td>

                                <!-- Received -->
                                <td>
                                    <?php if ($transfer['to_hod_id'] == $user_id && empty($transfer['received_date'])): ?>
                                        <form action="<?= site_url('asset-transfer/receiveAsset/' . $transfer['id']) ?>" method="post">
                                            <button type="submit" class="btn btn-success btn-sm">Mark Received</button>
                                        </form>
                                    <?php else: ?>
                                        <?php if (!empty($transfer['received_date'])): ?>
                                            <span class="badge bg-success">Received on <?= esc($transfer['received_date']) ?></span><br>
                                            <span class="badge bg-info">
                                                By: <?= esc($userModel->find($transfer['received_by'])['username'] ?? 'Unknown') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Waiting</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>