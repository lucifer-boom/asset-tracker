<h2 style="margin-left: 30px; color:blue;">User Manage</h2>

<div class="d-flex justify-content-end mb-3" style="margin-right: 30px;">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
        Create Users
    </button>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/auth/users/add" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Create a User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

                    <select name="department_id" class="form-control mb-2" required>
                        <option value="">--Select Department--</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= esc($dept['id']) ?>"><?= esc($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="system_role[]" class="form-control mb-2"  required>
                                                    <option value="">-- Select the System Role --</option>

                        <?php foreach ($systemRoles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="department_role[]" class="form-control mb-2"  required>
                        <option value="">-- Select the Department Role --</option>
                        <?php foreach ($departmentRoles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">All Users</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>System Roles</th>
                            <th>Department Roles</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php
                                $systemRoleNames = array_column(array_filter($user['roles'], fn($r)=>$r['type']=='system'), 'name');
                                $departmentRoleNames = array_column(array_filter($user['roles'], fn($r)=>$r['type']=='department'), 'name');
                            ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['department_name']) ?></td>
                                <td><?= implode(', ', $systemRoleNames) ?></td>
                                <td><?= implode(', ', $departmentRoleNames) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?= $user['id'] ?>"><i class="fa-solid fa-key"></i></button>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $user['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>

                            <!-- Edit User Modal -->
                            <div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="/auth/users/update/<?= $user['id'] ?>" method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="text" name="username" class="form-control mb-2" value="<?= esc($user['username']) ?>" required>
                                                <input type="email" name="email" class="form-control mb-2" value="<?= esc($user['email']) ?>" required>

                                                <select name="department_id" class="form-control mb-2" required>
                                                    <?php foreach ($departments as $dept): ?>
                                                        <option value="<?= esc($dept['id']) ?>" <?= $user['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                                                            <?= esc($dept['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <select name="system_role[]" class="form-control mb-2" multiple required>
                                                    <?php foreach ($systemRoles as $role): ?>
                                                        <option value="<?= $role['id'] ?>" <?= in_array($role['name'], $systemRoleNames) ? 'selected' : '' ?>><?= esc($role['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <select name="department_role[]" class="form-control mb-2" multiple required>
                                                    <?php foreach ($departmentRoles as $role): ?>
                                                        <option value="<?= $role['id'] ?>" <?= in_array($role['name'], $departmentRoleNames) ? 'selected' : '' ?>><?= esc($role['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update User</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="resetPasswordModalLabel<?= $user['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="resetPasswordModalLabel<?= $user['id'] ?>">Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p>Are you sure you want to reset the password for <strong><?= esc($user['username']) ?></strong>?</p>
        <p class="text-danger"><small>The password will be reset to <strong>icasl@123</strong>.</small></p>
      </div>
      <div class="modal-footer">
        <form action="<?= base_url('/auth/users/reset-password/' . $user['id']) ?>" method="post">
          <?= csrf_field() ?>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Reset Password</button>
        </form>
      </div>
    </div>
  </div>
</div>


                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    // Get the input and checkbox elements
    const passwordInput = document.getElementById('password');
    const showPasswordCheckbox = document.getElementById('showPassword');

    // Listen for checkbox changes
    showPasswordCheckbox.addEventListener('change', function() {
        // Toggle password visibility
        passwordInput.type = this.checked ? 'text' : 'password';
    });
</script>