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

?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">

  <h1>Today's Reservations</h1>
</header>

<body>
  <div class="button-container" style="margin-top: 50px;">
    <form action="employerAllReservations.php">
      <input class="signup_button" type="submit" value="All Reservations" style="display: inline;">
    </form>
    <form action="employer_reservations_create.php">
      <input class="signup_button" type="submit" value="Create Reservation" style="display: inline;">
    </form>

  </div>

  <table>
    <tr>
      <th>Reservation ID</th>
      <th>Customer ID</th>
      <th>Customer Name</th>
      <th>Guest ID</th>
      <th>Guest Name</th>
      <th>Table Number</th>
      <th>Date</th>
      <th>Time</th>
      <th>Status</th>
    </tr>
    <?php
    if (isset($_POST['reservation_id']) && isset($_POST['status'])) {
      $reservation_id = $_POST['reservation_id'];
      $status = $_POST['status'];

      $update_sql = "UPDATE reservations SET status='$status' WHERE reservation_id='$reservation_id'";
      if ($conn->query($update_sql) === TRUE) {
        echo "Status updated successfully.";
        $reservation_id = $_GET['reservation_id'];
        $table_number = $_GET['table_number'];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
      } else {
        echo "Error updating order status: " . $conn->error;
      }
    }
    $date = date('Y-m-d');
    $sql = "SELECT reservations.*, customers.full_name, guests.name 
              FROM reservations 
              LEFT JOIN customers ON reservations.customer_id = customers.customer_id 
              LEFT JOIN guests ON reservations.guest_id = guests.guest_id 
              WHERE reservation_date = '$date' 
              ORDER BY reservation_date DESC";



    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td>" . $row['reservation_id'] . "</td>";
      echo "<td>" . ($row['customer_id'] ? $row['customer_id'] : 'NaN') . "</td>";
      echo "<td>" . ($row['full_name'] ? $row['full_name'] : 'NaN') . "</td>";
      echo "<td>" . ($row['guest_id'] ? $row['guest_id'] : 'NaN') . "</td>";
      echo "<td>" . ($row['name'] ? $row['name'] : 'NaN') . "</td>";

      echo "<td>" . $row['table_number'] . "</td>";
      echo "<td>" . $row['reservation_date'] . "</td>";
      echo "<td>" . $row['reservation_time'] . "</td>";

      echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to update the reservations status?\");' action='" . $_SERVER['PHP_SELF'], "'>";
      echo "<input type='hidden' name='reservation_id' value='" . $row["reservation_id"] . "'/>";
      echo "<td><select name='status' onchange='if(confirm(\"Are you sure you want to update the reservation status?\")) { this.form.submit(); }'>";
      echo "<option value='Pending' " . ($row["status"] == 'Pending' ? 'selected' : '') . ">Pending</option>";
      echo "<option value='Arrived' " . ($row["status"] == 'Arrived' ? 'selected' : '') . ">Arrived</option>";
      echo "<option value='Completed' " . ($row["status"] == 'Completed' ? 'selected' : '') . ">Completed</option>";
      echo "<option value='Cancelled' " . ($row["status"] == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>";
      echo "</select>";
      echo "</form>";


      echo "</tr>";
    }

    ?>

  </table>
</body>

</html>