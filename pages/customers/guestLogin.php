<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

// Check if the user is logged in as a guest
if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'guest') {
  // User is logged in as a guest, redirect them to the appropriate page
  header("Location: ../../pages/customers/customer_menu.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<header>
  <style>
    .error-message {
      color: Blue;
      font-weight: bold;
      font-size: 18px;
    }
  </style>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Guest Verification</h1>
</header>

<body>
  <form class="form_login" action="../../admin/guestlogin.scr.php" method="post">
    <?php
    if (isset($_GET['error'])) {
      $error = $_GET['error'];
      if ($error === 'incorrectpin') {
        echo '<p class="error-message">The table pin is incorrect.</p>';
      } else if ($error === 'invalidtable') {
        echo '<p class="error-message">Incorrect table number.</p>';
      } else if ($error === 'emptyfields') {
        echo '<p class="error-message">Fill in every field.</p>';
      } else if ($error === 'invalidguest') {
        echo '<p class="error-message">Account is already been used create a new guest account.</p>';
      }
    } ?>
    <input class="input_login" type="text" name="VN" placeholder="Enter Reference Number">
    <input class="input_login" type="number" name="table_number" placeholder="Table Number">
    <input class="input_login" type="text" name="PIN" placeholder="Table Pincode">
    <button class="form_button" type="submit" name="login-submit">Login</button>

    <p class="p4">If you don't have a reference number, quick add your details please <a class="redirect_link_style" href="guestSignup.php">click here</a> to get a reference number.</p>
  </form>
</body>

</html>