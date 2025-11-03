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
          <a class="nav-link" href="menu_items.php">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="transactions.php">Transactions</a>
        </li>
      </ul>
      <button class="btn btn-outline-danger ms-auto" id="logoutBtn">Logout</button>
    </div>
  </nav>

  <!-- TRANSACTION RECORDS -->
  <main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
    </div>
    <label for="start_date">Start Date</label>
    <input type="date" id="start_date" class="mb-3">
    <label for="end_date">End Date</label>
    <input type="date" id="end_date" class="mb-3">
    <button id="resetDates">Reset date range</button>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Handled By</th>
            <th>Total</th>
            <th>Date Added</th>
          </tr>
        </thead>
        <tbody id="transactionsTable">
          <!-- appended by js -->
        </tbody>
      </table>
    </div>
  </main>
  <!-- /TRANSACTION RECORDS -->

  <script>
    async function showTransactions(start_date = '', end_date = '') {
      try {
        const response = await fetch('../api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'getTransactions', start_date: start_date, end_date: end_date })
        });

        const result = await response.json();

        if (result.status === 'success') {
          let tbody = document.getElementById('transactionsTable');
          tbody.innerHTML = "";

          if (result.transactions.length > 0) {
            result.transactions.forEach((row) => {
              tbody.innerHTML += `
                <tr class="t_Row" data-transactionId="${row.id}">
                  <td>${row.id}</td>
                  <td>${row.cashier_username}</td>
                  <td>${row.total}</td>
                  <td>${row.date_added}</td>
                </tr>

                <tr class="transactionDetails d-none" data-parentid="${row.id}">
                  <td colspan="4">
                    <div class="p-3 border-start border-end border-bottom bg-light">
                      <strong>Transaction Items:</strong>
                      <ul class="list-group list-group-flush mt-2"></ul>
                    </div>
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
      showTransactions('');

      // search transactions
      const startDate = document.getElementById('start_date');
      const endDate = document.getElementById('end_date');
      startDate.addEventListener('input', () => showTransactions(startDate.value, endDate.value));
      endDate.addEventListener('input', () => showTransactions(startDate.value, endDate.value));
      document.getElementById('resetDates').addEventListener('click', () => {startDate.value = ''; endDate.value = ''; showTransactions(startDate.value, endDate.value)});

      const table = document.getElementById('transactionsTable');
      table.addEventListener('click', async (e) => {
        const row = e.target.closest('.t_Row');
        if (!row) return;

        const id = row.dataset.transactionid;
        const detailsRow = document.querySelector(`.transactionDetails[data-parentid='${id}']`);

        // Hide all other open dropdowns
        document.querySelectorAll('.transactionDetails').forEach(tr => {
          if (tr !== detailsRow) {
            tr.classList.add('d-none');
          }
        });

        // Toggle the clicked one
        if (detailsRow.classList.contains('d-none')) {
          const response = await fetch('../api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'getTransactionDetails', transactionId: id })
          });
          const result = await response.json();
          if (result.status === 'success') {
            const items = result.details; // [{ name, qty, subtotal }]
            const ul = detailsRow.querySelector('ul.list-group');
            ul.innerHTML = items.map(i => {
              const name = i.item_name ?? '';
              const price = Number(i.item_price) || 0;
              const qty = Number(i.item_quantity) || 0;
              const subtotal = Number(i.item_subtotal) || 0;
              return `
                <li class="list-group-item d-flex justify-content-between">
                  <span>${name} - ₱${price} (×${qty})</span><span>₱${subtotal.toFixed(2)}</span>
                </li>
              `;
            }).join('');
          }
          detailsRow.classList.remove('d-none');
        } else {
          detailsRow.classList.add('d-none');
        }
      });
      
      // logout
      const logoutBtn = document.getElementById('logoutBtn');
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