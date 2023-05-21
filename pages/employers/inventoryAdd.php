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

$search = isset($_GET['search']) ? $_GET['search'] : '';
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'fullName';
$reset = isset($_GET['reset']) ? true : false;
if ($reset) {
  $search = '';
}

$errors = [];
if (isset($_POST['signup-submit'])) {
  // Validate the form data
  $errors = validateFormData();

  // If there are no errors, insert the data into the database
  if (empty($errors)) {
    // Get the form data
    $ingredient_name = $_POST['ingredient_name'];
    $inventory_stock = $_POST['inventory_stock'];
    $measure_unit = $_POST['measure_unit'];
    $supplier_name = $_POST['supplier_name'];
    $expiry_date = $_POST['expiry_date'];
    $category = $_POST['category'];


    if ($conn) {
      // Prepare and execute the SQL statement
      $sql = "INSERT INTO ingredients (ingredient_name, inventory_stock, measure_unit,supplier_name, expiry_date, category) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssssss", $ingredient_name, $inventory_stock, $measure_unit, $supplier_name, $expiry_date, $category);

      if ($stmt->execute()) {
        echo "Ingredient created successfully";
        header("Location: employer_inventory_items.php");
        exit();
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        var_dump($stmt->error);
      }
    } else {
      echo "Error: No database connection";
    }
    //} else {
    //if (!empty($errors)) {
    ///echo "<ul>";
    //foreach ($errors as $error) {
    //echo "<li>$error</li>";
    //}
    //echo "</ul>";
    //}
  }
}

//print_r($_POST);
?>


<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Ingredient Management</h1>
</header>

<body>

  <div class="container-signup">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <table>
        <h2>Add more ingredients</h2>
        <th>Ingredient Name</th>
        <th>Inventory Stock</th>
        <th>Measure Unit</th>
        <th>Supplier Name</th>
        <th>Expiry Date</th>
        <th>Category</th>
        <tr>
          <td><input class="input_signup" type="text" name="ingredient_name" value="<?php echo isset($_POST['ingredient_name']) ? $_POST['ingredient_name'] : ''; ?>"><br>
            <?php displayError("nameErr", $errors); ?> </td>

          <td><input class="input_signup" type="text" name="inventory_stock" value="<?php echo isset($_POST['inventory_stock']) ? $_POST['inventory_stock'] : ''; ?>"><br>
            <?php displayError("dobErr", $errors); ?></td>

          <td><input class="input_signup" type="text" name="measure_unit" value="<?php echo isset($_POST['measure_unit']) ? $_POST['measure_unit'] : ''; ?>"><br>
            <?php displayError("emailErr", $errors); ?></td>


          <td><select name="supplier_name">
              <option value="ABC Supplier">ABC Supplier</option>
              <option value="XYZ Supplier">XYZ Supplier</option>
              <option value="MNO Supplier">MNO Supplier</option>
              <option value="PQR Supplier">PQR Supplier</option>
              <option value="LMN Supplier">LMN Supplier</option>
            </select></td>

          <td><input class="input_signup" type="date" name="expiry_date" value="<?php echo isset($_POST['expiry_date']) ? $_POST['expiry_date'] : ''; ?>"><br>
            <?php displayError("telErr", $errors); ?> </td>
          <td><select name="category">
              <option value=""></option>
              <option value="Vegetables">Vegetables</option>
              <option value="Baking">Baking</option>
              <option value="Cooking">Cooking</option>
              <option value="Dairy">Dairy</option>
              <option value="Legumes">Legumes</option>
              <option value="Meat">Meat</option>
              <option value="Seafood">Seafood</option>
              <option value="Poultry">Poultry</option>
              <option value="Spices">Spices</option>
              <option value="Grains">Grains</option>
              <option value="Fruits">Fruits</option>
              <option value="Alcoholic Beverages">Alcoholic Beverages</option>
            </select></td>
        </tr>
      </table>
      <div class="button-container">
        <td><button class="signup_button" type="submit" name="signup-submit">Add Ingredient</button></td>
      </div>
    </form>
  </div>