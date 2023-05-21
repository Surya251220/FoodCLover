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


if (isset($_POST['update'])) {

    $id = trim($_POST['id']);
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $pwd = $_POST['pwd'];
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
    $sql = "UPDATE employers SET fullName='$fullName', email='$email', phone='$phone', address='$address', role='$role', pwd='$hashedPwd' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        echo "Record updated successfully.";
        header("Location: employerProfile.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
if (isset($_POST['delete'])) {
    $id = trim($_POST['id']);
    // execute the delete query
    $sql = "DELETE FROM employers WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        echo "Record deleted successfully.";
        // redirect the user to the employer list page
        header("Location: employerSignup.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
$id = trim($_GET['id']);
$sql = "SELECT * FROM employers WHERE id=$id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">

    <h1>Profile</h1>
    <script>
        function generatePassword() {
            var length = 8;
            var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+<>?:{}|~";
            var password = "";
            for (var i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            document.getElementById("pwd").value = password;
        }
    </script>
</header>

<body>
    <form class="employerUpdate" action="employerUpdate.php" method="POST">
        <h2>Update your details</h2>
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <label>Full Name:</label>
        <input type="text" name="fullName" value="<?php echo $row['fullName']; ?>" readonly><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
        <label>Password:</label>
        <input type="text" name="pwd" id="pwd" value="" required><br>
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $row['phone']; ?>"><br>
        <label>Address:</label>
        <textarea name="address" style="width: 250px; resize: vertical;"><?php echo $row['address']; ?></textarea><br>

        <input type="submit" name="update" value="Update">
        <input type="button" name="generatePwd" value="Generate Password" onclick="generatePassword()"><br>
    </form>
</body>

</html>