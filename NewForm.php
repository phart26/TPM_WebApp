<?php 
	include_once("../vendor/autoload.php");
	// reference the Dompdf namespace
	use Dompdf\Dompdf;
	
	require 'includes/db.php';
	$coil_number = intval( $_GET["coil_number"] );
	$no_of_items = intval( $_GET["no_of_items"] );
	
	$limit = $coil_number + $no_of_items;
	$coil_number_start = $coil_number;
	
	for( $i = 1; $i <= $no_of_items; $i++ ) {
		
		$sql = "INSERT INTO coilno_tbl (coil_no , no_of_items) VALUES ('$coil_number' , '$no_of_items')";	
		$result = mysqli_query($db , $sql);
		mysqli_error($db);
		
		$coil_number++;
		
	}
	
	

	
	
	
	
	$html = '<div style=" width : 100%; border 1px solid; background-color : white; ">
				<div class="">
					<div class="db-header-extra form-inline" style="text-align : right;"> 
						<br>
						<div class="db-header-extra form-inline" style="text-align : right; margin-top : 0px; margin-right : 72px;">
							<b>TPM Steel</b><br>
							<b>Receiving Sheet</b><br>
						</div>	
						
						
						<div class="db-header-extra form-inline" style="text-align : left; margin-top : -35px; margin-left : 40px;">
							<img src="pages/logo_tpm.png" width="300px" height="80px">
						</div>		
						<div style="padding: 10px 0px; border-bottom: 1px solid black; width: 93%; margin-top: 20px; margin-left: 40px; text-align : center;">				
							<b></b>			
						</div>			  
					</div>		  
				</div>

				<br><br>
				<div class="row" style="clear: both;">
					<div class="col-md-12">
						<div class="">            
							<div class="table-responsive">
								<div >
									<table style="width:100%; margin-left : 40px; border-collapse: collapse; " border="0">
										<thead>
											<tr style="color:Black; text-align: left; ">                       
												<th width="20%">Coil Number</th>                        
												<th width="20%">Work Number</th>
												<th width="20%">Weight</th>
												<th width="20%">Cycles</th>            
												<th width="20%">Operator</th>            
											</tr>					
										</thead>
									</table>
								</div>';
								for($i = $coil_number_start ; $i < $limit ; $i++)
								{
									$html .= '<p style="overflow: auto; padding: 10px 0px; border-bottom: 1px solid black; width: 88%; margin: 20px 20px 20px 50px; text-align : center;">				
										<span style="float: left;">'.$coil_number_start.'</span>
										<b></b>			
									</p>';
									$coil_number_start++;
								}	
								// $html .= '<div style="padding: 10px 0px; border-bottom: 1px solid black; width: 88%; margin: 20px 20px 20px 50px; text-align : center;">				
										// <b></b>			
									// </div>';
						
							$html .= '</div>
						</div>
					</div>      
				</div>
			</div>';
			
			echo $html;
// echo $html;
// exit;
//$dompdf = new Dompdf();

///$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
//$value = rand(0,1000);
//$pdfName = "AUDIT-report-$quote".$value;
//$dompdf->render($pdfName);

// Output the generated PDF to Browser
//$dompdf->stream($pdfName, array("Attachment" => false));
?>
<script>
	window.print();
</script>


