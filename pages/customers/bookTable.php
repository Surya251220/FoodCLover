<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

?>

<!DOCTYPE html>
<html>
<header>
	<h1>Book a Table</h1>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
	<link rel="stylesheet" href="..\..\assets\css\main_style.css">
	<link rel="stylesheet" href="..\..\assets\css\header_style.css">
</header>
<?php
// Connect to the database
if (isset($_SESSION['id']) || isset($_SESSION['customer_id'])) {

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
		$customer_id = $_SESSION['customer_id'];
		$customer_name = $_SESSION['full_name'];
		$email = $_SESSION['email'];;
		$table_number = $_POST['table_number'];
		$number_guests = $_POST['number_guests'];
		$reservation_time = $_POST['reservation_time'];
		$reservation_date = $_POST['reservation_date'];

		// Insert data into reservations table
		$sql = "INSERT INTO reservations (customer_id, table_number, num_guests, reservation_time, reservation_date) VALUES ('$customer_id', '$table_number', '$number_guests', '$reservation_time', '$reservation_date')";

		if ($conn->query($sql) === TRUE) {
			echo "Reservation successful!";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
?>


	<body>

		<form method="POST" action="bookTable.php">
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

			<br>
			<br>
			<form method="POST" action="customer_menu.php" name="signup-submit">
				<input type="hidden" id="customer_id" name="customer_id" value="<?php echo $_SESSION['customer_id']; ?>" required>

				<table>
					<thead>
						<tr>
							<th>Customer Name:</th>
							<th>Email:</th>
							<th>Reservation Date:</th>
							<th>Table Number:</th>
							<th>Number of Guests:</th>
							<th>Reservation Time:</th>
							<th>Actions:</th>

						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="text" id="customer_name" name="customer_name" value="<?php echo $_SESSION['full_name']; ?>" required></td>
							<td><input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" required></td>
							<td><input type="reservation_date" id="reservation_date" name="reservation_date" readonly></td>
							<td><input type="text" id="table_number" name="table_number" readonly></td>
							<td><input type="number" id="number_guests" name="number_guests" min="1" max="6" required>
								<span id="max_guests_msg" style="color:red; display:none;">Please contact the restaurant for reservations with more than 6 guests.</span>
							</td>
							<td><input type="time" id="reservation_time" name="reservation_time" value="" readonly></td>
							<td><button name="signup-submit" type="submit">Book Table</button></td>
						</tr>
					</tbody>
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
						// When the date is changed, reload the page with the new date
						window.location.href = "bookTable.php?date=" + dateStr;
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
		<?php
	} else {

		echo "<div class='center'>";
		echo "<div class='btn2-container'>";
		echo "<h2>Please Login or Signup to make reservation.</h2>";
		echo "<h2>Thank you!</h2>";
		echo "<a style='height=500px;' href='customerLogin.php' class='btn2 btn-primary'>Login</a>";
		echo "<a href='customerSignup.php' class='btn2 btn-primary'>Signup</a>";
		echo "</div>";
		echo "</div>";
	}
		?>
	</body>

</html>