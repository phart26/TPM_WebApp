<?php

require_once __DIR__ . '/tcpdf/tcpdf.php';
// require_once __DIR__ . '/reports_db.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/api/core/config.php');
$db = new mysqli($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['database']);

/**
 * Description of OrderStatus
 *
 * @author eng.mahmoud
 */
class OrderStatus extends TCPDF {

  public function __construct($customer_id = null) {
    parent::__construct('L', 'pt', 'A4', true, 'UTF-8', false);
    $this->SetCreator('Eng. Mahmoud S. S. Abuwarda');

    global $db;

//    $lg['a_meta_charset'] = 'UTF-8';
//    $lg['a_meta_dir'] = 'ltr';
//    $lg['a_meta_language'] = 'en';
//    $lg['w_page'] = 'page';
//    $this->setLanguageArray($lg);
    $this->AddPage();

    $this->SetFont('times', 'N');
    $this->SetFontSize(25);
    $html = "<p style=\"border-top: 2px solid black; border-bottom: 2px solid black;\">TPM Order Status</p>";
    $this->writeHTML($html);


    $sql = 'SELECT * FROM cust_tbl';
    if (isset($customer_id)) {
      $sql .= ' WHERE cust_id = ' . intval($customer_id);
    }

    $customers_result = $db->query($sql);

    $this->SetY(70);

    while ($c = $customers_result->fetch_assoc()) {
      
      $this->SetX(25);
      $this->SetFont('times', 'B');
      $this->SetFontSize(13);
      $this->Cell(50, 13, 'Customer:');

      $this->SetX(20 + 80);
      $this->SetFont('times', 'N');
      $this->SetFontSize(13);
      $this->Cell(50, 13, $c['customer']);

//      $this->SetY(20 + 70);
      $this->Ln();

      $sql = "SELECT orders_tbl.job
                   , orders_tbl.cust_id
                   , orders_tbl.po
                   , orders_tbl.part
                   , orders_tbl.quantity
                   , orders_tbl.ordered
                   , orders_tbl.due
                   , DATE_FORMAT(orders_tbl.due, '%m/%d/%Y') due_formatted
                   , orders_tbl.has_shipped
                   , cust_tbl.customer
                   , orders_tbl.ordered AS ordered_date
                   , DATE_FORMAT(orders_tbl.ordered, '%m/%d/%Y') orders_date_formatted
                   , orders_tbl.item
                   , orders_tbl.InStr
                   , orders_tbl.balance
                   , orders_tbl.status
                   , orders_tbl.call
                   , DATE_FORMAT(orders_tbl.call, '%m/%d/%Y') call_formatted
                   , orders_tbl.u886168621_tpm AS tpm
                   , part_tbl.description
              FROM cust_tbl
              JOIN part_tbl INNER JOIN orders_tbl
                ON part_tbl.part = orders_tbl.part
                  AND (cust_tbl.cust_id = part_tbl.cust_id)
                  AND (cust_tbl.cust_id = orders_tbl.cust_id)
              WHERE (((orders_tbl.has_shipped) = 0)) AND cust_tbl.cust_id = {$c['cust_id']}
              ORDER BY orders_tbl.due;
              ";
//              die($sql);
      $orders_result = $db->query($sql);

      $this->SetFont('times', 'B');
      $this->SetFontSize(10);

      $this->Cell(60, 13, 'Ordered');
      $this->Cell(80, 13, 'PO number');
      $this->Cell(25, 13, 'item');
      $this->Cell(40, 13, 'Job');
      $this->Cell(80, 13, 'Part No.');
      $this->Cell(140, 13, 'Description');
      $this->Cell(25, 13, 'Qty.');
      $this->Cell(60, 13, 'Due');
      $this->Cell(70, 13, 'Instructions');
      $this->Cell(50, 13, 'Balance');
      $this->Cell(50, 13, 'Status');
      $this->Cell(50, 13, 'Call');
      $this->Cell(50, 13, 'TPM');

      $i = 0;
      $this->SetFont('times', 'N');
      $this->SetFontSize(10);
      while ($o = $orders_result->fetch_assoc()) {
//        $this->SetY(20 + 70 + 20);
        $this->Ln();
//        $order_date = DateTime::createFromFormat($o['ordered_date']);
        $this->Cell(60, 13, $o['orders_date_formatted']);
        $this->Cell(80, 13, $o['po']);
        $this->Cell(25, 13, $o['item']);
        $this->Cell(40, 13, $o['job']);
        $this->Cell(80, 13, $o['part']);
        $this->Cell(140, 13, $o['description']);
        $this->Cell(25, 13, $o['quantity']);
        $this->Cell(60, 13, $o['due_formatted']);
        $this->Cell(70, 13, $o['InStr']);
        $this->Cell(50, 13, $o['balance']);
        $this->Cell(50, 13, $o['status']);
        $this->Cell(50, 13, $o['call_formatted']);
        $this->Cell(50, 13, $o['tpm']);
      }
      
      $this->Ln();
      $this->Ln();
    }
  }

}
