<?php
session_start();
// *access control
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.33.3/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
  <!-- *NAVBAR -->
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
  <!-- /NAVBAR -->

  
  <!-- *MAIN LAYOUT -->
  <div class="container my-4">
    <div class="row g-0 flex-nowrap vh-100">
      <!-- *MENU CONTAINER -->
      <div class="col-md-9 pe-4 overflow-auto">
        <div class="bg-danger text-white p-3 mb-3 fw-bold fs-5">Menu</div>

        <!-- header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h6>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addToMenuModal">+ Add Product</button>
        </div>

        <!-- search -->
        <input type="text" id="searchProduct" class="form-control mb-3" placeholder="Search products...">

        <!-- items -->
        <div id="menuContainer" class="row g-4">
          <!-- Cards will be generated dynamically by JS -->
        </div>
      </div>

      <!-- *CART CONTAINER -->
      <div class="col-md-3 d-flex flex-column h-75">
        <!-- header -->
        <div class="bg-warning text-white p-3 fw-bold fs-5">Ordered Items</div>

        <!-- items -->
        <div class="flex-grow-1 overflow-auto p-3 border h-75" id="cartItemsArea">
          <ul class="list-group" id="cartItems"></ul>
        </div>

        <!-- checkout -->
        <div class="border-top p-3 bg-white">
          <div class="d-flex justify-content-between fw-bold mb-2">
            <span>Total:</span>
            <span>₱<span id="cartTotal">0.00</span></span>
          </div>
          <input type="number" id="amountPaid" class="form-control mb-2" placeholder="Enter the amount here">
          <input type="hidden" id="cashierUsername" value="<?= $_SESSION['username'] ?>">
          <button class="btn btn-success w-100" id="payBtn">Pay!</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /MAIN LAYOUT -->


  <!-- *ADD MENU ITEM MODAL -->
  <div class="modal fade" id="addToMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="registerForm">
            <div class="row g-3">
              <!-- *added by -->
              <input type="hidden" id="addedBy" value="<?= $_SESSION['username'] ?>">
              <!-- *name -->
              <div class="col-md-6">
                <label class="form-label fw-medium">Name</label>
                <input type="text" id="itemName" class="form-control" placeholder="name">
              </div>
              <!-- *price -->
              <div class="col-md-6">
                <label class="form-label fw-medium">Price</label>
                <input type="number" id="itemPrice" class="form-control" placeholder="price">
              </div>
              <!-- *image -->
              <div class="col-md-12">
                <label class="form-label fw-medium">Image</label>
                <input type="file" id="itemImage" class="form-control" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <!-- *add modal buttons -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addMenuItem" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /ADD MENU ITEM MODAL -->


  <!-- *UPDATE MENU ITEM MODAL -->
  <div class="modal fade" id="updateMenuItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="updateForm">
            <!-- *item id -->
            <input type="hidden" id="updateId">
            <!-- *new item name -->
            <div class="mb-3">
              <label class="form-label fw-medium">New Item Name</label>
              <input type="text" id="updateItemName" class="form-control">
            </div>
            <!-- *new item price -->
            <div class="mb-3">
              <label class="form-label fw-medium">New Price</label>
              <input type="number" id="updateItemPrice" class="form-control">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <!-- *update modal buttons -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="confirmUpdate" class="btn btn-success">Update</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /UPDATE MENU ITEM MODAL -->


  <!-- *DELETE MENU ITEM CONFIRMATION MODAL -->
  <div class="modal fade" id="deleteMenuItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Delete Product</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">Are you sure you want to delete this item?</div>
        <div class="modal-footer">
          <!-- *delete modal buttons -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmDelete" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /DELETE MENU ITEM CONFIRMATION MODAL -->


  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const addToMenuModal = new bootstrap.Modal(document.getElementById('addToMenuModal'));
      const updateMenuItemModal = new bootstrap.Modal(document.getElementById('updateMenuItemModal'));

      const logoutBtn = document.getElementById('logoutBtn');
      const addMenuItem = document.getElementById('addMenuItem');
      const confirmUpdate = document.getElementById('confirmUpdate');
      const confirmDelete = document.getElementById('confirmDelete');
      let selectedItemId = null;
      

      // MENU
      // *fetch menu items
      async function showMenu(query = '') {
        try {
          const response = await fetch('../api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'getMenu', search: query })
          });

          const result = await response.json();

          if (result.status === 'success') {
            const container = document.getElementById('menuContainer');
            container.innerHTML = "";

            if (result.menu.length > 0) {
              let cardsHtml = '';
              result.menu.forEach((row) => {
                cardsHtml += `
                  <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 text-center">
                      <img src="../images/${row.img_src}" class="card-img-top img-fluid" alt="${row.item_name}">
                      <div class="card-body d-flex flex-column">
                        <div class="card-title mb-1 d-flex align-items-center position-relative">
                          <h6 class="flex-grow-1 text-center m-0 w-100">${row.item_name}</h6>
                          <button class="btn btn-sm btn-outline-info px-2 infoBtn position-absolute end-0 me-1"
                            data-id="${row.id}"
                            data-user="${row.added_by}"
                            data-date="${row.date_added}"
                            title="View Info">
                            <i class="bi bi-info-circle"></i>
                          </button>
                        </div>
                        <p class="text-muted small mb-2">₱${row.item_price}</p>

                        <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                          <input type="number" class="form-control form-control-sm text-center qtyInput w-50" min="1" value="1" data-itemId="${row.id}">
                          <button type="button" class="btn btn-sm btn-danger addToCartBtn flex-shrink-0"data-itemId="${row.id}" data-itemName="${row.item_name}" data-itemPrice="${row.item_price}">
                            Add to order
                          </button>
                        </div>

                        <div class="mt-auto d-flex justify-content-center gap-2">
                          <button type="button" class="btn btn-sm btn-primary updateBtn" data-bs-toggle="modal"
                            data-bs-target="#updateMenuItemModal" data-itemId="${row.id}" data-itemName="${row.item_name}"
                            data-itemPrice="${row.item_price}">Update</button>
                          <button type="button" class="btn btn-sm btn-secondary deleteBtn" data-bs-toggle="modal"
                            data-bs-target="#deleteMenuItemModal" data-itemId="${row.id}">Delete</button>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                `;
              });

              container.innerHTML = cardsHtml;
            } else {
              container.innerHTML = `<div class="col-12 text-center text-muted">No records found</div>`;
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
      
      // *show records
      showMenu('');

      // *search records
      document.getElementById('searchProduct').addEventListener('input', e => showMenu(e.target.value.trim()));

      // *display record details
      document.addEventListener('click', (e) => {
        const infoBtn = e.target.closest('.infoBtn');
        if (!infoBtn) return;

        const addedBy = infoBtn.dataset.user || 'Unknown';
        const dateAdded = infoBtn.dataset.date || 'Not available';

        Swal.fire({
          icon: 'info',
          title: 'Item Information',
          html: `
            <p><strong>Added by:</strong> ${addedBy}</p>
            <p><strong>Date added:</strong> ${new Date(dateAdded).toLocaleString()}</p>
          `,
          confirmButtonText: 'OK'
        });
      });

      
      // CART
      let cart = [];

      // *add menu item to cart
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('addToCartBtn')) {
          const id = e.target.getAttribute('data-itemId');
          const name = e.target.getAttribute('data-itemName');
          const price = parseFloat(e.target.getAttribute('data-itemPrice'));

          // find quantity input for this item card
          const qtyInput = document.querySelector(`.qtyInput[data-itemId='${id}']`);
          const qty = Math.max(1, parseInt(qtyInput?.value || 1));

          const existing = cart.find(item => item.id === id);
          if (existing) {
            existing.qty += qty;
          } else {
            cart.push({ id, name, price, qty });
          }

          updateCartUI();
        }
      });

      // *update cart
      function updateCartUI() {
        const list = document.getElementById('cartItems');
        const totalEl = document.getElementById('cartTotal');
        list.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
          list.innerHTML = `<li class="list-group-item text-center text-muted">Cart is empty</li>`;
        } else {
          cart.forEach(item => {
            total += item.price * item.qty;
            list.innerHTML += `
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">${item.name}</div>
                  <small class="text-muted">₱${item.price.toFixed(2)} x ${item.qty}</small>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="qty controls">
                  <button type="button" class="btn btn-outline-secondary" onclick="changeQty('${item.id}', -1)">-</button>
                  <button type="button" class="btn btn-outline-secondary" onclick="changeQty('${item.id}', 1)">+</button>
                </div>
              </li>
            `;
          });
        }

        totalEl.textContent = total.toFixed(2);
      }

      // *update cart on item quantity change
      window.changeQty = function (id, delta) {
        // check if item in cart
        const item = cart.find(i => i.id === id);
        if (!item) return;

        item.qty += delta;
        if (item.qty <= 0) {
          cart = cart.filter(i => i.id !== id);
        }
        updateCartUI();
      };

      updateCartUI();


      // MODALS
      // *populate edit form modal and get the selected item id
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

      // *add menu item modal
      addMenuItem.addEventListener('click', async (e) => {
        e.preventDefault();

        const data = new FormData();
        data.append('action', 'createMenuItem');
        data.append('added_by', document.getElementById('addedBy').value);
        data.append('item_name', document.getElementById('itemName').value);
        data.append('item_price', document.getElementById('itemPrice').value);
        data.append('image', document.getElementById('itemImage').files[0]);

        try {
          const response = await fetch('../api.php', {
            method: 'POST',
            body: data
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
              title: 'Response made, but something went wrong',
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
      
      // *reset form inputs when cancelling adding of item midway
      document.getElementById('addToMenuModal').addEventListener('hidden.bs.modal', () => {document.getElementById('registerForm').reset();});

      // *update menu item modal
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

      // *delete menu item modal
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


      // MISC
      // *process transaction
      document.getElementById('payBtn').addEventListener('click', async () => {

        // Compute subtotals per item and total overall
        const cartData = cart.map(item => ({
          name: item.name,
          price: item.price,
          qty: item.qty,
          subtotal: item.price * item.qty
        }));

        const total = cartData.reduce((sum, item) => sum + item.subtotal, 0);

        const data = {
          action: 'recordTransaction',
          cashier_username: document.getElementById('cashierUsername').value,
          items: cartData,
          total: total,
          amountPaid: parseFloat(document.getElementById('amountPaid').value || 0)
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
              timer: 2000,
              showConfirmButton: false
            });
            document.getElementById('amountPaid').value = '';
            cart = [];
            updateCartUI();
          } else {
            Swal.fire({
              icon: 'warning',
              title: result.status.toUpperCase(),
              text: result.message
            });
          }

        } catch (error) {
          Swal.fire({ icon: 'error', title: 'Fetch Error', text: error.message });
        }
      });
      
      // *logout
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