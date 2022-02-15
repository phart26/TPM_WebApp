<?php 
	include_once("../vendor/autoload.php");
	// reference the Dompdf namespace
	use Dompdf\Dompdf;
	
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
									<table style="width:100%; margin-left : 20px; border-collapse: collapse; " border="0">
										<thead>
											<tr style="color:Black; text-align: center; ">                       
												<th width="20%">Coil Number</th>                        
												<th width="20%">Work Number</th>
												<th width="20%">Weight</th>
												<th width="20%">Cycles</th>            
												<th width="20%">Operator</th>            
											</tr>					
										</thead>
									</table>
								</div>';
								for($i = 0 ; $i < 12 ; $i++)
								{
									$html .= '<p style="padding: 10px 0px; border-bottom: 1px solid black; width: 88%; margin: 20px 20px 20px 50px; text-align : center;">				
										<b></b>			
									</p>';
								}	
								$html .= '<div style="padding: 10px 0px; border-bottom: 1px solid black; width: 88%; margin: 20px 20px 20px 50px; text-align : center;">				
										<b></b>			
									</div>';
						
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


