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

if ($guest_id !== null || $customer_id !== null) {

	$id_column = $guest_id !== null ? 'guest_id' : 'customer_id';
	$id_value = $guest_id !== null ? $guest_id : $customer_id;

	$sql = "SELECT c.full_name, o.order_id, o.customer_id, o.reservation_id, o.table_number, r.status, o.created_at, o.updated_at, o.guest_id, g.name, g.verification_number
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            JOIN reservations r ON r.reservation_id = o.reservation_id
            LEFT JOIN guests g ON o.guest_id = g.guest_id
            WHERE r.status = 'Pending' AND r.$id_column = $id_value
            ORDER BY r.created_at ASC
            LIMIT 1";

	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$table_number = $row['table_number'];
		$reservation_id = $row['reservation_id'];
	} else {
		$table_number = null;
		$reservation_id = null;
	}

	mysqli_close($conn);
} else {
	$table_number = null;
	$reservation_id = null;
}

?>
<html>
<header>
	<link rel="stylesheet" href="..\..\assets\css\main_style.css">
	<link rel="stylesheet" href="..\..\assets\css\header_style.css">
	<h1>Menu</h1>
	<style>
		.container {
			max-width: 600px;
			margin: auto;
			padding: 20px;
			text-align: center;

		}

		.thanks {
			font-size: 36px;
			text-align: center;
			margin-top: 100px;
			color: white;
		}

		h2 {
			font-size: 24px;
			margin-top: 30px;
		}

		form {
			display: inline-block;
			margin-right: 10px;
			/* reduced the margin between the buttons */
		}

		input[type="submit"] {
			background-color: white;
			color: black;
			padding: 10px 20px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 18px;
			font-weight: bold;
		}

		input[type="submit"]:hover {
			background-color: grey;

		}
	</style>
</header>

<body>
	<div class="container">
		<h1 class="thanks">Thank You for Your Order!</h1>
		<h2>Reservation ID: <?php echo $reservation_id; ?></h2>
		<h2>Enjoy your Meal!</h2>

		<?php
		if (isset($row["reservation_id"]) && isset($row["table_number"]) && isset($row["customer_id"]) || isset($row["guest_id"])) {
			echo '<form method="get" action="customer_menu.php">
			<button class="btn btn-primary" type="submit">Return to Home</button>
		  	</form>';

			echo '<form method="get" action="..\employers\customerReceipts.php">';
			echo '<input type="hidden" name="reservation_id" value="' . $row["reservation_id"] . '">';
			echo '<input type="hidden" name="table_number" value="' . $row["table_number"] . '">';
			if (!empty($row["customer_id"])) {
				echo '<input type="hidden" name="customer_id" value="' . $row["customer_id"] . '">';
			} elseif (!empty($row["guest_id"])) {
				echo '<input type="hidden" name="guest_id" value="' . $row["guest_id"] . '">';
			}
			echo '<button class="btn btn-primary" type="submit">View Receipt</button>';
			echo '</form>';
		} else {
			echo '-';
		}
		?>


	</div>
</body>

</html>