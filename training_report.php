<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once($_SERVER['DOCUMENT_ROOT'].'/api/core/config.php');
include_once("/vendor/autoload.php");


// reference the Dompdf namespace
use Dompdf\Dompdf;

$userid = $_GET['userid'];
$partn = $_GET['partn'];
$od = $_GET['od'];
$length = $_GET['length'];
$clauses = array();
$output='';

function mres($value){
    
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, $value);
}

if (!empty($userid)){
    $val1 = mres($userid);
    $clauses[] = "customer='$val1'";
}
if(!empty($partn)){
    $val2 = mres($partn);
    $clauses[] ="Part='$val2'";
}
if(!empty($od)){
    $val3 = mres($od);
    $clauses[] = "dim='$val3'";
}
if(!empty($length)){
    $val4 = mres($length);
    $clauses[]="length=$val4";
}

if (count($clauses) < 1){
    echo 'Please Select Specific Detail!.';
}

$query = "SELECT * FROM `mat_req` WHERE ";

if (count($clauses)>0){
   
    $query .= implode(' AND ', $clauses);

}

$stmt = $dbh->prepare($query); 
$stmt->execute();
$result = $stmt->fetchAll();


// $dbh = db_connect();
if (!$dbh) {
    die("Error in database connection!");
}


$html='<html style="margin:120px 50px"><head>
<style>
    h1 { color: #231F20; font-family:"Book Antiqua", serif; font-style: normal;font-weight: bold; text-decoration: none; font-size: 24pt; }
   p,table {color: #231F20; font-family:"Book Antiqua", serif; font-style: normal;font-weight: normal; text-decoration: none; font-size: 12pt; margin:0pt; }
</style>
</head>
<body>
 <div style="width:100%;margin:0 auto; text-align:center;margin-top:0 !important">
     <h1 style="margin-top:0 !important;text-align:center;padding-bottom:15px;    margin-bottom: 20px;border-bottom: 3px double #000;">Material Requirments Report</h1>
     <div>
     <table style="width:100%;" cellspacing="0">
          <thead>
              <tr>
                  <td style="padding:5px 0;text-align:center"><b>Id</b></td>
                  <td style="padding:5px 0;text-align:center"><b>po</b></td>
                  <td style="padding:5px 0;text-align:center"><b>customer</b></td>
                  <td style="padding:5px 0;text-align:center"><b>Part</b></td>
                  <td style="padding:5px 0;text-align:center"><b>quantity</b></td>
                  <td style="padding:5px 0;text-align:center"><b>dim</b></td>
                  <td style="padding:5px 0;text-align:center"><b>length</b></td>
                  <td style="padding:5px 0;text-align:center"><b>pattern</b></td>
                  <td style="padding:5px 0;text-align:center"><b>holes</b></td>
                  <td style="padding:5px 0;text-align:center"><b>centers</b></td>
                  <td style="padding:5px 0;text-align:center"><b>gage</b></td>
                  <td style="padding:5px 0;text-align:center"><b>strip</b></td>
                  <td style="padding:5px 0;text-align:center"><b>is_od</b></td>
                  <td style="padding:5px 0;text-align:center"><b>material</b></td>
                  <td style="padding:5px 0;text-align:center"><b>Weight_bs</b></td>
              </tr>
          </thead>
          <tbody>';
         
          foreach($result as $rowsingle){
            $html .= '<tr>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['Id'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['po'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['customer'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['Part'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['quantity'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['dim'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['length'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['pattern'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['holes'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['centers'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['gage'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['strip'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['is_od'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['material'].'</td>
                    <td style="border-bottom:1px solid lightgray;text-align:center;padding:5px 0">'.$rowsingle['Weight_bs'].'</td>
                </tr>';

        }
          $html.='
</tbody>
</table></div>
</div>
</body>
</html>';
// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->set_paper('a4', 'landscape');
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'landscape');
// Render the HTML as PDF
$value = rand(0,1000);
$pdfName = "Material Requirments on Search Result ".$clauses;
$dompdf->render($pdfName);

// Output the generated PDF to Browser
$dompdf->stream($pdfName, array("Attachment" => false));

exit(0);