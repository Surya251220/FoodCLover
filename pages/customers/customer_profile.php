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
<!DOCTYPE html>
<html>
<header>

    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Customer Profile</h1>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .section-header {
            font-size: 24px;
            font-weight: bold;
            margin-top: 24px;
            color: white;
        }

        .section {

            border-radius: 4px;
            padding: 16px;


        }

        table th {
            background-color: #899499;
            font-weight: bold;
            color: white;
        }

        table td {
            padding: 8px;
            color: black;
            font-size: 16px;
            font-weight: bolder;
        }
    </style>
</header>

<body>
    <?php
    // Get employer details from database
    $customer_id = $_SESSION['customer_id'];
    $sql = "SELECT * FROM customers WHERE customer_id = '$customer_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Customer not found.";
        exit;
    }

    ?>

    <div class="section">
        <div class="section-header">Personal Details</div>
        <table>
            <tr>
                <th>ID</th>
                <td><?php echo $row['customer_id']; ?></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?php echo $row['full_name']; ?></td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td><?php echo $row['age']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $row['email']; ?></td>

            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo $row['phone']; ?></td>

            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo $row['address']; ?></td>

            </tr>
            <tr>
                <th>Allergies</th>
                <td><?php echo $row['allergies']; ?></td>

            </tr>
        </table>
        <td><button onclick="location.href='customerProfileUpdate.php?customer_id=<?php echo $row['customer_id']; ?>'">Update</button></td>
    </div>

    <div class="section">
        <div class="section-header">Reservations</div>

        <table>
            <tr>
                <th>Reservation ID</th>
                <th>Table Number</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
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
            $customer_id = $_SESSION['customer_id'];
            $sql = "SELECT reservations.*, customers.full_name, guests.name 
              FROM reservations 
              LEFT JOIN customers ON reservations.customer_id = customers.customer_id 
              LEFT JOIN guests ON reservations.guest_id = guests.guest_id 
              WHERE reservations.customer_id = $customer_id
              ORDER BY 
                  CASE WHEN reservations.status = 'pending' THEN 0 ELSE 1 END, 
                  reservation_date DESC";

            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['reservation_id'] . "</td>";
                echo "<td>" . $row['table_number'] . "</td>";
                echo "<td>" . $row['reservation_date'] . "</td>";
                echo "<td>" . $row['reservation_time'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";

                if ($row["status"] == "Pending") {
                    // Check if reservation is less than 24 hours away
                    $now = time();
                    $reservation_time = strtotime($row['reservation_date'] . ' ' . $row['reservation_time']);
                    $time_diff = $reservation_time - $now;
                    $hours_diff = $time_diff / 3600;

                    if ($hours_diff < 24) {
                        echo "<td>Cannot cancel this reservation. Please contact the restaurant.</td>";
                    } else {
                        echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "' onsubmit='return confirm(\"Are you sure you want to cancel this reservation?\");'>";
                        echo "<input type='hidden' name='reservation_id' value='" . $row["reservation_id"] . "'/>";
                        echo "<td><input type='submit' name='status' value='Cancelled'/>";
                        echo "</form>";
                    }
                } else if ($row["status"] == "Cancelled") {
                    echo "<td>" . $row["status"] . "</td>";
                } else if ($row["status"] == "Completed") {
                    if (isset($row["reservation_id"]) && isset($row["table_number"]) && (isset($row["full_name"]) || isset($row["name"]))) {
                        echo '<form method="get" action="personalReceipts.php">';
                        echo '<input type="hidden" name="reservation_id" value="' . $row["reservation_id"] . '">';
                        echo '<input type="hidden" name="table_number" value="' . $row["table_number"] . '">';
                        echo '<input type="hidden" name="full_name" value="' . $row["full_name"] . '">';
                        echo '<input type="hidden" name="full_name" value="' . $row["customer_id"] . '">';
                        echo '<td><button type="submit">Receipts</button>';
                        echo "</td>";
                        echo "</form>";
                    } else {
                        echo '-';
                    }
                } else if ($row["status"] == "Arrived") {
                    echo "<td>Wait for receipt until the restaurant completes your reservation.</td>";
                }
                echo "</tr>";
            }

            ?>



    </div>

</body>

</html>