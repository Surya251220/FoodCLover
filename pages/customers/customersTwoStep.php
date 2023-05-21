<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

// Check if the user is logged in as a customer
if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'customer') {
  // User is already logged in as a customer, redirect them to the appropriate page
  header("Location: customer_menu.php");
  exit();
}

// Check if the user is logged in as an employer
if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'employer') {
  header("Location: ../employers/employer_home.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Customer 2-Step Varfication</h1>
  <style>
    .error-message {
      color: Blue;
      font-weight: bold;
      font-size: 18px;
    }
  </style>
</header>

<body>


  <form class="form_login" action="../../admin/twoStepCustomer.scr.php" method="post">

    <h3 style="text-align: center;">Two-Step Verification</h3>
    <?php
    if (isset($_GET['error'])) {
      $error = $_GET['error'];
      if ($error === 'incorrectcode') {
        echo '<p class="error-message">Please fill in the correct varfication code.</p>';
      }
    }
    ?>
    <input class="input_login" type="text" name="verification_code" placeholder="Verification Code">
    <button class="form_button" type="submit" name="generate-submit">Generate 2-Step Varfication</button>
    <button class="form_button" type="submit" name="verify-submit">Verify</button>
  </form>

</body>

</html>