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

    $supplier_id = trim($_POST['supplier_id']);
    $supplier_name = $_POST['supplier_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $delivery_frequency = $_POST['delivery_frequency'];
    $sql = "UPDATE suppliers SET supplier_name='$supplier_name', address='$address', phone='$phone', email='$email', delivery_frequency='$delivery_frequency' WHERE supplier_id=$supplier_id";
    if (mysqli_query($conn, $sql)) {
        echo "Record updated successfully.";
        header("Location: employerSupplier.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
if (isset($_POST['delete'])) {
    $supplier_id = trim($_POST['supplier_id']);
    // execute the delete query
    $sql = "DELETE FROM suppliers WHERE supplier_id=$supplier_id";
    if (mysqli_query($conn, $sql)) {
        echo "Record deleted successfully.";
        // redirect the user to the employer list page
        header("Location: employerSupplier.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
$supplier_id = trim($_GET['supplier_id']);
$sql = "SELECT * FROM suppliers WHERE supplier_id=$supplier_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Staff Management</h1>
</header>

<body>
    <form class="employerUpdate" action="supplierUpdate.php" method="POST">
        <h2>Update / Delete Supplier Form</h2>
        <input type="hidden" name="supplier_id" value="<?php echo $row['supplier_id']; ?>">
        <label>Supplier Name:</label>
        <input type="text" name="supplier_name" value="<?php echo $row['supplier_name']; ?>"><br>
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo $row['address']; ?>"><br>
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $row['phone']; ?>"><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
        <label>Delivery Frequency:</label>
        <select name="delivery_frequency">
            <option value="daily" <?php if ($row['delivery_frequency'] == 'daily') echo 'selected'; ?>>Daily</option>
            <option value="weekly" <?php if ($row['delivery_frequency'] == 'weekly') echo 'selected'; ?>>Weekly</option>
            <option value="monthly" <?php if ($row['delivery_frequency'] == 'monthly') echo 'selected'; ?>>Monthly</option>
        </select><br>
        <input type="submit" name="update" value="Update">
        <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
    </form>
</body>

</html>