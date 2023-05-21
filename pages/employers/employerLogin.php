<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

if (isset($_SESSION['email']) && isset($_SESSION['usertype'])) {
  header("Location: employer_home.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<header>
  <h1>Employer Login</h1>
</header>
<style>
  .error-message {
    color: Blue;
    font-weight: bold;
    font-size: 18px;
  }
</style>

<body>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <title>Restaurant</title>


  <form class="form_login" action="../../admin/login.scr.php" method="post">

    <?php
    // Display input validation messages
    if (isset($_GET['error'])) {
      $error = $_GET['error'];
      if ($error === 'emptyfields') {
        echo '<p class="error-message">Please fill in all fields.</p>';
      } else if ($error === 'wrongpassword') {
        echo '<p class="error-message">Incorrect password.</p>';
      } else if ($error === 'nouser') {
        echo '<p class="error-message">User not found. Please contact admin.</p>';
      } else if ($error === 'sqlerror') {
        echo '<p class="error-message">Database error.</p>';
      }
    }
    ?>
    <input class="input_login" type="text" name="mail" placeholder="Email">
    <input class="input_login" type="password" name="pwd" placeholder="Password">
    <button class="form_button" type="submit" name="login-submit">Login</button>
  </form>
</body>

</html>