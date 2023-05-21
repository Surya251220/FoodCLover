<?php
  // function to sanitize input data
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function displayError($fieldName, $errors) {
    if (isset($errors[$fieldName])) {
      $errorMessage = $errors[$fieldName];
      echo '<span class="error">' . $errorMessage . '</span>';
    }
  }


  // function to validate form data
  function validateFormData() {
  // define variables and set to empty strings
    $errors = array();
    $nameErr = $emailErr = $pwdErr = $telErr = $addressErr = $dobErr = $insuranceErr = $hdErr = $ingredient_nErr = $stockErr = $mUnitErr = $expErr = $supplierErr = $categoryErr = $quantityErr = $delErr = $priceErr = $dMeasureErr = $deliveryFreqErr = "";
    
    // validate full name
    if (isset($_POST['FN'])) {
    if (empty($_POST['FN'])) {
      $nameErr = "Full Name is required";
    } else {
      $fullname = test_input($_POST['FN']);
      if (!preg_match("/^[a-zA-Z ]*$/",$fullname)) {
        $nameErr = "Only letters and white space";
      }
    }
  }
    
    // Validate Email
    if (isset($_POST['email'])) {
    if (empty($_POST['email'])) {
      $emailErr = "Email is required";
    } else {
      $email = test_input($_POST['email']);
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
      }
    }
  }
      // Validate National Insurance
    if (isset($_POST['in'])) {
    if (empty($_POST['in'])) {
      $insuranceErr = "Insurance number is required";
    } else {
      $insuranceNumber = test_input($_POST['in']);
      if (!preg_match("/^([a-zA-Z]){2}( )?([0-9]){2}( )?([0-9]){2}( )?([0-9]){2}( )?([a-zA-Z]){1}?$/", $insuranceNumber)) {
        $insuranceErr = "Invalid Insurance Number format";
      }
    }
  }
    // Validate Password
    if (isset($_POST['pwd'])) {
    if (empty($_POST['pwd'])) {
      $pwdErr = "Password is required";
    } else {
      $password = test_input($_POST['pwd']);
    }}
    
    // Validate Phone Number
    if (isset($_POST['tel'])) {
    if (empty($_POST['tel'])) {
      $telErr = "Phone Number is required";
    } else {
      $phone = test_input($_POST['tel']);
      if (!preg_match("/^((\+44)?|0)7\d{9}$/",$phone)) {
        $telErr = "Invalid Phone Number format";
      }
    }
  }
    // Validate Address
    if (isset($_POST['address'])) {
    if (empty($_POST['address'])) {
      $addressErr = "Address is required";
    } else {
      $address = test_input($_POST['address']);
    }
  }
    
    // Validate Date of Birth
    if (isset($_POST['age'])) {
    if (empty($_POST['age'])) {
      $dobErr = "Date of Birth is required";
    } else {
      $age = test_input($_POST['age']);
      $age = date('Y-m-d', strtotime($age));
    }
  }

  // Validate Date of Birth
  if (isset($_POST['hd'])) {
    if (empty($_POST['hd'])) {
      $hdErr = "Hired date is required";
    } else {
      $hiredDate = test_input($_POST['hd']);
    } 
  }


  if (isset($_POST['ingredient_name'])) {
    if (empty($_POST['ingredient_name'])) {
      $ingredient_nErr = "Ingredient name is required";
    } else {
      $ingredient_name = test_input($_POST['ingredient_name']);
      if (!preg_match("/^[a-zA-Z ]*$/",$ingredient_name)) {
        $ingredient_nErr = "Only letters and white space";
      }
    }
  }
    

  if (isset($_POST['price'])) {
    if (empty($_POST['price'])) {
      $pricekErr = "Please give me the price ";
    } else {
      $price = test_input($_POST['price']);
      // validate input using regular expression
      if (!preg_match("/^[0-9,]+(\.[0-9]+)?$/", str_replace('.', ',', $price))) {
        $priceErr = "Only numbers are allowed for price";
      }
    } 
  }



    if (isset($_POST['inventory_stock'])) {
      if (empty($_POST['inventory_stock'])) {
        $stockErr = "Number of available stock ";
      } else {
        $inventory_stock = test_input($_POST['inventory_stock']);
        // validate input using regular expression
        if (!preg_match("/^[0-9,]+(\.[0-9]+)?$/", str_replace('.', ',', $inventory_stock))) {
          $stockErr = "Only numbers are allowed for available stock";
        }
      } 
    }
    
    if (isset($_POST['quantity'])) {
      if (empty($_POST['quantity'])) {
        $quantityErr = "Number of quantity ";
      } else {
        $quantity = test_input($_POST['quantity']);
        // validate input using regular expression
        if (!preg_match("/^[0-9,]+(\.[0-9]+)?$/", str_replace('.', ',', $quantity))){
          $quantityErr = "Only numbers are allowed for quantity";
        }
      } 
    }

    if (isset($_POST['delivery_frequency'])) {
      if ($_POST['delivery_frequency'] == 'Select') {
        $deliveryFreqErr = "Please select a delivery frequency";
      } else {
        $delivery_frequency = test_input($_POST['delivery_frequency']);
      }
    }

    if (isset($_POST['dMeasure_unit'])) {
      if ($_POST['dMeasure_unit'] == 'Select') {
        $dMeasureErr = "Please select a measure unit";
      } else {
        $dMeasure_unit = test_input($_POST['dMeasure_unit']);
      }
    }
    

  if (isset($_POST['expiry_date'])) {
    if (empty($_POST['expiry_date'])) {
      $expErr = "Expiry required";
    } else {
      $expiry_date = test_input($_POST['expiry_date']);
      $today = date('Y-m-d'); // get today's date
      if ($expiry_date < $today) { // compare input date with today's date
        $expErr = "Expiry date should be today or a future date";
      } else {
        $expiry_date = date('Y-m-d', strtotime($expiry_date));
      }
    }
  }

  if (isset($_POST['delivery_date'])) {
    if (empty($_POST['delivery_date'])) {
      $delErr = "Delivery Date required";
    } else {
      $delivery_date = test_input($_POST['delivery_date']);
      $today = date('Y-m-d'); // get today's date
      if ($delivery_date < $today) { // compare input date with today's date
        $delErr = "Expiry date should be today or a future date";
      } else {
        $delivery_date = date('Y-m-d', strtotime($delivery_date));
      }
    }
  }

  if (isset($_POST['supplier_name'])) {
    if ($_POST['supplier_name'] == 'Select') {
      $supplierErr = "Please select a supplier";
    } else {
      $supplier_name = test_input($_POST['supplier_name']);
    }
  }

  if (isset($_POST['category'])) {
    if (empty($_POST['category'])) {
      $categoryErr = "Category is required";
    } else {
      $category = test_input($_POST['category']);
    } 
  }

    // add error messages to array if any
    if (!empty($supplierErr)) {
      $errors["supplierErr"] = $supplierErr;
    }
    if (!empty($deliveryFreqErr)) {
      $errors["deliveryFreqErr"] = $deliveryFreqErr;
    }
    if (!empty($pricekErr)) {
      $errors["pricekErr"] = $pricekErr;
    }
    if (!empty($quantityErr)) {
      $errors["quantityErr"] = $quantityErr;
    }
    if (!empty($delErr)) {
      $errors["delErr"] = $delErr;
    }
    if (!empty($dMeasureErr)) {
      $errors["dMeasureErr"] = $dMeasureErr;
    }
    if (!empty($nameErr)) {
      $errors["nameErr"] = $nameErr;
    }
    if (!empty($emailErr)) {
      $errors["emailErr"] = $emailErr;
    }
    if (!empty($pwdErr)) {
      $errors["pwdErr"] = $pwdErr;
    }
    if (!empty($telErr)) {
      $errors["telErr"] = $telErr;
    }
    if (!empty($addressErr)) {
      $errors["addressErr"] = $addressErr;
    }
    if (!empty($dobErr)) {
      $errors["dobErr"] = $dobErr;
    }
    if (!empty($insuranceErr)) {
      $errors["insuranceErr"] = $insuranceErr;
    }
    if (!empty($hdErr)) {
      $errors["hdErr"] = $hdErr;
    }
    if (!empty($ingredient_nErr)) {
      $errors["ingredient_nErr"] = $ingredient_nErr;
    }
    if (!empty($stockErr)) {
      $errors["stockErr"] = $stockErr;
    }
    if (!empty($mUnitErr)) {
      $errors["mUnitErr"] = $mUnitErr;
    }
    if (!empty($expErr)) {
      $errors["expErr"] = $expErr;
    }
    if (!empty($categoryErr)) {
      $errors["categoryErr"] = $categoryErr;
    }

    // return array containing error messages
    return $errors;
  }
?>
<style>
  .error {
    color: red;
  }
</style>    