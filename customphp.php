<?php

require 'includes/db.php';

	$coil = $_POST['coil_no'];	
	$check_coil = "SELECT * FROM coil_tbl WHERE coil_no = '".$coil."'";
	$result_coil = mysqli_query($db , $check_coil);
	//echo mysqli_error($db);
	
	$nums = mysqli_num_rows($result_coil);
	// if($nums > 0)
	// {
		// $status='exists';echo json_encode(0);
	// }
	// else
	// {
	$weight = $_POST['weight'];
		$work_no = $_POST['work'];	
		$operator = $_POST['operator'];
		$cycles = $_POST['cycles'];
		$allocated = $_POST['allocated'];
		$tpm_job = $_POST['job'];		
		$date_received = $_POST['date_received'];
		$footage = $_POST['footage'];  //chnage by vishal
		$no_coil = $_POST['no_coil'];  //chnage by vishal
	
		if($nums > 0){
			$sql = "UPDATE `coil_tbl` SET `work`='$work_no',`weight`='$weight',`allocated`='$allocated',`job`='$tpm_job',`date_received`='$date_received',`operator`='$operator',`cycles`='$cycles',`footage`='$footage',`no_of_coil`='$no_coil' WHERE `coil_no`='$coil'";
		}else{
			$sql = "INSERT INTO coil_tbl (coil_no , weight , work , operator , cycles , allocated , job , date_received, footage, no_of_coil)
				VALUES('$coil' , '$weight' , '$work_no' , '$operator' , '$cycles' , '$allocated' , '$tpm_job' , '$date_received', '$footage', '$no_coil')";
		}
		
		$result = mysqli_query($db , $sql);
		// if( mysqli_error($db) ) {
		// 	echo json_encode( 2 );
		// 	exit;
		// }
		
		$sql = "SELECT * FROM steel_tbl WHERE work = '$work_no'";
		$result = mysqli_query( $db, $sql );
		$row = mysqli_fetch_assoc( $result );
		
		$tpm_po_no = $row['tpm_po'];
		$material = $row['material'];
		$gage = $row['gage'];
		$width = $row['width'];
		$hole_pattern = $row['pattern'];
		$hole_size = $row['holes'];
		$hole_center = $row['centers'];
		$heat = $row['heat'];	
		
		
		if($nums > 0){
			$sql = "DELETE FROM `new_materials` WHERE coil_no='$coil'";
			mysqli_query($db , $sql);
		}
		$sql = "INSERT INTO new_materials(coil_no , tpm_po_no , work_no , weight , material , gage , width , hole_pattern , hole_size , hole_centers , heat_number , date_received , allocated , tpm_job , cycles)
								VALUES('$coil' , '$tpm_po_no' , '$work_no' , '$weight' , '$material' , '$gage' , '$width' , '$hole_pattern' , '$hole_size' , '$hole_center' , '$heat' , '$date_received' , '$allocated' , '$tpm_job' , '$cycles')";
		//$r = $db->query($sql);
		//echo $r->error;
		$result = mysqli_query($db , $sql);
		if($result){
			echo json_encode(1);
		}
		// if( mysqli_error($db) ) {
		// 	echo json_encode( 3);
		// }
		// else {
		// 	echo json_encode(1);
		// }
		
		
	// }