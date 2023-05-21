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
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'full_name';
$reset = isset($_GET['reset']) ? true : false;
if ($reset) {
  $search = '';
}
$tableNumber = $_GET['table_number'];
?>
<!DOCTYPE html>
<html>
<header>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <?php
  $customers_query = "SELECT customer_id, full_name, phone, email FROM customers";
  $customers_result = mysqli_query($conn, $customers_query);
  if (isset($_GET['table_number'])) {
    $table_number = $_GET['table_number'];
    echo "<h1>Create a Booking for Table $table_number</h1>";
  }
  ?>
</header>

<body>

  <form method="post">
    <table>
      <tr>
        <th>Guest Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>Table Number</th>
        <th>Number Guests</th>
        <th>Reserve</th>
      </tr>
      <tr>
        <td><input type="text" name="name"></td>
        <td><input type="email" name="email"></td>
        <td><input type="tel" name="phone_number"></td>
        <td><?php echo $table_number; ?></td>
        <td>
          <select name="num_guests">
            <?php
            // Display options for number of guests
            for ($i = 1; $i <= 6; $i++) {
              echo "<option value=\"$i\">$i</option>";
            }
            ?>
          </select>
        </td>
        <td colspan="2"><input type="submit" name="guest_reserve" value="Reserve"></td>
      </tr>
    </table>
  </form>
  <?php

  $verification_number = substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 4)), 0, 4);
  $verification_number .= rand(10, 99);

  if (isset($_POST['guest_reserve'])) {
    // Get input values from form
    $guest_name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $table_number = $_GET['table_number'];
    $num_guests = $_POST['num_guests'];

    // Insert guest information into guests table
    $guest_query = "INSERT INTO guests (name, email, phone_number, verification_number) VALUES ('$guest_name', '$email', '$phone_number', '$verification_number')";
    mysqli_query($conn, $guest_query);

    // Get guest ID from guests table
    $guest_id = mysqli_insert_id($conn);

    // Insert reservation into reservations table
    $reservation_time = date('H:i:s');
    $reservation_date = date('Y-m-d');
    $reservation_query = "INSERT INTO reservations (guest_id, table_number, num_guests, reservation_time, reservation_date, status) VALUES ('$guest_id', '$table_number', '$num_guests', '$reservation_time', '$reservation_date', 'Arrived')";
    mysqli_query($conn, $reservation_query);

    // Display success message
    echo "Reservation added successfully!";
    header("Location: floorPlan.php"); // redirect to floorPlan.php
    exit(); // terminate the script to ensure no more output is sent to the browser
  }

  ?>

  <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar">

    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <select name="criteria">
      <option value="customer_id" <?php if ($criteria === 'customer_id') {
                                    echo ' selected';
                                  } ?>>Customer ID</option>
      <option value="email" <?php if ($criteria === 'email') {
                              echo ' selected';
                            } ?>>Email</option>
      <option value="full_name" <?php if ($criteria === 'full_name') {
                                  echo ' selected';
                                } ?>>Full Name</option>
    </select>
    <?php if (!empty($search)) { ?>
      <button type="submit" name="reset" value="1">Reset</button>
    <?php } else { ?>
      <button type="submit">Submit</button>
    <?php } ?>
    <input type="hidden" name="table_number" value="<?php echo $table_number; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
  </form>

  <?php
  $customers_query = "SELECT customer_id, full_name, phone, email FROM customers";
  $customers_result = mysqli_query($conn, $customers_query);
  if (isset($_GET['table_number'])) {
    $table_number = $_GET['table_number'];
  }
  ?>

  <body>
    <form method="post">
      <table>
        <tr>
          <th>Customer ID</th>
          <th>Customer Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Table Number</th>
          <th>Number Guests</th>
          <th>Reserve</th>
        </tr>
        <?php
        $sql = "SELECT * FROM customers";
        if (!empty($search)) {
          $sql .= " WHERE $criteria LIKE '%$search%'";
        }
        $customers_result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($customers_result)) {
          $customer_id = $row['customer_id'];
          $customer_name = $row['full_name'];
          $phone = $row['phone'];
          $email = $row['email'];
          $table_number = $_GET['table_number'];
        ?>
          <tr>
            <td><?php echo $customer_id; ?></td>
            <td><?php echo $customer_name; ?></td>
            <td><?php echo $phone; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo $table_number; ?></td>
            <td>
              <select name="num_guests">
                <?php
                // Display options for number of guests
                for ($i = 1; $i <= 6; $i++) {
                  echo "<option value=\"$i\">$i</option>";
                }
                ?>
              </select>
            </td>
            <td>
              <button type="submit" name="reserve" value="<?php echo $customer_id; ?>">Reserve</button>
            </td>
          </tr>
        <?php } ?>
      </table>
    </form>
    <?php
    if (isset($_POST['reserve'])) {
      $customer_id = $_POST['reserve'];
      $table_number = $_GET['table_number'];
      $num_guests = $_POST['num_guests'];
      $reservation_time = date('H:i:s', time());
      $reservation_date = date('Y-m-d', time());
      $reservation_query = "INSERT INTO reservations (customer_id, num_guests, table_number, reservation_time, reservation_date, status) 
                      VALUES ('$customer_id', '$num_guests', '$table_number', '$reservation_time', '$reservation_date', 'arrived')";
      mysqli_query($conn, $reservation_query);
      header("Location: floorPlan.php"); // redirect to floorPlan.php
      exit(); // terminate the script to ensure no more output is sent to the browser
    }
    ?>
    </table>
    </form>
  </body>

</html>