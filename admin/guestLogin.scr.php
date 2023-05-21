<?php
include_once 'C:\xampp\htdocs\RestaurantManagementSystem\lib\config.php';
session_start();

if (isset($_POST['login-submit'])) {
  $verification_number = $_POST['VN'];
  $table_number = $_POST['table_number'];
  $pin_code = $_POST['PIN'];

  if (empty($verification_number) || empty($table_number) || empty($pin_code)) {
    header("Location: ../pages/customers/guestLogin.php?error=emptyfields");
    exit();
  }

  $sql = "SELECT verification_number, email, guest_id, name, phone_number, usertype FROM guests WHERE verification_number=? AND account_status=0";
  $stmt = mysqli_stmt_init($conn);

  if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: ../pages/customers/customer_menu.php?error=sqlerror");
    exit();
  }

  mysqli_stmt_bind_param($stmt, "s", $verification_number);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
    $guest_verification_number = $row['verification_number'];
    $email = $row['email'];
    $guest_id = $row['guest_id'];
    $name = $row['name'];
    $phone_number = $row['phone_number'];
    $usertype = $row['usertype'];

    $sql = "SELECT * FROM restaurant_tables WHERE table_number=? AND is_available=1";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
      header("Location: ../pages/customers/guestLogin.php?error=sqlerror");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $table_number);
    mysqli_stmt_execute($stmt);
    $result2 = mysqli_stmt_get_result($stmt);

    if ($row2 = mysqli_fetch_assoc($result2)) {
      if ($row2['verification_code'] == $pin_code) {
        // Start session and set session variables
        $_SESSION['verification_number'] = $guest_verification_number;
        $_SESSION['email'] = $email;
        $_SESSION['guest_id'] = $guest_id;
        $_SESSION['name'] = $name;
        $_SESSION['phone_number'] = $phone_number;
        $_SESSION['usertype'] = $usertype;
        $_SESSION['table_number'] = $table_number;
        $_SESSION['verification_code'] = $pin_code;

        // Insert data into the reservation table
        $reservation_date = date("Y-m-d");
        $reservation_time = date("H:i:s");

        $query = "INSERT INTO reservations (guest_id, table_number, reservation_date, reservation_time, status) 
          VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        $status = "Arrived"; 
        if (!mysqli_stmt_prepare($stmt, $query)) {
          header("Location: ../pages/customers/guestLogin.php?error=sqlerror");
          exit();
        }

        mysqli_stmt_bind_param($stmt, "iisss", $_SESSION['guest_id'], $_SESSION['table_number'], $reservation_date, $reservation_time, $status);
        mysqli_stmt_execute($stmt);

        header("Location: ../pages/customers/customer_menu.php?login=success&guest_id=$guest_id&table_number=$table_number&verification_code=$pin_code");
        exit();
      } else {
        // Incorrect pin code
        header("Location: ../pages/customers/guestLogin.php?error=incorrectpin");
        exit();
      }
    } else {
      // Invalid table number or table is not available
      header("Location: ../pages/customers/guestLogin.php?error=invalidtable");
      exit();
    }
  } else {
    // Invalid verification number or account is not active
    header("Location: ../pages/customers/guestLogin.php?error=invalidguest");
    exit();
  }
} else {
  // Redirect back to the login page
  header("Location: ../pages/customers/guestLogin.php");
  exit();
}
