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
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Guest Signup</h1>
  <style>
    body {
      background-color: #f5f5f5;
      font-family: Arial, sans-serif;
    }

    .title {
      color: white;
      font-size: 36px;
      font-weight: bold;
      margin-top: 50px;
      text-align: center;
    }

    .subtitle {
      color: white;
      font-size: 24px;
      font-weight: bolder;
      margin-bottom: 5px;
      text-align: center;
    }

    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 60vh;
    }

    .button {

      background-color: white;
      border: none;
      color: black;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      font-size: 16px;
      cursor: pointer;
      outline: none;
      font-weight: bolder;
      border-radius: 5px;
    }

    .buttons {
      display: flex;
    }

    .buttons form {
      margin: 20px 10px;
    }

    .button:hover {
      background-color: grey;
      color: white;
    }
  </style>

</header>

<body>
  <div class="container">
    <?php
    // Get the guest's name and verification number from the URL
    $guest_name = isset($_GET['name']) ? $_GET['name'] : '';
    $verification_number = isset($_GET['verification_number']) ? $_GET['verification_number'] : '';

    // Display a thank you message with the guest's name and verification number
    echo "<h1 class='title'>Thank you, $guest_name!</h1>";
    echo "<h2 class='subtitle'>Your reference number is: $verification_number</h2>";
    echo "<h2 class='subtitle'>Enjoy your meals!</h2>";
    ?>
    <div class="buttons">
      <form action="customer_menu.php">
        <input type="submit" value="Go to menu" class="button">
      </form>
    </div>
  </div>
</body>

</html>