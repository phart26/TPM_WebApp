<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/api/core/config.php');
include_once("/vendor/autoload.php");
$dbh = $config['database']['dbh'];
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// reference the Dompdf namespace
use Dompdf\Dompdf;


// Getting Quote value
$coil_number = $_GET["coil_number"];
$no_of_items=$_GET["no_of_items"];

if (empty($coil_number)) {
    die("Error Occured");
}

function getItemDetail($dbh, $coil_number,$no_of_items) {
    $query = "select coil_no,work,weight,operator,'' comment_1 from coil_tbl where coil_no >= $coil_number limit $no_of_items;";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
}

// $dbh = db_connect();
if (!$dbh) {
    die("Error in database connection!");
}
$result = getItemDetail($dbh, $coil_number,$no_of_items);
if (empty($result)) {
    die("Error in Generating Report!");
}


$html='<html style="margin:120px 50px"><head>
<style>
    h1 { color: #231F20; font-family:"Book Antiqua", serif; font-style: normal;font-weight: bold; text-decoration: none; font-size: 24pt; }
   p,table {color: #231F20; font-family:"Book Antiqua", serif; font-style: normal;font-weight: normal; text-decoration: none; font-size: 12pt; margin:0pt; }
</style>
</head>
<body>
 <div style="width:100%;margin:0 auto; text-align:center;margin-top:0 !important">
     <h1 style="margin-top:0 !important;text-align:center;padding-bottom:15px;    margin-bottom: 20px;border-bottom: 3px double #000;">TPM Steel Receiving Sheet</h1>
     <div>
     <table style="width:100%;" cellspacing="0">
          <thead>
              <tr>
                  <td style="padding:5px 0;text-align:center"><b>Coil Number</b></td>
                  <td style="padding:5px 0;text-align:center"><b>Work Number</b></td>
                  <td style="padding:5px 0;text-align:center"><b>Weight</b></td>
                  <td style="padding:5px 0;text-align:center"><b>Operator</b></td>
                  <td style="padding:5px 0;text-align:center"><b>Comment</b></td>
              </tr>
          </thead>
          <tbody>';
          foreach($result as $key=>$item){
            $html .= '<tr>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$item['coil_no'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$item['work'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$item['weight'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$item['operator'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$item['comment_1'].'</td>
                </tr>';

        }
          $html.='<tr>
          <td style="text-align:left;padding-top:100px;border-top:1px solid #000">Work#</td>
          <td style="text-align:left;padding-top:100px;border-top:1px solid #000">Material</td>
          <td style="text-align:left;padding-top:100px;border-top:1px solid #000">Gage</td>
          <td style="text-align:left;padding-top:100px;border-top:1px solid #000">Hole Pattern</td>
          <td style="text-align:left;padding-top:100px;border-top:1px solid #000">Width</td>
      </tr>
</tbody>
</table></div>
</div>
</body>
</html>';
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