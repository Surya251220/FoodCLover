<?php
include_once 'C:\xampp\htdocs\RestaurantManagementSystem\lib\config.php';
session_start();

if (isset($_SESSION['email']) && isset($_SESSION['usertype'])) {
  header("Location: ../pages/customers/customer_menu.php");
  exit();
}

if (isset($_POST['login-submit'])) {
  $email = $_POST['mail'];
  $password = $_POST['pwd'];

  if (empty($email) || empty($password)) {
    header("Location: ../pages/employers/employerLogin.php?error=emptyfields");
    exit();
  } else {
    $sql = "SELECT * FROM employers WHERE email=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      header("Location: ../pages/employers/employerLogin.php?error=sqlerror");
      exit();
    } else {
      mysqli_stmt_bind_param($stmt, "s", $email);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if ($row = mysqli_fetch_assoc($result)) {
        $pwdCheck = password_verify($password, $row['pwd']);
        if ($pwdCheck == false) {
          header("Location: ../pages/employers/employerLogin.php?error=wrongpassword");
          exit();
        } else if ($pwdCheck == true) {
          $_SESSION['email'] = $row['email'];
          header("Location: ../pages/employers/employersTwoStep.php?error=success");
          exit();
        } else {
          header("Location: ../pages/employers/employerLogin.php?error=wrongpassword");
          exit();
        }
      } else {
        header("Location: ../pages/employers/employerLogin.php?error=nouser");
        exit();
      }
    }
  }
} else {
  header("Location: ../pages/customers/customer_menu.php");
  exit();
}
