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
  <h1>Pending Orders</h1>

  <style>
    /* CSS for the buttons */
    button[type="submit"] {
      background-color: black;
      border: none;
      color: white;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 5px;
      cursor: pointer;
      width: 150px;
    }

    button[type="submit"]:hover {
      background-color: white;
      color: black;
    }

    button[type="submit"]:active {
      background-color: #3e8e41;
    }
  </style>

</header>

<body>
  <div class="button-container" style="margin-top: 50px;">
    <form action="employers_total_orders.php">
      <input class="signup_button" type="submit" value="All Reservations" style="display: inline;">
    </form>

  </div>
  <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <select name="criteria">
      <option value="full_name" <?php if ($criteria === 'full_name') {
                                  echo ' selected';
                                } ?>>Customer Name</option>
      <option value="name" <?php if ($criteria === 'name') {
                              echo ' selected';
                            } ?>>Guest Name</option>
      <option value="verification_number" <?php if ($criteria === 'verification_number') {
                                            echo ' selected';
                                          } ?>>verification Number</option>
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
  if (!empty($search)) {
    $sql = "SELECT c.full_name, o.order_id, o.customer_id o.reservation_id, o.table_number, r.status, o.created_at, o.updated_at, o.guest_id, g.name, g.verification_number
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            RIGHT JOIN reservations r ON r.reservation_id = o.reservation_id
            LEFT JOIN guests g ON o.guest_id = g.guest_id
            WHERE c.full_name LIKE '%$search%' OR r.status LIKE '%$search%' OR g.verification_number LIKE '%$search%'OR g.name LIKE '%$search%'
            GROUP BY o.table_number, o.reservation_id";
  } else {
    $sql = "SELECT c.full_name, o.order_id, o.customer_id, o.reservation_id, o.table_number, r.status, o.created_at, o.updated_at, o.guest_id, g.name, g.verification_number
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            JOIN reservations r ON r.reservation_id = o.reservation_id
            LEFT JOIN guests g on o.guest_id = g.guest_id
            WHERE r.status = 'Arrived'
            GROUP BY o.table_number, o.reservation_id";
  }


  $result = mysqli_query($conn, $sql);


  // Display the orders in an HTML table
  echo "<table>";
  echo "<tr><th>Table Number</th><th>Guest Name</th><th>Guest Reference Number</th><th>Customer Name</th><th>Reservation ID</th><th>Reservation Status</th><th>Created At</th><th>Updated At</th><th>Actions</th></tr>";
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row["table_number"] . "</td>";
    echo "<td>" . ($row['name'] ? $row['name'] : 'NaN') . "</td>";
    echo "<td>" . ($row['verification_number'] ? $row['verification_number'] : 'NaN') . "</td>";
    echo "<td>" . ($row['full_name'] ? $row['full_name'] : 'NaN') . "</td>";;
    echo "<td>" . $row["reservation_id"] . "</td>";




    echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to update the delivery status?\");' action='" . $_SERVER['PHP_SELF'], "'>";
    echo "<input type='hidden' name='reservation_id' value='" . $row["reservation_id"] . "'/>";
    echo "<td><select name='status' onchange='if(confirm(\"Are you sure you want to update the delivery status?\")) { this.form.submit(); }'>";
    echo "<option value='Pending' " . ($row["status"] == 'Pending' ? 'selected' : '') . ">Pending</option>";
    echo "<option value='Arrived' " . ($row["status"] == 'Arrived' ? 'selected' : '') . ">Arrived</option>";
    echo "<option value='Completed' " . ($row["status"] == 'Completed' ? 'selected' : '') . ">Completed</option>";
    echo "<option value='Cancelled' " . ($row["status"] == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>";
    echo "</select>";
    echo "</form>";

    echo "<td>" . $row["created_at"] . "</td>";
    echo "<td>" . $row["updated_at"] . "</td>";
    echo '<td>';
    if (isset($row["reservation_id"]) && isset($row["table_number"]) && isset($row["full_name"]) || isset($row["name"])) {
      echo '<form method="get" action="singleOrder.php">
                    <input type="hidden" name="reservation_id" value="' . $row["reservation_id"] . '">
                    <input type="hidden" name="table_number" value="' . $row["table_number"] . '">
                    <input type="hidden" name="customer_id" value="' . $row["full_name"] . '">
                    <input type="hidden" name="guest_id" value="' . $row["name"] . '">
                    <button type="submit">See more details</button>
                  </form>';
      echo '<form method="get" action="employerMenu.php">
                    <input type="hidden" name="reservation_id" value="' . $row["reservation_id"] . '">
                    <input type="hidden" name="table_number" value="' . $row["table_number"] . '">
                    <input type="hidden" name="customer_id" value="' . $row["full_name"] . '">
                    <input type="hidden" name="guest_id" value="' . $row["name"] . '">
                    <button type="submit">Take orders</button>
                  </form>';


      echo '<form method="get" action="customerReceipts.php">';
      echo '<input type="hidden" name="reservation_id" value="' . $row["reservation_id"] . '">';
      echo '<input type="hidden" name="table_number" value="' . $row["table_number"] . '">';
      if (!empty($row["full_name"])) {
        echo '<input type="hidden" name="full_name" value="' . $row["full_name"] . '">';
      } elseif (!empty($row["name"])) {
        echo '<input type="hidden" name="name" value="' . $row["name"] . '">';
      }
      echo '<button type="submit">Receipts</button>';
      echo '</form>';
    } else {
      echo '-';
    }

    echo '</td>';

    echo "</tr>";
  }
  echo "</table>";

  // Close the database connection
  mysqli_close($conn);
  ?>
</body>

</html>