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


?>
<!DOCTYPE html>
<html>
<header>

    <link rel="stylesheet" href="..\..\assets\css\main_style.css">
    <link rel="stylesheet" href="..\..\assets\css\header_style.css">
    <h1> Menu Management</h1>
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
    <?php
    if (isset($_POST['add'])) {
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
            $sql = "INSERT INTO dishes (dish_name, description, allergies, price, category, kitchen_section,  course_order, image_path, average_orders_per_day, estimated_prep_time, estimated_eating_time, estimated_total_time) 
        VALUES ('$dish_name', '$description', '$allergies', '$price', '$category', '$new_kitchen_section',  '$course_order', '$image_path', '$average_orders_per_day', '$estimated_prep_time', '$estimated_eating_time', '$estimated_total_time')";
            if (mysqli_query($conn, $sql)) {
                echo "New dish added successfully.";
                header("Location: menuManagement.php");
                exit();
            } else {
                $errors[] = 'Failed to add new category.';
            }
            mysqli_stmt_close($stmt);
        } else {

            // Insert form data into the dishes table
            $sql = "INSERT INTO dishes (dish_name, description, allergies, price, category, kitchen_section,  course_order, image_path, average_orders_per_day, estimated_prep_time, estimated_eating_time, estimated_total_time) 
            VALUES ('$dish_name', '$description', '$allergies', '$price', '$category', '$kitchen_section',  '$course_order', '$image_path', '$average_orders_per_day', '$estimated_prep_time', '$estimated_eating_time', '$estimated_total_time')";
            if (mysqli_query($conn, $sql)) {
                echo "New dish added successfully.";
                header("Location: menuManagement.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
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

    ?>

    <form class="form-container" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-header">
            <h1>Add New Dish</h1>
        </div>
        <div class="left-section">
            <label>Dish Name:</label>
            <input type="text" name="dish_name" required>
            <label>Description:</label>
            <textarea name="description" required></textarea>
            <label>Allergies:</label>
            <table class="allergies-table">
                <tr>
                    <td>
                        <label2>No Allergies</label2><input type="checkbox" name="allergies[]" value="No Allergies">
                    </td>
                    <td>
                        <label2>Celery</label><input type="checkbox" name="allergies[]" value="Celery">
                    </td>
                    <td>
                        <label2>Gluten</label><input type="checkbox" name="allergies[]" value="Gluten">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label2>Eggs</label><input type="checkbox" name="allergies[]" value="Eggs">
                    </td>
                    <td>
                        <label2>Fish</label><input type="checkbox" name="allergies[]" value="Fish">
                    </td>
                    <td>
                        <label2>Lupin</label><input type="checkbox" name="allergies[]" value="Lupin">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label2>Milk</label><input type="checkbox" name="allergies[]" value="Milk">
                    </td>
                    <td>
                        <label2>Molluscs</label><input type="checkbox" name="allergies[]" value="Molluscs">
                    </td>
                    <td>
                        <label2>Mustard</label><input type="checkbox" name="allergies[]" value="Mustard">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label2>Peanuts</label><input type="checkbox" name="allergies[]" value="Peanuts">
                    </td>
                    <td>
                        <label2>Sesame</label><input type="checkbox" name="allergies[]" value="Sesame">
                    </td>
                    <td>
                        <label2>Soybeans</label><input type="checkbox" name="allergies[]" value="Soybeans">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label2>Sulfites</label><input type="checkbox" name="allergies[]" value="Sulfites">
                    </td>
                    <td>
                        <label2>Sulphites</label><input type="checkbox" name="allergies[]" value="Sulphites">
                    </td>
                    <td>
                        <label2>Tree Nuts</label><input type="checkbox" name="allergies[]" value="Tree nuts">
                    </td>
                </tr>
            </table>
            <label>Price:</label>
            <input type="number" name="price" min="0.00" step="0.01" required placeholder="Price in Pounds">
            <label>Category:</label>
            <select name="category" required>
                <option value="" selected disabled>Select a category</option>
                <option value="Starters">Starters</option>
                <option value="Vegetarian dishes">Vegetarian dishes</option>
                <option value="Non-vegetarian dishes">Non-vegetarian dishes</option>
                <option value="Sides">Sides</option>
                <option value="Alcoholic drink">Alcoholic drink</option>
                <option value="Non-Alcoholic drink">Non-Alcoholic drink</option>
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
                <option value="1">Starter</option>
                <option value="2">Main Dish</option>
                <option value="3">Sides</option>
                <option value="4">Drinks</option>
            </select>

            <label>Image Filepath:</label>
            <input type="text" name="image_path" required placeholder="Please give me the Filepath of the Image">
            <label>Average Orders Per Day:</label>
            <input type="number" name="average_orders_per_day" min="0" required>
            <label>Estimated Prep Time:</label>
            <input type="number" name="estimated_prep_time" min="0" placeholder="Time in Minutes" required>
            <label>Estimated Eating Time:</label>
            <input type="number" name="estimated_eating_time" min="0" placeholder="Time in Minutes" required>
            <label>Estimated Total Time:</label>
            <input type="number" name="estimated_total_time" min="0" placeholder="Time in Minutes" required>
            <br><br>
            <div class="DI-Submit">
                <input type="submit" value="Add Dish" id="add" name="add">
            </div>

        </div>
        </div>

    </form>
</body>

</html>