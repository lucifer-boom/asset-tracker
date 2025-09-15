<h2 class="mb-3" style="margin-left: 30px; color:blue;">Asset Transfers & Disposals</h2>

<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <div class="mb-3">
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer Asset</button>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disposalModal">Dispose Asset</button>

    </div>
</div>

<!-- Transfer Asset Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/assets/movements/transfer" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="asset_id" class="form-control" required>
                            <option value="">-- Select Asset --</option>
                            <?php foreach ($assets as $a): ?>
                                <?php if ($a['status'] != 'disposed'): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['asset_code'] ?> - <?= $a['model_name'] ?> (<?= $a['current_location'] ?? 'N/A' ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <!-- <input type="text" name="from_location" class="form-control" placeholder="From Location" required> -->
                        <select class="form-control" name="from_locations">
                            <option value="--Select From Location--">--Select From Location--</option>
                            <option value="Administration & Events">Administration & Events</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="to_location" class="form-control" placeholder="To Location" required>
                    </div>
                    <div class="form-group">
                        <textarea name="remarks" class="form-control" placeholder="Remarks (optional)"></textarea>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Disposal Asset Modal -->
<div class="modal fade" id="disposalModal" tabindex="-1" aria-labelledby="disposalModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dispose Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/assets/movements/dispose" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="asset_id" class="form-control" required>
                            <option value="">-- Select Asset --</option>
                            <?php foreach ($assets as $a): ?>
                                <?php if ($a['status'] != 'disposed'): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['asset_code'] ?> - <?= $a['model_name'] ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="date" class="form-control" name="disposal_date" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <textarea name="reason" class="form-control" placeholder="Reason"></textarea>
                    </div>
                    <div class="form-group">
                        <textarea name="remarks" class="form-control" placeholder="Remarks (optional)"></textarea>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Dispose</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#transfer">Transfer Asset</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#dispose">Dispose Asset</a>
    </li>
</ul>
<div class="container-fluid">
    <div class="tab-content mt-3">

        <div class="tab-pane fade show active" id="transfer">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Asset Transfers</h6>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered" id="transferTable" width="100%" cellspacing="0">

                            <thead>
                                <tr>
                                    <th>Asset Code</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transfers as $t): ?>
                                    <tr>
                                        <td><?= $t['asset_code'] ?></td>
                                        <td><?= $t['from_location'] ?></td>
                                        <td><?= $t['to_location'] ?></td>
                                        <td><?= $t['transfer_date'] ?></td>
                                        <td><?= $t['remarks'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <!-- Disposal Tab -->
        <div class="tab-pane fade" id="dispose">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Asset Disposals</h6>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered" id="disposalTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Date</th>
                                    <th>Reason</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($disposals as $d): ?>
                                    <tr>
                                        <td><?= $d['asset_code'] ?></td>
                                        <td><?= $d['disposal_date'] ?></td>
                                        <td><?= $d['reason'] ?></td>
                                        <td><?= $d['remarks'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>