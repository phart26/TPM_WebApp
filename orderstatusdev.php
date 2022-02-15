<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/classes/OrderStatus.php';

//$pdf = new OrderStatus(20);
$pdf = new OrderStatus(null);
$pdf->Output("order status.pdf");
