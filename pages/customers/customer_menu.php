<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

if (isset($_SESSION['email']) && isset($_SESSION['usertype'])) {
  if ($_SESSION['usertype'] == 'employer') {
    // Employer is logged in, redirect to employerLogin.php
    header("Location: ../../pages/employers/employerLogin.php");
    exit();
  } else {
    // Customer or guest is logged in, give access to $_SERVER['PHP_SELF']
    // Your code here to grant access to $_SERVER['PHP_SELF']
  }
}

?>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Menu</h1>
</header>

<div class="center">
  <div class="btn-container">
    <?php


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
    }

    if (isset($_SESSION['id'])) {
      // do nothing or show a message that the user is logged in as an employer
    } else if (isset($_SESSION['verification_number']) || isset($_SESSION['customer_id']) && isset($_GET['customer_id']) && isset($_GET['table_number']) && isset($_GET['verification_code'])) {
      echo "<a href='customer_sum.php' class='btn btn-primary'>Go to Your Order</a>";
    } else if (isset($_SESSION['customer_id'])) {

      // Check if there is a reservation for the current day with arrived status
      $customer_id = $_SESSION['customer_id'];
      $date_today = date("Y-m-d");
      $sql = "SELECT * FROM reservations WHERE customer_id = '$customer_id' AND reservation_date = '$date_today' AND status = 'Arrived'";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        // Show reservation button
        $row = $result->fetch_assoc();
        $reservation_id = $row["reservation_id"];
        $reservation_date = $row["reservation_date"];
        $reservation_time = $row["reservation_time"];

        echo "<a href='customerTable.php' style='font-weight: bold'class='btn btn-primary'>Reservation #$reservation_id on $reservation_date at $reservation_time <br> <br>Please verify your table.</a>";
      } else {
        // Show table number button
        echo "<a href='customerTable.php' class='btn btn-primary'>Give your table number</a>";
      }
    } else {
      // User is not logged in
      echo "<div class='center'>";
      echo "<div class='btn-container'>";
      echo "<a href='customerLogin.php' class='btn btn-primary'>Login</a>";
      echo "<a href='customerSignup.php' class='btn btn-primary'>Sign up</a>";
      echo "<a href='guestLogin.php' class='btn btn-primary'>Continue as Guest</a>";
      echo "</div>";
      echo "</div>";
    }


    ?>
  </div>
</div>

<?php
$sql = "SELECT * FROM dishes ORDER BY category_order, category, dish_name";
$result = $conn->query($sql);
$current_category = null;
if ($result->num_rows > 0) {
  echo "<form method='post'>";

  while ($row = $result->fetch_assoc()) {
    $category = $row["category"];
    $dish_name = $row["dish_name"];
    $dish_description = $row["description"];
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
    echo "<div class='image_path'><img src='$image_path' style='max-width:100%; height:250px; width:400px;'></div>";
    echo "<div class='dish-description'>$dish_description</div>";
    echo "<div class='dish-allergies'>Allergies: $allergies</div>";
    echo "<div class='dish-price'>Price: £ $price</div>";


    if (isset($_SESSION['verification_number']) || isset($_SESSION['customer_id']) && isset($_GET['customer_id']) && isset($_GET['table_number']) && isset($_GET['verification_code'])) {
      // User is logged in, show the "Quantity" and "Add to Order" button
      echo "<input type='number' name='quantity[$dish_name]' value='1' min='1' class='input-quantity'>";
      echo "<button type='submit' name='add_to_cart' value='$dish_name,$price' class='dish-button'>Add to Order</button>";
    }

    echo "</div>";
  }
  echo "</form>";
}

if (isset($_POST['add_to_cart'])) {
  $dish_name = explode(",", $_POST['add_to_cart'])[0];
  $price = explode(",", $_POST['add_to_cart'])[1];
  $quantity = $_POST['quantity'][$dish_name];


  if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $guest_id = null;
  } elseif (isset($_SESSION['guest_id'])) {
    $guest_id = $_SESSION['guest_id'];
    $customer_id = null;
  } else {
    // handle case where no customer id is set in session
    $guest_id = null;
    $customer_id = null;
  }

  // Retrieve reservation number and table number from the reservations table
  $query = "SELECT reservation_id, table_number FROM reservations 
          WHERE (customer_id = ? OR guest_id = ?) 
          AND status = 'Arrived' 
          AND DATE(reservation_date) = CURDATE()";

  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "ii", $customer_id, $guest_id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
    $reservation_id = $row['reservation_id'];
    $table_number = $row['table_number'];
  } else {
    // handle case where no reservation is found for the customer ID
    $reservation_id = null;
    $table_number = null;
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

  // Insert the order into the cart table
  $sql = "INSERT INTO cart (guest_id, customer_id, reservation_id, table_number, dish_id, quantity, notes, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "iiiiiiss", $guest_id, $customer_id, $reservation_id, $table_number, $dish_id, $quantity, $notes, $status);

  if (mysqli_stmt_execute($stmt)) {
    echo "Order added to cart successfully.";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

$conn->close();
?>

</html>