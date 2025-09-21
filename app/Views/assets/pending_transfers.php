<h2 class="mb-3" style="margin-left: 30px; color:blue;">Pending Approvals</h2>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pendingTable">
                     <thead>
                        <tr>
                            <th>Asset</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transfers as $transfer): ?>

                            <tr>
                                <td><?= esc($transfer['asset_name']) ?></td>
                                <td><?= esc($transfer['from_name']) ?></td>
                                <td><?= esc($transfer['to_name']) ?></td>
                                <td><?= esc($transfer['reason_for_transfer']) ?></td>
                                <td>
                                    <form method="post" action="/asset-transfer/approve/<?= $transfer['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm" name="action" value="approve">Approve</button>
                                        <button type="submit" class="btn btn-danger btn-sm" name="action" value="reject">Reject</button>
                                    </form>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- PAST APPROVALS -->
<h2 class="mb-3" style="margin-left: 30px; color:green;">Past Approvals</h2>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Assets</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pastTable">
                     <thead>
                        <tr>
                            <th>Asset</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>HOD Approval Status</th>
                            <th>HOD Approval Date</th>
                            <th>Admin Approval Status</th>
                            <th>Admin Approval Date</th>
                            <th>CEO Approval Status</th>
                            <th>CEO Approval Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pastTransfers as $transfer): ?>
                            <tr>
                                <td><?= esc($transfer['asset_name']) ?></td>
                                <td><?= esc($transfer['from_name']) ?></td>
                                <td><?= esc($transfer['to_name']) ?></td>
                                <td><?= esc($transfer['reason_for_transfer']) ?></td>

                                <!-- HOD Approval Status -->
                               <!-- HOD Approval Status -->
<td>
    <?php
    if ($transfer['hod_status'] === 'approved') {
        echo '<span class="badge bg-success">Approved</span>';
    } elseif ($transfer['hod_status'] === 'rejected') {
        echo '<span class="badge bg-danger">Rejected</span>';
    } else {
        echo '<span class="badge bg-warning text-dark">Pending</span>';
    }
    ?>
</td>

<!-- HOD Approval Date -->
<td>
    <?= !empty($transfer['hod_approval_date']) ? esc($transfer['hod_approval_date']) : 'Pending' ?>
</td>

<!-- Admin Approval Status -->
<td>
    <?php
    if ($transfer['admin_status'] === 'approved') {
        echo '<span class="badge bg-success">Approved</span>';
    } elseif ($transfer['admin_status'] === 'rejected') {
        echo '<span class="badge bg-danger">Rejected</span>';
    } else {
        echo '<span class="badge bg-warning text-dark">Pending</span>';
    }
    ?>
</td>

<!-- Admin Approval Date -->
<td>
    <?= !empty($transfer['admin_approval_date']) ? esc($transfer['admin_approval_date']) : 'Pending' ?>
</td>

<!-- CEO Approval Status -->
<td>
    <?php
    if ($transfer['ceo_status'] === 'approved') {
        echo '<span class="badge bg-success">Approved</span>';
    } elseif ($transfer['ceo_status'] === 'rejected') {
        echo '<span class="badge bg-danger">Rejected</span>';
    } else {
        echo '<span class="badge bg-warning text-dark">Pending</span>';
    }
    ?>
</td>

<!-- CEO Approval Date -->
<td>
    <?= !empty($transfer['ceo_approval_date']) ? esc($transfer['ceo_approval_date']) : 'Pending' ?>
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
    $(document).ready(function() {
    $('#pendingTable').DataTable();
    $('#pastTable').DataTable();
});

</script>

