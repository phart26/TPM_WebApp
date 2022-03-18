<?php
include_once("./vendor/autoload.php");

// reference the Dompdf namespace
use Dompdf\Dompdf;

// Getting Quote value
$coil_number = $_GET["coil_number"];
$no_of_items=$_GET["no_of_items"];

if (empty($coil_number)) {
    die("Error Occured");
}

function db_connect() {
    $host = "localhost";
    $db = "final";
    $username = "root";
    $pass = "123456";
    $dbh = null;
    $dbh = new PDO("mysql:host=$host;dbname=$db", $username, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $dbh;
}



function getItemDetail($dbh, $coil_number,$no_of_items) {
    $query = "select coil_no,work,weight,operator,'' comment_1 from coil_tbl where coil_no >= $coil_number limit $no_of_items";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
}
$hostname = 'mysql.hostinger.in';
$db_name = 'u886168621_tpmv2';
$db_username = 'u886168621_tpmv2';
$db_password = 'Reset123';
$db = new mysqli($hostname, $db_username, $db_password, $db_name);

$dbh = db_connect();
if (!$db) {
    die("Error in database connection!");
}
$result = getItemDetail($db, $coil_number,$no_of_items);
if (empty($result)) {
    die("Error in Generating Report!");
}


$html="<html>
	<head>
		<style>
			body, h1,h2,h3,h4,h5,div,table,tr,td,hr, table,p,tr,td{
				padding: 5px;
				margin: 0;
			}
			table {
				border-collapse: collapse;
			}
		</style>
	</head>
	<body>
			<div style='width: 100%;'>
				<div style='text-align: center;'>
					<h1>TPM Steel Receiving Sheet</h1>
				</div>
				<table  style='border:0 !important;;float:left;width:100%;border-top-style:solid;border-bottom-style:solid;border-bottom-width: 2px;border-color: black;margin-top: 10px;'>
					<tr style='border-bottom:2px solid'>
						<td align='center' width='10%'>Coil Number</td>
						<td align='center' width='30%'>Work Number</td>
						<td align='center' width='20%'>Weight</td>
						<td align='center' width='20%'>Operator</td>
						<td align='center' width='20%'>Comment</td>
					</tr>";
                                        foreach($result as $key=>$item){
                                            $html .= "
                                             <tr style='border-bottom:2px solid;width:'>
                                                    <td align='center' width='10%'>".$item['coil_no']."</td>
                                                    <td align='center' width='30%'>".$item['work']."</td>
                                                    <td align='center' width='20%'>".$item['weight']."</td>
                                                    <td align='center' width='20%'>".$item['operator']."</td>
                                                    <td align='center' width='20%'>".$item['comment_1']."</td>
                                                </tr>"; 
                                        }                                       
            $html.="</table ></div>
						<div style='clear: both;'></div><br><br><br><br><div style='width: 100%;'><br><br><br><br><br>
					<table style='float:left;width:100%;margin-top: 10px;'><tr>
                                        <td align='center' width='10%'>Work#</td>
                                        <td align='center' width='30%'>Material</td>
                                        <td align='center' width='20%'>Gage</td>
                                        <td align='center' width='20%'>Hole Pattern</td>
                                        <td align='center' width='20%'>Width</td>
                                        </tr></table>
		</div>
	</body>
</html>";
//// instantiate and use the dompdf class
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