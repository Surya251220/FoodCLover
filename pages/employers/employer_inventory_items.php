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

$sql = "SELECT * FROM suppliers";
$result = mysqli_query($conn, $sql);
$suppliers = mysqli_fetch_all($result, MYSQLI_ASSOC);

$search = isset($_GET['search']) ? $_GET['search'] : '';
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'ingredient_name';
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
	<h1>Inventory: Ingredients</h1>
</header>

<body>


	<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar">
		<label for="search">Search:</label>
		<input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
		<select name="criteria">
			<option value="Ingredient_id" <?php if ($criteria === 'Ingredient_id') {
												echo ' selected';
											} ?>>Ingredient ID</option>
			<option value="Ingredient_name" <?php if ($criteria === 'Ingredient_name') {
												echo ' selected';
											} ?>>Ingredient Name</option>
			<option value="supplier_name" <?php if ($criteria === 'supplier_name') {
												echo ' selected';
											} ?>>Supplier Name</option>
			<option value="category" <?php if ($criteria === 'category') {
											echo ' selected';
										} ?>>Category</option>
			<option value="status" <?php if ($criteria === 'status') {
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
	<div class="button-container" style="margin-top: 10px; margin-bottom: -20px;  ">
		<button type="submit" class="signup_button" onclick="location.href='addIngredient.php'">Add Ingredient</button>
		<button type="submit" class="signup_button" onclick="location.href='noUseIngredient.php'">Not in Use Ingredients</button>
	</div>

	<?php

	// Select data from the ingredients table
	$sql = "SELECT ingredient_id, ingredient_name, inventory_stock, measure_unit, supplier_name, expiry_date, category, status, availability 
FROM ingredients 
WHERE availability = 'In use'
ORDER BY CASE WHEN status = 'urgent' THEN 0 ELSE 1 END, ingredient_id;
";

	if (!empty($search)) {
		$sql = "SELECT * FROM ingredients WHERE $criteria LIKE '%$search%'";
	}
	$result = mysqli_query($conn, $sql);

	// Display data in a table
	echo "<table>";
	echo "<tr><th>Ingredient ID</th><th>Ingredient Name</th><th>Inventory Stock</th><th>Measure Unit</th><th>Supplier Name</th><th>Expiry Date</th><th>Category</th><th>Availability</th><th>Status</th><th>Actions</th></tr>";
	while ($row = mysqli_fetch_assoc($result)) {
		echo "<tr>";
		echo "<td>" . $row["ingredient_id"] . "</td>";
		echo "<td>" . $row["ingredient_name"] . "</td>";
		echo "<td>" . $row["inventory_stock"] . "</td>";
		echo "<td>" . $row["measure_unit"] . "</td>";
		echo "<td>" . $row["supplier_name"] . "</td>";
		echo "<td>" . $row["expiry_date"] . "</td>";
		echo "<td>" . $row["category"] . "</td>";
		$statusStyle = "";
		if ($row["status"] == "Urgent") {
			$statusStyle = "background-color: red; color: white; font-weight: bold;";
		} elseif ($row["status"] == "Enough") {
			$statusStyle = "background-color: green; color: white;font-weight: bold;";
		}
		echo "<td style='$statusStyle'>" . $row["status"] . "</td>";
		echo "<td>" . $row["availability"] . "</td>";
		echo "<td>";

		echo "<form method='post' action='inventoryUpdate.php?ingredient_id=" . $row["ingredient_id"] . "'>";
		echo "<input type='hidden' name='ingredient_id' value='" . $row["ingredient_id"] . "'/>";
		echo "<button style='margin-top: 10px' class='signup_button'>Update</button>";
		echo "</form>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	// Close the database connection
	mysqli_close($conn);

	?>
</body>

</html>