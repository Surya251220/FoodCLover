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
  <h1>Menu</h1>

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }

    h2 {
      margin-top: 50px;
      padding-top: 20px;
      border-top: 1px solid #ccc;
      text-transform: uppercase;
    }

    .dish-box {
      display: inline-block;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
      padding: 20px;
      background-color: #fff;
      box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    }

    .dish-name {
      font-size: 24px;
      font-weight: bold;
    }

    .dish-description {
      margin-top: 10px;
      font-size: 16px;
    }

    .dish-allergies {
      margin-top: 10px;
      font-size: 16px;
      font-style: italic;
      color: #666;
    }

    .dish-price {
      margin-top: 10px;
      font-size: 16px;
      font-weight: bold;
    }

    .input-quantity {
      width: 50px;
      height: 35px;

      padding: 0px 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      text-align: center;
      color: #666;
    }

    .dish-button {
      background-color: #00cc99;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .dish-button:hover {
      background-color: #00b386;
    }
  </style>
</header>

<body>
  <div class="center">
    <div class="btn-container">
      <?php
      // Retrieve reservation number and table number from the URL
      $reservation_id = $_GET['reservation_id'];
      $table_number = $_GET['table_number'];
      $customer_id = $_GET['customer_id'];
      $guest_id = $_GET['guest_id'];

      // Determine whether to use guest ID or customer ID
      if ($customer_id) {
        $id_type = 'customer_id';
        $id_value = $customer_id;
      } else {
        $id_type = 'guest_id';
        $id_value = $guest_id;
      }

      if (empty($reservation_id) && empty($customer_id) && empty($guest_id)) {
        // If reservation_id, customer_id, and guest_id are empty, redirect to the reservations page for this table
        header("Location: MenuCreateReservations.php?table_number=" . urlencode($table_number));
        exit;
      } else {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reservation_id"]) && isset($_POST["status"])) {
          $reservation_id = $_POST["reservation_id"];
          $status = $_POST["status"];

          // Prepare and execute the SQL statement to update the status
          $sql = "UPDATE reservations SET status = '$status' WHERE reservation_id = '$reservation_id'";
          if ($conn->query($sql) === TRUE) {
            // Status updated successfully
            header("Location: floorPlan.php");
            exit;
          } else {
            // Error updating status
            echo "Error updating status: " . $conn->error;
          }
        }


        // Retrieve the reservation data
        $sql = "SELECT reservations.reservation_id, reservations.table_number, customers.full_name, reservations.reservation_date, reservations.reservation_time, guests.verification_number, reservations.status 
FROM reservations 
LEFT JOIN customers ON reservations.customer_id = customers.customer_id 
LEFT JOIN guests ON reservations.guest_id = guests.guest_id 
WHERE reservations.table_number = '$table_number' AND reservations.status = 'Arrived'
ORDER BY reservations.reservation_date DESC";

        $result = $conn->query($sql);

        // Display the reservation data in a table
        // Display the reservation data in a table
        if ($result->num_rows > 0) {
          echo "<table>";
          echo "<tr><th>Reservation ID</th><th>Table Number</th><th>Customer Name</th><th>Reservation Date</th><th>Reservation Time</th><th>Status</th></tr>";

          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["reservation_id"] . "</td>";
            echo "<td>" . $row["table_number"] . "</td>";
            echo "<td>";
            if ($row["full_name"]) {
              echo $row["full_name"];
            } elseif ($row["verification_number"]) {
              echo $row["verification_number"] . " (Guests)";
            } else {
              echo "Unknown";
            }
            echo "</td>";
            echo "<td>" . $row["reservation_date"] . "</td>";
            echo "<td>" . $row["reservation_time"] . "</td>";

            // Add the form tag here
            echo "<td>";
            echo "<form method='POST'>";
            echo "<input type='hidden' name='reservation_id' value='" . $row["reservation_id"] . "'/>";
            echo "<select name='status' onchange='if(confirm(\"Are you sure you want to update the reservation status?\")) { this.form.submit(); }'>";
            echo "<option value='Arrived' " . ($row["status"] == 'Arrived' ? 'selected' : '') . ">Arrived</option>";
            echo "<option value='Completed' " . ($row["status"] == 'Completed' ? 'selected' : '') . ">Completed</option>";
            echo "</select>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
          }
          echo "</table>";
        } else {
          echo "No reservations found.";
        }
        // Otherwise, show the menu and nav
        $sql = "SELECT DISTINCT category FROM dishes";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          echo "<div class='navbar'>";
          echo "<ul>";
          while ($row = $result->fetch_assoc()) {
            $category = $row["category"];
            echo "<li><a href='#$category'>$category</a></li>";
          }
          echo "</ul>";

          echo "</div>";

          $reservation_id = $_GET['reservation_id'];
          $table_number = $_GET['table_number'];
          $customer_id = $_GET['customer_id'];
          $guest_id = $_GET['guest_id'];
          echo "<a href='employer_sum.php?reservation_id=" . $reservation_id . "&table_number=" . $table_number . "&customer_id=" . $customer_id . "&guest_id=" . $guest_id . "' class='btn btn-primary'>Go to Your Order</a>";
          echo "<a href='singleOrder.php?reservation_id=" . $reservation_id . "&table_number=" . $table_number . "&customer_id=" . $customer_id . "&guest_id=" . $guest_id . "' class='btn btn-primary'>See Confirmed Orders</a>";
        }

        echo "</div>";
        echo "</div>";
        $sql = "SELECT * FROM dishes ORDER BY category_order, category, dish_name";
        $result = $conn->query($sql);
        $current_category = null;
        if ($result->num_rows > 0) {
          echo "<form method='post'>";

          while ($row = $result->fetch_assoc()) {
            $category = $row["category"];
            $dish_name = $row["dish_name"];
            $allergies = $row["allergies"];
            $price = $row["price"];
            $image_path = $row["image_path"];

            // Output the section heading if the category changes
            if ($category != $current_category) {
              // Close the previous category's div, if any
              if ($current_category !== null) {
                echo "</div>";
              }

              // Output the new category heading
              echo "<h2 id='$category'>$category</h2>";

              // Update the current category
              $current_category = $category;
            }
            // Output the dish information and "Add to Order" button
            echo "<div class='dish-box'>";
            echo "<div class='dish-name'>$dish_name</div>";
            echo "<div class='dish-allergies'>Allergies: $allergies</div>";
            echo "<div class='dish-price'>Price: £ $price</div>";
            echo "<input type='number' name='quantity[$dish_name]' value='1' min='1' class='input-quantity'>";
            echo "<button type='submit' name='add_to_cart' value='$dish_name,$price' class='dish-button'>Add to Order</button>";
            echo "</div>";
          }
          echo "</form>";
        }

        if (isset($_POST['add_to_cart'])) {
          $dish_name = explode(",", $_POST['add_to_cart'])[0];
          $price = explode(",", $_POST['add_to_cart'])[1];
          $quantity = $_POST['quantity'][$dish_name];

          // Retrieve reservation number and table number from the URL
          $reservation_id = $_GET['reservation_id'];
          $table_number = $_GET['table_number'];
          $customer_id = $_GET['customer_id'];
          $guest_id = $_GET['guest_id'];

          // Determine whether to use guest ID or customer ID
          if ($customer_id) {
            $id_type = 'customer_id';
            $id_value = $customer_id;
          } else {
            $id_type = 'guest_id';
            $id_value = $guest_id;
          }

          // Check if the reservation is still pending and retrieve the ID
          $query = "SELECT $id_type FROM reservations 
        WHERE reservation_id = ? 
        AND status = 'Arrived' 
        AND DATE(reservation_date) = CURDATE()";

          $stmt = mysqli_prepare($conn, $query);
          mysqli_stmt_bind_param($stmt, "i", $reservation_id);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);

          if ($row = mysqli_fetch_assoc($result)) {
            $id_value = $row[$id_type];
          } else {
            // handle case where the reservation is not found or not pending
            $reservation_id = null;
            $table_number = null;
            $customer_id = null;
            $guest_id = null;
          }

          $dish_id = null;
          $notes = null;
          $status = 'unconfirmed';

          // Get the dish_id from the dishes table based on the dish_name
          $sql = "SELECT dish_id FROM dishes WHERE dish_name = ?";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "s", $dish_name);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);

          if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dish_id = $row['dish_id'];
          }

          // Insert data into the cart table
          $query = "INSERT INTO cart (reservation_id, table_number, $id_type, dish_id, quantity, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
          $stmt = mysqli_prepare($conn, $query);
          mysqli_stmt_bind_param($stmt, "iiisiss", $reservation_id, $table_number, $id_value, $dish_id, $quantity, $notes, $status);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);
        }
      }

      ?>

</body>

</html>