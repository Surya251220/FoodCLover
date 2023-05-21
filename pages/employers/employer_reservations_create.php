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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Reservations</h1>
</header>

<body>
    <?php

    $today = date('Y-m-d');

    // Check if a reservation date has been selected
    if (isset($_GET['date'])) {
        $reservation_date = $_GET['date'];
    } else {
        $reservation_date = date('Y-m-d', strtotime($today . ' +1 day'));
    }

    // Get the next available reservation date
    $nextDay = date('Y-m-d', strtotime($today . ' +1 day'));

    // Get the available reservation times
    $reservation_times = array(
        'Monday' => array('12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'),
        'Tuesday' => array('12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'),
        'Wednesday' => array('12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'),
        'Thursday' => array('12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'),
        'Friday' => array('12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'),
        'Saturday' => array('11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'),
        'Sunday' => array('11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00')
    );

    // Get the reservations for the selected date
    $reservations_query = "SELECT table_number, reservation_time FROM reservations WHERE reservation_date = '$reservation_date'";
    $reservations_result = mysqli_query($conn, $reservations_query);

    // Store the taken reservation times in an array
    $taken_times = array();
    while ($row = mysqli_fetch_assoc($reservations_result)) {
        $taken_times[$row['table_number']][] = $row['reservation_time'];
    }

    // Get the available table slots
    $available_slots = array();
    foreach ($reservation_times[date('l', strtotime($reservation_date))] as $time) {
        $reserved = false;
        foreach ($taken_times as $table => $times) {
            if (in_array($time, $times)) {
                $reserved = true;
                break;
            }
        }
        if (!$reserved) {
            $available_slots[] = $time;
        }
    }
    if (isset($_POST['signup-submit'])) {
        $customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
        $table_number = $_POST['table_number'];
        $number_guests = $_POST['number_guests'];
        $reservation_time = $_POST['reservation_time'];
        $reservation_date = $_POST['reservation_date'];

        if (empty($customer_id)) {
            echo "Error: Customer ID is required.";
            return;
        }

        if (!ctype_digit($customer_id)) {
            echo "Error: Invalid customer ID.";
            return;
        }

        // Check if the customer exists
        $customer_query = "SELECT * FROM customers WHERE customer_id = '$customer_id'";
        $customer_result = mysqli_query($conn, $customer_query);
        if (mysqli_num_rows($customer_result) == 0) {
            echo "Error: Invalid customer ID.";
            return;
        }

        // Insert data into reservations table
        $sql = "INSERT INTO reservations (customer_id, table_number, num_guests, reservation_time, reservation_date) VALUES ('$customer_id', '$table_number', '$number_guests', '$reservation_time', '$reservation_date')";

        if ($conn->query($sql) === TRUE) {
            echo "Reservation successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    ?>
    <form action="employer_reservations_create.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Table Number</th>
                    <?php foreach ($available_slots as $time) { ?>
                        <th><?php echo date('h:i A', strtotime($time)); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $tables_query = "SELECT table_number FROM restaurant_tables";
                $tables_result = mysqli_query($conn, $tables_query);
                while ($row = mysqli_fetch_assoc($tables_result)) {
                    $table_number = $row['table_number'];
                ?>
                    <tr>
                        <td><?php echo $table_number; ?></td>
                        <?php
                        foreach ($available_slots as $time) {
                            $reserved_query = "SELECT table_number FROM reservations WHERE reservation_date = '$reservation_date' AND reservation_time = '$time' AND table_number = '$table_number' AND status='pending'";
                            $reserved_result = mysqli_query($conn, $reserved_query);
                        ?>
                            <td>
                                <?php if (mysqli_num_rows($reserved_result) == 0) { ?>
                                    <input type="radio" name="table_slot" value="<?php echo $table_number . '_' . $time; ?>" onclick="updateUrl(this.value)">
                                <?php } else { ?>
                                    Booked
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>
    <form action="employer_reservations_create.php" method="POST" name="signup-submit">
        <table>
            <tr>
                <th>Customer Name:</th>
                <th>Email:</th>
                <th>Phone:</th>
                <th>Reservation Date:</th>
                <th>Reservation Time:</th>
                <th>Number of Guests:</th>
                <th>Table Number:</th>
                <th>Actions</th>
            </tr>
            <tr>
                <?php
                if (isset($_GET['customer_id'])) {
                    $customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
                    $customer_query = "SELECT * FROM customers WHERE customer_id = '$customer_id'";
                    $customer_result = mysqli_query($conn, $customer_query);
                    $customer = mysqli_fetch_assoc($customer_result);
                    // populate the form fields with the retrieved data
                }
                ?>
                <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                <td><input type="text" name="customer_name" value="<?php echo isset($customer['full_name']) ? $customer['full_name'] : ''; ?>"></td>
                <td><input type="email" name="email" value="<?php echo isset($customer['email']) ? $customer['email'] : ''; ?>"></td>
                <td><input type="tel" name="phone" value="<?php echo isset($customer['phone']) ? $customer['phone'] : ''; ?>"></td>


                <td><input type="reservation_date" id="reservation_date" name="reservation_date" readonly></td>
                <td><input type="time" id="reservation_time" name="reservation_time" readonly></td>
                <td>
                    <select name="number_guests" id="number_guests">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </td>
                <td><input type="text" id="table_number" name="table_number" readonly></td>
                <td><input type="submit" name="signup-submit" value="Create Reservation"></td>
            </tr>
        </table>
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>
    <script>
        flatpickr("#reservation_date", {
            minDate: "today",
            dateFormat: "Y-m-d",
            disable: [
                function(date) {
                    // Disable dates that are not in the future
                    return date < new Date().fp_incr(1);
                }
            ],
            onReady: function(selectedDates, dateStr, instance) {
                // Get the date from the URL query string
                var urlParams = new URLSearchParams(window.location.search);
                var date = urlParams.get('date');

                // If the date is present in the query string, set it as the selected date
                if (date) {
                    instance.setDate(date);
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                // When the date is changed, reload the page with the new date and customer_id
                var customerId = <?php echo $customer_id; ?>;
                window.location.href = "employer_reservations_create.php?customer_id=" + customerId + "&date=" + dateStr;
            }
        });


        document.getElementById("number_guests").addEventListener("change", function() {
            if (this.value > 8) {
                document.getElementById("max_guests_msg").style.display = "block";
                this.setCustomValidity("Please enter a number between 1 and 6.");
            } else {
                document.getElementById("max_guests_msg").style.display = "none";
                this.setCustomValidity("");
            }
        });
    </script>
    <script>
        function updateUrl(tableNumber, reservationTime) {
            var url = new URL(window.location.href);
            url.searchParams.set('table', tableNumber + '_' + reservationTime);
            window.history.replaceState({}, '', url);
            window.dispatchEvent(new PopStateEvent('popstate'));
        }

        window.onload = function() {
            var urlParams = new URLSearchParams(window.location.search);
            var tableParam = urlParams.get('table');
            if (tableParam) {
                var parts = tableParam.split('_');
                var tableNumber = parts[0];
                var reservationTime = parts[1].replace('%3A', ':');
                document.getElementById('table_number').value = tableNumber;
                document.getElementById('reservation_time').value = reservationTime;
            }
        };


        document.getElementById('table_number').onchange = function() {
            var tableNumber = this.value;
            var reservationTime = document.getElementById('reservation_time').value;
            updateUrl(tableNumber, reservationTime);
        };

        document.getElementById('reservation_time').onchange = function() {
            var tableNumber = document.getElementById('table_number').value;
            var reservationTime = this.value;
            updateUrl(tableNumber, reservationTime);
        };

        window.addEventListener('popstate', function(event) {
            location.reload();
        });
    </script>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <select name="criteria">
            <option value="customer_id" <?php if ($criteria === 'customer_id') {
                                            echo ' selected';
                                        } ?>>Customer ID</option>
            <option value="email" <?php if ($criteria === 'email') {
                                        echo ' selected';
                                    } ?>>Email</option>
            <option value="full_name" <?php if ($criteria === 'full_name') {
                                            echo ' selected';
                                        } ?>>Full Name</option>
        </select>
        <?php if (!empty($search)) { ?>
            <button type="submit" name="reset" value="1">Reset</button>
        <?php } else { ?>
            <button type="submit">Submit</button>
        <?php } ?>
        <input type="hidden" name="page" value="<?php echo $page; ?>">
    </form>
    <table name="customer_table">
        <h2>Customer table</h2>
        <tr>
            <th>Customer ID</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Actions</th>
        </tr>
        <?php
        $sql = "SELECT * FROM customers";
        if (!empty($search)) {
            $sql = "SELECT * FROM customers WHERE $criteria LIKE '%$search%'";
        }
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td contenteditable='true' data-field='id'>" . $row['customer_id'] . "</td>";
            echo "<td contenteditable='true' data-field='fullName'>" . $row['full_name'] . "</td>";
            echo "<td contenteditable='true' data-field='email'>" . $row['email'] . "</td>";
            echo "<td contenteditable='true' data-field='phone'>" . $row['phone'] . "</td>";
            echo "<td><button onclick=\"location.href='employer_reservations_create.php?customer_id=" . $row['customer_id'] . "'\">Make reservation</button></td>";
            echo "</tr>";
        }
        ?>
    </table>




</body>

</html>