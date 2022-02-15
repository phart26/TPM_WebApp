<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/classes/StandardPrices.php';


$hostname = 'localhost';
$db_name = 'final';
$db_username = 'root';
$db_password = 'anuj';
$db = new mysqli($hostname, $db_username, $db_password, $db_name);
//
//'host'     => '31.220.110.218',
//            'username' => 'u886168621_tpm',
//            'password' => 'TPReTest123',
//            'database' => 'u886168621_tpm'

$sql = 'SELECT cust_tbl.cust_id, cust_tbl.customer, part_tbl.part, part_tbl.dim, part_tbl.finished_length, pricing.quantity, part_tbl.description, part_tbl.type
  , pricing.price
FROM cust_tbl INNER JOIN part_tbl ON cust_tbl.cust_id = part_tbl.cust_id INNER JOIN pricing ON part_tbl.part = pricing.part
WHERE part_tbl.part = \'2 3/8" screen - RII\'
ORDER BY cust_tbl.customer, part_tbl.part, part_tbl.dim, part_tbl.finished_length, pricing.quantity

;
';

$sql = 'SELECT * FROM part_tbl WHERE part = \'2 3/8" screen - RII\'';

//$sql = 'SELECT * FROM pricing WHERE part = \'5\" screen - RII\'';

//$r = $db->query($sql);
//$all = $r->fetch_all(MYSQLI_ASSOC);
//var_dump($all);
//die();

$pdf = new StandardPrices(20);

//
//$pdf = new TCPDF();
//$lg['a_meta_charset'] = 'UTF-8';
//$lg['a_meta_dir'] = 'ltr';
//$lg['a_meta_language'] = 'en';
//$lg['w_page'] = 'page';
//
//$pdf->setLanguageArray($lg);
//
////foreach ($all as $v) {
//  $pdf->AddPage();
//  $html = "<p style=\"border-top: 1px solid black; border-bottom: 1px solid black;\">Weatherford Well Screen</p>";
//  $pdf->writeHTML($html);
////}
//  
  $pdf->Output("standard Prices.pdf");