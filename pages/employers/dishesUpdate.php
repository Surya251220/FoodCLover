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


$sql = "SELECT DISTINCT kitchen_section FROM dishes";
$result = mysqli_query($conn, $sql);
$kitchen_sections = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
$dish_id = isset($_GET['dish_id']) ? $_GET['dish_id'] : null;
?>

<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1>Update/Delete Dishes</h1>
    <style>
        .form-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: stretch;
            max-width: 960px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }

        .form-header {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
            background-color: grey;
        }

        .form-header h1 {
            font-size: 32px;
            font-weight: bold;
            color: white;
        }

        .form-container .form-section {
            margin-bottom: 20px;
        }

        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="file"],
        .form-container select,
        .form-container textarea {
            display: block;
            width: 95%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
            background-color: #f9f9f9;
        }

        .form-container select {
            appearance: none;
            -moz-appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M.5 0L0 .5l5 5 5-5L9.5 0 5 4.5.5 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px 6px;
            padding-right: 30px;
        }

        .form-container input[type="checkbox"] {
            margin-right: 5px;
            margin-top: 4px;
        }

        .form-container .allergies-table {

            border-collapse: collapse;
            border-spacing: 0;
            width: 95%;
            max-width: 500px;
            margin-bottom: 10px;
        }

        .form-container .allergies-table td {
            padding: 5px;
            border: 1px solid #ccc;
            text-align: center;
            font-size: 14px;
            color: #333;
        }

        .form-container .allergies-table td label {
            display: block;
            font-size: 12px;
            font-weight: normal;
            margin-bottom: 3px;
            color: #333;
        }

        .form-container .left-section {
            flex: 1;
        }

        .form-container .right-section {
            flex: 1;
        }

        .form-container .error {
            color: #ff0000;
            font-size: 18px;
        }
    </style>
</header>

<body>


    <body>
        <?php
        if (isset($_POST['update'])) {
            $dish_id = isset($_GET['dish_id']) ? $_GET['dish_id'] : null;
            $dish_name = $_POST['dish_name'];
            $description = $_POST['description'];
            $allergies_arr = isset($_POST['allergies']) ? $_POST['allergies'] : array();
            $allergies = implode(',', $allergies_arr);
            $price = $_POST['price'];
            $category = $_POST['category'];
            $kitchen_section = $_POST['kitchen_section'];
            $course_order = $_POST['course_order'];
            $image_path = addslashes($_POST['image_path']);
            $average_orders_per_day = $_POST['average_orders_per_day'];
            $estimated_prep_time = $_POST['estimated_prep_time'];
            $estimated_eating_time = $_POST['estimated_eating_time'];
            $estimated_total_time = $_POST['estimated_total_time'];
            $new_kitchen_section = $_POST['new_kitchen_section'];

            if (!empty($new_kitchen_section)) {
                // Insert the new category into the database
                $stmt = $conn->prepare("UPDATE dishes SET dish_name=?, description=?, allergies=?, price=?, category=?, kitchen_section=?, course_order=?, image_path=?, average_orders_per_day=?, estimated_prep_time=?, estimated_eating_time=?, estimated_total_time=? WHERE dish_id=?");
                $stmt->bind_param("ssssssssssssi", $dish_name, $description, $allergies, $price, $category, $new_kitchen_section, $course_order, $image_path, $average_orders_per_day, $estimated_prep_time, $estimated_eating_time, $estimated_total_time, $dish_id);

                if ($stmt->execute()) {
                    echo "Dish is Updated successfully.";
                    header("Location: menuManagement.php");
                    exit();
                } else {
                    echo "Failed Update Dish.";
                }
                mysqli_stmt_close($stmt);
            } else {

                // Update form data into the dishes table
                $stmt = $conn->prepare("UPDATE dishes SET dish_name=?, description=?, allergies=?, price=?, category=?, kitchen_section=?, course_order=?, image_path=?, average_orders_per_day=?, estimated_prep_time=?, estimated_eating_time=?, estimated_total_time=? WHERE dish_id=?");
                $stmt->bind_param("ssssssssssssi", $dish_name, $description, $allergies, $price, $category, $kitchen_section, $course_order, $image_path, $average_orders_per_day, $estimated_prep_time, $estimated_eating_time, $estimated_total_time, $dish_id);
                if ($stmt->execute()) {
                    echo "Dish is Updated successfully.";
                    header("Location: menuManagement.php");
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }

        if (isset($_POST['new_section'])) {
            $newSection = $_POST['new_section'];
            $sql = "ALTER TABLE dishes MODIFY COLUMN kitchen_section ENUM('kitchen', 'bar', '$newSection')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo "New section added successfully!";
            } else {
                echo "Error adding new section: " . mysqli_error($conn);
            }
        }
        if (isset($_POST['delete'])) {
            $id = trim($_GET['dish_id']);
            // execute the delete query
            $sql = "DELETE FROM dishes WHERE dish_id=$dish_id";
            if (mysqli_query($conn, $sql)) {
                echo "Dish deleted successfully.";
                // redirect the user to the employer list page
                header("Location: dishes.php");
                exit();
            } else {
                echo "Error deleting record: " . mysqli_error($conn);
            }
        }

        $dish_id = trim($_GET['dish_id']);
        $sql = "SELECT * FROM dishes WHERE dish_id=$dish_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        ?>

        <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?dish_id=' . $dish_id; ?>" method="POST">
            <div class="form-header">
                <h1>Update/Delete Dish Form</h1>
            </div>
            <div class="left-section">
                <label>Dish Name:</label>
                <input type="text" name="dish_name" value="<?php echo $row['dish_name']; ?>" required>


                <label>Description:</label>
                <textarea name="description" required><?php echo $row['description']; ?></textarea>

                <label>Allergies:</label>
                <table class="allergies-table">
                    <tr>
                        <td>
                            <label2>No Allergies</label2><input type="checkbox" name="allergies[]" value="No Allergies" <?php if (!empty($row['allergies']) && strpos($row['allergies'], 'No Allergies') !== false) echo 'checked '; ?>>
                        </td>
                        <td>
                            <label2>Celery</label2><input type="checkbox" name="allergies[]" value="Celery" <?php if (!empty($row['allergies']) && strpos($row['allergies'], 'Celery') !== false) echo 'checked '; ?>>
                        </td>
                        <td>
                            <label2>Gluten</label2><input type="checkbox" name="allergies[]" value="Gluten" <?php if (strpos($row['allergies'], 'Gluten') !== false) echo 'checked'; ?>>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label2>Eggs</label2><input type="checkbox" name="allergies[]" value="Eggs" <?php if (strpos($row['allergies'], 'Eggs') !== false) echo 'checked '; ?>>
                        </td>
                        <td>
                            <label2>Fish</label2><input type="checkbox" name="allergies[]" value="Fish" <?php if (strpos($row['allergies'], 'Fish') !== false) echo 'checked'; ?>>
                        </td>
                        <td>
                            <label2>Lupin</label2><input type="checkbox" name="allergies[]" value="Lupin" <?php if (strpos($row['allergies'], 'Lupin') !== false) echo 'checked'; ?>>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label2>Milk</label2><input type="checkbox" name="allergies[]" value="Milk" <?php if (strpos($row['allergies'], 'Milk') !== false) echo 'checked'; ?>>
                        </td>
                        <td>
                            <label2>Molluscs</label2><input type="checkbox" name="allergies[]" value="Molluscs" <?php if (strpos($row['allergies'], 'Molluscs') !== false) echo 'checked'; ?>>
                        </td>
                        <td>
                            <label2>Mustard</label2><input type="checkbox" name="allergies[]" value="Mustard" <?php if (strpos($row['allergies'], 'Mustard') !== false) echo 'checked'; ?>>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label2>Peanuts</label2><input type="checkbox" name="allergies[]" value="Peanuts" <?php if (strpos($row['allergies'], 'Peanuts') !== false) echo 'checked'; ?>>
                        </td>
                        <td>
                            <label2>Sesame</label2><input type="checkbox" name="allergies[]" value="Sesame" <?php if (strpos($row['allergies'], 'Sesame') !== false) echo 'checked'; ?>>
                        </td>
                        <td>
                            <label2>Soybeans</label2><input type="checkbox" name="allergies[]" value="Soybeans" <?php if (strpos($row['allergies'], 'Soybeans') !== false) echo 'checked'; ?>>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label2>Sulfites</label2><input type="checkbox" name="allergies[]" value="Sulfites" <?php if (strpos($row['allergies'], 'Sulfites') !== false) echo 'checked'; ?>>
                        </td>
                        <td>
                            <label2>Sulphites</label2><input type="checkbox" name="allergies[]" value="Sulphites" <?php if (strpos($row['allergies'], 'Sulphites') !== false) echo 'checked'; ?>>
                        </td>
                        <td>
                            <label2>Tree Nuts</label2><input type="checkbox" name="allergies[]" value="Tree nuts" <?php if (strpos($row['allergies'], 'Tree nuts') !== false) echo 'checked'; ?>>
                        </td>
                    </tr>
                </table>
                <label>Price:</label>
                <input type="number" name="price" min="0.00" step="0.01" required placeholder="Price in Pounds" value="<?php echo $row['price']; ?>">
                <label>Category:</label>
                <select name="category" required>
                    <option value="" selected disabled>Select a category</option>
                    <option value="Starters" <?php if ($row['category'] == 'Starters') echo 'selected'; ?>>Starters</option>
                    <option value="Vegetarian dishes" <?php if ($row['category'] == 'Vegetarian dishes') echo 'selected'; ?>>Vegetarian dishes</option>
                    <option value="Non-vegetarian dishes" <?php if ($row['category'] == 'Non-vegetarian dishes') echo 'selected'; ?>>Non-vegetarian dishes</option>
                    <option value="Sides" <?php if ($row['category'] == 'Sides') echo 'selected'; ?>>Sides</option>
                    <option value="Alcoholic drink" <?php if ($row['category'] == 'Alcoholic drink') echo 'selected'; ?>>Alcoholic drink</option>
                    <option value="Non-Alcoholic drink" <?php if ($row['category'] == 'Non-Alcoholic drink') echo 'selected'; ?>>Non-Alcoholic drink</option>
                </select>
            </div>

            <div class="right-section">
                <label>Restaurant Section:</label>

                <select name="kitchen_section" required>
                    <option value="" selected disabled>Select Section</option>
                    <?php foreach ($kitchen_sections as $kitchen_section) { ?>
                        <option value="<?php echo $kitchen_section['kitchen_section']; ?>"><?php echo $kitchen_section['kitchen_section']; ?></option>
                    <?php } ?>
                    <option value="add_new_kitchen_section" style="font-weight:bold;">Add New Kitchen Section</option>
                </select>

                <div id="new-kitchen-section-input" style="display:none;">
                    <label>New Section:</label>
                    <input type="text" name="new_kitchen_section" value="<?php echo isset($_POST['new_kitchen_section']) ? $_POST['new_kitchen_section'] : ''; ?>">
                </div>

                <script>
                    var selectEl = document.querySelector('select[name="kitchen_section"]');
                    var newCategoryInputEl = document.querySelector('#new-kitchen-section-input');

                    selectEl.addEventListener('change', function() {
                        if (selectEl.value === 'add_new_kitchen_section') {
                            newCategoryInputEl.style.display = 'block';
                        } else {
                            newCategoryInputEl.style.display = 'none';
                        }
                    });
                </script>

                <label>Course Order:</label>
                <input type="hidden" name="category_order" min="0" required>
                <select name="course_order" required>
                    <option value="" selected disabled>Select a Course Order</option>
                    <option value="1" <?php if ($row['course_order'] == '1') echo 'selected'; ?>>Starter</option>
                    <option value="2" <?php if ($row['course_order'] == '2') echo 'selected'; ?>>Main Dish</option>
                    <option value="3" <?php if ($row['course_order'] == '3') echo 'selected'; ?>>Sides</option>
                    <option value="4" <?php if ($row['course_order'] == '4') echo 'selected'; ?>>Drinks</option>
                </select>

                <label>Image Filepath:</label>
                <input type="text" name="image_path" required placeholder="Please give me the Filepath of the Image" value="<?php echo $row['image_path']; ?>">
                <label>Average Orders Per Day:</label>
                <input type="number" name="average_orders_per_day" min="0" required value="<?php echo $row['average_orders_per_day']; ?>">
                <label>Estimated Prep Time:</label>
                <input type="number" name="estimated_prep_time" min="0" placeholder="Time in Minutes" required value="<?php echo $row['estimated_prep_time']; ?>">
                <label>Estimated Eating Time:</label>
                <input type="number" name="estimated_eating_time" min="0" placeholder="Time in Minutes" required value="<?php echo $row['estimated_eating_time']; ?>">
                <label>Estimated Total Time:</label>
                <input type="number" name="estimated_total_time" min="0" placeholder="Time in Minutes" required value="<?php echo $row['estimated_total_time']; ?>">
                <br><br>
                <input type="submit" value="Update Dish" id="update" name="update">
                <input type="submit" value="Delete Dish" id="delete" name="delete">
            </div>
            </div>
        </form>
    </body>

</html>