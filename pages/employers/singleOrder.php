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


if (isset($_GET['reservation_id']) && isset($_GET['table_number'])) {
    $reservation_id = $_GET['reservation_id'];
    $table_number = $_GET['table_number'];
}
?>
<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <?php if (isset($table_number) && isset($reservation_id)) {
        echo "<h1>Orders for Table " . $table_number . "</h1>";
    } ?>
</header>

<body>
    <?php
    if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
        $order_id = $_POST['order_id'];
        $order_status = $_POST['order_status'];

        $update_sql = "UPDATE orders SET order_status='$order_status' WHERE order_id='$order_id'";
        if ($conn->query($update_sql) === TRUE) {
            echo "Order status updated successfully.";
            $reservation_id = $_GET['reservation_id'];
            $table_number = $_GET['table_number'];

            // Get the updated order status value
        } else {
            echo "Error updating order status: " . $conn->error;
        }
    }
    if (isset($_GET['reservation_id'])) {
        $reservation_id = $_GET['reservation_id'];

        // Retrieve the order details from the database
        $sql = "SELECT o.order_id, d.dish_name, d.price, o.quantity, o.notes, r.status
            FROM orders o
            JOIN dishes d ON o.dish_id = d.dish_id
            JOIN reservations r ON o.reservation_id = r.reservation_id
            WHERE o.reservation_id = '$reservation_id'";

        $result = mysqli_query($conn, $sql);
        echo "<table>";
        echo "<tr><th>Order ID</th><th>Dish Name</th><th>Quantity</th><th>Price</th><th>Order Status</th><th>Notes</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            $order_id = $row['order_id'];

            // Get the updated order status value
            $result_status = mysqli_query($conn, "SELECT order_status FROM orders WHERE order_id='$order_id'");
            $row_status = mysqli_fetch_assoc($result_status);
            $updated_status = $row_status['order_status'];

            echo "<tr>";
            echo "<td>" . $row["order_id"] . "</td>";
            echo "<td>" . $row["dish_name"] . "</td>";
            echo "<td>" . $row["quantity"] . "</td>";
            echo "<td>" . $row["price"] . "</td>";
            // Status button with confirmation message
            echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to update the order status?\");' action='" . $_SERVER['PHP_SELF'] . "?reservation_id=" . $reservation_id . "&table_number=" . $table_number . "'>";
            echo "<input type='hidden' name='order_id' value='" . $row["order_id"] . "'/>";
            echo "<td><select name='order_status' onchange='if(confirm(\"Are you sure you want to update the order status?\")) { this.form.submit(); }'>";
            echo "<option value='Pending' " . ($updated_status == 'Pending' ? 'selected' : '') . ">Pending</option>";
            echo "<option value='Completed' " . ($updated_status == 'Completed' ? 'selected' : '') . ">Completed</option>";
            echo "<option value='Cancelled' " . ($updated_status == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>";
            echo "</select>";
            // Display the order status as text on the button
            echo "</form>";
            // Text area for notes column
            echo "<td><textarea class='expandable' name='notes' placeholder='Click to expand'>" . $row["notes"] . "</textarea></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "<div style='text-align: center; margin-top: 20px;'>";
    echo "<a href='floorPlan.php' style='display: inline-block; text-align: center; padding: 10px 20px; background-color: black; color: white; text-decoration: none; font-size: 16px; border-radius: 4px;'>Go back to Floor Plan</a>";
    echo "</div>";



    // Close the database connection
    mysqli_close($conn);
    ?>

</body>

</html>