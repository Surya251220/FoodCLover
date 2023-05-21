<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

// Check if the user is logged in as a guest
if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'guest') {
    // User is logged in as a guest, redirect them to the appropriate page
    header("Location: ../../pages/customers/customer_menu.php");
    exit();
}

// Check if the form was submitted
if (isset($_POST['signup-submit'])) {
    // Get the form data
    $full_name = $_POST['FN'];
    $email = $_POST['mail'];
    $phone = $_POST['tel'];

    // Validate the form data (you can add more validation rules here)
    if (empty($full_name) || empty($email)) {
        $error_message = "Please fill in the required fields.";
    } else {
        // Prepare and execute the SQL statement with prepared statements
        $sql = "INSERT INTO guests (name, email, phone_number, verification_number) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $full_name, $email, $phone, $verification_number);

        // Generate a random verification number (4 characters, 2 digits)
        $verification_number = substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 4)), 0, 4);
        $verification_number .= rand(10, 99);

        if ($stmt->execute()) {
            // Redirect to guestCon.php with the guest's name and verification number in the URL
            header("Location: guestCon.php?name=" . urlencode($full_name) . "&verification_number=" . urlencode($verification_number));
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Guest Signup</h1>
</header>

<body>

    <form class="form_signup" method="POST">
        <label for="FN">Full Name:</label>
        <input class="input_signup" type="text" id="FN" name="FN" required><br><br>
        <label for="mail">Email:</label>
        <input class="input_signup" type="email" id="mail" name="mail" required><br><br>
        <label for="tel">Phone:</label>
        <input class="input_signup" type="tel" id="tel" name="tel"><br><br>
        <?php if (isset($error_message)) { ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php } ?>
        <button class="form_button" type="submit" name="signup-submit">Sign Up</button>
    </form>