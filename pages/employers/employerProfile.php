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

?>
<!DOCTYPE html>
<html>
<header>

    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Employer Profile</h1>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .section-header {
            font-size: 24px;
            font-weight: bold;
            margin-top: 24px;
            color: white;
        }

        .section {

            border-radius: 4px;
            padding: 16px;


        }

        table th {
            background-color: #899499;
            font-weight: bold;
            color: black;
        }

        table td {
            padding: 8px;
            color: black;
            font-size: 16px;
            font-weight: bolder;
        }
    </style>
</header>

<body>
    <?php
    // Get employer details from database
    $id = $_SESSION['id'];
    $sql = "SELECT * FROM employers WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Employer not found.";
        exit;
    }

    $conn->close();
    ?>
    <div class="section">
        <div class="section-header">Employment Details</div>
        <table>
            <tr>
                <th>Role</th>
                <td><?php echo $row['role']; ?></td>
            </tr>
            <tr>
                <th>Insurance Number</th>
                <td><?php echo $row['insurance_number']; ?></td>
            </tr>
            <tr>
                <th>Hired Date</th>
                <td><?php echo $row['hired_date']; ?></td>
            </tr>
            <tr>
                <th>Days Worked</th>
                <td><?php echo $row['days_worked']; ?></td>
            </tr>
        </table>
    </div>
    <div class="section">
        <div class="section-header">Personal Details</div>
        <table>
            <tr>
                <th>ID</th>
                <td><?php echo $row['id']; ?></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?php echo $row['fullName']; ?></td>
            </tr>
            <tr>
                <th>Age</th>
                <td><?php echo $row['age']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $row['email']; ?></td>

            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo $row['phone']; ?></td>

            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo $row['address']; ?></td>

            </tr>
        </table>
        <td><button class="signup_button" onclick="location.href='profileUpdate.php?id=<?php echo $row['id']; ?>'">Update</button></td>

    </div>


</body>

</html>