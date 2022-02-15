<?php

require_once __DIR__ . '/tcpdf/tcpdf.php';
// require_once __DIR__ . '/reports_db.php';
include_once($_SERVER['DOCUMENT_ROOT'].'api/core/config.php');
$db = new mysqli($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['database']);


/**
 * Description of Report1
 *
 * @author eng.mahmoud
 */
class StandardPrices extends TCPDF {

  private $parts;

  public function __construct($customer_id) {
    parent::__construct();

    global $db;

    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'ltr';
    $lg['a_meta_language'] = 'en';
    $lg['w_page'] = 'page';

    $this->setLanguageArray($lg);

    $sql = 'SELECT * FROM cust_tbl WHERE cust_id = ' . intval($customer_id);
    $customer = $db->query($sql)->fetch_assoc();
    $customer_name = $customer['customer'];

    //foreach ($all as $v) {
//    $this->AddPage();
//    $html = "<p style=\"border-top: 1px solid black; border-bottom: 1px solid black;\">{$customer_name}</p>";
//    $this->writeHTML($html);

    $this->parts = [];
    $sql = 'SELECT * FROM part_tbl WHERE cust_id = ' . $customer_id . ' AND part IN (SELECT part FROM pricing)';
//    die($sql);
    $r = $db->query($sql);
    while ($p = $r->fetch_assoc()) {
      $this->parts[] = $p;
    }

//    var_dump($this->parts);
//    die($sql);

    $i = 1;
    foreach ($this->parts as $part) {
      if (empty($part['part'])) {
        continue;
      }
      if (($i % 3) == 1) {
        $this->SetFont('times', 'N');
        $this->SetFontSize(15);
        $this->AddPage();
        $html = "<p style=\"border-top: 1px solid black; border-bottom: 1px solid black;\">{$customer_name}</p>";
        $this->writeHTML($html);
      }

      $part_no = mysqli_real_escape_string($db, $part['part']);
      $sql = "SELECT cust_tbl.cust_id, cust_tbl.customer, part_tbl.part, part_tbl.dim, part_tbl.finished_length, pricing.quantity, part_tbl.description, part_tbl.type
  , pricing.price
FROM cust_tbl INNER JOIN part_tbl ON cust_tbl.cust_id = part_tbl.cust_id INNER JOIN pricing ON part_tbl.part = pricing.part
WHERE cust_tbl.cust_id = {$customer_id} AND part_tbl.part = '{$part_no}'
ORDER BY cust_tbl.customer, part_tbl.part, part_tbl.dim, part_tbl.finished_length, pricing.quantity
;
";
//die($sql);
      $r = $db->query($sql);
      $tmp = $r->fetch_assoc();

      if (empty($tmp['price'])) {
        continue;
      }

      $this->Ln();

      $this->SetX(20);
      $this->SetFont('helvetica', 'B');
      $this->SetFontSize(10);
      $this->Cell(40, 13, 'Part no.');
      $this->SetX(20 + 40);
      $this->Cell(40, 13, 'Part Desc.');
      $this->SetX(20 + 40 + 40);
      $this->Cell(40, 13, 'Material');
      $this->SetX(20 + 40 + 40 + 40);
      $this->Cell(40, 13, 'Length');

      $this->Ln();



      $this->SetX(20);
      $this->SetFont('times', 'N');
      $this->SetFontSize(10);
      $this->Cell(40, 13, $tmp['part']);
      $this->SetX(20 + 40);
      $this->Cell(40, 13, $tmp['description']);
      $this->SetX(20 + 40 + 40);
      $this->Cell(40, 13, $tmp['type']);
      $this->SetX(20 + 40 + 40 + 40);
      $this->Cell(40, 13, $tmp['finished_length']);

      $this->Ln();
      $this->SetFont('helvetica', 'B');
      $this->SetX(20 + 40 + 40);
      $this->Cell(40, 13, 'Minimum Quantity');
      $this->SetX(20 + 40 + 40 + 40);
      $this->Cell(40, 13, 'Price (ea.)');


      $this->setCellHeightRatio(0.4);

      $this->SetFont('times', 'N');
      $this->Ln();
      $this->SetX(20 + 40 + 40);
      $this->Cell(40, 13, $tmp['quantity']);
      $this->SetX(20 + 40 + 40 + 40);
      $this->Cell(40, 13, $tmp['price']);

      while ($tmp = $r->fetch_assoc()) {
//      $this->Ln();
        $this->SetY($this->GetY() + 5);
        $this->SetX(20 + 40 + 40);
        $this->Cell(40, 13, $tmp['quantity']);
        $this->SetX(20 + 40 + 40 + 40);
        $this->Cell(40, 13, $tmp['price']);
      }

      $i++;
    }
  }

}
