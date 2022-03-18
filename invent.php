<?php

include_once("./vendor/autoload.php");
// reference the Dompdf namespace
use Dompdf\Dompdf;



function db_connect()
{
	$host="localhost";
	$db="final";
	$username="root";
	$pass="123456";
	$dbh = null;
	$dbh = new PDO("mysql:host=$host;dbname=$db", $username, $pass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $dbh;
}

function getAuditDetail($dbh)
{
	$query = "SELECT 
				`rnd`, 
				audit_tbl.coil, 
				coil_tbl.weight, 
				steel_tbl.material, 
				steel_tbl.gage, 
				steel_tbl.width, 
				steel_tbl.holes, 
				steel_tbl.centers, 
				steel_tbl.pattern
			FROM audit_tbl INNER JOIN (steel_tbl INNER JOIN coil_tbl ON steel_tbl.work = coil_tbl.work) ON audit_tbl.coil = coil_tbl.coil_no
			ORDER BY `rnd` limit 50";
	$stmt = $dbh->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll();
	return $result;
}

$dbh = db_connect();
if(!$dbh)
{
	die("Error in database connection!");
}

$results = getAuditDetail($dbh);

if(empty($results))
{
	die("Error in Generating Report!");
}

$html = "<html>
	<head>
		<style>
			body, h1,h2,h3,h4,h5,div,table,tr,td,hr, table,p,tr,td{
				padding: 0;
				margin: 0;
			}
			table {
				border-collapse: collapse;
			}
		</style>
	</head>
	<body>
			<div style='width: 100%;'>
				<br>
				<div style='text-align: left;'>
					<h1>Inventory Audit Check</h1>
				</div>
				<div style='text-align: left;float:left;font-weight: bold;width:100%;border-bottom-size:2px;border-bottom-style:solid;border-color:black;'>
					<table width='100%' style='font-weight: bold;line-height: 30px;font-size: 17px;'>
						<tr>
							<td width='10%'>Coil</td>
							<td width='10%'>Weight</td>
							<td width='10%'>Material</td>
							<td width='10%'>Gage</td>
							<td width='10%'>Hole size</td>
							<td width='15%'>Hole centers</td>
							<td width='15%'>Hole pattern</td>
						</tr>
					</table>
				</div>
				<div style='clear: both;'>
				<div style='width: 100%;float:left;'>
					<table width='100%' style='line-height: 30px;'>";
					
					
				foreach($results as $result)
				{
							$html .= "<tr>
							<td width='10%'>$result[coil]</td>
							<td width='10%'>$result[weight]</td>
							<td width='10%'>$result[material]</td>
							<td width='10%'>$result[gaze]</td>
							<td width='10%'>$result[width]</td>
							<td width='15%'>$result[centers]</td>
							<td width='15%'>$result[pattern]</td>
						</tr>";
				}
						
			$html .= 		"</table>
				</div>
				
			</div>
	</body>
</html>	";

// instantiate and use the dompdf class
$dompdf = new Dompdf();

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$value = rand(0,1000);
$pdfName = "AUDIT-report-$quote".$value;
$dompdf->render($pdfName);

// Output the generated PDF to Browser
$dompdf->stream($pdfName, array("Attachment" => false));
exit(0);

?>
