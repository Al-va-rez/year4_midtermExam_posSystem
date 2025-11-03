<?php

session_start();

if (!isset($_SESSION['username']) || $_SESSION['is_suspended']) {
  header("Location: login.php");
} else if (!$_SESSION['is_cashier']) {
  header("Location: superAdmin/all_Cashiers.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.33.3/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <nav class="navbar navbar-light bg-light border-bottom shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <span class="navbar-brand fw-bold">Cashier Dashboard</span>
      <button id="logoutBtn" class="btn btn-outline-danger">Logout</button>
    </div>
  </nav>

  <main class="container my-5">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>You are logged in as a cashier.</p>

    <h1>Classic Pinoy Snacks Store</h1>

    <div class="snack-container">
      <!-- Snack 1 -->
      <div class="snack">
        <h2>Nestlé Pops</h2>
        <img src="https://i.redd.it/0q4wp1p641x81.jpg" alt="Nestlé Pops">
        <p><strong>₱15.00</strong> per pack</p>
        <form id="form1" class="snackForm" data-snack="Nestlé Pops" data-price="15" onsubmit="processTransaction(event, 'form1')">
          <input type="number" class="cash" placeholder="Enter cash"><br>
          <input type="number" class="quantity" placeholder="Enter quantity"><br>
          <button type="submit">Buy Now</button>
        </form>
        <div class="result"></div>
      </div>

      <!-- Snack 2 -->
      <div class="snack">
        <h2>Monde Tini Wini Cookies</h2>
        <img src="https://subselfie.com/wp-content/uploads/2021/07/1.png?w=1024" alt="Monde Tini Wini Cookies">
        <p><strong>₱10.00</strong> per pack</p>
        <form id="form2" class="snackForm" data-snack="Monde Tini Wini Cookies" data-price="10" onsubmit="processTransaction(event, 'form2')">
          <input type="number" class="cash" placeholder="Enter cash"><br>
          <input type="number" class="quantity" placeholder="Enter quantity"><br>
          <button type="submit">Buy Now</button>
        </form>
        <div class="result"></div>
      </div>

      <!-- Snack 3 -->
      <div class="snack">
        <h2>Granny Goose Kornets</h2>
        <img src="https://georgiafilipino.co.uk/wp-content/uploads/2023/09/granny-goose-kornets.png" alt="Granny Goose Kornets">
        <p><strong>₱12.00</strong> per pack</p>
        <form id="form3" class="snackForm" data-snack="Granny Goose Kornets" data-price="12" onsubmit="processTransaction(event, 'form3')">
          <input type="number" class="cash" placeholder="Enter cash"><br>
          <input type="number" class="quantity" placeholder="Enter quantity"><br>
          <button type="submit">Buy Now</button>
        </form>
        <div class="result"></div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const logoutBtn = document.getElementById('logoutBtn');

      // *convert to event listener
      async function processTransaction(event, theform) {
        event.preventDefault();

        const form = document.getElementById(theform);

        const snackName = form.dataset.snack;
        const price = parseFloat(form.dataset.price);
        const cash = parseFloat(form.querySelector('.cash').value);
        const quantity = parseInt(form.querySelector('.quantity').value);
        const resultDiv = form.nextElementSibling;
        
        try {
          const response = await fetch('myApi.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ snackName, price, cash, quantity })
          });

          const data = await response.json();

          if (response.ok) {
            resultDiv.innerHTML = `Transaction successful!<br>
            Total: ₱${data.total.toFixed(2)}<br>
            Change: ₱${data.change.toFixed(2)}`;
          } else {
            resultDiv.innerHTML = `Error: ${data.error}`;
          }
        } catch (error) {
            resultDiv.innerHTML = `Error: ${data.error}`;
        }
      }
      
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