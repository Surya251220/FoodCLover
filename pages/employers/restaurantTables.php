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
?>

<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Restaurant Tables</h1>
  <style>
    table {
      width: 40%;
      border-collapse: collapse;
      margin: 0 auto;
      /* Add this line to center the table horizontally */
      margin-bottom: 20px;
      margin-top: 50px;

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


    .new_table_btn {
      padding: 10px 20px;
      margin: 20px auto;
      /* Center the button horizontally */
      display: block;
      /* Set the button as a block-level element */
      background-color: #000;
      color: #fff;
      border-radius: 5px;
      text-decoration: none;
      transition: all 0.2s ease-in-out;
      text-align: center;
      font-size: 16px;
    }

    .new_table_btn:hover {
      background-color: #fff;
      color: #000;
      border: 2px solid #000;
    }
  </style>
</header>

<body>
  <?php

  if (isset($_POST['add_table'])) {
    // Get the next table number
    $next_table_number = 1;
    $result = mysqli_query($conn, "SELECT MAX(table_number) AS max_table_number FROM restaurant_tables");
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      $next_table_number = $row['max_table_number'] + 1;
    }
    $verification_code = rand(1000, 9999);
    $sql = "INSERT INTO restaurant_tables (table_number, verification_code) VALUES ('$next_table_number', '$verification_code')";
    mysqli_query($conn, $sql);
  }


  // Handle deleting a table
  if (isset($_POST['delete_table'])) {
    $table_number = $_POST['delete_table'];
    $sql = "DELETE FROM restaurant_tables WHERE table_number = '$table_number'";
    mysqli_query($conn, $sql);
  }

  // Display the table of restaurant tables
  $sql = "SELECT * FROM restaurant_tables";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>Table Number</th><th>Verification Code</th><th>Action</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td>" . $row["table_number"] . "</td>";
      echo "<td>" . $row["verification_code"] . "</td>";
      echo "<td><form method='post' onsubmit=\"return confirm('Are you sure you want to delete table number " . $row["table_number"] . "?');\"><button type='submit' name='delete_table' value='" . $row["table_number"] . "'>Delete</button></form></td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "No tables found.";
  }

  // Display the form to add a new table
  echo "<form method='post'>";
  echo "<button class='new_table_btn' type='submit' name='add_table'>Add New Table</button>";
  echo "</form>";

  mysqli_close($conn);
  ?>

</body>

</html>