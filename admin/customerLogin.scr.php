<?php
include_once '../lib/config.php';
session_start();

// Redirect if the user is already logged in
if (isset($_SESSION['email']) && isset($_SESSION['usertype'])) {
  header("Location: ../pages/customers/customer_menu.php");
  exit();
}

if (isset($_POST['login-submit'])) {
  $mail = $_POST['mail'];
  $password = $_POST['pwd'];

  if (empty($mail) || empty($password)) {
    header("Location: ../pages/customers/customerLogin.php?error=emptyfields");
    exit();
  } else {
    $sql = "SELECT * FROM customers WHERE email=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
      header("Location: ../pages/customers/customerLogin.php?error=sqlerror");
      exit();
    } else {
      $stmt->bind_param("s", $mail);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($row = $result->fetch_assoc()) {
        $pwdCheck = password_verify($password, $row['pwd']);

        if ($pwdCheck === false) {
          header("Location: ../pages/customers/customerLogin.php?error=wrongpassword");
          exit();
        } else if ($pwdCheck === true) {
          $_SESSION['email'] = $row['email'];

          header("Location: ../pages/customers/customersTwoStep.php");
          exit();
        } else {
          header("Location: ../pages/customers/customerLogin.php?error=wrongpassword");
          exit();
        }
      } else {
        header("Location: ../pages/customers/customerLogin.php?error=nouser");
        exit();
      }
    }
  }
} else {
  // Clear login-related session data and log out the user
  unset($_SESSION['email']);
  unset($_SESSION['customer_id']);
  unset($_SESSION['full_name']);
  unset($_SESSION['usertype']);
}
