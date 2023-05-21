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


if (isset($_POST['update'])) {

  $ingredient_id = trim($_POST['ingredient_id']);
  $ingredient_name = $_POST['ingredient_name'];
  $inventory_stock = $_POST['inventory_stock'];
  $measure_unit = $_POST['measure_unit'];
  $supplier_id = $_POST['supplier_id'];
  $supplier_name = $_POST['supplier_name'];
  $expiry_date = $_POST['expiry_date'];
  $availability = $_POST['availability'];
  $sql = "UPDATE ingredients SET ingredient_name='$ingredient_name', inventory_stock='$inventory_stock', measure_unit='$measure_unit', supplier_id='$supplier_id', supplier_name='$supplier_name',expiry_date='$expiry_date', availability= '$availability'  WHERE ingredient_id=$ingredient_id";
  if (mysqli_query($conn, $sql)) {
    echo "Record updated successfully.";
    header("Location: employer_inventory_items.php");
    exit();
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
}

$ingredient_id = trim($_GET['ingredient_id']);
$sql = "SELECT * FROM ingredients WHERE ingredient_id=$ingredient_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Ingredient Management</h1>
</header>

<body>
  <form class="ingredientUpdate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <h2>Update Ingredient Form</h2>
    <input type="hidden" name="ingredient_id" value="<?php echo $row['ingredient_id']; ?>">
    <label>Ingredient Name:</label>
    <input type="text" name="ingredient_name" value="<?php echo $row['ingredient_name']; ?>"><br>
    <label>Inventory Stock:</label>
    <input type="text" name="inventory_stock" value="<?php echo $row['inventory_stock']; ?>"><br>
    <label>Measure Unit:</label>
    <input type="text" name="measure_unit" value="<?php echo $row['measure_unit']; ?>"><br>
    <input type="hidden" name="supplier_id" value="<?php echo $row['supplier_id']; ?>"><br>
    <label>Supplier Name:</label>
    <select name="supplier_name">
      <option value="ABC Supplier" <?php if ($row['supplier_name'] == 'ABC Supplier') echo 'selected'; ?>>ABC Supplier</option>
      <option value="XYZ Supplier" <?php if ($row['supplier_name'] == 'XYZ Supplier') echo 'selected'; ?>>XYZ Supplier</option>
      <option value="MNO Supplier" <?php if ($row['supplier_name'] == 'MNO Supplier') echo 'selected'; ?>>MNO Supplier</option>
      <option value="PQR Supplier" <?php if ($row['supplier_name'] == 'PQR Supplier') echo 'selected'; ?>>PQR Supplier</option>
      <option value="LMN Supplier" <?php if ($row['supplier_name'] == 'LMN Supplier') echo 'selected'; ?>>LMN Supplier</option>
    </select><br>
    <label>Expirey Date:</label>
    <input type="date" name="expiry_date" value="<?php echo $row['expiry_date']; ?>"><br>
    <label>Status:</label>
    <select name="availability">
      <option value="In Use" <?php if ($row['availability'] == 'In Use') echo 'selected'; ?>>In Use</option>
      <option value="Not in Use" <?php if ($row['availability'] == 'Not in Use') echo 'selected'; ?>>Not in Use</option>
    </select><br>
    <input type="submit" name="update" value="Update">
  </form>
</body>

</html>