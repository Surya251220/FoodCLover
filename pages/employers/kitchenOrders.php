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


// Retrieve order data from the database
$sql = "SELECT * FROM orders WHERE kitchen_status = 'new' ORDER BY priority DESC, created_at ASC";
$result = mysqli_query($conn, $sql);

// Create an array to store the orders for each table
$orders_by_table = array();

// Loop through each order and add it to the corresponding table's array
while ($row = mysqli_fetch_assoc($result)) {
   $table_number = $row['table_number'];
   $orders_by_table[$table_number][] = $row;
}

// Loop through all possible table numbers and check if there are any orders for that table number
$max_table_number = 10; // change this to the maximum table number
for ($i = 1; $i <= $max_table_number; $i++) {
   if (!isset($orders_by_table[$i])) {
      // If there are no orders for this table, add an empty array
      $orders_by_table[$i] = array();
   }
}
?>

<!DOCTYPE html>
<html>

<head>
   <title>Kitchen Dashboard</title>
   <style>
      /* Define CSS styles here */
   </style>
</head>

<body>
   <h1>Kitchen Dashboard</h1>
   <?php foreach ($orders_by_table as $table_number => $table_orders) : ?>
      <h2>Table <?php echo $table_number; ?></h2>
      <table>
         <thead>
            <tr>
               <th>Order #</th>
               <th>Time Placed</th>
               <th>Dish</th>
               <th>Quanity</th>
               <th>Notes</th>
               <th>Time Due</th>
               <th>Priority</th>
               <th>Status</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($table_orders as $row) : ?>
               <tr>
                  <td><?php echo $row['order_id']; ?></td>
                  <td><?php echo date('H:i:s', strtotime($row['created_at'])); ?></td>
                  <td><?php echo $row['dish_id']; ?></td>
                  <td><?php echo $row['quantity']; ?></td>
                  <td><?php echo $row['notes']; ?></td>
                  <td><?php echo $row['time_due']; ?></td>
                  <td><?php echo $row['priority']; ?></td>
                  <td>
                     <?php if ($row['kitchen_status'] == 'new') : ?>
                        <span class="kitchen_status new">New</span>
                     <?php elseif ($row['kitchen_status'] == 'in_progress') : ?>
                        <span class="kitchen_status in-progress">In Progress</span>
                     <?php elseif ($row['kitchen_status'] == 'complete') : ?>
                        <span class="kitchen_status complete">Complete</span>
                     <?php endif; ?>
                  </td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
   <?php endforeach; ?>
</body>

</html>