<?php
include_once '../lib/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

// Start the session
session_start();

if (isset($_POST['generate-submit'])) {
    // Get the email from the session
    if (isset($_SESSION['email'])) {
        $emailTo = $_SESSION['email'];
    } else {
        // Email not available in the session
        $errorParam = "missing_email";
        header("Location: ..\pages\employers\employersTwoStep.php?error=$errorParam");
        exit();
    }

    // Check if the email exists in the employers table
    $emailExistsQuery = $conn->prepare("SELECT * FROM employers WHERE email = ?");
    $emailExistsQuery->bind_param("s", $emailTo);
    $emailExistsQuery->execute();
    $result = $emailExistsQuery->get_result();

    if ($result->num_rows == 0) {
        // Email does not exist in the employers table
        $errorParam = "invalid_email";
        header("Location: ..\pages\employers\employersTwoStep.php?error=$errorParam");
        exit();
    }

    $code = generateRandomCode(5);

    // Update the existing row with the 2-Step verification code
    $updateCodeQuery = $conn->prepare("UPDATE employers SET two_step_verification = ? WHERE email = ?");
    $updateCodeQuery->bind_param("ss", $code, $emailTo);
    $updateCodeQuery->execute();

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'foodclover.restaurant@gmail.com';
        $mail->Password = 'zetbgbcmoguzhbzd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('foodclover.restaurant@gmail.com', 'FoodClover');
        $mail->addAddress($emailTo);
        $mail->addReplyTo('no-reply@gmail.com', 'No reply');
        $mail->isHTML(true);
        $mail->Subject = 'Here is your 2-Step verification code.';
        $mail->Body = "You requested a 2-Step verification:
                        <h1>$code</h1>";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo 'Email has been sent with the 2-Step verification code.';

        // Redirect to the previous page
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else if (isset($_POST['verify-submit'])) {
    if (isset($_SESSION['email'])) {
        $verificationCode = $_POST['verification_code'];

        $sql = "SELECT * FROM employers WHERE email=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../pages/employers/employersTwoStep.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $storedCode = $row['two_step_verification'];

                if ($verificationCode == $storedCode) {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['fullName'] = $row['fullName'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['usertype'] = $row['usertype'];
                    header("Location: ../pages/employers/employer_home.php?error=success");
                    exit();
                } else {
                    header("Location: ../pages/employers/employersTwoStep.php?error=incorrectcode");
                    exit();
                }
            } else {
                header("Location: ../pages/employers/employersTwoStep.php?error=nouser");
                exit();
            }
        }
    } else {
        header("Location: ../pages/customers/customer_menu.php");
        exit();
    }
} else {
    // Generate button not submitted
    // Redirect or display an error message as needed
}

/**
 * Generates a random code with the specified length using only capital letters and alphanumeric characters.
 *
 * @param int $length The length of the code
 * @return string The generated code
 */
function generateRandomCode($length) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    $maxIndex = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, $maxIndex)];
    }
    
    return $code;
}
