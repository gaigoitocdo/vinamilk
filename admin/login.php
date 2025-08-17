<?php
include_once __DIR__ . "/views/header.php";
admin_header("Đăng nhập admin");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Đăng nhập admin</title>
  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
 <!-- CSS -->
<link rel="stylesheet" href="/admin/assets/css/nucleo-svg.css">
<link rel="stylesheet" href="/admin/assets/css/material-dashboard.css?v=3.1.0">

<!-- JS Core -->
<script src="/admin/assets/js/core/popper.min.js"></script>
<script src="/admin/assets/js/core/bootstrap.min.js"></script>

<!-- JS Plugins -->
<script src="/admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="/admin/assets/js/plugins/smooth-scrollbar.min.js"></script>

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->

  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="bg-gray-200">
  <main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Sign in</h4>
                </div>
              </div>
              <div class="card-body">
                <form role="form" id="form-login" class="text-start">
                  <div id="login-notification" class="text-center mb-3"></div>
                  <div class="input-group input-group-outline my-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                    <span class="error-message text-red-500 text-sm hidden"></span>
                  </div>
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <span class="error-message text-red-500 text-sm hidden"></span>
                  </div>
                  <div class="text-center">
                    <button type="submit" id="login-btn" class="btn bg-gradient-primary w-100 my-4 mb-2">Sign in</button>
                  </div>
                  <p class="mt-4 text-sm text-center">
                    Don't have an account?
                    <a href="../pages/sign-up.html" class="text-primary text-gradient font-weight-bold">Sign up</a>
                  </p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer position-absolute bottom-2 py-2 w-100">
        <div class="container">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-12 col-md-6 my-auto">
              <div class="copyright text-center text-sm text-white text-lg-start">
                © <script>document.write(new Date().getFullYear())</script>,
                made with <i class="fa fa-heart" aria-hidden="true"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold text-white" target="_blank">Creative Tim</a>
                for a better web.
              </div>
            </div>
            <div class="col-12 col-md-6">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item"><a href="https://www.creative-tim.com" class="nav-link text-white" target="_blank">Creative Tim</a></li>
                <li class="nav-item"><a href="https://www.creative-tim.com/presentation" class="nav-link text-white" target="_blank">About Us</a></li>
                <li class="nav-item"><a href="https://www.creative-tim.com/blog" class="nav-link text-white" target="_blank">Blog</a></li>
                <li class="nav-item"><a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-white" target="_blank">License</a></li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  <!-- Core JS Files -->
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    $(document).ready(function() {
      // API helper function
      function api({ data, onSuccess }) {
        $.ajax({
          url: '<?= route("ajax/index.php") ?>',
          type: 'POST',
          data,
          dataType: 'json',
          success: response => {
            if (response.success) {
              onSuccess(response);
            } else {
              toastr.error(response.message || "Có lỗi xảy ra");
              $('#login-notification').text(response.message || "Đăng nhập thất bại!").addClass('text-red-500');
            }
          },
          error: (xhr, status, error) => {
            console.error("Lỗi AJAX:", error);
            toastr.error("Lỗi kết nối: " + error);
            $('#login-notification').text("Lỗi kết nối, vui lòng thử lại.").addClass('text-red-500');
          }
        });
      }

      // Form validation
      function validateForm() {
        const form = document.getElementById('form-login');
        const inputs = form.querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
          const errorElement = input.nextElementSibling;
          errorElement.classList.add('hidden');
          input.classList.remove('border-red-500');

          if (!input.value.trim()) {
            isValid = false;
            errorElement.textContent = 'Vui lòng điền trường này';
            errorElement.classList.remove('hidden');
            input.classList.add('border-red-500');
          }
        });

        return isValid;
      }

      // Form submission
      $('#form-login').on('submit', function(e) {
        e.preventDefault();
        if (!validateForm()) return;

        const data = { action_type: 'login' };
        $(this).serializeArray().forEach(element => {
          data[element.name] = element.value;
        });

        const submitBtn = $('#login-btn');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...');

        api({
          data,
          onSuccess: response => {
            toastr.success("Đăng nhập thành công!");
            setTimeout(() => {
              window.location.href = "/admin";
            }, 1000);
          }
        }).always(() => {
          submitBtn.prop('disabled', false).text(originalText);
        });
      });

      // Clear error messages on input
      $('#form-login input').on('input', function() {
        $(this).next('.error-message').addClass('hidden').text('');
        $(this).removeClass('border-red-500');
        $('#login-notification').text('').removeClass('text-red-500');
      });
    });
  </script>
  <!-- Material Dashboard JS -->
  <script async defer src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>
</html>
<?php admin_footer(); ?>