<h2 class="mb-3" style="margin-left: 30px; color:blue;">Asset Transfers</h2>

<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <div class="mb-3">
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer Asset</button>
    </div>
</div>


<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/asset-transfer/store" method="post">
                <div class="modal-body">
                    <div class="form-group" style="position: relative; max-width: 400px;">
                        <!-- Search input -->
                        <input type="text" id="assetSearch" class="form-control mb-2" placeholder="Search asset by code or name">

                        <!-- Asset dropdown -->
                        <select id="assetSelect" name="asset_id" class="form-control" size="5" required>
                            <?php foreach ($assets as $asset): ?>
                                <option value="<?= esc($asset['id']) ?>">
                                    <?= esc($asset['model_name']) ?> (<?= esc($asset['asset_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="form-group">
                        <select name="from_location" class="form-control" required>
                            <option value="">-- Select From Location --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept['id']) ?>"><?= esc($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="to_location" class="form-control" required>
                            <option value="">-- Select To Location --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept['id']) ?>">
                                    <?= esc($dept['name']) ?>
                                    <?= $dept['type'] == 'external' ? '(External)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="reason_for_transfer" class="form-control" placeholder="Reason For Transfer" required></textarea>
                    </div>
                    <div class="form-group">
                        <p>Asset Custodian: <b><?= esc($user['username']) ?></b></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Tranfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                            <th>#</th>
                            <th>Asset</th>
                            <th>Asset Code</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Transfer Date</th>
                            <th>Reason</th>
                            <th>Asset Custodian</th>
                            <th>Approval Status</th>
                            <th>Received Date Assets</th>
                            <th>Received By</th>
                            <th>Transfer Note
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transfers as $index => $transfer): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= esc($transfer['asset_name'] ?? '') ?></td>
                                <td><?= esc($transfer['asset_code'] ?? '') ?></td>
                                <td><?= esc($transfer['from_location_name'] ?? '') ?></td>
                                <td><?= esc($transfer['to_location_name'] ?? '') ?></td>
                                <td><?= esc($transfer['transfer_date']) ?></td>
                                <td><?= esc($transfer['reason_for_transfer']) ?></td>
                                <td><?= esc($transfer['custodian_name'] ?? '') ?></td>
                                <td>
                                    <?php
                                    if ($transfer['hod_status'] === 'pending') {
                                        echo "HOD Approval Pending";
                                    } elseif ($transfer['admin_status'] === 'pending') {
                                        echo "Admin Approval Pending";
                                    } elseif ($transfer['ceo_status'] === 'pending') {
                                        echo "CEO Approval Pending";
                                    } elseif ($transfer['hod_status'] === 'rejected' || $transfer['admin_status'] === 'rejected' || $transfer['ceo_status'] === 'rejected') {
                                        echo "Rejected";
                                    } else {
                                        echo "Approved";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (empty($transfer['received_date'])): ?>
                                        <span class="badge bg-warning text-dark">Not Received</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= esc($transfer['received_date']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($transfer['received_by'])): ?>
                                        <span class="badge bg-success">
                                            <?= esc((new \App\Models\UserModel())->find($transfer['received_by'])['username'] ?? '') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Not Received</span>
                                    <?php endif; ?>
                                </td>
                                <td>
<a href="<?= base_url('asset-transfer/downloadTransferNote/'.$transfer['id']) ?>" target="_blank">
    <i class="fa-solid fa-download"></i>
</a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>






            <script>
                const searchInput = document.getElementById('assetSearch');
                const select = document.getElementById('assetSelect');

                searchInput.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();

                    for (let i = 0; i < select.options.length; i++) {
                        const option = select.options[i];
                        const text = option.text.toLowerCase();

                        if (text.includes(filter)) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
            </script>