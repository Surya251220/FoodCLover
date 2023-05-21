<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';
include_once '..\..\includes\errorsFormatting.php';

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

$sql1 = "SELECT * FROM suppliers";
$result1 = mysqli_query($conn, $sql1);
$suppliers = mysqli_fetch_all($result1, MYSQLI_ASSOC);

$sql = "SELECT DISTINCT delivery_frequency FROM delivery";
$result = mysqli_query($conn, $sql);
$delivery = mysqli_fetch_all($result, MYSQLI_ASSOC);


$date_one_week_ago = date('Y-m-d', strtotime('-1 week'));
$sql = "SELECT delivery.*, suppliers.supplier_name, ingredients.ingredient_name
            FROM delivery 
            INNER JOIN suppliers ON delivery.supplier_id = suppliers.supplier_id 
            INNER JOIN ingredients ON delivery.ingredient_id = ingredients.ingredient_id 
            WHERE (delivery_status='pending' OR delivery_status='not made') AND delivery_date >= '$date_one_week_ago' ORDER BY delivery_date DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Delivery Management</h1>
</header>

<body>
    <div class="button-container" style="margin-top: 50px;">
        <a href="DeliveryInvoice.php"><button class="signup_button">All Deliveries and Invoices</button></a>
        <a href="supplierInvoices.php"><button class="signup_button">Supplier Invoices</button></a>
        <a href="addIngredient.php"><button class="signup_button">Add Ingredient and Delivery</button></a>

    </div>

    <body>

        <div class="container-signup">

            <table>
                <tr>
                    <th>Delivery ID</th>
                    <th>Supplier Name</th>
                    <th>Ingredient Name</th>
                    <th>Delivery Date</th>
                    <th>Delivery Frequency</th>
                    <th>Price</th>
                    <th>Invoice Number</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>

                <?php

                if (isset($_POST['delivery_id']) && isset($_POST['delivery_status'])) {
                    $delivery_id = $_POST['delivery_id'];
                    $delivery_status = $_POST['delivery_status'];

                    $update_sql = "UPDATE delivery SET delivery_status='$delivery_status' WHERE delivery_id='$delivery_id'";
                    if ($conn->query($update_sql) === TRUE) {
                        echo "Delivery status updated successfully.";
                        header("Location: deliveryUpdate.php");
                        exit();
                    } else {
                        echo "Error updating delivery status: " . $conn->error;
                    }
                }


                if ($result->num_rows > 0) {
                    $num_deliveries = $result->num_rows;

                    echo "<p>There are $num_deliveries deliveries pending for this week:</p>";
                    // output data of each row
                    while ($row = $result->fetch_assoc()) {

                        echo "<tr><td>" . $row["delivery_id"] . "</td><td>" . $row["supplier_name"] . "</td><td>" . $row["ingredient_name"] . "</td><td>" . $row["delivery_date"] . "</td><td>" . $row["delivery_frequency"] . "</td><td>£ " . $row["price"] . "</td><td>" . $row["invoice_number"] . "</td><td>" . $row["quantity"] . "</td><td>";

                        // Status button with confirmation message
                        echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to update the delivery status?\");' action='deliveryUpdate.php'>";
                        echo "<input type='hidden' name='delivery_id' value='" . $row["delivery_id"] . "'/>";
                        echo "<select name='delivery_status' onchange='if(confirm(\"Are you sure you want to update the delivery status?\")) { this.form.submit(); }'>";
                        echo "<option value='pending' " . ($row["delivery_status"] == 'pending' ? 'selected' : '') . ">Pending</option>";
                        echo "<option value='completed' " . ($row["delivery_status"] == 'completed' ? 'selected' : '') . ">Completed</option>";
                        echo "<option value='not made' " . ($row["delivery_status"] == 'not made' ? 'selected' : '') . ">Not Made</option>";
                        echo "</select>";
                        echo "</form>";

                        echo "</td></tr>";
                    }
                } else {
                    echo "0 Deliveries";
                }

                // Close the database connect
                mysqli_close($conn);

                ?>

            </table>
    </body>


</html>