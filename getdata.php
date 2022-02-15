<?php 
error_reporting(E_ALL); ini_set('display_errors', 1); 
	require 'includes/db.php';
	$coil = $_GET['coil'];
	
	$sql = "SELECT coil_tbl.* , steel_tbl.* , steel_tbl.work as awork 	
			FROM coil_tbl JOIN 
			steel_tbl ON coil_tbl.work  = steel_tbl.work 
			WHERE coil_tbl.coil_no = '".$coil."'";
			
	$result = mysqli_query($db, $sql);
	echo mysqli_error($db);
	
	$data = mysqli_fetch_assoc($result);
	echo json_encode( $data );






?>