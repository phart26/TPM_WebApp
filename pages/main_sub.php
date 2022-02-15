<?php

ini_set('error_displays','1');

//session_start();

require_once('classes/quote_setting.php');
require_once('classes/setting.php');
require_once('classes/standard_price.php');
require_once('classes/training_report.php');

function get_latest($table,$coll){
    global $db;    
    $tablest = addcslashes($table,'\'');
    $colls = addcslashes($coll,'\'');
    $query = "SELECT `$colls` FROM `$tablest` ORDER by `$colls` DESC LIMIT 1";
    $result = $db->query($query);
    return $result;

}

function getStrip($partNum){
    global $db;
    $query = "SELECT * FROM part_tbl WHERE part = '$partNum'";
    $result = $db->query($query);
    return $result;
}

function getPart($job){
    global $db;
    $query = "SELECT * FROM stamping_orders_tbl WHERE job = '$job'";
    $result = $db->query($query);
    return $result;
}

function matsearch($dataall){
    global $db;
    $userid = $dataall['userid'];
    $partn = $dataall['partn'];
    $od = $dataall['od'];
    $length = $dataall['length'];
    $is_od =  (int)$dataall['odcheck'];
    $clauses = array();
    $output='';
  
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
    if(!empty($is_od)){
        $val5 = $is_od;
        $clauses[]="is_od=$val5";
    }
   
    $query = "SELECT * FROM `mat_req` WHERE ";

    if (count($clauses)>0){
       
        $query .= implode(' AND ', $clauses);
    
    }
    
    $result = $db->query($query);

    foreach( $result as $rowsingle){
        $output .='<tr><td>'.$rowsingle['Id'].'</td><td>'.$rowsingle['customer'].'</td><td>'.$rowsingle['Part'].'</td><td>'.$rowsingle['quantity'].'</td><td>'.$rowsingle['dim'].'</td><td>'.$rowsingle['length'].'</td><td>'.$rowsingle['pattern'].'</td><td>'.$rowsingle['holes'].'</td><td>'.$rowsingle['centers'].'</td><td>'.$rowsingle['gage'].'</td><td>'.$rowsingle['strip'].'</td><td>'.$rowsingle['is_od'].'</td><td>'.$rowsingle['po'].'</td><td>'.$rowsingle['material'].'</td><td>'.$rowsingle['Weight_bs'].'</td></tr>';
    }
    if ($result == null){
        return '<tr><td colspan="10">No Record found.</td></tr>';
        
    }else{
        return $output;
    }
    

}
function mres($value){
    
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, $value);
}

function user_setting_save($data){
    
    $connect = new setting_save($_SESSION['user_id'], $data); 
    
    if ($connect->user_setting_exist()!=0){
        
        print_r( $connect->update_setting());

    }else{

         echo $connect->insert_setting();
 
    }
     $cl = new fet_field($_SESSION['user_id']);
    
     return $cl->input_print();
 

}
function od_length($table){
    global $db;$table = mres($table);
    $result = $db->query("SELECT DISTINCT `dim`,`length` FROM `$table`");
    return $result;
}
function die_stamp($die_id, $progression){
    global $db;
    $result = $db->query("INSERT INTO `die_stamp_tbl` (`die_id`, `progression`)  VALUES ('".$die_id."', '".$progression."'");
    return $result;
}
function odlengthpart($table){
    global $db;
    $result = $db->query("SELECT DISTINCT `dim`,`finished_length` FROM `part_tbl`");
    return $result;
}

function employee(){
    global $db;
    $query1 = "SELECT DISTINCT name FROM `employee` WHERE mill_operator=1";
    $result1 = $db->query($query1);
    $query2 = "SELECT DISTINCT name FROM `employee` WHERE cutoff_operator=1";
    $result2 = $db->query($query2);
    $query3 = "SELECT DISTINCT name FROM `employee` WHERE repair_welder=1";
    $result3 = $db->query($query3);
    $query4 = "SELECT DISTINCT name FROM `employee` WHERE inspector=1";
    $result4 = $db->query($query4);

    $allarray = array(
        'mill_operator'=>$result1,
        'cutoff_operator'=>$result2,
        'repair_welder'=>$result3,
        'inspector'=>$result3,
    );

    return $allarray;
}
function training_report($all){
    $client  = mres( $all['userid']);
    $part  = mres( $all['part']);
    $cutoff_operator  = mres( $all['cutoff_operator']);
    $inspector  = mres( $all['inspector']);
    $mill_operator  = mres( $all['mill_operator']);
    $repair_welder  = mres( $all['repair_welder']);
    
    $search = new training_report($client,$part,$cutoff_operator,$inspector,$mill_operator,$repair_welder);
    
    return $search->all_reusult();
   
   
    //return $all;
    
}
function standard_prices($get){
    $is_od = (int) $get['odcheck'];
    $standard = new standard_price($get['customer_id'],$get['part_id'],$get['selectod'],$get['length'],$is_od);
    $data = $standard->result_return();
    $retrundtaa;
    if (count($data) >0 && $data != 'No Record found!'){
        foreach($data as $dat){
            $cust = $dat['cust_id'];
            $custname = $dat['customer'];
            $part = $dat['part'];
            $dim = $dat['dim'];
            $finished_length = $dat['finished_length'];
            $quantity = $dat['quantity'];
            $description = $dat['description'];
            $type = $dat['type'];
            $price = $dat['price'];
            $is_od = $dat['is_od'];
            $retrundtaa .= "<tr><td>$cust</td><td>$custname</td><td>$part</td><td>$dim</td><td>$is_od</td><td>$finished_length</td><td>$quantity</td><td>$description</td><td>$type</td><td>$price</td></tr>";
        }
    }

    else{
        $retrundtaa .= "<tr><td colspan=5>Record Not Found!</td></tr>";
    }
    
    return $retrundtaa; //$standard->result_return();

}

function Enter_as_Used_mesh($newval,$mesh_n,$oldone){
   global $db;
    $total  = ((int)$oldone)-((int)$newval);
    $retRe = '';
    if(!empty($mesh_n)){
        
        $querys = "SELECT * FROM `mesh_tbl` WHERE mesh_no=".$mesh_n;
        $resultf = $db->query($querys);

        foreach($resultf as $res){
            $mesh_no=$res["mesh_no"];
            $supplier=$res["supplier"];            
            $allocated=$res["allocated"];
            $job = $res["job"];            
            $tpm_po = $res["tpm_po"];
            $date_received=$res["date_received"];
            $operator= $res["operator"];
            $width = $res["width"];
            $length= $newval;
            $heat = $res["heat"];
            $mesh = $res["mesh"];
            $type = $res["type"];  
            $TPM_JOB= $res["TPM_JOB"];


            $query = "INSERT INTO `used_mesh` (`mesh_no`, `supplier`, `allocated`, `job`, `tpm_po`, `date_received`, `operator`, `width`, `length`, `heat`, `mesh`, `type`, `TPM_JOB`) VALUES ('".$mesh_no."', '".$supplier."','".$allocated."', '".$job."', '".$tpm_po."', '".$date_received."', '".$operator."', '".$width."', '".$length."', '".$heat."', '".$mesh."', '".$type."', '".$TPM_JOB."')";
            $result = $db->query($query);
        };
        if($total !=0){
            $query ="Update mesh_tbl set length = $total where mesh_no = $mesh_n" ;
            $result = $db->query($query);
            $retRe['query'] = 1;
        }
        else{
            $query = "DELETE FROM mesh_tbl WHERE mesh_no = $mesh_n";
            $result = $db->query($query);
            $retRe['query'] = 2;
        }
    }
    else{
      $retRe['query'] = 3;
    }
    
    return $retRe;
}
function meshtotal_show($jobid){
    $jobid = mres($jobid);
    global $db;
    $query1 = "SELECT length AS ML FROM `mesh_tbl` WHERE TPM_JOB = $jobid";
    $weight1 = $db->query($query1);
    $query2 = "SELECT length AS El FROM `used_mesh` where TPM_JOB = $jobid";
    $weight2 = $db->query($query2);
    $res=[];
    if(count($weight1)>0){
        $total1;
        foreach($weight1 as $key=>$weight){
            $total1 = $total1 + (int) $weight['ML'];
        }
        $res['ML'] = $total1;
    }else{
       $res['ML'] = 0 ; 
    }
    if(count($weight2)>0){
        $total2;
        foreach($weight2 as $weight){
            $total2 = $total2 + (int) $weight['El'];
        }
        $res['EL'] = $total2;
    }else{
       $res['EL'] = 0 ; 
    }
    return $res;
}
