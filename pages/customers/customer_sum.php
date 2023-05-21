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
} else {
  // No one is logged in, redirect to employerLogin.php
  header("Location: ../../pages/customers/customerLogin.php");
  exit();
}

?>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Menu</h1>
</header>
<?php
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

$query = "SELECT reservation_id, table_number FROM reservations WHERE (customer_id = ? OR guest_id = ?)
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


$sql = "SELECT * FROM cart WHERE (customer_id = ? AND reservation_id = ?) OR (guest_id = ? AND customer_id IS NULL)
AND status = 'unconfirmed'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $customer_id, $reservation_id, $guest_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);




if (isset($_POST['delete'])) {
  $cart_id = $_POST['delete'];
  $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
  $stmt->bind_param("i", $cart_id);
  $stmt->execute();
  $stmt->close();
}
$unique_cart_ids = array(); // array to keep track of unique cart IDs added to orders table
if (isset($_POST['confirm_order'])) {
  foreach ($_POST['quantities'] as $cart_id => $quantity) {
    $notes = $_POST['notes'][$cart_id];
    $stmt = $conn->prepare("UPDATE cart SET quantity = ?, notes = ?, status = 'confirmed', cart_id = ? WHERE cart_id = ?");
    $stmt->bind_param("isii", $quantity, $notes, $cart_id, $cart_id);
    $stmt->execute();
    $stmt->close();

    $row = $conn->query("SELECT dish_id FROM cart WHERE cart_id = $cart_id")->fetch_assoc();
    $dish_id = $row['dish_id'];

    // check if cart ID is already in the array
    if (!in_array($cart_id, $unique_cart_ids)) {
      $unique_cart_ids[] = $cart_id;
    }


    if ($reservation_id === null) {
      $row = $conn->query("SELECT reservation_id FROM cart WHERE cart_id = $cart_id")->fetch_assoc();
      $reservation_id = $row['reservation_id'];
    }

    // array to keep track of unique cart IDs added to orders table
    $stmt = $conn->prepare("INSERT INTO orders (cart_id, reservation_id, customer_id, guest_id, table_number, dish_id, quantity, notes) 
        SELECT cart_id, reservation_id, customer_id, guest_id, table_number, dish_id, quantity, notes
        FROM cart 
        WHERE (customer_id = ? OR (guest_id = ? AND customer_id IS NULL))
          AND reservation_id = ? 
          AND table_number = ? 
          AND status = 'confirmed'
          AND cart_id = ?");
    $stmt->bind_param("iiiii", $customer_id, $guest_id, $reservation_id, $table_number, $cart_id);
    $stmt->execute();
    $stmt->close();


    // delete all rows in cart with matching customer_id/guest_id, reservation_id, and table_number
    $stmt = $conn->prepare("DELETE FROM cart WHERE (customer_id = ? OR (guest_id = ? AND customer_id IS NULL)) AND reservation_id = ? AND table_number = ? AND cart_id = ?");
    $stmt->bind_param("iiiii", $customer_id, $guest_id, $reservation_id, $table_number, $cart_id);
    $stmt->execute();
    $stmt->close();
  }

  header("Location: customer_con_order.php");
  exit();
}


if (isset($_POST['update_order'])) {
  $unique_cart_ids = array(); // array to keep track of unique cart IDs added to orders table
  foreach ($_POST['quantities'] as $cart_id => $quantity) {
    $notes = $_POST['notes'][$cart_id];
    $stmt = $conn->prepare("UPDATE cart SET quantity = ?, notes = ?, status = 'confirmed', cart_id = ? WHERE cart_id = ?");
    $stmt->bind_param("isii", $quantity, $notes, $cart_id, $cart_id);
    $stmt->execute();
    $stmt->close();


    if ($reservation_id === null) {
      $row = $conn->query("SELECT reservation_id FROM cart WHERE cart_id = $cart_id")->fetch_assoc();
      $reservation_id = $row['reservation_id'];
    }
  }

  header("Location: customer_sum.php");
  exit();
}

echo "<h2>Customer Summary</h2>";
echo "<form class='order-summary' action='customer_sum.php' method='POST'>";
echo "<table>";
echo "<tr><th>Dish Name</th><th>Quantity</th><th>Price</th><th>Total Price</th><th>Notes</th><th>Action</th></tr>";

$total_quantity = 0; // initialize total quantity to 0
$total_price = 0; // initialize total price to 0

while ($row = $result->fetch_assoc()) {
  $cart_id = $row["cart_id"];
  $dish_id = $row["dish_id"];
  $quantity = $row["quantity"];
  $table_number = $row["table_number"];
  $reservation_id = $row["reservation_id"];
  $notes = $row["notes"];

  $sql = "SELECT dish_name, price FROM dishes WHERE dish_id = $dish_id";
  $dish_result = $conn->query($sql);
  $dish_row = $dish_result->fetch_assoc();
  $dish_name = $dish_row["dish_name"];
  $price = $dish_row["price"];
  $total_price_item = $price * $quantity;
  $total_quantity += $quantity; // add quantity to the total
  $total_price += $total_price_item; // add total price of the item to the total

  echo "<tr>";
  echo "<td>$dish_name</td>";
  echo "<td><input type='number' name='quantities[$cart_id]' value='$quantity'></td>";
  echo "<td> £ $price</td>";
  echo "<td>£ $total_price_item</td>";
  echo "<td><textarea class='expandable' name='notes[$cart_id]'>$notes</textarea></td>";
  echo "<td><button type='submit' name='delete' value='$cart_id'>Delete</button></td>";
  echo "</tr>";
}

// add a row at the bottom for total quantity and total price
echo "<tr>";
echo "<td><strong>Total Order</strong></td>";
echo "<td><strong>$total_quantity</strong></td>";
echo "<td></td>";
echo "<td><strong>£ $total_price</strong></td>";
echo "<td></td>";
echo "<td></td>";
echo "</tr>";

echo "</table>";
echo "<button type='submit' class='confirm-button' name='confirm_order'>Confirm Order</button>";
echo "<button type='submit' class='confirm-button' name='update_order'>Update Order</button>";
echo "</form>";


$conn->close();

?>

</html>