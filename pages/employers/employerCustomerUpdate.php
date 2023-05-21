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

    $customer_id = trim($_POST['customer_id']);
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $allergies_arr = isset($_POST['allergies']) ? $_POST['allergies'] : array();
    $allergies = implode(',', $allergies_arr);
    $age = $_POST['age'];
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
    $sql = "UPDATE customers SET full_name='$fullName', email='$email', pwd='$hashedPwd', phone='$phone', address='$address', allergies='$allergies' WHERE customer_id=$customer_id";

    if (mysqli_query($conn, $sql)) {
        echo "Record updated successfully.";
        header("Location: employerCustomer.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
if (isset($_POST['delete'])) {
    $customer_id = trim($_POST['customer_id']);
    // execute the delete query
    $sql = "DELETE FROM customers WHERE customer_id=$customer_id";
    if (mysqli_query($conn, $sql)) {
        echo "Record deleted successfully.";
        // redirect the user to the employer list page
        header("Location: employerCustomer.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
$customer_id = trim($_GET['customer_id']);
$sql = "SELECT * FROM customers WHERE customer_id=$customer_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Staff Management</h1>
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
    <form class="employerUpdate" action="employerCustomerUpdate.php" method="POST">
        <h2>Update / Delete Staff Form</h2>
        <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>" required>
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?php echo $row['full_name']; ?>" required><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $row['email']; ?>" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" required><br>
        <label>Password:</label>
        <input type="text" name="pwd" id="pwd" value=""><br>
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $row['phone']; ?>" required><br>
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo $row['address']; ?>" required><br>
        <label>Allergies:</label><br>
        <label><input type="checkbox" name="allergies[]" value="celery" <?php if (strpos($row['allergies'], 'celery') !== false) echo 'checked'; ?>>Celery</label>
        <label><input type="checkbox" name="allergies[]" value="gluten" <?php if (strpos($row['allergies'], 'gluten') !== false) echo 'checked'; ?>>Gluten</label>
        <label><input type="checkbox" name="allergies[]" value="eggs" <?php if (strpos($row['allergies'], 'eggs') !== false) echo 'checked'; ?>>Eggs</label><br>
        <label><input type="checkbox" name="allergies[]" value="fish" <?php if (strpos($row['allergies'], 'fish') !== false) echo 'checked'; ?>>Fish</label>
        <label><input type="checkbox" name="allergies[]" value="lupin" <?php if (strpos($row['allergies'], 'lupin') !== false) echo 'checked'; ?>>Lupin</label>
        <label><input type="checkbox" name="allergies[]" value="milk" <?php if (strpos($row['allergies'], 'milk') !== false) echo 'checked'; ?>>Milk</label><br>
        <label><input type="checkbox" name="allergies[]" value="molluscs" <?php if (strpos($row['allergies'], 'molluscs') !== false) echo 'checked'; ?>>Molluscs</label>
        <label><input type="checkbox" name="allergies[]" value="mustard" <?php if (strpos($row['allergies'], 'mustard') !== false) echo 'checked'; ?>>Mustard</label>
        <label><input type="checkbox" name="allergies[]" value="peanuts" <?php if (strpos($row['allergies'], 'peanuts') !== false) echo 'checked'; ?>>Peanuts</label><br>
        <label><input type="checkbox" name="allergies[]" value="sesame" <?php if (strpos($row['allergies'], 'sesame') !== false) echo 'checked'; ?>>Sesame</label>
        <label><input type="checkbox" name="allergies[]" value="soybeans" <?php if (strpos($row['allergies'], 'soybeans') !== false) echo 'checked'; ?>>Soybeans</label>
        <label><input type="checkbox" name="allergies[]" value="sulfites" <?php if (strpos($row['allergies'], 'sulfites') !== false) echo 'checked'; ?>>Sulfites</label>
        <label><input type="checkbox" name="allergies[]" value="sulphites" <?php if (strpos($row['allergies'], 'sulphites') !== false) echo 'checked'; ?>>Sulphites</label>
        <label><input type="checkbox" name="allergies[]" value="tree nuts" <?php if (strpos($row['allergies'], 'tree nuts') !== false) echo 'checked'; ?>>Tree Nuts</label><br>
        <input type="submit" name="update" value="Update">
        <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
        <input type="button" name="generatePwd" value="Generate Password" onclick="generatePassword()"><br>
    </form>
</body>

</html>