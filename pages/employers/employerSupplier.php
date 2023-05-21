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
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'supplier_name';
$reset = isset($_GET['reset']) ? true : false;
if ($reset) {
  $search = '';
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['supplier_name']) && !empty($_POST['supplier_name'])) {
    $supplier_name = $_POST['supplier_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['mail'];
    $delivery_frequency = $_POST['delivery_frequency'];
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, address, phone, email, delivery_frequency) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $supplier_name, $address, $phone, $email, $delivery_frequency);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
      echo '<p>Supplier added successfully!</p>';
    } else {
      echo '<p>There was an error adding the supplier. Please try again.</p>';
    }
  }
  header('Location: employerSupplier.php');
  exit();
}
?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Suppliers Management</h1>
</header>

<body>
  <div class="container-signup">
    <form action="employerSupplier.php" method="post">
      <table>
        <h2>Add Supplier</h2>
        <th>Supplier Name</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Delivery Frequency</th>
        <tr>
          <td><input class="input_signup" type="text" name="supplier_name"></td>
          <td><textarea class='expandable' name='address' placeholder='Click to expand'></textarea></td>
          <td><input class="input_signup" type="tel" name="phone"></td>
          <td><input class="input_signup" type="email" name="mail"></td>
          <td><select name="delivery_frequency">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select></td>
        </tr>
      </table>
      <div class="button-container">
        <td><button class="signup_button" type="submit" name="signup-submit">Add Supplier</button></td>
      </div>
    </form>
  </div>
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
    echo "<tr><td>" . $row["supplier_id"] . "</td><td>" . $row["supplier_name"] . "</td><td><textarea class='expandable' name='address' placeholder='Click to expand'>" . $row['address'] . "</textarea></td><td>" . $row["phone"] . "</td><td>" . $row["email"] . "</td><td>" . $row["delivery_frequency"] . "</td><td><button onclick=\"location.href='supplierUpdate.php?supplier_id=" . $row['supplier_id'] . "'\">Edit / Delete</button></td></tr>";
  }
  echo "</table>";
  mysqli_close($conn);
  ?>
</body>

</html>