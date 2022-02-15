<?php


ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

$customer_name = $_GET['customer_name'];

if(isset($_GET['variation']) AND $_GET['variation'] == "cutoff")
{
	$variation = "cutoff";
}
elseif(isset($_GET['variation']) AND $_GET['variation'] == "welder")
{
	$variation = "welder";
}
elseif(isset($_GET['variation']) AND $_GET['variation'] == "mill")
{
	$variation = "mill";
}
else
{
	$variation = "";
}

error_reporting(E_ALL);

if(empty($variation))
{
	require_once __DIR__ . '/classes/TrainingInspectors.php';
	//$pdf = new OrderStatus(20);

	$pdf = new TrainingInspectors($customer_name);
}
elseif($variation == "cutoff")
{
	require_once __DIR__ . '/classes/CutoffOperator.php';
	//$pdf = new OrderStatus(20);

	$pdf = new CutOffOperator($customer_name);
}
elseif($variation == "welder")
{
	require_once __DIR__ . '/classes/RepairWelder.php';
	//$pdf = new OrderStatus(20);

	$pdf = new RepairWelder($customer_name);
}
elseif($variation == "mill")
{
	require_once __DIR__ . '/classes/MillOperator.php';
	//$pdf = new OrderStatus(20);

	$pdf = new MillOperator($customer_name);
}


$pdf->Output("training inspectors.pdf");
