<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

?>
<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="..\..\assets\css\main_style.css">
  <link rel="stylesheet" href="..\..\assets\css\header_style.css">
  <style>
  </style>
</head>

<body>

  <?php
  ob_start();
  ob_flush();
  require_once '..\..\lib\TCPDF-main\TCPDF-main\tcpdf.php';
  //get the delivery id from the URL
  $delivery_id = $_GET['delivery_id'];

  //get the delivery details
  $sql = "SELECT delivery.*, suppliers.supplier_name, suppliers.address, suppliers.phone, suppliers.email, ingredients.ingredient_name, ingredients.measure_unit
FROM delivery 
INNER JOIN suppliers ON delivery.supplier_id = suppliers.supplier_id 
INNER JOIN ingredients ON delivery.ingredient_id = ingredients.ingredient_id 
WHERE delivery.delivery_id=$delivery_id";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
      $delivery_status = $row["delivery_status"];

      $supplier_id = $row["supplier_id"];
      $supplier_name = $row["supplier_name"];
      $supplier_address = $row["address"];
      $supplier_phone = $row["phone"];
      $supplier_email = $row["email"];

      $ingredient_id = $row["ingredient_id"];
      $ingredient_name = $row["ingredient_name"];
      $measure_unit = $row["measure_unit"];
      $quantity = $row["quantity"];
      $price = $row["price"];
      $delivery_date = $row["delivery_date"];
      $delivery_frequency = $row["delivery_frequency"];
      $invoice_number = $row["invoice_number"];
    }
  } else {
    echo "0 results";
  }


  $total_amount = 0; // Initialize total amount

  // Calculate and add row total to total amount if delivery_status is pending or completed
  if ($delivery_status === "pending" || $delivery_status === "completed") {
    $total_amount = $quantity * $price;
  }

  $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
  $pdf->AddPage();
  $pdf->SetFont('times', 'B', 30);
  $pdf->Cell(0, 10, 'Delivery Financial Report', 0, 1, 'C');
  $pdf->Ln(8);


  // add company details
  $pdf->SetFont('times', 'B', 16);
  $pdf->Cell(0, 10, 'Food Clover Restaurant', 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Address:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, 'Aston Street, Birmingham B4 7ET', 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Phone:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, '0121 204 3000', 0, 1);
  $pdf->Ln(8);

  // add supplier details
  $pdf->SetFont('times', 'B', 16);
  $pdf->Cell(0, 8, 'From', 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Name:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_name), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Address:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_address), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Phone:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_phone), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Email:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_email), 0, 1);
  $pdf->Ln(8);
  // add invoice details
  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Invoice Number:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, $invoice_number, 0, 1);


  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Delivery Date:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, $delivery_date, 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Delivery Status:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($delivery_status), 0, 1);

  $pdf->Ln(10);

  // add ingredient table
  $pdf->SetFont('times', 'B', 12);
  $pdf->Cell(30, 8, 'Ingredient ID', 1);
  $pdf->Cell(70, 8, 'Ingredient Name', 1);
  $pdf->Cell(30, 8, 'Quantity', 1);
  $pdf->Cell(30, 8, 'Price', 1);
  $pdf->Cell(30, 8, 'Delivery Status', 1);
  $pdf->Ln();

  $pdf->SetFont('times', '', 12);
  $delivery_id = $_GET['delivery_id'];

  //get the delivery details
  $sql = "SELECT delivery.*,ingredients.ingredient_name, ingredients.measure_unit
FROM delivery 
INNER JOIN ingredients ON delivery.ingredient_id = ingredients.ingredient_id 
WHERE delivery.delivery_id=$delivery_id";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $pdf->Cell(30, 8, $row["ingredient_id"], 1);
      $pdf->Cell(70, 8, $row["ingredient_name"], 1);
      $pdf->Cell(30, 8, $row["quantity"] . ' ' . $row["measure_unit"], 1);
      $pdf->Cell(30, 8, '£ ' . $row["price"], 1);
      $pdf->Cell(30, 8, ucwords($row["delivery_status"]), 1);
      $pdf->Ln();
    }
  } else {
    $pdf->Cell(190, 8, 'No data available', 1);
    $pdf->Ln();
  }
  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Total Amount:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(45); // Set position of next cell
  $pdf->Cell(0, 8, '£ ' . $total_amount, 0, 1);

  ob_end_flush();
  ob_end_clean();
  $pdf->Output("invoice_$invoice_number.pdf", 'I');
  $conn->close();

  ?>

</body>

</html>