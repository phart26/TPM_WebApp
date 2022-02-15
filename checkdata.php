<?php
	include 'includes/db.php';
	
	$action = $_GET['action'];
	if($action == 'work_no_check')
	{
		$work_no = $_GET['work_no'];	
		
		
		$check_work = "SELECT * FROM steel_tbl WHERE work = $work_no";
		$result_work = mysqli_query($db , $check_work);
		echo mysqli_error($db);
		
		$nums = mysqli_num_rows($result_work);
		//echo json_encode($nums);
		if($nums > 0 )
		{
			//echo '<h3 style="color : red;">Work Number Exists!</h3>';
			echo 'Work Number Exists!';
		}
	}
	elseif($action == 'coil_no_check')
	{
		$coil = $_GET['coil_no'];	
		$check_coil = "SELECT * FROM coil_tbl WHERE coil_no = $coil";
		
		$result_coil = mysqli_query($db , $check_coil);
		echo mysqli_error($db);
		
		$nums = mysqli_num_rows($result_coil);
		if($nums > 0)
		{
			//echo '<h3 style="color : red;">Coil Number Exists!</h3>';
			echo 'Coil Number Exists!';
		}
	}
	else if($action == 'work_no_check_ajax')
	{
		$work_no = $_GET['work_no'];	
		
		
		$check_work = "SELECT * FROM steel_tbl WHERE work = $work_no";
		$result_work = mysqli_query($db , $check_work);
		echo mysqli_error($db);
		
		$nums = mysqli_num_rows($result_work);
		//echo json_encode($nums);
		if($nums > 0 )
		{
			echo json_encode( 1 );
		}
		else 
			echo json_encode( 0 );
	}
	elseif($action == 'get_wo_no')   // change by vishal
	{
		$coil = $_GET['coil_no'];	
		$check_coil = "SELECT work FROM coil_tbl WHERE coil_no = $coil";
		
		$result_wo = mysqli_query($db , $check_coil);
		$date = mysqli_fetch_assoc($result_wo);
		echo $date["work"];
		
	}
?>
