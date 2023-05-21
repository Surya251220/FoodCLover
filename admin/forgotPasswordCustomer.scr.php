<?php
include_once '../lib/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

if (isset($_POST['email'])) {
    $emailTo = $_POST['email'];

    // Check if the email exists in the customers table
    $emailExistsQuery = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $emailExistsQuery->bind_param("s", $emailTo);
    $emailExistsQuery->execute();
    $result = $emailExistsQuery->get_result();

    if ($result->num_rows == 0) {
        // Email does not exist in the customers table
        $errorParam = "invalid_email";
        header("Location: ..\pages\customers\customerForgotPwd.php?error=$errorParam");
        exit();
    }

    $code = uniqid(true);
    $sql = "INSERT INTO reset_password (token, customer_email) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $code, $emailTo);
    $stmt->execute();

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'foodclover.restaurant@gmail.com';
        $mail->Password = 'tdxvtvjvwvpvlhun';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('foodclover.restaurant@gmail.com', 'FoodClover');
        $mail->addAddress($emailTo);
        $mail->addReplyTo('no-reply@gmail.com', 'No reply');
        $url = "http://localhost/RestaurantManagementSystem/pages/employers/password_reset_verify.php?token=$code";
        $mail->isHTML(true);
        $mail->Subject = 'Here is your link to reset your password';
        $mail->Body = "<h1>You requested a password reset</h1>
                            Click <a href='$url'>this link</a> to reset the password. ";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo 'Email has been sent to reset your password.';

        // Execute your SQL queries or complete any database operations here

        header("Location: ..\pages\customers\customerLogin.php");
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
