<h2 style="margin-left:30px; color:blue;">Assets QR Codes</h2>

<div class="container mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Assets</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Asset Name</th>
                            <th>Asset Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assets as $asset): ?>
                            <tr>
                                <td><?= $asset['id'] ?></td>
<td><?= $asset['model_name'] ?></td>
                                <td><?= $asset['asset_code'] ?></td>
                                <td>
                                    <a href="/assets/generateQr/<?= $asset['id'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                        Generate QR
                                    </a>
                                    <a href="/assets/downloadSticker/<?= $asset['id'] ?>" class="btn btn-success">
    Download Sticker
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
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>