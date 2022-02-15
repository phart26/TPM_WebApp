<?php
ini_set('display_errors', '1');
if (isset($_GET['standard_prices'])){
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  include_once($_SERVER['DOCUMENT_ROOT'].' /api/core/config.php');
  include_once("./vendor/autoload.php");
  $db = $config['database']['dbh'];
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  require_once __DIR__ . '/classes/StandardPricesdev.php';
 
  $customer = $_GET['standard_prices'];
  if(isset($_GET['Part_number'])){
    $part = $_GET['Part_number'];
  }
  else{
    $part = '';
  }

  // $sql = 'SELECT cust_tbl.cust_id, cust_tbl.customer, part_tbl.part, part_tbl.dim, part_tbl.finished_length, pricing.quantity, part_tbl.description, part_tbl.type
  //   , pricing.price
  // FROM cust_tbl INNER JOIN part_tbl ON cust_tbl.cust_id = part_tbl.cust_id INNER JOIN pricing ON part_tbl.part = pricing.part
  // WHERE part_tbl.part = '.$part.'
  // ORDER BY cust_tbl.customer, part_tbl.part, part_tbl.dim, part_tbl.finished_length, pricing.quantity';
  // $sql = 'SELECT * FROM part_tbl WHERE part = '.$part.'';
  $pdf = new StandardPrices((int) $customer, $part);
  $pdf->Output("standard Prices.pdf");
}

