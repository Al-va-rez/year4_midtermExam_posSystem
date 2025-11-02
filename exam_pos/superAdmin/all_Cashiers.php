<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: ../login.php");
} else if ($_SESSION['is_cashier']) {
  header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.33.3/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold">Super Admin Panel</a>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="all_Cashiers.php">Cashiers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="menu_items.php">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="transactions.php">Transactions</a>
        </li>
      </ul>
      <button class="btn btn-outline-danger ms-auto" id="logoutBtn">Logout</button>
    </div>
  </nav>

  <!-- CASHIER RECORDS -->
  <main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Add Cashier</button>
    </div>
    <input type="text" id="searchUser" class="mb-3" placeholder="Search users...">

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Is Suspended</th>
            <th>Date Added</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="userTable">
          <!-- appended by js -->
        </tbody>
      </table>
    </div>
  </main>

  <!-- Add Cashier Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="registerForm">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="username" class="form-label fw-medium">Username</label>
                <input type="text" id="username" class="form-control" placeholder="Username">
              </div>
              <div class="col-md-6">
                <label for="username" class="form-label fw-medium">First Name</label>
                <input type="text" id="firstname" class="form-control" placeholder="First Name">
              </div>
              <div class="col-md-6">
                <label for="username" class="form-label fw-medium">Last Name</label>
                <input type="text" id="lastname" class="form-control" placeholder="Last Name">
              </div>
              <div class="col-md-6">
                <label for="username" class="form-label fw-medium">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Must be 8+ characters long">
              </div>
              <div class="col-md-6">
                <label for="username" class="form-label fw-medium">Confirm Password</label>
                <input type="password" id="confirmPass" class="form-control" placeholder="Must be the same">
              </div>
              <div class="col-md-6 d-flex align-items-center">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="isCashier">
                  <label class="form-check-label" for="isCashier">Cashier Account</label> <!-- new id; double check references -->
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addUser" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Admin Modal -->
  <div class="modal fade" id="updateUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="updateForm">
            <input type="hidden" id="updateId">
            <div class="mb-3">
              <label for="username" class="form-label fw-medium">Username</label>
              <input type="text" id="updateUsername" class="form-control">
            </div>
            <div class="mb-3">
              <label for="username" class="form-label fw-medium">First Name</label>
              <input type="text" id="updateFirstname" class="form-control">
            </div>
            <div class="mb-3">
              <label for="username" class="form-label fw-medium">Last Name</label>
              <input type="text" id="updateLastname" class="form-control">
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="updateIsSuspended">
              <label class="form-check-label" for="updateIsSuspended">Suspend Account</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="confirmUpdate" class="btn btn-success">Update</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Admin Confirmation Modal -->
  <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Delete User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this user?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmDelete" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /CASHIER RECORDS -->

  <script>
    async function showUsers(query = '') {
      try {
        const response = await fetch('../api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'read', search: query })
        });

        const result = await response.json();

        if (result.status === 'success') {
          let tbody = document.getElementById('userTable');
          tbody.innerHTML = "";

          if (result.users.length > 0) {
            result.users.forEach((row) => {
              if (row.is_cashier) {
                tbody.innerHTML += `
                  <tr>
                    <td>${row.id}</td>
                    <td>${row.username}</td>
                    <td>${row.firstname}</td>
                    <td>${row.lastname}</td>
                    <td>${row.is_suspended ? 'TRUE' : 'FALSE'}</td>
                    <td>${row.date_added}</td>
                    <td>
                      <button type="button" class="btn btn-primary updateBtn" data-bs-toggle="modal" data-bs-target="#updateUserModal"
                        data-userid="${row.id}" data-username="${row.username}" data-firstname="${row.firstname}"
                        data-lastname="${row.lastname}" data-isSuspended="${row.is_suspended}">Update</button>
                      <button type="button" class="btn btn-secondary deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                        data-userid="${row.id}">Delete</button>
                    </td>
                  </tr>
                `;
              }
            });
          } else {
            tbody.innerHTML = `<tr><td colspan="7">No records found</td></tr>`;
          }
        } else {
          Swal.fire({
            icon: 'Error',
            title: 'Something went wrong',
            text: result.message,
            confirmButtonText: 'OK'
          });
        }
      } catch (error) {
        Swal.fire({
          icon: 'Error',
          title: 'FETCH ERROR',
          text: error.message,
          confirmButtonText: 'OK'
        });
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      // show all records
      showUsers('');

      // search users
      document.getElementById('searchUser').addEventListener('input', e => showUsers(e.target.value.trim()));
      
      const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
      const updateUserModal = new bootstrap.Modal(document.getElementById('updateUserModal'));

      const logoutBtn = document.getElementById('logoutBtn');
      const addUser = document.getElementById('addUser');
      const confirmUpdate = document.getElementById('confirmUpdate');
      const confirmDelete = document.getElementById('confirmDelete');
      let selectedUserId = null;

      // populate edit form modal and get the selected user id
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('updateBtn')) {
          selectedUserId = e.target.getAttribute('data-userid');
          document.getElementById('updateId').value = selectedUserId;
          document.getElementById('updateUsername').value = e.target.getAttribute('data-username');
          document.getElementById('updateFirstname').value = e.target.getAttribute('data-firstname');
          document.getElementById('updateLastname').value = e.target.getAttribute('data-lastname');
          document.getElementById('updateIsSuspended').checked = e.target.getAttribute('data-isSuspended') == 1;
        }

        if (e.target.classList.contains('deleteBtn')) {
          selectedUserId = e.target.getAttribute('data-userid');
        }
      });

      // add new users
      addUser.addEventListener('click', async (e) => {
        e.preventDefault();
        
        const data = {
          action: 'create',
          username: document.getElementById('username').value,
          firstname: document.getElementById('firstname').value,
          lastname: document.getElementById('lastname').value,
          password: document.getElementById('password').value,
          confirm_password: document.getElementById('confirmPass').value,
          is_cashier: document.getElementById('isCashier').checked
        };

        try {
          const response = await fetch('../api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
          });
          
          const result = await response.json();

          if (result.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: result.status.toUpperCase(),
              text: result.message,
              timer: 3000,
              timerProgressBar: true,
              confirmButtonText: 'OK'
            }).then(() => { // data.bs.dismiss attribute is removed on save button to keep modal on screen during input validations, so this is the workaround
              addUserModal.hide();
              document.getElementById('registerForm').reset()
            });
            showUsers('');

          } else {
            Swal.fire({
              icon: 'Error',
              title: 'Something went wrong',
              text: result.message,
              confirmButtonText: 'OK'
            });
          }
        } catch (error) {
          Swal.fire({
            icon: 'Error',
            title: 'FETCH ERROR',
            text: error.message,
            confirmButtonText: 'OK'
          });
        }
      });
      // reset form inputs when cancelling adding of user midway
      document.getElementById('addUserModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('registerForm').reset();
      });

      // confirm update
      confirmUpdate.addEventListener('click', async (e) => {
        e.preventDefault();

        const data = {
          action: 'update',
          userId: document.getElementById('updateId').value,
          username: document.getElementById('updateUsername').value,
          firstname: document.getElementById('updateFirstname').value,
          lastname: document.getElementById('updateLastname').value,
          is_suspended: document.getElementById('updateIsSuspended').checked
        };

        try {
          const response = await fetch('../api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
          });

          const result = await response.json();

          if (result.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: result.status.toUpperCase(),
              text: result.message,
              timer: 3000,
              timerProgressBar: true,
              confirmButtonText: 'OK'
            }).then(() => { // data.bs.dismiss attribute is removed on save button to keep modal on screen during input validations, so this is the workaround
              updateUserModal.hide();
              document.getElementById('updateForm').reset()
            });
            showUsers('');

          } else {
            Swal.fire({
              icon: 'Error',
              title: 'Something went wrong',
              text: result.message,
              confirmButtonText: 'OK'
            });
          }
        } catch (error) {
          Swal.fire({
            icon: 'Error',
            title: 'FETCH ERROR',
            text: error.message,
            confirmButtonText: 'OK'
          });
        }
      });

      // confirm delete
      confirmDelete.addEventListener('click', async (e) => {
        e.preventDefault();

        try {
          const response = await fetch('../api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', userId: selectedUserId })
          });

          const result = await response.json();

          if (result.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: result.status.toUpperCase(),
              text: result.message,
              timer: 3000,
              timerProgressBar: true,
              confirmButtonText: 'OK'
            });
            showUsers('');

          } else {
            Swal.fire({
              icon: 'Error',
              title: 'Something went wrong',
              text: result.message,
              confirmButtonText: 'OK'
            });
          }
        } catch (error) {
          Swal.fire({
            icon: 'Error',
            title: 'FETCH ERROR',
            text: error.message,
            confirmButtonText: 'OK'
          });
        }
      });

      // logout
      logoutBtn.addEventListener('click', async () => {
        try {
          const response = await fetch('../api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'logout' })
          });

          const result = await response.json();

          if (result.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: result.status.toUpperCase(),
              text: result.message,
              timer: 1500,
              timerProgressBar: true,
              showConfirmButton: false
            });
            setTimeout(() => window.location.href = '../login.php', 1500);

          } else {
            Swal.fire({
              icon: 'Error',
              title: 'Something went wrong',
              text: result.message,
              confirmButtonText: 'OK'
            });
          }
        } catch (error) {
          Swal.fire({
            icon: 'Error',
            title: 'FETCH ERROR',
            text: error.message,
            confirmButtonText: 'OK'
          });
        }
      });
    });
  </script>
</body>
</html>