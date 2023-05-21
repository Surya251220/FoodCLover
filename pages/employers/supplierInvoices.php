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

<body>
  <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <select name="criteria">
      <option value="supplier_id" <?php if ($criteria === 'supplier_id') {
                                    echo ' selected';
                                  } ?>>Supplier ID</option>
      <option value="supplier_name" <?php if ($criteria === 'supplier_name') {
                                      echo ' selected';
                                    } ?>>Supplier Name</option>
      <option value="delivery_frequency" <?php if ($criteria === 'delivery_frequency') {
                                            echo ' selected';
                                          } ?>>Delivery Frequency</option>
    </select>
    <?php if (!empty($search)) { ?>
      <button type="submit" name="reset" value="1">Reset</button>
    <?php } else { ?>
      <button type="submit">Submit</button>
    <?php } ?>
    <input type="hidden" name="page" value="<?php echo $page; ?>">

  </form>
  <?php
  $sql = "SELECT supplier_id, supplier_name, address, phone, email, delivery_frequency FROM suppliers";
  if (!empty($search)) {
    $sql .= " WHERE $criteria LIKE '%$search%'";
  }
  $result = mysqli_query($conn, $sql);
  echo "<table>";
  echo "<tr><th>Supplier ID</th><th>Supplier Name</th><th>Address</th><th>Phone</th><th>Email</th><th>Delivery Frequency</th><th>Actions</th></tr>";
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>" . $row["supplier_id"] . "</td><td>" . $row["supplier_name"] . "</td><td><textarea class='expandable' name='address' placeholder='Click to expand'>" . $row['address'] . "</textarea></td><td>" . $row["phone"] . "</td><td>" . $row["email"] . "</td><td>" . $row["delivery_frequency"] . "</td><td><button onclick=\"location.href='supInvoices.php?supplier_id=" . $row['supplier_id'] . "'\">Combined Invoices</button></td></tr>";
  }
  echo "</table>";
  mysqli_close($conn);
  ?>
</body>

</html>