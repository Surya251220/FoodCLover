<?php
include_once '..\..\lib\config.php';
include_once '..\..\includes\header.php';

?>
<!DOCTYPE html>
<html>
<header>
</header>

<body>

  <?php
  ob_start();
  ob_flush();
  require_once '..\..\lib\TCPDF-main\TCPDF-main\tcpdf.php';
  //get the delivery id from the URL
  $supplier_id = $_GET['supplier_id'];

  //get the delivery details
  $sql = "SELECT delivery.*, suppliers.supplier_name, suppliers.address, suppliers.phone, suppliers.email, ingredients.ingredient_name, ingredients.measure_unit
FROM delivery 
INNER JOIN suppliers ON delivery.supplier_id = suppliers.supplier_id 
INNER JOIN ingredients ON delivery.ingredient_id = ingredients.ingredient_id 
WHERE delivery.supplier_id=$supplier_id";
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
      $delivery_count = $row["delivery_count"];
      $delivery_frequency = $row["delivery_frequency"];
      $invoice_number = $row["invoice_number"];
    }
  } else {
    echo "0 results";
  }
  $total_amount = $quantity * $price;

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
  $pdf->setX(45); // Set position of next cell
  $pdf->Cell(0, 8, 'Aston Street, Birmingham B4 7ET', 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Phone:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(45); // Set position of next cell
  $pdf->Cell(0, 8, '0121 204 3000', 0, 1);
  $pdf->Ln(8);

  // add supplier details
  $pdf->SetFont('times', 'B', 16);
  $pdf->Cell(0, 8, 'From', 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Name:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(45); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_name), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Address:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(45); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_address), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Phone:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(45); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_phone), 0, 1);

  $pdf->SetFont('times', 'B', 12); // Set font to bold
  $pdf->Cell(35, 8, 'Supplier Email:', 0, 0, 'B');
  $pdf->SetFont('times', '', 12); // Set font to normal
  $pdf->setX(45); // Set position of next cell
  $pdf->Cell(0, 8, ucwords($supplier_email), 0, 1);
  $pdf->Ln(8);
  // add invoice details

  $pdf->SetFont('times', '', 12);

  //get the delivery details
  // get delivery data grouped by month and year
  $sql = "SELECT MONTH(delivery_date) AS month, YEAR(delivery_date) AS year, SUM(quantity*price) AS total_amount
        FROM delivery
        WHERE supplier_id=$supplier_id
        GROUP BY MONTH(delivery_date), YEAR(delivery_date)";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      // generate invoice header
      $pdf->SetFont('times', 'B', 16);
      $pdf->Cell(0, 10, 'Invoice for ' . date('F Y', strtotime($row['year'] . '-' . $row['month'] . '-01')), 0, 1, 'C');
      $pdf->Ln(5);

      // generate ingredient table
      $pdf->SetFont('times', 'B', 12);
      $pdf->Cell(10, 8, 'ID', 1);
      $pdf->Cell(30, 8, 'Name', 1);
      $pdf->Cell(30, 8, 'Quantity', 1);
      $pdf->Cell(30, 8, 'Price', 1);
      $pdf->Cell(30, 8, 'Invoice No.', 1);
      $pdf->Cell(30, 8, 'Delivery Date', 1);
      $pdf->Cell(30, 8, 'Status', 1);
      $pdf->Ln();

      // get delivery data for the current month and year
      $month = $row['month'];
      $year = $row['year'];
      $sql2 = "SELECT delivery.*,ingredients.ingredient_name, ingredients.measure_unit
                FROM delivery 
                INNER JOIN ingredients ON delivery.ingredient_id = ingredients.ingredient_id 
                WHERE delivery.supplier_id=$supplier_id AND MONTH(delivery_date)=$month AND YEAR(delivery_date)=$year";
      $result2 = $conn->query($sql2);

      if ($result2->num_rows > 0) {
        $total_amount = 0; // Initialize total amount for the current invoice

        while ($row2 = $result2->fetch_assoc()) {
          $pdf->Cell(10, 8, $row2["ingredient_id"], 1);
          $pdf->Cell(30, 8, $row2["ingredient_name"], 1);
          $pdf->Cell(30, 8, $row2["quantity"] . ' ' . $row2["measure_unit"], 1);
          $pdf->Cell(30, 8, '£ ' . $row2["price"], 1);
          $pdf->Cell(30, 8, $row2["invoice_number"], 1);
          $pdf->Cell(30, 8, $row2["delivery_frequency"], 1);
          $pdf->Cell(30, 8, ucwords($row2["delivery_status"]), 1);
          $pdf->Ln();

          if ($row2["delivery_status"] == "completed" || $row2["delivery_status"] == "pending") {
            // calculate and add row total to total amount
            $row_total = $row2["quantity"] * $row2["price"];
            $total_amount += $row_total;
          }
        }

        // add total amount to PDF for the current month
        $pdf->SetFont('times', 'B', 12); // Set font to bold
        $pdf->Cell(35, 8, 'Total Amount:', 0, 0, 'B');
        $pdf->SetFont('times', '', 12); // Set font to normal
        $pdf->setX(45); // Set position of next cell
        $pdf->Cell(0, 8, '£' . $total_amount, 0, 1);
      } else {
        $pdf->Cell(190, 8, 'No data available', 1);
        $pdf->Ln();
      }
    }
  }

  ob_end_flush();
  ob_end_clean();
  $pdf->Output("invoice_$invoice_number.pdf", 'I');
  $conn->close();


  ?>

</body>

</html>