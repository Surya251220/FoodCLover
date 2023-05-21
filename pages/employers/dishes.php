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
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'dish_name';
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
  <h1>Dishes</h1>
  <style>
    table {
      width: 99%;
      border-collapse: collapse;
      margin: 0 auto;
      /* Add this line to center the table horizontally */
      margin-bottom: 20px;
      margin-top: 20px;

      align-items: center;
    }

    table th,
    table td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ccc;
    }

    table th {
      background-color: #f2f2f2;
      font-weight: bold;
    }
  </style>
</header>

<body>

  <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar" onsubmit="trimSearch()">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <select name="criteria">
      <option value="dish_id" <?php if ($criteria === 'dish_id') {
                                echo ' selected';
                              } ?>>Delivery ID</option>
      <option value="dish_name" <?php if ($criteria === 'dish_name') {
                                  echo ' selected';
                                } ?>>Dish Name</option>
      <option value="category" <?php if ($criteria === 'category') {
                                  echo ' selected';
                                } ?>>Category</option>
    </select>
    <?php if (!empty($search)) { ?>
      <button type="submit" name="reset" value="1">Reset</button>
    <?php } else { ?>
      <button type="submit">Submit</button>
    <?php } ?>
    <input type="hidden" name="page" value="<?php echo $page; ?>">
  </form>

  <script>
    function trimSearch() {
      var searchInput = document.getElementById("search");
      searchInput.value = searchInput.value.trim();
    }
  </script>
  <div class="button-container" style="margin-top: 10px; margin-bottom: -20px;  ">
    <a href="addDish.php"><button class="signup_button">Add Dishes</button></a>
  </div>
  <?php
  // Define the SQL query
  $sql = "SELECT * FROM dishes";
  if (!empty($search)) {
    $sql = "SELECT * FROM dishes WHERE $criteria LIKE '%$search%'";
  }
  // Execute the query
  $result = $conn->query($sql);

  // Check for errors
  if (!$result) {
    die('Query failed: ' . $conn->error);
  }

  // Start the HTML table and form
  echo '<form method="post">';
  echo '<table>';
  echo '<thead><tr><th>ID</th><th>Dish Name</th><th>Description</th><th>Allergies</th><th style="width: 100px">Price</th><th>Category</th><th>Actions</th></thead>';
  echo '<tbody>';

  // Loop through the result set and display each row
  while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $row['dish_id'] . '</td>';
    echo '<td>' . $row['dish_name'] . '</td>';
    echo '<td>' . $row['description'] . '</td>';
    echo '<td>' . $row['allergies'] . '</td>';
    echo '<td>£ ' . $row['price'] . '</td>';
    echo '<td>' . $row['category'] . '</td>';
    echo '<td>';
    echo '<a href="dishesUpdate.php?dish_id=' . $row['dish_id'] . '">Update</a>';
    echo '</td>';
    echo '</tr>';
  }

  // End the HTML table and form
  echo '</tbody></table>';
  echo '</form>';

  // Close the database connection
  $conn->close();
  ?>
</body>

</html>