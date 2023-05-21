<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Reset Password</h1>
  <style>
    .error-message {
      color: Blue;
      font-weight: bold;
      font-size: 18px;
    }
  </style>
</header>

<body>

  <form class="form_login" method="POST" action="../../admin/forgotPasswordCustomer.scr.php">
    <?php
    if (isset($_GET['error'])) {
      $error = $_GET['error'];
      if ($error === 'invalid_email') {
        echo '<p class="error-message">Email doesnt Exist.</p>';
      }
    }
    ?>
    <h3 style="text-align: center;">Please give your email to reset your password.</h3>
    <input class="input_login" type="text" name="email" placeholder="Email" autocomplete="off">
    <input class="form_button" type="submit" name="submit" value="Email.">
  </form>

</body>

</html>