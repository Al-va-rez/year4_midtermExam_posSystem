<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.33.3/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="text-center mb-4">Login</h4>
            <form id="loginForm">
              <div class="mb-3">
                <label for="username" class="form-label fw-medium">Username</label>
                <input type="text" id="username" class="form-control" placeholder="Enter your username">
              </div>
              <div class="mb-3">
                <label for="password" class="form-label fw-medium">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Enter your password">
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>     
    document.addEventListener('DOMContentLoaded', () => {
      const loginForm = document.getElementById('loginForm');

      loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const data = {
          action: 'login',
          username: document.getElementById('username').value,
          password: document.getElementById('password').value
        };

        try {
          const response = await fetch('api.php', {
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
              timer: 1500,
              timerProgressBar: true,
              showConfirmButton: false
            });

            setTimeout(() => {
              window.location.href = result.is_cashier ? 'index.php' : 'superAdmin/all_Cashiers.php';
            }, 1500);

          } else {
            Swal.fire({
              icon: 'error',
              title: 'Something went wrong',
              text: result.message,
              confirmButtonText: 'OK'
            });
          }
        } catch (error) {
          Swal.fire({
            icon: 'error',
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