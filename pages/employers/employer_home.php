<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

if (isset($_SESSION['email']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'employer') {
  // Employer is logged in, give access to $_SERVER['PHP_SELF']
  // Your code here to grant access to $_SERVER['PHP_SELF']

} else if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'guest') {
  // Guest is logged in, redirect to customerLogin.php
  header("Location: ../../pages/customers/customerLogin.php");
  exit();
} else if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'customer') {
  // Customer is logged in, redirect to customerLogin.php
  header("Location: ../../pages/customers/customerLogin.php");
  exit();
} else {
  // No one is logged in, redirect to customerLogin.php
  header("Location: ../../pages/customers/customerLogin.php");
  exit();
}

$showAll = false;
$showStaff = false;
$showInventory = false;
$showDelivery = false;
$showMenu = false;
if (isset($_SESSION['role'])) {
  if ($_SESSION['role'] == 'admin+') {
    $showAll = true;
    $showStaff = true;
    $showInventory = true;
    $showDelivery = true;
    $showMenu = true;
  } else if ($_SESSION['role'] == 'admin') {
    $showAll = true;
    $showStaff = false;
    $showInventory = true;
    $showDelivery = true;
    $showMenu = true;
  } else if ($_SESSION['role'] == 'manager') {
    $showAll = true;
    $showStaff = false;
    $showInventory = true;
    $showDelivery = true;
    $showMenu = true;
  } else if ($_SESSION['role'] == 'staff') {
    $showAll = true;
    $showStaff = false;
    $showInventory = false;
    $showDelivery = true;
    $showMenu = false;
  } else {
    $showAll = false;
    $showStaff = false;
    $showInventory = false;
    $showMenu = false;
  }
} else {
  echo "Role not set";
}
?>

<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Dashboard</h1>
</header>

<body>
  <div class="dashboard_container">
    <?php if ($showAll || $showStaff) : ?>
      <div class="box">
        <h2 style="color:black">Take Orders</h2>
        <a href="floorPlan.php">View Details</a>
      </div>
      <div class="box">
        <h2 style="color:black">Orders</h2>
        <a href="takeOrders.php">View Details</a>
      </div>
      <div class="box">
        <h2 style="color:black">Reservations</h2>
        <a href="employer_reservations.php">View Details</a>
      </div>
      <?php if ($showDelivery) : ?> <!-- Add a condition to show delivery management for staff -->
        <div class="box">
          <h2 style="color:black">Delivery Management</h2>
          <a href="deliveryUpdate.php">View Details</a>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($showAll && ($showInventory || $showStaff)) : ?>
      <div class="box">
        <h2 style="color:black">Inventory Items</h2>
        <a href="employer_inventory_items.php">View Details</a>
      </div>
      <div class="box">
        <h2 style="color:black">Suppliers Management</h2>
        <a href="employerSupplier.php">View Details</a>
      </div>
      <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'admin+') : ?>
        <div class="box">
          <h2 style="color:black">Customer Management</h2>
          <a href="employerCustomer.php">View Details</a>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($showAll && $showStaff && $showMenu) : ?>
      <div class="box">
        <h2 style="color:black">Staff Management</h2>
        <a href="employerSignup.php">View Details</a>
      </div>
    <?php endif; ?>

    <?php if ($showMenu) : ?> <!-- Add a condition to show delivery management for staff -->
      <div class="box">
        <h2 style="color:black">System Management</h2>
        <a href="menuManagement.php">View Details</a>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>