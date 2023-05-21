<?php
/* This PHP code will end the session meaning the user will be logged out of his account*/
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../pages/customers/customer_menu.php");

?>