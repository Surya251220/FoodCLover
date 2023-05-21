<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'supplier_name';
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
    <h1>Delivery Management</h1>
</header>
<style>
    .pending {
        background-color: yellow;
    }

    .completed {
        background-color: green;
    }

    .not-made {
        background-color: red;
    }
</style>


<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar" onsubmit="trimSearch()">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <select name="criteria">
        <option value="delivery_id" <?php if ($criteria === 'delivery_id') {
                                        echo ' selected';
                                    } ?>>Delivery ID</option>
        <option value="supplier_name" <?php if ($criteria === 'supplier_name') {
                                            echo ' selected';
                                        } ?>>Supplier Name</option>
        <option value="ingredient_name" <?php if ($criteria === 'ingredient_name') {
                                            echo ' selected';
                                        } ?>>Ingredient Name</option>
        <option value="delivery_status" <?php if ($criteria === 'delivery_status') {
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

<script>
    function trimSearch() {
        var searchInput = document.getElementById("search");
        searchInput.value = searchInput.value.trim();
    }
</script>


<body>
    <table>
        <tr>
            <th>Delivery ID</th>
            <th>Supplier Name</th>
            <th>Ingredient Name</th>
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
                header("Location: deliveryInvoice.php");
                exit();
            } else {
                echo "Error updating delivery status: " . $conn->error;
            }
        }


        $delivery_id = isset($_GET['delivery_id']) ? mysqli_real_escape_string($conn, $_GET['delivery_id']) : '';

        $sql = "SELECT delivery.*, suppliers.supplier_name, ingredients.ingredient_name
FROM delivery 
INNER JOIN suppliers ON delivery.supplier_id = suppliers.supplier_id 
INNER JOIN ingredients ON delivery.ingredient_id = ingredients.ingredient_id 
WHERE 1
ORDER BY delivery.invoice_number DESC";
        if (!empty($search)) {
            $sql = "SELECT delivery.*, suppliers.supplier_name, ingredients.ingredient_name
        FROM delivery 
        INNER JOIN suppliers ON delivery.supplier_id = suppliers.supplier_id 
        INNER JOIN ingredients ON delivery.ingredient_id = ingredients.ingredient_id 
        WHERE suppliers.supplier_name LIKE '%$search%' OR ingredients.ingredient_name LIKE '%$search%' OR delivery.delivery_status LIKE '%$search%' OR delivery.delivery_id LIKE '%$search%'
        ORDER BY delivery.delivery_id DESC";
        }


        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                $statusClass = str_replace(' ', '-', strtolower($row["delivery_status"]));
                echo "<tr>";
                echo "<td>" . $row["delivery_id"] . "</td>";
                echo "<td>" . $row["supplier_name"] . "</td>";
                echo "<td>" . $row["ingredient_name"] . "</td>";
                echo "<td>" . $row["delivery_frequency"] . "</td>";
                echo "<td>£ " . $row["price"] . "</td>";
                echo "<td>";

                // Invoice button
                echo "<form method='post' action='invoices.php?delivery_id=" . $row["delivery_id"] . "'>";
                echo "<input type='hidden' name='delivery_id' value='" . $row["delivery_id"] . "'/>";
                echo "<button>" . $row["invoice_number"] . "</button>";
                echo "</form>";

                echo "</td>";
                echo "<td>" . $row["quantity"] . "</td>";
                echo "<td class='" . $statusClass . "'>";

                // Status button with confirmation message
                echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to update the delivery status?\");' action='deliveryInvoice.php'>";
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
            echo "<tr><td colspan='8'>0 Deliveries</td></tr>";
        }


        // Close the database connect
        mysqli_close($conn);

        ?>

    </table>
</body>


</html>