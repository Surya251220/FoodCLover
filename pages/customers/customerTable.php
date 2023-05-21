<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';
if (isset($_SESSION['email']) && isset($_SESSION['usertype'])) {
  if ($_SESSION['usertype'] == 'employer') {
    // Employer is logged in, redirect to employerLogin.php
    header("Location: ../../pages/employers/employerLogin.php");
    exit();
  } else {
    // Customer or guest is logged in, give access to $_SERVER['PHP_SELF']
    // Your code here to grant access to $_SERVER['PHP_SELF']
  }
} else {
  // No one is logged in, redirect to employerLogin.php
  header("Location: ../../pages/customers/customerLogin.php");
  exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login-submit'])) {

  // Get the input values
  $table_number = $_POST['table_number'];
  $pincode = $_POST['PIN'];

  // Get the current date
  date_default_timezone_set("Europe/London");
  $reservation_date = date("Y-m-d");

  // Check if there is a pending reservation for today
  $sql = "SELECT * FROM reservations WHERE customer_id = {$_SESSION['customer_id']} AND reservation_date = '$reservation_date' AND status = 'pending'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Use the existing reservation id and update the table number
    $reservation = $result->fetch_assoc();
    $reservation_id = $reservation['reservation_id'];

    $sql = "UPDATE reservations r
    JOIN orders o ON r.reservation_id = o.reservation_id
    JOIN cart c ON r.reservation_id = c.reservation_id
    SET r.table_number = $table_number, o.table_number = $table_number, c.table_number = $table_number
    WHERE r.reservation_id = $reservation_id";
    if ($conn->query($sql) === TRUE) {
      // Set session variables for table number and verification code
      $_SESSION['table_number'] = $table_number;
      $_SESSION['verification_code'] = $pincode;

      // Redirect to customer_menu.php with the necessary parameters in the URL
      $url = "customer_menu.php?customer_id={$_SESSION['customer_id']}&table_number=$table_number&verification_code=$pincode";
      header("Location: $url");
      exit();
    } else {
      echo "Error updating reservation: " . $conn->error;
    }
  } else {
    // Create a new reservation
    $reservation_time = date("H:i:s");

    $sql = "INSERT INTO reservations (customer_id, table_number, reservation_date, reservation_time, status)
            VALUES ({$_SESSION['customer_id']}, $table_number, '$reservation_date', '$reservation_time', 'Arrived')";
    if ($conn->query($sql) === TRUE) {
      $reservation_id = $conn->insert_id;

      // Set session variables for table number and verification code
      $_SESSION['table_number'] = $table_number;
      $_SESSION['verification_code'] = $pincode;

      // Redirect to customer_menu.php with the necessary parameters in the URL
      $url = "customer_menu.php?customer_id={$_SESSION['customer_id']}&table_number=$table_number&verification_code=$pincode";
      header("Location: $url");
      exit();
    } else {
      echo "Error creating reservation: " . $conn->error;
    }
  }
}

?>


<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Table Verification</h1>
</header>

<body>
  <form class="form_login" action="" method="post">
    <input class="input_login" type="number" name="table_number" placeholder="Table Number">
    <input class="input_login" type="text" name="PIN" placeholder="Table Pincode">
    <button class="form_button" type="submit" name="login-submit">Verify</button>
  </form>
</body>

</html>