<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once($_SERVER['DOCUMENT_ROOT'].'/api/core/config.php');
include_once("/vendor/autoload.php");
$db = $config['database']['dbh'];
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// reference the Dompdf namespace
use Dompdf\Dompdf;

$client  = mres( $_GET['userid']);
$part  = mres( $_GET['part']);
$cutoff_operator  = mres( $_GET['cutoff_operator']);
$inspector  = mres( $_GET['inspector']);
$mill_operator  = mres( $_GET['mill_operator']);
$repair_welder  = mres( $_GET['repair_welder']);



$search = new training_report($client,$part,$cutoff_operator,$inspector,$mill_operator,$repair_welder);

$output='';

function mres($value){
    
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, $value);
}


$html='<html style="margin:120px 50px"><head>
<style>
    h1 { color: #231F20; font-family:"Book Antiqua", serif; font-style: normal;font-weight: bold; text-decoration: none; font-size: 24pt; }
   p,table {color: #231F20; font-family:"Book Antiqua", serif; font-style: normal;font-weight: normal; text-decoration: none; font-size: 12pt; margin:0pt; }
</style>
</head>
<body>
 <div style="width:100%;margin:0 auto; text-align:center;margin-top:0 !important">
     <h1 style="margin-top:0 !important;text-align:center;padding-bottom:15px;    margin-bottom: 20px;border-bottom: 3px double #000;">Training Report</h1>
     <div>
     <table style="width:100%;" cellspacing="0">';
     $html.= $search->all_reusult();    
$html.='
</tbody>
</table></div>
</div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->set_paper('a4', 'landscape');
$dompdf->loadHtml($html);
$value = rand(0,1000);
$pdfName = "Training Report".$clauses;
$dompdf->render($pdfName);
$dompdf->stream($pdfName, array("Attachment" => false));
exit(0);



class training_report{
    
        private $que = array();
        private $field;
        
        public function __construct($customer_id,$part_id,$cutoff_operator,$inspector,$mill_operator,$repair_welder){
            if( !empty($customer_id)){
                $this->que[] = "orders_tbl.cust_id='$customer_id'";
            }
            if( !empty($part_id)){
                $this->que[] = "orders_tbl.part='$part_id'"; 
            }
            if( !empty($mill_operator)){
                $this->que[] = "orders_tbl.mill_operator='$mill_operator'"; 
            }
            if( !empty($cutoff_operator)){
                $this->que[] = "orders_tbl.cutoff_operator='$cutoff_operator'"; 
            }
            if( !empty($repair_welder)){
                $this->que[] = "orders_tbl.repair_welder='$repair_welder'"; 
            }
            if( !empty($inspector)){
                $this->que[] = "orders_tbl.inspector='$inspector'"; 
            }
        }
    public function all_reusult(){
        
        global $db; 
        $query = "SELECT orders_tbl.job,cust_tbl.customer,orders_tbl.part,orders_tbl.quantity,orders_tbl.mill_operator,orders_tbl.cutoff_operator,orders_tbl.repair_welder,orders_tbl.inspector,orders_tbl.weld_spec_mill, orders_tbl.weld_spec_repair, orders_tbl.cust_id, DATE_FORMAT(orders_tbl.began, '%m/%d/%Y') began  FROM orders_tbl  INNER JOIN cust_tbl ON  orders_tbl.cust_id = cust_tbl.cust_id HAVING ";
    
        if (count($this->que)>0){
            $query .= implode(' AND ', $this->que);
            // $query .=" AND ((orders_tbl.began)>=Now()-180))";
        }
        else{
            // $query .=" ((orders_tbl.began)>=Now()-180))";
        }
        $query .= " ORDER BY orders_tbl.job DESC";
        $this->field .= "<tr><td>Job No</td><td>Customer</td><td>Part</td><td>Quantity</td><td>Mill Operator</td><td>Cutoff Operator</td><td>Repair Welder</td><td>Inspector</td><td>Weld Spec Mill</td><td>Weld Spec Repair</td><td>Began</td></tr>";
    
        $prepare = $db->prepare($query);
        $prepare->execute();
       
        $prepare->setFetchMode(PDO::FETCH_ASSOC); 

        $result = $prepare->fetchAll();
      
        foreach($result as $resul) {
                $job =$resul['job'];
                $cust = $resul['customer'];
                $part = $resul['part'];
                $quantity = $resul['quantity'];
                $mill = $resul['mill_operator'];
                $cutt = $resul['cutoff_operator'];
                $rw = $resul['repair_welder'];
                $ins = $resul['inspector'];
                $wsm = $resul['weld_spec_mill'];
                $wsr = $resul['weld_spec_repair'];
                $bg = $resul['began'];
                $this->field .= "<tr><td>$job</td><td>$cust</td><td>$part</td><td>$quantity</td><td>$mill</td><td>$cutt</td><td>$rw</td><td>$ins</td><td>$wsm</td><td>$wsr</td><td>$bg</td></tr>";
            }
  
    
    return $this->field;
        
    }
    
    
    
    }