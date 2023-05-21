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

$sql = "SELECT * FROM restaurant_tables";
$result = $conn->query($sql);
$tables = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
}

// Render tables as boxes
?>
<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Restaurant Tables</h1>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
        }

        .table-container {
            margin: 50px auto;
            max-width: 1000px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .table-box {
            width: 170px;
            height: 170px;
            border: 3px solid #2e2e2e;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 25px;
            color: #2e2e2e;
            background-color: #fff;
            margin: 20px;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }

        .table-box:hover {
            border-color: #008cba;
            color: #008cba;
        }

        .table-number {
            font-size: 30px;
            font-weight: bold;
        }

        .verification-code {
            font-size: 20px;
            margin-top: 5px;
        }

        @media (max-width: 1000px) {
            .table-container {
                max-width: 500px;
            }
        }

        @media (max-width: 500px) {
            .table-container {
                max-width: 250px;
            }
        }

        /* Align 5 tables next to each other */
        @media (min-width: 850px) {
            .table-container {
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .table-box {
                margin: 10px;
            }
        }
    </style>
</header>

<body>
    <div class="table-container">
        <?php
        $sql = "SELECT rt.table_number, rt.verification_code
                    FROM restaurant_tables rt
                    ORDER BY rt.table_number ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tableNumber = $row['table_number'];
                $verificationCode = $row['verification_code'];

        ?>
                <a href="MenuReservations.php?table_number=<?php echo $tableNumber; ?>&verification_code=<?php echo $verificationCode; ?>" class="table-box">
                    <div class="table-number">Table <?php echo $tableNumber; ?></div>
                    <?php if ($verificationCode != "") : ?>
                        <div class="verification-code">Code: <?php echo $verificationCode; ?></div>
                    <?php endif; ?>
                </a>
        <?php
            }
        }
        ?>
    </div>

    </div>

</body>

</html>