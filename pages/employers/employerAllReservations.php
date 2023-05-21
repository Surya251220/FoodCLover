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


$search = isset($_GET['search']) ? $_GET['search'] : '';
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'full_name';
$reset = isset($_GET['reset']) ? true : false;
if ($reset) {
  $search = '';
}

?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">

  <h1>All Reservations</h1>
</header>

<body>
  <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <select name="criteria">
      <option value="reservation_id" <?php if ($criteria === 'reservation_id') {
                                        echo ' selected';
                                      } ?>>Reservation ID</option>
      <option value="full_name" <?php if ($criteria === 'full_name') {
                                  echo ' selected';
                                } ?>>Customer Name</option>
      <option value="verification_number" <?php if ($criteria === 'verification_number') {
                                            echo ' selected';
                                          } ?>>Guest Reference</option>
      <option value="name" <?php if ($criteria === 'name') {
                              echo ' selected';
                            } ?>>Guest Name</option>
      <option value="table_number" <?php if ($criteria === 'table_number') {
                                      echo ' selected';
                                    } ?>>Table Number</option>
      <option value="reservation_date" <?php if ($criteria === 'reservation_date') {
                                          echo ' selected';
                                        } ?>>Date (Year-Month-Day)</option>
      <option value="status" <?php if ($criteria === 'status') {
                                echo ' selected';
                              } ?>>Status</option>

    </select>
    <?php if (!empty($search)) { ?>
      <button type="submit" name="reset" value="1">Reset</button>
    <?php } else { ?>
      <button type="submit">Submit</button>
    <?php } ?>
    <input type="hidden" name="page" value="<?php echo $page; ?>">
  </form>
  <table>
    <tr>
      <th>Reservation ID</th>
      <th>Customer ID</th>
      <th>Customer Name</th>
      <th>Guest Reference</th>
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
    $search_term = mysqli_real_escape_string($conn, $search);

    if (!empty($search)) {
      $sql = "SELECT c.full_name, c.customer_id, r.reservation_id, r.reservation_date, r.reservation_time ,r.table_number, r.status, g.guest_id, g.name, g.verification_number
            FROM reservations r
            LEFT JOIN customers c ON r.customer_id = c.customer_id
            LEFT JOIN guests g ON r.guest_id = g.guest_id
            WHERE " . $criteria . " LIKE '%" . $search . "%'";
    } else {
      $sql = "SELECT reservations.*, customers.full_name, guests.name, guests.verification_number
        FROM reservations 
        LEFT JOIN customers ON reservations.customer_id = customers.customer_id 
        LEFT JOIN guests ON reservations.guest_id = guests.guest_id 
        ORDER BY reservation_date DESC";
    }


    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td>" . $row['reservation_id'] . "</td>";
      echo "<td>" . ($row['customer_id'] ? $row['customer_id'] : 'NaN') . "</td>";
      echo "<td>" . ($row['full_name'] ? $row['full_name'] : 'NaN') . "</td>";
      echo "<td>" . ($row['verification_number'] ? $row['verification_number'] : 'NaN') . "</td>";
      echo "<td>" . ($row['name'] ? $row['name'] : 'NaN') . "</td>";

      echo "<td>" . $row['table_number'] . "</td>";
      echo "<td>" . $row['reservation_date'] . "</td>";
      echo "<td>" . $row['reservation_time'] . "</td>";

      echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to update the reservations status?\");' action='" . $_SERVER['PHP_SELF'], "'>";
      echo "<input type='hidden' name='reservation_id' value='" . $row["reservation_id"] . "'/>";
      echo "<td><select name='status' onchange='if(confirm(\"Are you sure you want to update the reservation status?\")) { this.form.submit(); }'>";
      echo "<option value='Pending' " . ($row["status"] == 'Pending' ? 'selected' : '') . ">Pending</option>";
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