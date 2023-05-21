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

if (isset($_POST['update'])) {

  $reservation_id = trim($_POST['reservation_id']);
  $customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : null;
  $guest_id = isset($_POST['guest_id']) ? $_POST['guest_id'] : null;
  $reservation_time = $_POST['reservation_time'];
  $table_number = $_POST['table_number'];
  $status = $_POST['status'];
  $sql = "UPDATE reservations SET reservation_time='$reservation_time', table_number='$table_number', status='$status' WHERE reservation_id=$reservation_id";
  if (mysqli_query($conn, $sql)) {
    echo "Record updated successfully.";
    header("Location: floorPlan.php");
    exit();
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
}

$reservation_id = trim($_GET['reservation_id']);
$table_number = trim($_GET['table_number']);
$sql = "SELECT * FROM reservations WHERE reservation_id=$reservation_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Update Reservation</h1>
</header>

<body>
  <form class="employerUpdate" action="reservationUpdate.php" method="POST">
    <h2>Update Reservation Form</h2>
    <label>Reservation ID:</label>
    <input type="number" name="reservation_id" value="<?php echo $row['reservation_id']; ?>" readonly>
    <label>Customer Name:</label>
    <?php
    if ($row['customer_id']) {
      $customer_id = $row['customer_id'];
      $sql = "SELECT full_name FROM customers WHERE customer_id=$customer_id";
      $result = mysqli_query($conn, $sql);
      $customer = mysqli_fetch_assoc($result);
      echo $customer['full_name'];
    } else {
      $guest_id = $row['guest_id'];
      $sql = "SELECT verification_number FROM guests WHERE guest_id=$guest_id";
      $result = mysqli_query($conn, $sql);
      $guest = mysqli_fetch_assoc($result);
      echo $guest['verification_number'] . " (Guest Reference)";
    }
    ?><br>

    <label>Reservation Time:</label>
    <input type="time" name="reservation_time" value="<?php echo $row['reservation_time']; ?>"><br>
    <label>Table Number:</label>
    <?php
    $tables_sql = "SELECT table_number FROM restaurant_tables";
    $tables_result = mysqli_query($conn, $tables_sql);
    $table_numbers = array();
    while ($table = mysqli_fetch_assoc($tables_result)) {
      $table_numbers[] = $table['table_number'];
    }
    ?>

    <select name="table_number">
      <?php foreach ($table_numbers as $table_number) : ?>
        <option value="<?php echo $table_number; ?>" <?php if ($row['table_number'] == $table_number) echo 'selected'; ?>><?php echo $table_number; ?></option>
      <?php endforeach; ?>
    </select>
    <label>Status:</label>
    <select name="status">
      <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
      <option value="Arrived" <?php if ($row['status'] == 'Arrived') echo 'selected'; ?>>Arrived</option>
      <option value="Completed" <?php if ($row['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
      <option value="Cancelled" <?php if ($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
    </select><br>
    <input type="submit" name="update" value="Update">
  </form>
</body>

</html>