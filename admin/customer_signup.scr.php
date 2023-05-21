<?php
include_once '../lib/config.php';

if (isset($_POST['signup-submit'])) {
    // Get the form data
    $full_name = $_POST['FN'];
    $email = $_POST['mail'];
    $password = $_POST['pwd'];
    $pwd_repeat = $_POST['pwd-repeat'];
    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    $phone = $_POST['tel'];
    $address = $_POST['address'];
    $allergies_arr = isset($_POST['allergies']) ? $_POST['allergies'] : array();
    $allergies = implode(',', $allergies_arr);
    $dob = $_POST['age'];

    // Validate form data
    if (empty($full_name) || empty($email) || empty($password) || empty($pwd_repeat) || empty($dob)) {
        header("Location: ../pages/customers/customerSignup.php?error=emptyfields");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^([a-zA-Z' ]+)$/", $full_name)) {
        header("Location: ../pages/customers/customerSignup.php?error=invalidmailFN");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../pages/customers/customerSignup.php?error=invalidmail");
        exit();
    } elseif (!preg_match("/^([a-zA-Z' ]+)$/", $full_name)) {
        header("Location: ../pages/customers/customerSignup.php?error=invalidFN");
        exit();
    } elseif ($password !== $pwd_repeat) {
        header("Location: ../pages/customers/customerSignup.php?error=passwordcheck");
        exit();
    } elseif (!preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
        header("Location: ../pages/customers/customerSignup.php?error=invalidpwd");
        exit();
    } else {
        // Check if email is already taken
        $sql = "SELECT email FROM customers WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $resultCheck = $stmt->num_rows;
        if ($resultCheck > 0) {
            header("Location: ../pages/customers/customerSignup.php?error=emailtaken");
            exit();
        } else {
            // Calculate age based on date of birth
            $today = new DateTime();
            $birthdate = DateTime::createFromFormat('Y-m-d', $dob);
            $formatted_birthdate = $birthdate->format('Y-m-d');
            $age = $today->diff($birthdate)->y;

            // Validate age (minimum 16 years old)
            if ($age < 16) {
                header("Location: ../pages/customers/customerSignup.php?error=invalidage");
                exit();
            }

            // Insert data into database using prepared statement
            $sql = "INSERT INTO customers (full_name, email, pwd, phone, address, allergies, age) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $full_name, $email, $hashedPwd, $phone, $address, $allergies, $formatted_birthdate);
            $stmt->execute();
            header("Location: ../pages/customers/customerLogin.php?signup=success");
            exit();
        }
    }
} else {
    header("Location: ../pages/customers/customerLogin.php");
    exit();
}
