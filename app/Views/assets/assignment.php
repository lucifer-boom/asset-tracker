<h2 class="mb-3" style="margin-left: 30px; color:blue;">Asset Assignment & Return</h2>

<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
  <div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">Assign Asset</button>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#returnModal">Return Asset</button>
  </div>
</div>

<!-- Assign Asset Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assignModal">Assign Asset</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="/assets/assignments/store" method="post">
        <div class="modal-body">
          <div class="form-group">
            <input type="text" id="assignAssetSearch" class="form-control mb-2" placeholder="Search asset by code or model">

            <select id="assignAssetSelect" name="asset_id" class="form-control searchable" size="5" required>
              <?php foreach ($assets as $a): ?>
                <option value="<?= $a['id'] ?>">
                  <?= $a['asset_code'] ?> - <?= $a['model_name'] ?>
                </option>
              <?php endforeach; ?>
            </select>


          </div>
          <div class="form-group">
            <!-- Assign Asset Modal -->
            <input type="text" id="userSearch" class="form-control mb-2" placeholder="Search user...">

            <select id="userSelect" name="user_id" class="form-control searchable" size="5">

              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>">
                  <?= $u['username'] ?> (<?= $u['department_name'] ?? 'No Department' ?>)
                </option>
              <?php endforeach; ?>
            </select>


          </div>
          <div class="form-group">
            <input type="date" name="assigned_date" class="form-control" max="<?= date('Y-m-d') ?>" placeholder="Assigned Date" required>
          </div>
          <div class="form-group">

            <input type="text" name="remarks" class="form-control" placeholder="Remarks/Requirement">
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

<!-- Return Asset Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Return Asset</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="/assets/assignments/return" method="post">
        <div class="modal-body">
          <div class="form-group">
            <input type="text" id="returnAssetSearch" class="form-control mb-2" placeholder="Search return asset...">


            <select id="returnAssetSelect" name="assignment_id" class="form-control searchable" size="5" required>
              <?php foreach ($activeAssignments as $as): ?>
                <option value="<?= $as['id'] ?>">
                  <?= $as['asset_code'] ?> - <?= $as['model_name'] ?> | <?= $as['username'] ?> (<?= $as['department_name'] ?? 'No Department' ?>)
                </option>
              <?php endforeach; ?>
            </select>


          </div>
          <div class="form-group">
            <input type="date" name="return_date" class="form-control" max="<?= date('Y-m-d') ?>" placeholder="Returned Date" required>

          </div>


          <div class="form-group">
            <input type="text" name="remarks" class="form-control" placeholder="Remarks">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Return</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Assets Table -->
<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Assets Asignments & Return History</h6>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Asset Code</th>
            <th>Model Name</th>
            <th>Assigned To</th>
            <th>Assigned Date</th>
            <th>Return Date</th>
            <th>Remarks</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($history as $h): ?>
            <tr>
              <td><?= $h['asset_code'] ?></td>
              <td><?= $h['model_name'] ?></td>
              <td><?= $h['username'] ?> (<?= $h['department_name'] ?? 'No Department' ?>)</td>
              <td><?= $h['assigned_date'] ?></td>
              <td><?= $h['returned_date'] ?? '<span class="badge bg-success">Not Returned</span>' ?></td>
              <td><?= $h['remarks'] ?? '-' ?></td>
              <td><?= ucfirst($h['status']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function setupSearch(inputId, selectId) {
    const input = document.getElementById(inputId);
    const select = document.getElementById(selectId);
    if (!input || !select) return;

    input.addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      for (let i = 0; i < select.options.length; i++) {
        const option = select.options[i];
        option.style.display = option.text.toLowerCase().includes(filter) ? '' : 'none';
      }
    });
  }

  // Initialize searches
  setupSearch('assignAssetSearch', 'assignAssetSelect');
  setupSearch('userSearch', 'userSelect');
  setupSearch('returnAssetSearch', 'returnAssetSelect');
</script>