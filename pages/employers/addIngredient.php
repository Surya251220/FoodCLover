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
mysqli_free_result($result1); // Free the result set before executing the next query

// Query 2
$sql = "SELECT DISTINCT delivery_frequency FROM delivery";
$result = mysqli_query($conn, $sql);
$delivery = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result); // Free the result set before executing the next query

// Query 3
$sql = "SELECT DISTINCT category FROM ingredients";
$result2 = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result2, MYSQLI_ASSOC);
mysqli_free_result($result2);

$errors = [];

if (isset($_POST['ingredientDelivery-submit'])) {
  // Validate the form data
  $errors = validateFormData();
  if (empty($errors)) {
    // Get the form data
    $supplier_name = $_POST['supplier_name'];
    $ingredient_name = $_POST['ingredient_name'];
    $delivery_date = $_POST['delivery_date'];
    $delivery_frequency = $_POST['delivery_frequency'];
    $quantity = $_POST['quantity'];
    $dMeasure_unit = $_POST['dMeasure_unit'];
    $price = $_POST['price'];
    $inventory_stock = $_POST['inventory_stock'];
    $imeasure_unit = $_POST['Imeasure_unit'];
    $expiry_date = $_POST['expiry_date'];
    $category = $_POST['category'];

    $sql = "SELECT supplier_id FROM suppliers WHERE supplier_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $supplier_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $supplier_id = mysqli_fetch_assoc($result)['supplier_id'];

    mysqli_free_result($result);
    mysqli_stmt_free_result($stmt);
    $new_category = $_POST['new_category'];

    if (empty($errors)) {
      if (!empty($new_category)) {
        // Insert the new category into the database
        $sql = "INSERT INTO ingredients (supplier_id, supplier_name, expiry_date, measure_unit, inventory_stock, ingredient_name, category) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssss", $supplier_id, $supplier_name, $expiry_date, $imeasure_unit, $inventory_stock, $ingredient_name, $new_category);
        if (mysqli_stmt_execute($stmt)) {
          // Set the category to the new category
          $category = $new_category;

          // Get the ingredient ID
          $ingredient_id = mysqli_insert_id($conn);

          // Insert data into the delivery table
          $sql = "INSERT INTO delivery (supplier_id, ingredient_id, delivery_date, delivery_frequency, quantity, price) VALUES (?, ?, ?, ?, ?, ?)";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "iisssd", $supplier_id, $ingredient_id, $delivery_date, $delivery_frequency, $quantity, $price);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);

          // Close connection
          mysqli_close($conn);
          header("Location: deliveryUpdate.php");
          exit();
        } else {
          $errors[] = 'Failed to add new category.';
        }
        mysqli_stmt_close($stmt);
      } else {
        // Insert data into the ingredients table
        $sql = "INSERT INTO ingredients (supplier_id, supplier_name, expiry_date, measure_unit, inventory_stock, ingredient_name, category) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssss", $supplier_id, $supplier_name, $expiry_date, $imeasure_unit, $inventory_stock, $ingredient_name, $category);
        mysqli_stmt_execute($stmt);

        // Get the ingredient ID
        $ingredient_id = mysqli_insert_id($conn);

        // Insert data into the delivery table
        $sql = "INSERT INTO delivery (supplier_id, ingredient_id, delivery_date, delivery_frequency, quantity, price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisssd", $supplier_id, $ingredient_id, $delivery_date, $delivery_frequency, $quantity, $price);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Close connection
        mysqli_close($conn);
        header("Location: deliveryUpdate.php");
        exit();
      }
    }
  }
}

?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Ingredient Management</h1>
</header>

<body>

  <form class="ingredientDelivery" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <div class="delivery-info">
      <h2>Delivery Info</h2>

      <input type="hidden" name="supplier_id">

      <label>Supplier Name:</label>
      <?php displayError("supplierErr", $errors); ?>

      <select name="supplier_name" value="<?php echo isset($_POST['supplier_name']) ? $_POST['supplier_name'] : ''; ?>">
        <option>Select</option>
        <?php foreach ($suppliers as $supplier) { ?>
          <option value="<?php echo $supplier['supplier_name']; ?>"><?php echo $supplier['supplier_name']; ?></option>
        <?php } ?>
      </select>

      <label>Delivery Date:</label>
      <?php displayError("delErr", $errors); ?><br>
      <input type="date" name="delivery_date">

      <label>Delivery Frequency:</label>
      <?php displayError("deliveryFreqErr", $errors); ?>
      <select name="delivery_frequency">
        <option>Select</option>
        <?php foreach ($delivery as $deliver) { ?>
          <option value="<?php echo $deliver['delivery_frequency']; ?>"><?php echo $deliver['delivery_frequency']; ?></option>
        <?php } ?>
      </select>


      <label>Delivery Quanity:</label>
      <?php displayError("quantityErr", $errors); ?>
      <input type="text" name="quantity">


      <label>Delivery Measure Unit:</label>
      <?php displayError("dMeasureErr", $errors); ?>
      <select id="dMeasure_unit" name="dMeasure_unit">
        <option value="Select">Select</option>
        <option value="kg">Kilograms</option>
        <option value="ltr">Litres</option>
      </select>


      <label>Delivery Price:</label>
      <?php displayError("pricekErr", $errors); ?>
      <input type="text" name="price">
    </div>
    <div class="ingredient-info">
      <h2>Ingredient Info</h2>
      <input type="hidden" name="ingredient_id">

      <label>Ingredient Name:</label>
      <?php displayError("ingredient_nErr", $errors); ?>
      <input type="text" name="ingredient_name">

      <label>Available Stock:</label>
      <?php displayError("stockErr", $errors); ?>
      <input type="text" name="inventory_stock">



      <label>Ingredient Measure Unit:</label>
      <input id="Imeasure_unit" type="text" name="Imeasure_unit" readonly>

      <script>
        const dMeasureUnit = document.getElementById("dMeasure_unit");
        const iMeasureUnit = document.getElementById("Imeasure_unit");

        dMeasureUnit.addEventListener("change", () => {
          if (dMeasureUnit.value === "kg") {
            iMeasureUnit.value = "Kilograms";
          } else if (dMeasureUnit.value === "ltr") {
            iMeasureUnit.value = "Litres";
          }
        });
      </script>

      <label>Expirey Date:</label>
      <?php displayError("expErr", $errors); ?><br>
      <input type="date" name="expiry_date">


      <label>Category:</label>
      <?php displayError("categoryErr", $errors); ?><br>

      <select name="category">
        <option value="" selected>Select</option>
        <?php foreach ($categories as $category) { ?>
          <option value="<?php echo $category['category']; ?>"><?php echo $category['category']; ?></option>
        <?php } ?>
        <option value="add_new_category" style="font-weight:bold;">Add New Category</option>
      </select>

      <div id="new-category-input" style="display:none;">
        <label>New Category:</label>
        <input type="text" name="new_category" value="<?php echo isset($_POST['new_category']) ? $_POST['new_category'] : ''; ?>">
      </div>
      <script>
        var selectEl = document.querySelector('select[name="category"]');
        var newCategoryInputEl = document.querySelector('#new-category-input');

        selectEl.addEventListener('change', function() {
          if (selectEl.value === 'add_new_category') {
            newCategoryInputEl.style.display = 'block';
          } else {
            newCategoryInputEl.style.display = 'none';
          }
        });
      </script>
      <div class="DI-Submit">
        <input type="submit" name="ingredientDelivery-submit" value="Add Ingredient and Delivery">
      </div>
    </div>

  </form>

</body>

</html>