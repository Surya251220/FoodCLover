<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

if (!isset($_GET["token"])) {
    // Handle the case when token is not provided
    exit("Token not provided.");
}

$code = $_GET["token"];

if (isset($_POST["submit"])) {
    $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
    $pwdRepeat = isset($_POST['pwd-repeat']) ? $_POST['pwd-repeat'] : '';
    if (empty($pwd) || empty($pwdRepeat)) {
        header("Location: ../employers/password_reset_verify.php?token=" . urlencode($code) . "&error=empty_fields");
        exit();
    }
    // Password validation
    if (strlen($pwd) < 8) {
        header("Location: ../employers/password_reset_verify.php?token=" . urlencode($code) . "&error=weak_password");
        exit();
    }
    if ($pwd !== $pwdRepeat) {
        header("Location: ../employers/password_reset_verify.php?token=" . urlencode($code) . "&error=password_mismatch");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
    $getEmailQuery = $conn->prepare("SELECT customer_email FROM reset_password WHERE token=?");
    $getEmailQuery->bind_param("s", $code);
    $getEmailQuery->execute();
    $result = $getEmailQuery->get_result();

    if ($result->num_rows == 0) {
        // Handle the case when no matching email is found for the provided token
        exit("No matching email found for the token.");
    } else {
        $row = $result->fetch_assoc();
        $email = $row["customer_email"];

        $updateQuery = $conn->prepare("UPDATE customers SET pwd=? WHERE email=?");
        $updateQuery->bind_param("ss", $hashedPwd, $email);
        $updateQuery->execute();

        if ($updateQuery->affected_rows > 0) {
            echo "Record updated successfully.";
        } else {
            echo "Error updating record: " . $updateQuery->error;
        }

        $deleteQuery = $conn->prepare("DELETE FROM reset_password WHERE token=?");
        $deleteQuery->bind_param("s", $code);
        $deleteQuery->execute();

        if ($deleteQuery->affected_rows > 0) {
            // Password updated and token deleted successfully
            header("Location: ../customers/customerLogin.php");
            exit();
        } else {
            // Handle the case when the token deletion query fails
            echo "Failed to delete the token.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Password Reset</h1>
    <style>
        .error-message {
            color: Blue;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</header>

<body>

    <form class="form_login" method="POST">

        <h3 style="text-align: center;">Please enter your new password.</h3>
        <?php
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            if ($error === 'weak_password') {
                echo '<p class="error-message">Password is to weak</p>';
            } else if ($error === 'password_mismatch') {
                echo '<p class="error-message">Password dont Match.</p>';
            } else if ($error === 'empty_fields') {
                echo '<p class="error-message">Fil in all fields.</p>';
            }
        }
        ?>
        <input class="input_login" type="password" name="pwd" placeholder="New Password">
        <input class="input_login" type="password" name="pwd-repeat" placeholder="Repeat Password">
        <input class="form_button" type="submit" name="submit" value="Update Password">
    </form>

</body>

</html>