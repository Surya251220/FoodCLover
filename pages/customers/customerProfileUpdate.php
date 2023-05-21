<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

if (isset($_POST['update'])) {

    $customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
    var_dump($customer_id);
    var_dump($_POST);
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $allergies_arr = isset($_POST['allergies']) ? $_POST['allergies'] : array();
    $allergies = implode(',', $allergies_arr);
    $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);


    $sql = "UPDATE customers SET full_name=?, email=?, phone=?, address=?, allergies=?, pwd=? WHERE customer_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $full_name, $email, $phone, $address, $allergies, $hashedPwd, $customer_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Record updated successfully.";
        header("Location: customer_profile.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}
var_dump($_POST);

if (isset($_POST['delete'])) {
    $customer_id = trim($_POST['customer_id']);
    // execute the delete query
    $sql = "DELETE FROM customers WHERE customer_id=$customer_id";
    if (mysqli_query($conn, $sql)) {
        echo "Record deleted successfully.";
        // redirect the user to the employer list page
        header("Location: customerSignup.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
if (isset($_GET['customer_id'])) {
    $customer_id = trim($_GET['customer_id']);
    $sql = "SELECT * FROM customers WHERE customer_id=$customer_id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
} else {
    echo "Customer ID parameter missing in URL.";
    exit();
}

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
    <form class="employerUpdate" action="customerProfileUpdate.php" method="POST">
        <h2>Update your details</h2>
        <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">

        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?php echo $row['full_name']; ?>" readonly><br>
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $row['phone']; ?>"><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
        <label>Password:</label>
        <input type="text" name="pwd" id="pwd" value="" required><br>
        <label>Address:</label>
        <textarea name="address" style="width: 250px; resize: vertical;"><?php echo $row['address']; ?></textarea><br>
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
        <input type="button" name="generatePwd" value="Generate Password" onclick="generatePassword()"><br>
    </form>
</body>

</html>