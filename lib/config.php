<?php
// Define database connection details
define('DB_HOST', 'localhost: 3306');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'foodclover');

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
date_default_timezone_set('Europe/London');

// Check connectionS
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());

} 
//{
  //echo "Connected to database successfully!";
//}

?>