<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';
if (isset($_SESSION['email']) && isset($_SESSION['usertype'])) {
  header("Location: customer_menu.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Customer Login</h1>
  <style>
    .button-group {
      display: flex;
      justify-content: space-between;
    }

    .button-group .form_button {
      width: 100%;
      box-sizing: border-box;
      font-size: 14px;
    }

    .form_button:not(:last-child) {
      margin-right: 5px;
    }

    .error-message {
      color: blue;
      font-weight: bold;
      font-size: 18px;
    }
  </style>
</header>

<body>

  <form class="form_login" id="login-form" method="post">
    <?php
    // Display input validation messages
    if (isset($_GET['error'])) {
      $error = $_GET['error'];
      if ($error === 'emptyfields') {
        echo '<p class="error-message">Please fill in all fields.</p>';
      } else if ($error === 'wrongpassword') {
        echo '<p class="error-message">Incorrect password.</p>';
      } else if ($error === 'nouser') {
        echo '<p class="error-message">User not found. Please Signup.</p>';
      } else if ($error === 'sqlerror') {
        echo '<p class="error-message">Database error.</p>';
      }
    }
    ?>
    <input class="input_login" type="text" name="mail" placeholder="Email">
    <input class="input_login" type="password" name="pwd" placeholder="Password">

    <button class="form_button" type="submit" name="login-submit" formaction="../../admin/customerLogin.scr.php" style="font-size: 14px;">Login</button>
    <div class="button-group" style="width: 100%;">
      <button class="form_button" type="submit" name="reset-submit" formaction="customerForgotPwd.php" style="width: 100%;">Reset Password</button>
      <button class="form_button" type="submit" name="signup" formaction="customerSignup.php" style="width: 100%;">Signup Here!</button>
    </div>
  </form>
  <script>
    document.querySelectorAll('.form_button').forEach(function(button) {
      button.addEventListener('click', function(event) {
        var form = document.getElementById('login-form');
        form.action = event.target.getAttribute('formaction');
      });
    });
  </script>

</body>

</html>