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

date_default_timezone_set('Europe/London');

$tableNumber = $_GET['table_number'];

$current_date = date('Y-m-d');
$sql = "SELECT * FROM reservations WHERE table_number = '$tableNumber' AND reservation_date = '$current_date'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <title>Reservations for Table <?php echo $tableNumber; ?></title>
  <h1>Reservations for Table <?php echo $tableNumber; ?> for The Date <?php echo $current_date; ?></h1>
  <style>
    body {
      text-align: center;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      max-width: 1200px;
      margin: 20px auto;
      font-family: Arial, sans-serif;
      background-color: #f8f8f8;
    }

    th,
    td {
      padding: 10px;
      text-align: center;
      border: 1px solid #ddd;
    }

    th {
      background-color: black;
      color: #fff;
      text-transform: uppercase;
    }

    td:nth-child(odd) {
      background-color: #fff;
    }

    td:nth-child(even) {
      background-color: #f2f2f2;
    }

    .customer-name {
      font-weight: bold;
    }

    .status {
      color: #008cba;
    }

    .num-guests {
      font-size: 1.2em;
      font-weight: bold;
    }

    .customer-guest {
      font-style: italic;
    }

    .error-message {
      background-color: #ff8080;
      color: #fff;
      padding: 10px;
      margin-bottom: 10px;
      font-weight: bold;

    }

    .success-message {
      background-color: #80ff80;
      color: #000;
      padding: 10px;
      margin-bottom: 10px;
      font-weight: bold;

    }
  </style>

</header>

<body>
  <?php

  // Get current date
  $currentDate = date('Y-m-d');

  // Check if there are any reservations with status arrived for the current date and table number
  $sql = "SELECT * FROM reservations WHERE table_number = '$tableNumber' AND reservation_date = '$currentDate' AND status = 'arrived'";
  $result = $conn->query($sql);

  // If there is only one row with status arrived, redirect to the employerMenu page for that reservation
  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $reservationId = $row['reservation_id'];
    $customerId = $row['customer_id'];
    $guestId = $row['guest_id'];

    $sql_table = "SELECT verification_code FROM restaurant_tables WHERE table_number = '$tableNumber'";
    $result_table = $conn->query($sql_table);
    $row_table = $result_table->fetch_assoc();
    $verificationCode = $row_table['verification_code'];

    header("Location: employerMenu.php?table_number=$tableNumber&reservation_id=$reservationId&guest_id=$guestId&customer_id=$customerId&verification_code=$verificationCode");
    exit;
  }

  // If there are two or more rows with status arrived, show a table with all reservations for the current date and table number with status arrived
  if ($result->num_rows >= 2) {
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Reservation ID</th>';
    echo '<th>Table Number</th>';
    echo '<th>Reservation Time</th>';
    echo '<th>Status</th>';
    echo '<th>Number Of Guests</th>';
    echo '<th>Customer/Guest</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    while ($row = $result->fetch_assoc()) {
      echo '<tr>';
      echo '<td class="customer-name">' . $row['reservation_id'] . '</td>';
      echo '<td>' . $row['table_number'] . '</td>';
      echo '<td>' . $row['reservation_time'] . '</td>';
      echo '<td class="status">' . $row['status'] . '</td>';
      echo '<td class="num-guests">' . $row['num_guests'] . '</td>';
      echo '<td class="customer-guest">';
      if ($row['customer_id']) {
        $customer_id = $row['customer_id'];
        $sql_customer = "SELECT full_name FROM customers WHERE customer_id = '$customer_id'";
        $result_customer = $conn->query($sql_customer);
        $row_customer = $result_customer->fetch_assoc();
        echo $row_customer['full_name'] . " (Customer Name)";
      } elseif ($row['guest_id']) {
        $guest_id = $row['guest_id'];
        $sql_guest = "SELECT verification_number FROM guests WHERE guest_id = '$guest_id'";
        $result_guest = $conn->query($sql_guest);
        $row_guest = $result_guest->fetch_assoc();
        echo $row_guest['verification_number'] . " (Guest Reference)";
      }
      echo '</td>';
      echo '<form action="reservationUpdate.php?reservation_id=' . $row['reservation_id'] . '&table_number=' . $row['table_number'] . '" method="post">';
      echo '<td><input type="submit" name="reservation_update" value="Update" style="display: inline;"></td>';
      echo '</form>';

      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
  } else {

    $sql = "SELECT * FROM reservations WHERE table_number = '$tableNumber' AND reservation_date = '$currentDate' AND status = 'Pending'";
    $result = $conn->query($sql);
    if (isset($_POST['reservation_id']) && isset($_POST['status'])) {
      $reservation_id = $_POST['reservation_id'];
      $status = $_POST['status'];

      $update_sql = "UPDATE reservations SET status='$status'";
      if ($status === 'Arrived') {
        $current_date_time = date('Y-m-d H:i:s');
        $update_sql .= ", reservation_date='$currentDate', reservation_time='$current_date_time'";
      }
      $update_sql .= " WHERE reservation_id='$reservation_id'";
      if ($conn->query($update_sql) === TRUE) {
        echo '<div class="success-message">Status updated successfully.</div>';
        $reservation_id = $_GET['reservation_id'];
        $tableNumber = $_GET['table_number'];
        header("Location: floorpLan.php");
        exit();
      } else {
        echo '<div class="error-message">Error updating order status: ' . $conn->error . '</div>';
      }
    }
  ?>


  <?php
    if ($result->num_rows > 0) {
      echo '<table>';
      echo '<thead>';
      echo '<tr>';
      echo '<th>Reservation ID</th>';
      echo '<th>Table Number</th>';
      echo '<th>Reservation Time</th>';
      echo '<th>Status</th>';
      echo '<th>Number Of Guests</th>';
      echo '<th>Customer/Guest</th>';
      echo '<th>Actions</th>';
      echo '</tr>';
      echo '</thead>';
      echo '<tbody>';
      while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td class="customer-name">' . $row['reservation_id'] . '</td>';
        echo '<td>' . $row['table_number'] . '</td>';
        echo '<td>' . $row['reservation_time'] . '</td>';
        echo '<td class="status">' . $row['status'] . '</td>';
        echo '<td class="num-guests">' . $row['num_guests'] . '</td>';
        echo '<td class="customer-guest">';
        if ($row['customer_id']) {
          $customer_id = $row['customer_id'];
          $sql_customer = "SELECT full_name FROM customers WHERE customer_id = '$customer_id'";
          $result_customer = $conn->query($sql_customer);
          $row_customer = $result_customer->fetch_assoc();
          echo $row_customer['full_name'] . " (Customer Name)";
        } elseif ($row['guest_id']) {
          $guest_id = $row['guest_id'];
          $sql_guest = "SELECT verification_number FROM guests WHERE guest_id = '$guest_id'";
          $result_guest = $conn->query($sql_guest);
          $row_guest = $result_guest->fetch_assoc();
          echo $row_guest['verification_number'] . " (Guest Reference)";
        }
        echo '</td>';
        echo "<td><form method='post' onsubmit='return confirm(\"Are you sure you want to update the reservation status?\");' action='" . $_SERVER['PHP_SELF'] . "'>";
        echo "<input type='hidden' name='reservation_id' value='" . $row["reservation_id"] . "'/>";
        echo "<select name='status' onchange='if(confirm(\"Are you sure you want to update the reservation status?\")) { this.form.submit(); }'>";
        echo "<option value='Pending' " . ($row["status"] == 'Pending' ? 'selected' : '') . ">Pending</option>";
        echo "<option value='Arrived' " . ($row["status"] == 'Arrived' ? 'selected' : '') . ">Arrived</option>";
        echo "<option value='Completed' " . ($row["status"] == 'Completed' ? 'selected' : '') . ">Completed</option>";
        echo "<option value='Cancelled' " . ($row["status"] == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>";
        echo "</select>";
        echo "</form></td>";
        echo '</tr>';
      }

      echo '</tbody>';
      echo '</table>';
    } else {
      echo '<div class="error-message">There are no reservations for the current date and table number with status arrived or pending.</div>';
    }
  }

  ?>
  <form action="MenuCreateReservations.php?table_number=<?php echo $tableNumber; ?>" method="POST">
    <input type="hidden" name="table_number" value="<?php echo $tableNumber; ?>">
    <input class="signup_button" type="submit" name="customer_confirm" value="Create Reservation" style="display: inline;">
  </form>
  <form action="floorpLan.php">
    <input class="signup_button" type="submit" name="table_number" value="Cancel" style="display: inline;">
  </form>
</body>

</html>