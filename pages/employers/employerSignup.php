<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';
include_once '..\..\includes\errorsFormatting.php';

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

$search = isset($_GET['search']) ? $_GET['search'] : '';
$criteria = isset($_GET['criteria']) ? $_GET['criteria'] : 'fullName';
$reset = isset($_GET['reset']) ? true : false;
if ($reset) {
  $search = '';
}

$errors = [];
if (isset($_POST['signup-submit'])) {
  // Validate the form data
  $errors = validateFormData();

  // If there are no errors, insert the data into the database
  if (empty($errors)) {
    // Get the form data
    $full_name = $_POST['FN'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $password = $_POST['pwd'];
    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    $phone = $_POST['tel'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $insuranceNumber = $_POST['in'];
    $hiredDate = $_POST['hd'];

    if ($conn) {
      // Prepare and execute the SQL statement
      $sql = "INSERT INTO employers (fullName, age, email, pwd, phone, address, role, insurance_number, hired_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sssssssss", $full_name, $age, $email, $hashedPwd, $phone, $address, $role, $insuranceNumber, $hiredDate);

      if ($stmt->execute()) {
        echo "Customer created successfully";
        header("Location: employerSignup.php");
        exit();
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        var_dump($stmt->error);
      }
    } else {
      echo "Error: No database connection";
    }
    //} else {
    //if (!empty($errors)) {
    ///echo "<ul>";
    //foreach ($errors as $error) {
    //echo "<li>$error</li>";
    //}
    //echo "</ul>";
    //}
  }
}

//print_r($_POST);
?>


<!DOCTYPE html>
<html>
<header>
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
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <h1>Staff Management</h1>
</header>

<body>

  <div class="container-signup">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <table>
        <h2>Add more staff</h2>
        <th>Full Name</th>
        <th>Date of Birth</th>
        <th>Email</th>
        <th>Password</th>
        <th>Phone Number</th>
        <th>Address</th>
        <th>Role</th>
        <th>National Insurance Number</th>
        <th>Hired Date</th>
        <tr>
          <td><input class="input_signup" type="text" name="FN" value="<?php echo isset($_POST['FN']) ? $_POST['FN'] : ''; ?>"><br>
            <?php displayError("nameErr", $errors); ?> </td>

          <td><input class="input_signup" type="date" name="age" value="<?php echo isset($_POST['age']) ? $_POST['age'] : ''; ?>"><br>
            <?php displayError("dobErr", $errors); ?></td>


          <td><input class="input_signup" type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"><br>
            <?php displayError("emailErr", $errors); ?></td>


          <td><input type="text" name="pwd" id="pwd" value="<?php echo isset($_POST['pwd']) ? $_POST['pwd'] : ''; ?>"><br>
            <?php displayError("pwdErr", $errors); ?> </td>



          <td><input class="input_signup" type="tel" name="tel" value="<?php echo isset($_POST['tel']) ? $_POST['tel'] : ''; ?>"><br>
            <?php displayError("telErr", $errors); ?> </td>
          <td><textarea class='expandable' type="text" name='address' placeholder='Click to expand' value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>"></textarea><br>
            <?php displayError("addressErr", $errors); ?></td>
          <td><select name="role">
              <option value="admin+">Admin +</option>
              <option value="admin">Admin</option>
              <option value="staff">Staff</option>
              <option value="manager">Manager</option>
            </select></td>
          <td><input class="input_signup" type="text" name="in" value="<?php echo isset($_POST['in']) ? $_POST['in'] : ''; ?>"><br>
            <?php displayError("insuranceErr", $errors); ?></td>
          <td><input class="input_signup" type="date" name="hd" value="<?php echo isset($_POST['hd']) ? $_POST['hd'] : ''; ?>"><br>
            <?php displayError("hdErr", $errors); ?> </td>
        </tr>
      </table>
      <div class="button-container">
        <td><button class="signup_button" type="submit" name="signup-submit">Add Staff</button></td>
        <input class="signup_button" type="button" name="generatePwd" value="Generate Password" onclick="generatePassword()"><br>
      </div>
    </form>
  </div>

  <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="searchbar">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <select name="criteria">
      <option value="id" <?php if ($criteria === 'id') {
                            echo ' selected';
                          } ?>>ID</option>
      <option value="email" <?php if ($criteria === 'email') {
                              echo ' selected';
                            } ?>>Email</option>
      <option value="fullName" <?php if ($criteria === 'fullName') {
                                  echo ' selected';
                                } ?>>Full Name</option>
    </select>
    <?php if (!empty($search)) { ?>
      <button type="submit" name="reset" value="1">Reset</button>
    <?php } else { ?>
      <button type="submit">Submit</button>
    <?php } ?>
    <input type="hidden" name="page" value="<?php echo $page; ?>">
  </form>


  <table>
    <tr>
      <th>ID</th>
      <th>Full Name</th>
      <th>Email</th>
      <th>Phone Number</th>
      <th>Address</th>
      <th>Role</th>
      <th>Insurance Number</th>
      <th>Hired Date</th>
      <th>Total days worked</th>
      <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT * FROM employers";
    if (!empty($search)) {
      $sql = "SELECT * FROM employers WHERE $criteria LIKE '%$search%'";
    }
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td contenteditable='true' data-field='id'>" . $row['id'] . "</td>";
      echo "<td contenteditable='true' data-field='fullName'>" . $row['fullName'] . "</td>";
      echo "<td contenteditable='true' data-field='email'>" . $row['email'] . "</td>";
      echo "<td contenteditable='true' data-field='phone'>" . $row['phone'] . "</td>";
      echo "<td><textarea class='expandable' name='address' placeholder='Click to expand'>" . $row['address'] . "</textarea></td>";
      echo "<td contenteditable='true' data-field='role'>" . $row['role'] . "</td>";
      echo "<td contenteditable='true' data-field='insurance_number'>" . $row['insurance_number'] . "</td>";
      echo "<td contenteditable='true' data-field='hired_date'>" . $row['hired_date'] . "</td>";
      echo "<td>" . $row['days_worked'] . "</td>";
      echo "<td><button onclick=\"location.href='employerUpdate.php?id=" . $row['id'] . "'\">Edit / Delete</button></td>";
      echo "</tr>";
    }
    ?>

  </table>

</body>

</html>