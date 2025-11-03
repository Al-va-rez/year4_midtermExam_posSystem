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
          <a class="nav-link" href="all_Cashiers.php">Cashiers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="menu_items.php">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="transactions.php">Transactions</a>
        </li>
      </ul>
      <button class="btn btn-outline-danger ms-auto" id="logoutBtn">Logout</button>
    </div>
  </nav>

  <!-- MENU -->
  <main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addToMenuModal">+ Add Product</button>
    </div>
    <input type="text" id="searchProduct" class="mb-3" placeholder="Search products...">

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Date Added</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="menuTable">
          <!-- appended by js -->
        </tbody>
      </table>
    </div>
  </main>

  <!-- Add Menu Item Modal -->
  <div class="modal fade" id="addToMenuModal" tabindex="-1" aria-hidden="true">
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
                <label for="username" class="form-label fw-medium">Name</label>
                <input type="text" id="itemName" class="form-control" placeholder="name">
              </div>
              <div class="col-md-6">
                <label for="username" class="form-label fw-medium">Price</label>
                <input type="number" id="itemPrice" class="form-control" placeholder="price">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addMenuItem" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Admin Modal -->
  <div class="modal fade" id="updateMenuItemModal" tabindex="-1" aria-hidden="true">
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
              <label for="username" class="form-label fw-medium">New Item Name</label>
              <input type="text" id="updateItemName" class="form-control">
            </div>
            <div class="mb-3">
              <label for="username" class="form-label fw-medium">New Price</label>
              <input type="number" id="updateItemPrice" class="form-control">
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
  <div class="modal fade" id="deleteMenuItemModal" tabindex="-1" aria-hidden="true">
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
  <!-- /MENU -->

  <script>
    async function showMenu(query = '') {
      try {
        const response = await fetch('../api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'getMenu', search: query })
        });

        const result = await response.json();

        if (result.status === 'success') {
          let tbody = document.getElementById('menuTable');
          tbody.innerHTML = "";

          if (result.menu.length > 0) {
            result.menu.forEach((row) => {
              tbody.innerHTML += `
                <tr>
                  <td>${row.id}</td>
                  <td>${row.item_name}</td>
                  <td>${row.item_price}</td>
                  <td>${row.date_added}</td>
                  <td>
                    <button type="button" class="btn btn-primary updateBtn" data-bs-toggle="modal" data-bs-target="#updateMenuItemModal"
                      data-itemId="${row.id}" data-itemName="${row.item_name}" data-itemPrice="${row.item_price}">Update</button>
                    <button type="button" class="btn btn-secondary deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteMenuItemModal"
                      data-itemId="${row.id}">Delete</button>
                  </td>
                </tr>
              `;
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
      showMenu('');

      // search products
      document.getElementById('searchProduct').addEventListener('input', e => showMenu(e.target.value.trim()));
      
      
      const addToMenuModal = new bootstrap.Modal(document.getElementById('addToMenuModal'));
      const updateMenuItemModal = new bootstrap.Modal(document.getElementById('updateMenuItemModal'));

      const logoutBtn = document.getElementById('logoutBtn');
      const addMenuItem = document.getElementById('addMenuItem');
      const confirmUpdate = document.getElementById('confirmUpdate');
      const confirmDelete = document.getElementById('confirmDelete');
      let selectedItemId = null;


      // populate edit form modal and get the selected user id
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('updateBtn')) {
          selectedItemId = e.target.getAttribute('data-itemId');
          document.getElementById('updateId').value = selectedItemId;
          document.getElementById('updateItemName').value = e.target.getAttribute('data-itemName');
          document.getElementById('updateItemPrice').value = e.target.getAttribute('data-itemPrice');
        }

        if (e.target.classList.contains('deleteBtn')) {
          selectedItemId = e.target.getAttribute('data-itemId');
        }
      });


      // add new menu items
      addMenuItem.addEventListener('click', async (e) => {
        e.preventDefault();
        
        const data = {
          action: 'createMenuItem',
          item_name: document.getElementById('itemName').value,
          item_price: document.getElementById('itemPrice').value
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
              addToMenuModal.hide();
              document.getElementById('registerForm').reset()
            });
            showMenu('');

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
      document.getElementById('addToMenuModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('registerForm').reset();
      });


      // confirm update
      confirmUpdate.addEventListener('click', async (e) => {
        e.preventDefault();

        const data = {
          action: 'updateMenuItem',
          itemId: document.getElementById('updateId').value,
          item_name: document.getElementById('updateItemName').value,
          item_price: document.getElementById('updateItemPrice').value
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
              updateMenuItemModal.hide();
              document.getElementById('updateForm').reset()
            });
            showMenu('');

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
            body: JSON.stringify({ action: 'deleteMenuItem', itemId: selectedItemId })
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
            showMenu('');

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