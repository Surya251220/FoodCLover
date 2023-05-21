<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';
if (isset($_SESSION['email']) && isset($_SESSION['usertype'])) {
  header("Location: customerLogin.php");
  exit();
}

?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Customer Signup</h1>
  <style>
    .error-message {
      color: Blue;
      font-weight: bold;
      font-size: 18px;
    }
  </style>
</header>

<body>
  <form class="form_signup" method="post" action="../../admin/customer_signup.scr.php">

    <?php

    if (isset($_GET['error'])) {
      $error = $_GET['error'];
      if ($error === 'emptyfields') {
        echo '<p class="error-message">Please fill in all fields.</p>';
      } else if ($error === 'invalidmailFN') {
        echo '<p class="error-message">Full name format.</p>';
      } else if ($error === 'invalidmail') {
        echo '<p class="error-message">Invalid email format.</p>';
      } else if ($error === 'invalidFN') {
        echo '<p class="error-message">Invalid full name format.</p>';
      } else if ($error === 'passwordcheck') {
        echo '<p class="error-message">Passwords do not match.</p>';
      } else if ($error === 'invalidpwd') {
        echo '<p class="error-message">Invalid password format.</p>';
      } else if ($error === 'emailtaken') {
        echo '<p class="error-message">Email is already taken. Please choose a different one.</p>';
      } else if ($error === 'invalidage') {
        echo '<p class="error-message">Minimum age requirement is 16 years old.</p>';
      }
    }
    ?>



    <label>Full Name:</label>
    <input class="input_signup" type="text" name="FN">
    <label>Date of Birth:</label>
    <input class="input_signup" type="date" name="age">
    <label>Email:</label>
    <input class="input_signup" type="email" name="mail">
    <label>Password:</label>
    <input class="input_signup" type="password" name="pwd">
    <label>Confirm Password:</label>
    <input class="input_signup" type="password" name="pwd-repeat">
    <label>Phone Number:</label>
    <input class="input_signup" type="tel" name="tel">
    <label>Address:</label>
    <input class="input_signup" type="text" name="address">
    <label>Allergies:</label>
    <table class="allergies-table">
      <tr>
        <td>
          <label2>No Allergies</label2><input type="checkbox" name="allergies[]" value="No Allergies">
        </td>
        <td>
          <label2>Celery</label><input type="checkbox" name="allergies[]" value="Celery">
        </td>
        <td>
          <label2>Gluten</label><input type="checkbox" name="allergies[]" value="Gluten">
        </td>
      </tr>
      <tr>
        <td>
          <label2>Eggs</label><input type="checkbox" name="allergies[]" value="Eggs">
        </td>
        <td>
          <label2>Fish</label><input type="checkbox" name="allergies[]" value="Fish">
        </td>
        <td>
          <label2>Lupin</label><input type="checkbox" name="allergies[]" value="Lupin">
        </td>
      </tr>
      <tr>
        <td>
          <label2>Milk</label><input type="checkbox" name="allergies[]" value="Milk">
        </td>
        <td>
          <label2>Molluscs</label><input type="checkbox" name="allergies[]" value="Molluscs">
        </td>
        <td>
          <label2>Mustard</label><input type="checkbox" name="allergies[]" value="Mustard">
        </td>
      </tr>
      <tr>
        <td>
          <label2>Peanuts</label><input type="checkbox" name="allergies[]" value="Peanuts">
        </td>
        <td>
          <label2>Sesame</label><input type="checkbox" name="allergies[]" value="Sesame">
        </td>
        <td>
          <label2>Soybeans</label><input type="checkbox" name="allergies[]" value="Soybeans">
        </td>
      </tr>
      <tr>
        <td>
          <label2>Sulfites</label><input type="checkbox" name="allergies[]" value="Sulfites">
        </td>
        <td>
          <label2>Sulphites</label><input type="checkbox" name="allergies[]" value="Sulphites">
        </td>
        <td>
          <label2>Tree Nuts</label><input type="checkbox" name="allergies[]" value="Tree nuts">
        </td>
      </tr>
    </table>

    <button class="form_button" type="submit" name="signup-submit">Signup</button>
  </form>
</body>

</html>