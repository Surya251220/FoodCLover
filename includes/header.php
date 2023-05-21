<?php
	include_once '..\..\lib\config.php';
  session_start();
  date_default_timezone_set('Europe/London');

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">

<nav>
  <div class="left-links">
  <ul>
      <li><a href="..\..\pages\customers\customer_menu.php">Menu</a></li>

    <?php
    if(isset($_SESSION['usertype']) && ($_SESSION['usertype'] == 'customer' || $_SESSION['usertype'] == 'guest') || !isset($_SESSION['usertype'])) {
      // show "Book a Table" link for customers, guests, and users who are not logged in
      echo '<li><a href="..\..\pages\customers\bookTable.php">Book a Table</a></li>';
    }
    ?>

      <li><a href="..\..\pages\customers\about_us.php">About Us</a></li>
      <li><a href="..\..\pages\customers\find_us.php">Find Us</a></li>
  </ul>
  </div>
  <div class="center-logo">
    <img src="..\..\assets\images\title_foodClover.png" alt="Logo">
  </div>
  <div class="right-links">
    <ul>

 <?php

if(isset($_SESSION['email']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'employer') {
  // employer is logged in, show the logout link, dashboard link, and profile link
     echo '<li><a href="..\..\pages\employers\employerProfile.php">Profile</a></li>';
     echo '<li><a href="..\..\pages\employers\employer_home.php">Dashboard</a></li>';
     echo '<li><a class="login" href="..\..\admin\logout.scr.php">Logout</a></li>';

} else if(isset($_SESSION['usertype']) && ($_SESSION['usertype'] == 'guest')) {
  // guest is logged in, show the logout link but no profile or dashboard link
  echo '<li><a class="login" href="..\..\admin\logout.scr.php">Logout</a></li>';
} else if(isset($_SESSION['usertype']) && ($_SESSION['usertype'] == 'customer')) {
  // customer is logged in, show the profile link and logout link but no dashboard link
  echo '<li><a href="..\..\pages\customers\customer_profile.php">Profile</a></li>';
  echo '<li><a class="login" href="..\..\admin\logout.scr.php">Logout</a></li>';
} else {
  // no one is logged in, show all links
  echo '<li><a class="login" href="..\..\pages\customers\customerLogin.php">Customer Signup / Login</a></li>';
  echo '<li><a class="login" href="..\..\pages\employers\employerLogin.php">Employer Login</a></li>';
}


 ?>
 
          
    </ul>
  </div>
</nav>
</head>
</html>
