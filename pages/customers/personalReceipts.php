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

  $customer_name = $_GET['full_name'];
  $reservation_id = $_GET['reservation_id'];
  $table_number = $_GET['table_number'];

  $sql = "SELECT *, customers.email AS customer_email,  orders.created_at AS created_at 
FROM orders
JOIN customers ON orders.customer_id = customers.customer_id
WHERE orders.reservation_id = $reservation_id";


  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row

    while ($row = $result->fetch_assoc()) {


      $customer_id = $row['customer_id'];
      $customer_name = $row['full_name'];
      $customer_email = $row["customer_email"];
      $customer_phone = $row["phone"];
      $reservation_date = $row["created_at"];
    }
  }
  $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
  $pdf->AddPage();
  $pdf->SetFont('times', 'B', 30);
  $pdf->Cell(0, 10, 'Customer Receipt', 0, 1, 'C');
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
  $pdf->Cell(0, 8, 'For', 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Customer Name:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($customer_name), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Customer Email:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($customer_email), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Customer Phone:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($customer_phone), 0, 1);
  $pdf->Ln(8);

  // add invoice details
  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Reservation No.:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, $reservation_id, 0, 1);


  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Reservation Date:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, $reservation_date, 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Table Number:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(50); // Set position of next cell
  $pdf->Cell(0, 8, ucwords("$table_number"), 0, 1);

  $pdf->Ln(10);

  // add ingredient table
  $pdf->SetFont('times', 'B', 12);
  $pdf->Cell(30, 8, 'Order ID', 1);
  $pdf->Cell(70, 8, 'Dish Name', 1);
  $pdf->Cell(30, 8, 'Quantity', 1);
  $pdf->Cell(30, 8, 'Price', 1);
  $pdf->Cell(30, 8, 'Order Status', 1);
  $pdf->Ln();

  $pdf->SetFont('times', '', 12);

  $sql = "SELECT *
FROM orders
LEFT JOIN dishes ON orders.dish_id = dishes.dish_id
WHERE orders.reservation_id = $reservation_id ";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $total_amount = 0;

    while ($row = $result->fetch_assoc()) {
      $quantity = $row["quantity"];
      $price = $row["price"];
      if ($row["order_status"] === "Pending" || $row["order_status"] === "Completed") {
        $row_total = $quantity * $price; // Calculate the total for the current row
        $total_amount += $row_total;
      }

      $pdf->Cell(30, 8, $row["order_id"], 1);
      $pdf->Cell(70, 8, $row["dish_name"], 1);
      $pdf->Cell(30, 8, $quantity, 1);
      $pdf->Cell(30, 8, '£' . $price, 1);
      $pdf->Cell(30, 8, ucwords($row["order_status"]), 1);
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
  $pdf->Output("FoodCloverReceipt_$customer_name.pdf", 'I');

  $conn->close();
