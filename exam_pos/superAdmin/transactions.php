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
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.33.3/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold">Admin Panel</a>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="all_Cashiers.php">Cashiers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="menu_items.php">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="transactions.php">Transactions</a>
        </li>
      </ul>
      <button class="btn btn-outline-danger ms-auto" id="logoutBtn">Logout</button>
    </div>
  </nav>

  <!-- CASHIER RECORDS -->
  <main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
      <button class="btn btn-primary">Print report</button> <!-- .pdf -->
    </div>
    <!-- date range, so date start cannot be higher than date end -->
    <input type="text" id="searchUser" class="mb-3" placeholder="date start">
    <input type="text" id="searchUser" class="mb-3" placeholder="date end">

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Is Admin</th>
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
  <!-- /CASHIER RECORDS -->

  <script>
    async function showTransactions(query = '') {
      try {
        const response = await fetch('api.php', {
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
              if (row.is_Admin) {
                tbody.innerHTML += `
                  <tr>
                    <td>${row.id}</td>
                    <td>${row.username}</td>
                    <td>${row.firstname}</td>
                    <td>${row.lastname}</td>
                    <td>${row.date_added}</td>
                    <td>
                      <button type="button" class="btn btn-primary updateBtn" data-bs-toggle="modal" data-bs-target="#updateUserModal"
                        data-userid="${row.id}" data-username="${row.username}" data-firstname="${row.firstname}"
                        data-lastname="${row.lastname}" data-isadmin="${row.is_admin}">Update</button>
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
      const logoutBtn = document.getElementById('logoutBtn');

      // show all records
      showUsers('');

      // search users
      document.getElementById('searchUser').addEventListener('input', e => showUsers(e.target.value.trim()));

      // logout
      logoutBtn.addEventListener('click', async () => {
        try {
          const response = await fetch('api.php', {
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
            setTimeout(() => window.location.href = 'login.php', 1500);

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