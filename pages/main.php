<?php
require_once("config.php");

require_once("session.php");


// default headers
header('Server: Apache');
header('X-Powered-By: PHP');

  
// CORS
if (isset($_SERVER['HTTP_ORIGIN']))
{
    header('Access-Control-Max-Age: 3600');
    header("Access-Control-Allow-Origin: ". $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: ". $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
    
    exit;
}


function done($result)
{
   // $result['profile'] = $_SESSION['profile'];   // change by vishal

    header('Content-Type: application/json');
    exit(json_encode($result));
}


class DB
{
    public $db;

    public function __construct()
    {
        $argc = func_num_args();

        if ($argc == 1)
        {
            $config = func_get_arg(0);
            $this->db = new mysqli( $config['host'], $config['username'], $config['password'], $config['database'] );

            return;
        }

        $config = func_get_args();
        $this->db = new mysqli( $config[0], $config[1], $config[2], $config[3] );
    }

    public function sanitize(&$input)
    {
        if (is_array($input))
        {
            foreach($input as $key=>$value)
                $this->sanitize($value);

            return $input;
        }

        return $input = $this->db->real_escape_string($input);
    }



    public function insert( $array )
    {
        $this->sanitize($value);

        return '(`'. implode('`,`', array_keys($array)) .'`) VALUES (\''. implode('\',\'', array_values($array)) .'\')';
    }

    public function update( $array )
    {
        $this->sanitize($array);

        $str = '';

        foreach($array as $key=>$val)
            $str .= ($str ? ',' : '') ." `$key`='$val'";

        return $str;
    }

    public function where( $array = array(), $equality=' = ', $operator=' AND ', $startEnclosing="'", $endEnclosing="'" )
    {
        $this->sanitize($array);

        $str = '';
if (is_array($array) || is_object($array))   // change by vishal
{
        foreach($array as $key=>$val)
            $str .= ($str ? $operator : '') . " `$key` $equality {$startEnclosing}{$val}{$endEnclosing}";
}
        return $str;
    }


    public function query($query_str, $mapFn = false, $isSingleTuple = false)
    {
        // fallback mapping function
        if ($mapFn==false) $mapFn = function( &$row ){ return $row; };

        $query = $this->db->query( $query_str );
		
       // if ($query && $query->num_rows)
        if ($query && isset($query->num_rows))   // change by vishal
        {
            $list = array();

            while($row = $query->fetch_array(1))
                $list[] = $mapFn( $row );

            if ($isSingleTuple) return $list[0];
            
            return $list;
        }

        return array();
    }
}



class Table
{
    public $db;
    public $table;

    public function __construct( $table )
    {
        global $db;

        $this->db    = $db;
        $this->table = $table;
    }

    public function fetch( $filter = array(), $limit = 20 )
    {
        $where = '';
if($this->table != 'part_tbl'){
        // search
		if (!is_array($filter) || !is_object($filter))
{
        if(!empty($filter))    // change by vishal
        {
            $searchArray  = array();
            $searchFields = explode(',', $filter['search']['fields']);

            foreach($searchFields AS $field)
                $searchArray[$field] = $filter['search']['text'];

            $where = '('. $this->db->where($searchArray, ' LIKE ', ' OR ', "'%", "%'") .')';

            unset($filter['search']);
        }
    }
}
		
    if($this->table == 'mac_add_tbl' ){    // change by vishal
        return $this->db->query("SELECT * FROM `{$this->table}` where device LIKE '%mill_%'");
    }else if($this->table == 'steel_tbl'){
        return $this->db->query("select * from steel_tbl where add_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)");
    }else if($this->table == 'drifts'){
        return $this->db->query("select * from drifts ORDER BY drift_od ASC");
    }else{
        // other condition
        $where .= ($where ? ' AND ' : '') . $this->db->where( $filter );
         
        if ($where) $where = ' WHERE ' . $where;

        // execute SQL
        
        return $this->db->query("SELECT * FROM `{$this->table}` $where");
           
    }
        
    }

    public function delete( $filter )
    {
        if($this->table == 'mesh_tbl' ){
            $mesh_no = $filter['mesh_no'];
            $this->db->query("DELETE FROM `mesh_tbl` WHERE mesh_no='$mesh_no'");
        }else{

            $this->db->query("DELETE FROM `". $this->table ."` WHERE ". $this->db->where( $filter ) . " LIMIT 1");
        }

        return $this->db->affected_rows;
    }

    public function insert( $array )
    {
        $this->db->query("INSERT INTO `". $this->table ."` ". $this->db->insert( $array ));

        return $this->db->affected_rows;
    }

    public function replace( $array )
    {
		
        $this->db->query("REPLACE INTO `". $this->table ."` ". $this->db->insert( $array ));

        $job_id = $array['job'];
        $device = $array['device'];
        $MAC_address = "";
        $file = 'logs.txt';
        file_put_contents($file, $device);

       // return $this->db->affected_rows;
       if($this->table == "orders_tbl")  // change by vishal
       {   
            require_once("db.php");
            
            $str = "SELECT * FROM `mac_add_tbl` where device='".$device."' ";
            $result_work = mysqli_query($db , $str);
            while($row = mysqli_fetch_assoc($result_work)) {
                $MAC_address = $row['MAC_address'];
            }
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://18.218.1.248:3000/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "jobid=$job_id&userid=$MAC_address",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
       }
        // return  $device;
    }

    public function update( $array, $filter )
    {
        $file = 'logs.txt';
        file_put_contents($file, 'data update');
        $id = (int)$array['id'];
        unset($array['id']);
        // unset($array['type_asbly']);
        // unset($array['stamping_cost']);
        // unset($array['scrap_mat']);
        // unset($array['scrap_mesh']);
        //unset($array['filter_mesh_seam_cost']);
        //unset($array['dpf_mc']);
unset($array['ppf_sellp']);
        unset($array['ppf_discount']);
        unset($array['ppf_fullp']);
        unset($array['ppf_discountp']);
        unset($array['ppf_packp']);



        unset($array['pflsm_pcrating']);
        unset($array['pflsm_ptfcrating']);
        unset($array['pflsm_ptcrating']);
        unset($array['pflsm_pocrating']);

        unset($array['pflsm_ptag']);
        unset($array['pflsm_ptftag']);
        unset($array['pflsm_pttag']);
        unset($array['pflsm_potag']);

        unset($array['pflsm_pshop_sppls']);
        unset($array['pflsm_ptfshop_sppls']);
        unset($array['pflsm_ptshop_sppls']);
        unset($array['pflsm_poshop_sppls']);

        unset($array['pflsm_pelctrc_cst']);
        unset($array['pflsm_ptfelctrc_cst']);
        unset($array['pflsm_ptelctrc_cst']);
        unset($array['pflsm_poelctrc_cst']);

        unset($array['pflsm_pblade_cst']);
        unset($array['pflsm_ptfblade_cst']);
        unset($array['pflsm_ptblade_cst']);
        unset($array['pflsm_poblade_cst']);

        unset($array['pflsm_pstmpng_cst']);
        unset($array['pflsm_ptfstmpng_cst']);
        unset($array['pflsm_ptstmpng_cst']);
        unset($array['pflsm_postmpng_cst']);

        unset($array['pflsm_pmtrl_cst']);
        unset($array['pflsm_ptfmtrl_cst']);
        unset($array['pflsm_ptmtrl_cst']);
        unset($array['pflsm_pomtrl_cst']);

        unset($array['pflsm_gas_cst']);
        unset($array['pflsm_ptfgas_cst']);
        unset($array['pflsm_ptgas_cst']);
        unset($array['pflsm_pogas_cst']);

        unset($array['pflsm_pstl_cst']);
        unset($array['pflsm_ptfstl_cst']);
        unset($array['pflsm_ptstl_cst']);
        unset($array['pflsm_postl_cst']);

        unset($array['pflsm_pctof_labr_cst']);
        unset($array['pflsm_ptfctof_labr_cst']);
        unset($array['pflsm_ptctof_labr_cst']);
        unset($array['pflsm_poctof_labr_cst']);

        unset($array['pflsm_pwldng_labr_cst']);
        unset($array['pflsm_ptfwldng_labr_cst']);
        unset($array['pflsm_ptwldng_labr_cst']);
        unset($array['pflsm_powldng_labr_cst']);

        unset($array['pflsm_psupply_cst']);
        unset($array['pflsm_ptfsupply_cst']);
        unset($array['pflsm_ptsupply_cst']);
        unset($array['pflsm_posupply_cst']);

        unset($array['pflsm_pmrgnl_cst']);
        unset($array['pflsm_ptfmrgnl_cst']);
        unset($array['pflsm_ptmrgnl_cst']);
        unset($array['pflsm_pomrgnl_cst']);

        unset($array['pflsm_pmarkup']);
        unset($array['pflsm_ptfmarkup']);
        unset($array['pflsm_ptmarkup']);
        unset($array['pflsm_pomarkup']);

        unset($array['pflsm_ptotal_cost']);
        unset($array['pflsm_ptftotal_cost']);
        unset($array['pflsm_pttotal_cost']);
        unset($array['pflsm_pototal_cost']);
        
        $this->db->query("UPDATE `". $this->table ."` SET ". $this->db->update( $array ) ." WHERE id=".$id);

            $t = "UPDATE `". $this->table ."` SET ". $this->db->update( $array ) ." WHERE id=".$id;
            

        return $this->db->affected_rows;
        
    }
    public function pagination( $array, $filter )
    {
        $this->db->query("UPDATE `". $this->table ."` SET ". $this->db->update( $array ) ." WHERE ". $this->db->where( $filter ));

        return $this->db->affected_rows;
    }

    public function newrecord($data){
        $field ='';
        $valset = '';
        unset($data['id']);
        // unset($data['type_asbly']);
        // unset($data['stamping_cost']);
        // unset($data['scrap_mat']);
        // unset($data['scrap_mesh']);
        //unset($data['filter_mesh_seam_cost']);
        //unset($data['dpf_mc']);

unset($data['ppf_sellp']);
        unset($data['ppf_discount']);
        unset($data['ppf_fullp']);
        unset($data['ppf_discountp']);
        unset($data['ppf_packp']);


        unset($data['pflsm_pcrating']);
        unset($data['pflsm_ptfcrating']);
        unset($data['pflsm_ptcrating']);
        unset($data['pflsm_pocrating']);

        unset($data['pflsm_ptag']);
        unset($data['pflsm_ptftag']);
        unset($data['pflsm_pttag']);
        unset($data['pflsm_potag']);

        unset($data['pflsm_pshop_sppls']);
        unset($data['pflsm_ptfshop_sppls']);
        unset($data['pflsm_ptshop_sppls']);
        unset($data['pflsm_poshop_sppls']);

        unset($data['pflsm_pelctrc_cst']);
        unset($data['pflsm_ptfelctrc_cst']);
        unset($data['pflsm_ptelctrc_cst']);
        unset($data['pflsm_poelctrc_cst']);

        unset($data['pflsm_pblade_cst']);
        unset($data['pflsm_ptfblade_cst']);
        unset($data['pflsm_ptblade_cst']);
        unset($data['pflsm_poblade_cst']);

        unset($data['pflsm_pstmpng_cst']);
        unset($data['pflsm_ptfstmpng_cst']);
        unset($data['pflsm_ptstmpng_cst']);
        unset($data['pflsm_postmpng_cst']);

        unset($data['pflsm_pmtrl_cst']);
        unset($data['pflsm_ptfmtrl_cst']);
        unset($data['pflsm_ptmtrl_cst']);
        unset($data['pflsm_pomtrl_cst']);

        unset($data['pflsm_gas_cst']);
        unset($data['pflsm_ptfgas_cst']);
        unset($data['pflsm_ptgas_cst']);
        unset($data['pflsm_pogas_cst']);

        unset($data['pflsm_pstl_cst']);
        unset($data['pflsm_ptfstl_cst']);
        unset($data['pflsm_ptstl_cst']);
        unset($data['pflsm_postl_cst']);

        unset($data['pflsm_pctof_labr_cst']);
        unset($data['pflsm_ptfctof_labr_cst']);
        unset($data['pflsm_ptctof_labr_cst']);
        unset($data['pflsm_poctof_labr_cst']);

        unset($data['pflsm_pwldng_labr_cst']);
        unset($data['pflsm_ptfwldng_labr_cst']);
        unset($data['pflsm_ptwldng_labr_cst']);
        unset($data['pflsm_powldng_labr_cst']);

        unset($data['pflsm_psupply_cst']);
        unset($data['pflsm_ptfsupply_cst']);
        unset($data['pflsm_ptsupply_cst']);
        unset($data['pflsm_posupply_cst']);

        unset($data['pflsm_pmrgnl_cst']);
        unset($data['pflsm_ptfmrgnl_cst']);
        unset($data['pflsm_ptmrgnl_cst']);
        unset($data['pflsm_pomrgnl_cst']);

        unset($data['pflsm_pmarkup']);
        unset($data['pflsm_ptfmarkup']);
        unset($data['pflsm_ptmarkup']);
        unset($data['pflsm_pomarkup']);

        unset($data['pflsm_ptotal_cost']);
        unset($data['pflsm_ptftotal_cost']);
        unset($data['pflsm_pttotal_cost']);
        unset($data['pflsm_pototal_cost']);
      




        foreach($data  as $key=>$data){
            $field .= "`".$key."`,"; 
            $valset .= "'".$data."',";
        }
        $field = rtrim($field,",");
        $valset = rtrim($valset,",");
        $this->db->query("INSERT INTO `$this->table` ($field) values($valset)");


        return $this->db->affected_rows;

    }

}



function assign($pattern, $callback)
{
    static $url;// = $_GET['url'];

    static $response = [];

    if (preg_match($pattern, $url, $match))
        $callback($match, $request, $response);

    done($response['data']);
}

function shipInfoTable() {
    global $db;
    return $db->query("
            select DISTINCTROW
            orders_tbl.job,
            cust_tbl.customer,
            orders_tbl.po,
            orders_tbl.quantity,
            orders_tbl.part,
            orders_tbl.item,
            cust_tbl.ship_to,
            part_tbl.description as 'desc',
            cust_tbl.bill_to as sold_to
            FROM
            (cust_tbl INNER JOIN orders_tbl
            ON cust_tbl.cust_id = orders_tbl.cust_id)
            INNER JOIN
            part_tbl ON (cust_tbl.cust_id = part_tbl.cust_id)
            AND
            (orders_tbl.part = part_tbl.part)
            ORDER BY orders_tbl.job DESC
        ");
}

function tablepagination( $table,$limit, $page )
    {
		
        global $db;
		
		if($table == 'new_materials')
		{
			
			$tble = filter_var($table,FILTER_SANITIZE_STRING);
			$data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM steel_tbl LEFT JOIN coil_tbl ON steel_tbl.work = coil_tbl.work"),$tble);
			$query  = $data['query'];
			//echo $db->query("SELECT COUNT(*) as total FROM steel_tbl LEFT JOIN coil_tbl ON steel_tbl.work = coil_tbl.work");
			
			//coil_tbl
			//steel_tbl
			$query_by_order = "SELECT steel_tbl.* , coil_tbl.* FROM steel_tbl LEFT JOIN coil_tbl ON steel_tbl.work = coil_tbl.work $query";
			
			$result['data'] = $db->query($query_by_order);
		}
		else
		{
			
			$tble = filter_var($table,FILTER_SANITIZE_STRING);
			$data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM $tble"),$tble);
			$query  = $data['query'];
		   
			$query_by_order = "SELECT * FROM $tble $query ";
			$result['data'] = $db->query($query_by_order);
		}
		
        
        $result['pageno'] = $data['pageno'];
        $result['limit']  = $data['limit'];
        $result['totalpage'] = $data['totalpage'];
        $result['pagination'] = $data['pagination'];
        $result['searchqu'] = $data['searchqu'];
        return $result;

    }

function custorderwise(){
        global $db;
        return $db->query("SELECT cust_id,customer FROM `cust_tbl` order by customer");
}

function activeJobs(){
    global $db;
    return $db->query("SELECT * FROM orders_tbl WHERE has_finished = 0");
}
    
function pagination($limit,$page,$table,$tblename){
	
    if( $limit != 'null' &&  $page != 'null'){
        $limitset   = (int)$limit;
        $pageno    = (int)$page;
    }else{
         $limitset   = 5;
         $pageno    = 1;
    }
    $totalpage = $table;
   
     if ($tblename == "uni_quote_tbl" || $tblename =="quote_tbl" ) {
            $query = 'order by quote desc';
            
        } elseif ($tblename == "orders_tbl") {
            $query = 'order by job desc';
            
        } elseif ($tblename == "pricing") {
            $query = 'order by updated desc';
            
        } else {
            $query = "";
        }
    $links = 7;
    if ( $limitset == 'all' ) {
        $query  =   $query . '';
    } else {
        $query = $query . " LIMIT " . ( ( $pageno - 1 ) * $limitset ) . ", $limitset";
    }
    $last       = ceil( intval($totalpage[0]['total']) / $limitset );
    $start      = ( ( $pageno - $links ) > 0 ) ? $pageno - $links : 1;
    $end        = ( ( $pageno + $links ) < $last ) ? $pageno + $links : $last;
    $html       = '<ul class="paginationul pagicommon">';
 
    $class      = ( $pageno == 1 ) ? "disabled" : "";
   if(isset($totalpage) && $totalpage[0] > 1){
        if($pageno == 1){
            $html       .= '<li class="' . $class . '">&laquo;</li>';
        }else{
            $html       .= '<li class="' . $class . '"><a data-limit="'.$limitset.'" href="'.( $pageno - 1 ) . '">&laquo;</a></li>';
        }
    }
   

    if ( $start > 1) {
        $html   .= '<li class="firstpagina" ><a data-limit="'.$limitset.'" href="1">1</a></li>';
        $html   .= '<li class="disabled"><span>...</span></li>';
    }
 
   
        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $pageno == $i ) ? "active" : "";
            if($i==1){
                $html   .= '<li class="' . $class . ' firstpagina"><a data-limit="'.$limitset.'" href="'. $i . '">' . $i . '</a></li>';    
            }
           else if($i == $last ){
                  $html   .= '<li class="' . $class . ' lastpagina"><a data-limit="'.$limitset.'" href="'. $i . '">' . $i . '</a></li>';
           }
            else{
                $html   .= '<li class="' . $class . '"><a data-limit="'.$limitset.'" href="'. $i . '">' . $i . '</a></li>';
                
            }
            
        }
  
  
        if ( $end < $last ) {
            $html   .= '<li class="disabled"><span>...</span></li>';
            $html   .= '<li class="lastpagina"><a data-limit="'.$limitset.'" href="'. $last . '">' . $last . '</a></li>';
        }
        if(isset($totalpage) && $totalpage[0] > 1){

    $class      = ( $pageno == $last ) ? "disabled" : "";
    if($pageno == $last){
        $html       .= '<li class="' . $class . '">&raquo;</li>';
    }else{
        $html       .= '<li class="' . $class . '"><a data-limit="'.$limitset.'" href="' . ( $pageno + 1 ) . '">&raquo;</a></li>';
    }
    
}
    $filtersearch='';
    if($totalpage >1){
        $filtersearch = '<div class="search_filter" data-connector="'.$tblename.'"><input placeholder="Search" type="text" name="filtersearch" /><select class="selectfieldsearch"></select><button class="searchbtn">Search</button><div class="searchshow"></div><button class="cancelthissearch">X</button></div>';
    }

 
    $html       .= '</ul>';

    $data['pageno'] = $pageno;
    $data['limit']  = $limitset;
    $data['totalpage']  = $totalpage;
    $data['searchqu'] = $filtersearch;
    $data['query'] = $query;
    $data['pagination'] = $html;
    return $data; 
}
function filtersearch($filterval,$field,$table){
    $filterval = filter_var($filterval,FILTER_SANITIZE_STRING);
    $field = filter_var($field,FILTER_SANITIZE_STRING);
    $table = filter_var($table,FILTER_SANITIZE_STRING);
    global $db;
    $result['data'] = $db->query("SELECT * FROM `$table` WHERE `$field` like '%$filterval%'");
    return $result['data'];

}
function order_yet_to_start($limit,$page) {
    global $db;
    $data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM `orders_tbl` WHERE has_started = 0"),'orders_tbl');
    $query  = $data['query'];
    $result['data'] = $db->query("SELECT * FROM orders_tbl WHERE has_started = 0 $query");
    $result['pageno'] = $data['pageno'];
    $result['limit']  = $data['limit'];
    $result['totalpage']  = $data['totalpage'];
    $result['pagination'] = $data['pagination'];
    $result['searchqu']=$data['searchqu'];
    return $result;
}

function order_started($limit,$page) {
    global $db;
    $data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM orders_tbl WHERE has_started = 1 AND has_finished = 0"),'orders_tbl');
    $query  = $data['query'];
    $result['data'] = $db->query("SELECT * FROM orders_tbl WHERE has_started = 1 AND has_finished = 0 $query");
    $result['pageno'] = $data['pageno'];
    $result['limit']  = $data['limit'];
    $result['totalpage']  = $data['totalpage'];
    $result['pagination'] = $data['pagination'];
    $result['searchqu']=$data['searchqu'];
    return $result;
}

function order_shipped($limit,$page) {
    // global $db;
    // return $db->query("
    //     SELECT *
    //     FROM orders_tbl
    //     WHERE has_shipped = 1
    //     ");

        global $db;
        $data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM orders_tbl WHERE has_shipped = 1"),'orders_tbl');
        $query  = $data['query'];
        $result['data'] = $db->query("SELECT * FROM orders_tbl WHERE has_shipped = 1  $query");
        $result['pageno'] = $data['pageno'];
        $result['limit']  = $data['limit'];
        $result['totalpage']  = $data['totalpage'];
        $result['pagination'] = $data['pagination'];
        $result['searchqu']=$data['searchqu'];
        return $result;

}

function order_finished($limit,$page) {
    global $db;
    $data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM orders_tbl  WHERE has_finished = 1 AND has_shipped = 0"),'orders_tbl');
    $query  = $data['query'];
    $result['data'] = $db->query("SELECT * FROM orders_tbl WHERE has_finished = 1 AND has_shipped = 0 $query");
    $result['pageno'] = $data['pageno'];
    $result['limit']  = $data['limit'];
    $result['totalpage']  = $data['totalpage'];
    $result['pagination'] = $data['pagination'];
    $result['searchqu']=$data['searchqu'];
    return $result;
}

function quote($limit,$page) {
    // global $db;
    // return $db->query("
    //     SELECT *, (SELECT customer FROM `cust_tbl` c WHERE c.cust_id = q.cust_id) AS customer FROM `quote_tbl` q
    // ");
    global $db;
    $data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM `quote_tbl`"),'quote_tbl');
    $query  = $data['query'];
    $result['data'] = $db->query("SELECT *, (SELECT customer FROM `cust_tbl` c WHERE c.cust_id = q.cust_id) AS customer FROM `quote_tbl` q  $query");
    $result['pageno'] = $data['pageno'];
    $result['limit']  = $data['limit'];
    $result['totalpage']  = $data['totalpage'];
    $result['pagination'] = $data['pagination'];
    $result['searchqu']=$data['searchqu'];
    return $result;
}



function uni_quote($limit,$page) {
    // global $db;
    // return $db->query("
    //     SELECT *, (SELECT customer FROM `cust_tbl` c WHERE c.cust_id = q.cust_id) AS customer FROM `uni_quote_tbl` q
    // ");
    global $db;
    $data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM `uni_quote_tbl`"),'uni_quote_tbl');
    $query  = $data['query'];
    $result['data'] = $db->query("SELECT *, (SELECT customer FROM `cust_tbl` c WHERE c.cust_id = q.cust_id) AS customer FROM `uni_quote_tbl` q  $query");
    $result['pageno'] = $data['pageno'];
    $result['limit']  = $data['limit'];
    $result['totalpage']  = $data['totalpage'];
    $result['pagination'] = $data['pagination'];
    $result['searchqu']=$data['searchqu'];
    return $result;
}

function excess_part($limit,$page) {
    // global $db;
    // return $db->query("
    //     SELECT *, (SELECT customer FROM `cust_tbl` c WHERE c.cust_id = q.cust_id) AS customer FROM `uni_quote_tbl` q
    // ");
    global $db;
    $data = pagination($limit,$page,$db->query("SELECT COUNT(*) as total FROM `excess_part`"),'excess_part');
    $query  = $data['query'];
    $result['data'] = $db->query("SELECT *, (SELECT customer FROM `cust_tbl` c WHERE c.cust_id = q.cust_id) AS customer FROM `excess_part` q  $query");
    $result['pageno'] = $data['pageno'];
    $result['limit']  = $data['limit'];
    $result['totalpage']  = $data['totalpage'];
    $result['pagination'] = $data['pagination'];
    $result['searchqu']=$data['searchqu'];
    return $result;
}
 function order_list_coil($type,$part_id,$job)
{
    global $db;
    $con = '';
    if(isset($type) && $type== 1)
       // $con = " and  coil_tbl.allocated ='1'";
        $query = "SELECT DISTINCT coil_tbl.coil_no,
                     coil_tbl.weight,
                  /*   steel_tbl.material, */
                     coil_tbl.work,
                  /*   steel_tbl.gage,
                     steel_tbl.pattern,
                     steel_tbl.holes,
                     steel_tbl.centers,
                     steel_tbl.width,
                     steel_tbl.heat, */
                     coil_tbl.date_received,
                     coil_tbl.allocated,
                     coil_tbl.job,
                     coil_tbl.Cycles,
                     coil_tbl.coil_stamping_status
        FROM coil_tbl
       /* INNER JOIN steel_tbl ON coil_tbl.work = steel_tbl.work
        INNER JOIN part_tbl ON part_tbl.pattern = steel_tbl.pattern
                            AND part_tbl.centers = steel_tbl.centers
                            AND part_tbl.holes = steel_tbl.holes
                            AND part_tbl.type = steel_tbl.material
                            AND part_tbl.gage = steel_tbl.gage  */
        WHERE coil_tbl.job='$job' AND coil_tbl.allocated ='1' 
        ORDER BY coil_tbl.job, coil_tbl.coil_no";
    //part_tbl.part = '0'
    if(isset($type) && $type== 2)
//        $con = " and  coil_tbl.allocated ='0'";
        $query = "SELECT coil_tbl.coil_no,
                     coil_tbl.weight,
                     steel_tbl.material,
                     coil_tbl.work,
                     steel_tbl.gage,
                     steel_tbl.pattern,
                     steel_tbl.holes,
                     steel_tbl.centers,
                     steel_tbl.width,
                     steel_tbl.heat,
                     coil_tbl.date_received,
                     coil_tbl.allocated,
                     coil_tbl.job,
                     coil_tbl.Cycles,
                     coil_tbl.coil_stamping_status
        FROM coil_tbl
        INNER JOIN steel_tbl ON coil_tbl.work = steel_tbl.work
                            
        WHERE coil_tbl.allocated=0

        ORDER BY coil_tbl.job, coil_tbl.coil_no";
    
    //part_tbl.part = '0' AND 
    //AND part_tbl.strip = steel_tbl.width

    if(isset($type) && $type== 3)
    $query = "SELECT coil_tbl.coil_no,
                     coil_tbl.weight,
                     steel_tbl.material,
                     coil_tbl.work,
                     steel_tbl.gage,
                     steel_tbl.pattern,
                     steel_tbl.holes,
                     steel_tbl.centers,
                     steel_tbl.width,
                     steel_tbl.heat,
                     coil_tbl.date_received,
                     coil_tbl.allocated,
                     coil_tbl.job,
                     coil_tbl.Cycles,
                     coil_tbl.coil_stamping_status
        FROM coil_tbl
        INNER JOIN steel_tbl ON coil_tbl.work = steel_tbl.work
        INNER JOIN part_tbl ON part_tbl.pattern = steel_tbl.pattern
                            AND part_tbl.centers = steel_tbl.centers
                            AND part_tbl.holes = steel_tbl.holes
                            AND part_tbl.type = steel_tbl.material
                            AND part_tbl.gage = steel_tbl.gage
                             AND part_tbl.strip = steel_tbl.width
        WHERE part_tbl.part='$part_id' AND coil_tbl.job = '0' AND coil_tbl.allocated = '0'
        ORDER BY coil_tbl.job, coil_tbl.coil_no";

//    $query = "SELECT coil_tbl.coil_no, coil_tbl.weight, steel_tbl.material, coil_tbl.work, steel_tbl.gage, steel_tbl.pattern, steel_tbl.holes, steel_tbl.centers, steel_tbl.width, steel_tbl.heat, coil_tbl.date_received, coil_tbl.allocated, coil_tbl.job, coil_tbl.Cycles FROM coil_tbl INNER JOIN steel_tbl ON coil_tbl.work = steel_tbl.work".$con;
    $result = $db->query( $query);
    if(!empty($result)){
        $data = '';
        foreach($result as $res){
            if($res['coil_stamping_status'] == 0){
                $coil_status = 'Not yet stamped';
            }
            if($res['coil_stamping_status'] == 1){
                $coil_status = 'received for stamping';
            }
            if($res['coil_stamping_status'] == 2){
                $coil_status = 'stamping completed';
            }
            if($type == 1){
               // $data .= '<tr><td><input type="checkbox" name="coildataselect" value='.$res['coil_no'].' onclick="getRadio(this.value)"></td><td>'.$res['coil_no'].'</td><td>'.$res['weight'].'</td><td>'.$res['work'].'</td><td>'.$res['job'].'</td><td>'.$res['allocated'].'</td></tr>';
               $data .= '<tr><td><input type="checkbox" name="coildataselect" value='.$res['coil_no'].' onclick="getRadio(this.value)"></td><td>'.$res['coil_no'].'</td><td>'.$res['weight'].'</td><td>-</td><td>'.$res['work'].'</td><td>-</td><td>'.$res['job'].'</td><td>'.$res['allocated'].'</td><td>'.$coil_status.'</td></tr>';

            }else{
                $data .= '<tr><td><input type="checkbox" name="coildataselect" value='.$res['coil_no'].' onclick="getRadio(this.value)"></td><td>'.$res['coil_no'].'</td><td>'.$res['weight'].'</td><td>'.$res['width'].'</td><td>'.$res['work'].'</td><td>'.$res['heat'].'</td><td>'.$res['job'].'</td><td>'.$res['allocated'].'</td><td>'.$coil_status.'</td></tr>';

            }
        };

         return $data;

    }
}

function allocate_all_part($all){
    global $db;
   $alldata;
   $job;

   if(isset($all['Allocated']) && !isset($all['All'])){

    if( !empty($all['job'])){
     
        $j = $all['job'];
        $job = "coil_tbl.job='$j' AND";
    
    }else{$job = "";}
    
        $query = "SELECT DISTINCT coil_tbl.coil_no, coil_tbl.weight, steel_tbl.material, coil_tbl.work, steel_tbl.gage, steel_tbl.pattern, steel_tbl.holes, steel_tbl.centers, steel_tbl.width, steel_tbl.heat, coil_tbl.date_received, coil_tbl.allocated, coil_tbl.job, coil_tbl.Cycles FROM coil_tbl INNER JOIN steel_tbl ON coil_tbl.work = steel_tbl.work INNER JOIN part_tbl ON part_tbl.pattern = steel_tbl.pattern AND part_tbl.centers = steel_tbl.centers AND part_tbl.holes = steel_tbl.holes AND part_tbl.type = steel_tbl.material AND part_tbl.gage = steel_tbl.gage WHERE $job  coil_tbl.allocated ='1' ORDER BY coil_tbl.job, coil_tbl.coil_no";
   
    
    $result = $db->query( $query);
        if(!empty($result)){
           
            foreach($result as $res){
                $alldata .= '<tr class="allocated"><td><input type="radio" name="coildataselect" value='.$res['coil_no'].' onclick="getRadio(this.value)"></td><td>'.$res['coil_no'].'</td><td>'.$res['weight'].'</td><td>'.$res['width'].'</td><td>'.$res['work'].'</td><td>'.$res['heat'].'</td><td>'.$res['job'].'</td><td>'.$res['allocated'].'</td></tr>';
            };
        }

    }

    if (isset($all['All'])){
        
        if(isset($all['part_no']) && $all['part_no'] != 'null'){
            $part =  mres($all['part_no']);
            $clouses = "WHERE part_tbl.part='$part' " ;
        }else{
            $clouses='';
        }

        $query2 = "SELECT coil_tbl.coil_no, coil_tbl.weight,steel_tbl.material,coil_tbl.work, steel_tbl.gage, steel_tbl.pattern, steel_tbl.holes, steel_tbl.centers, steel_tbl.width, steel_tbl.heat,coil_tbl.date_received, coil_tbl.allocated, coil_tbl.job, coil_tbl.Cycles FROM coil_tbl $clouses INNER JOIN steel_tbl ON coil_tbl.work = steel_tbl.work INNER JOIN part_tbl ON part_tbl.pattern = steel_tbl.pattern AND part_tbl.centers = steel_tbl.centers AND part_tbl.holes = steel_tbl.holes AND part_tbl.type = steel_tbl.material AND part_tbl.gage = steel_tbl.gage ORDER BY coil_tbl.job, coil_tbl.coil_no";
        $result2 = $db->query( $query2);
        if(!empty($result2)){
            
            foreach($result2 as $res){
                $alldata .= '<tr><td><input type="radio" name="coildataselect" value='.$res['coil_no'].' onclick="getRadio(this.value)"></td><td>'.$res['coil_no'].'</td><td>'.$res['weight'].'</td><td>'.$res['width'].'</td><td>'.$res['work'].'</td><td>'.$res['heat'].'</td><td>'.$res['job'].'</td><td>'.$res['allocated'].'</td></tr>';
            }
        }

}   //second end here
    if (isset($all['Part']) && !isset($all['All'])){

        if($all['part_no'] != 'null'){

            $part = mres( $all['part_no'] );

            $clouses3 = "WHERE part_tbl.part='$part' AND coil_tbl.job = '0' AND coil_tbl.allocated = '0'" ;
        }else{

            $clouses3="WHERE coil_tbl.job = '0' AND coil_tbl.allocated = '0'";

        }
            $query3 = "SELECT coil_tbl.coil_no,
            coil_tbl.weight,
            steel_tbl.material,
            coil_tbl.work,
            steel_tbl.gage,
            steel_tbl.pattern,
            steel_tbl.holes,
            steel_tbl.centers,
            steel_tbl.width,
            steel_tbl.heat,
            coil_tbl.date_received,
            coil_tbl.allocated,
            coil_tbl.job,
            coil_tbl.Cycles
    FROM coil_tbl
    INNER JOIN steel_tbl ON coil_tbl.work = steel_tbl.work
    INNER JOIN part_tbl ON part_tbl.pattern = steel_tbl.pattern
                AND part_tbl.centers = steel_tbl.centers
                AND part_tbl.holes = steel_tbl.holes
                AND part_tbl.type = steel_tbl.material
                AND part_tbl.gage = steel_tbl.gage
                AND part_tbl.strip = steel_tbl.width
                    $clouses3 ORDER BY coil_tbl.job, coil_tbl.coil_no";
       
       $result3 = $db->query( $query3);
        if(!empty($result3)){
            foreach($result3 as $res){
                $alldata .= '<tr><td><input type="radio" name="coildataselect" value='.$res['coil_no'].' onclick="getRadio(this.value)"></td><td>'.$res['coil_no'].'</td><td>'.$res['weight'].'</td><td>'.$res['width'].'</td><td>'.$res['work'].'</td><td>'.$res['heat'].'</td><td>'.$res['job'].'</td><td>'.$res['allocated'].'</td></tr>';
            }
        }

    }
   
    return $alldata;

}

function mat_value($material) {
    global $db;
    return $db->query("select cost+surcharge result from mat_tbl where material='$material'");
}

function delete_quote_detail($quote) {
    global $db;
    return $db->query("delete from quote_detail_tbl where quote='$quote'");
}


function quote_id() {
    global $db;
    return $db->query("SELECT max(quote) FROM `quote_tbl`");
}

function uni_quote_id() {
    global $db;
    return $db->query("SELECT max(quote) FROM `uni_quote_tbl`");
}

function order_list_mesh($type,$part_id,$rdchecked,$job)
{
   global $db;
        // $layer1val='';
        // $layer2val='';
        // $drainage1val='';
        // $drainage2val='';
        // $strip ='';
        

   $dataquery = "SELECT layer_1_mesh,layer_2_mesh,drainage_1_mesh,drainage_2_mesh,strip FROM part_tbl WHERE part='$part_id'";
    $results = $db->query( $dataquery);
    // print_r($results);exit;
   foreach($results as $ress){
        $layer1val=$ress['layer_1_mesh'];
        $layer2val=$ress['layer_2_mesh'];
        $drainage1val=$ress['drainage_1_mesh'];
        $drainage2val=$ress['drainage_2_mesh'];
        $strip = $ress['strip'];
        
   }


    //  if(isset($type) && $type== 1 && empty($layer1val)){
    //     if (isset($rdchecked) && $rdchecked == 1) {
    //     $con = "WHERE allocated ='1' AND TPM_JOB = '".$job."'";
    //     // WHERE mesh_tbl.mesh ='$layer1val' AND mesh_tbl.TPM_JOB='$job' AND mesh_tbl.allocated ='1'        
    //     }
    // }
     //$con = '';    
     if(isset($type) && $type== 1 && !empty($layer1val)){
        if (isset($rdchecked) && $rdchecked == 1) {
        $con = "WHERE mesh ='$layer1val' AND allocated ='1' AND TPM_JOB = '".$job."'";
        // WHERE mesh_tbl.mesh ='$layer1val' AND mesh_tbl.TPM_JOB='$job' AND mesh_tbl.allocated ='1'        
        }
        if (isset($rdchecked) && $rdchecked == 2) {
            $con = ""; 
            // WHERE mesh_tbl.mesh ='$drainage2val'       
        }
        if (isset($rdchecked) && $rdchecked == 3) {
            $con1 = "SELECT mesh_no, mesh, mesh_tbl.type, date_received, width, length, allocated, TPM_JOB, heat FROM mesh_tbl INNER JOIN part_tbl ON mesh_tbl.type = part_tbl.type AND mesh ='$layer1val' GROUP BY mesh_tbl.mesh_no";  
            // WHERE  mesh_tbl.mesh ='$layer1val' AND mesh_tbl.width='$strip' AND mesh_tbl.TPM_JOB = '0' AND mesh_tbl.allocated = '0'              
        }
        // $con = " WHERE  mesh_tbl.mesh ='$layer1val'";
    }
    if(isset($type) && $type== 2 && !empty($layer2val)){
           if (isset($rdchecked) && $rdchecked == 1) {
        $con = "WHERE mesh ='$layer2val' AND allocated ='1' AND TPM_JOB = '".$job."'";        
        }
        if (isset($rdchecked) && $rdchecked == 2) {
            $con = "";        
        }
        if (isset($rdchecked) && $rdchecked == 3) {
            $con1 = "SELECT mesh_no, mesh, mesh_tbl.type, date_received, width, length, allocated, TPM_JOB, heat FROM mesh_tbl INNER JOIN part_tbl ON mesh_tbl.type = part_tbl.type AND mesh ='$layer2val' GROUP BY mesh_tbl.mesh_no";            
        }
    }
    if(isset($type) && $type== 3 && !empty($drainage1val)){
           if (isset($rdchecked) && $rdchecked == 1) {
        $con = "WHERE mesh ='$drainage1val' AND allocated ='1' AND TPM_JOB = '".$job."'";        
        }
        if (isset($rdchecked) && $rdchecked == 2) {
            $con = "";        
        }
        if (isset($rdchecked) && $rdchecked == 3) {
            $con1 = "SELECT mesh_no, mesh, mesh_tbl.type, date_received, width, length, allocated, TPM_JOB, heat FROM mesh_tbl INNER JOIN part_tbl ON mesh_tbl.type = part_tbl.type AND mesh ='$drainage1val' GROUP BY mesh_tbl.mesh_no";               
        }
    }
    if(isset($type) && $type== 4 && !empty($drainage2val)){
           if (isset($rdchecked) && $rdchecked == 1) {
        $con = "WHERE mesh ='$drainage2val' AND allocated ='1' AND TPM_JOB = '".$job."'";        
        }
        if (isset($rdchecked) && $rdchecked == 2) {
            $con = "";        
        }
        if (isset($rdchecked) && $rdchecked == 3) {
            $con1 = "SELECT mesh_no, mesh, mesh_tbl.type, date_received, width, length, allocated, TPM_JOB, heat FROM mesh_tbl INNER JOIN part_tbl ON mesh_tbl.type = part_tbl.type AND mesh ='$drainage2val' GROUP BY mesh_tbl.mesh_no";              
        }
    }

      $query = "SELECT mesh_no, mesh, type, date_received, width, length, allocated, TPM_JOB, heat FROM mesh_tbl ".$con;
      // echo $query;exit;
    if ($con1) {
      $result = $db->query( $con1);
    } else {
      $result = $db->query( $query);
    }
    // exit;
    if(!empty($result)){
        $data1 = '';
        foreach($result as $res){

                $data1 .= '<tr><td><input type="checkbox" name="meshdataselect" value='.$res['mesh_no'].' onclick="getMeshRadio(this.value)"></td><td>'.$res['mesh'].'</td><td>'.$res['type'].'</td><td>'.$res['date_received'].'</td><td>'.$res['width'].'</td><td>'.$res['length'].'</td><td>'.$res['allocated'].'</td><td>'.$res['TPM_JOB'].'</td><td>'.$res['heat'].'</td></tr>';
        };

         return $data1;

    }
}

function update_data($type,$id,$job)
{  
    
    global $db;
    $id= str_replace('[','',$id);
    $id= str_replace(']','',$id);
    $id = explode(',',$id);
    foreach($id as $ids){
        
        if (isset($type) && $type== 1)
        $con = " allocated=1,job=".$job;

        if (isset($type) && $type== 2)
        $con = " allocated=0, job=0";
        
        if (isset($type) && ($type== 2 || $type== 1))
        $query = "UPDATE coil_tbl SET $con WHERE coil_no=".$ids;

        if (isset($type) && $type== 3) {
            $querys = "SELECT * FROM `coil_tbl` WHERE coil_no=".$ids;
            $result = $db->query($querys);

            foreach($result as $res){
                $coil_no=$res["coil_no"];
                $work=$res["work"];
                $weight= $res["weight"];
                $allocated=$res["allocated"];
                $job= $res["job"];
                $date_received=$res["date_received"];
                $operator= $res["operator"];

                $query = "INSERT INTO `used_coil` (`coil_no`, `work`, `weight`, `job`, `date_received`, `date_used`, `operator`) VALUES ('".$coil_no."', '".$work."','".$weight."', '".$job."', '".$date_received."', '".$date_received."', '".$operator."')";
            };
        }

        if (isset($type) && ($type==4 || $type==5)) {
        if ($type == 4) $con = " allocated=1,job='".$job."',TPM_JOB=".$job;
            if ($type == 5) $con = " allocated=0, job=0 ,TPM_JOB=0";
            $query = "UPDATE mesh_tbl SET $con WHERE mesh_no=".$ids;
        
            
        }

        $db->query($query);
    }//foreach close here 

    return true;
    // return $query;
}


function part_specs($part_name = '0') {
    global $db;

    $query = "SELECT * FROM part_tbl WHERE part = '$part_name'";
    $part = $db->query($query)[0];

    $query = "SELECT oa_factor FROM pat_tbl WHERE pattern = '$part[pattern]'";
    $oa_factor = $db->query($query)[0]['oa_factor'];

    $query = "SELECT thickness FROM gage_tbl WHERE gage = '$part[gage]'";
    $thickness = $db->query($query)[0]['thickness'];

    $oa = $part['holes'] == 0 ? 0 : pow($part['holes'], 2)/pow($part['centers'], 2)*$oa_factor;
    $od = $part['is_od'] == 1 ? $part['dim'] : $part['dim']+2*$thickness;
    $tube_weight = 0.29*$thickness*4*atan(1)*$od*$part['finished_length']*(100-$oa)/100;
    $weight_per_foot = $tube_weight/$part['finished_length']* 12;
    $feet = $part['finished_length']/12;
    $hspi = $part['holes'] == 0 ? 0 : ($oa/(78.54 * pow($part['holes'], 2))); 
                                    
    $side = sqrt(pow(4*atan(1)*$od, 2) - pow($part['strip'], 2));
    $angle = 90 - (atan($side/$part['strip'])*180/(4*atan(1)));
    $lf_ft = 4*atan(1)*$od/$part['strip'];
    $lf_tube = $feet*$lf_ft;

    $query = "SELECT * FROM orders_tbl WHERE part = '$part_name'";
    $order = $db->query($query)[0];

    $result['oa']= round($oa, 3);
    $result['tube_weight']= round($tube_weight, 3);
    $result['feet']= round($feet, 3);
    $result['weight_per_foot']= round($weight_per_foot, 3);
    $result['hspi']= round($hspi, 3);
    $result['angle']= round($angle, 3);
    $result['lf_ft']= round($lf_ft, 3);
    $result['lf_tube']= round($lf_tube, 3);
    
    $result['lf_req'] = round(1.1*$lf_tube, 3);
    $result['was'] = round($tube_weight*1.1, 3);
    $result['wbs'] = round(100*$result['was']/(100-$oa), 3);
    $result['tf'] = round($feet, 3);
    $result['price'] = round($order['price'], 3);
    $result['PO_total'] = round($order['price'], 3);

    return $result;
}

function get_die_stamp($die_id) {
    global $db;

    $query = "SELECT * FROM die_stamp_tbl WHERE die_id = '$die_id'";
    $die = $db->query($query);
    

    return $die;
}

//weight
function weight_listing($jobid){
    global $db;
   // $query1 = "SELECT IFNULL(SUM(weight),0) AS tw from abc WHERE job = $jobid";
    $query1 = "SELECT IFNULL(SUM(weight),0) AS tw from coil_tbl WHERE job = $jobid and allocated=1";
    $weight1 = $db->query($query1)[0];
    $query2 = "SELECT IFNULL(SUM(weight),0) as used from used_coil where job = $jobid";
    $weight2 = $db->query($query2)[0];
    $res=[];
    if($weight1['tw']==null){
        $res['tw'] = 0;
    }else{
        $res['tw'] = $weight1['tw'];
    }
    if($weight2['used']==null){
      $res['used'] = 0;
    }else{
       $res['used'] = $weight2['used']; 
    }
        
   
    return $res;
}
function meshweight_show($jobid){
    global $db;
    $query1 = "SELECT IFNULL(SUM(weight),0) AS tw from abc WHERE job = $jobid";
    $weight1 = $db->query($query1)[0];
    $query2 = "SELECT IFNULL(SUM(weight),0) as used from used_coil where job = $jobid";
    $weight2 = $db->query($query2)[0];
    $res=[];
    if($weight1['tw']==null){
        $res['tw'] = 0;
    }else{
        $res['tw'] = $weight1['tw'];
    }
    if($weight2['used']==null){
      $res['used'] = 0;
    }else{
       $res['used'] = $weight2['used']; 
    }
        
   
    return $res;
}
//partno updation
function part_show($partn){
    global $db;
    $query = "SELECT abc.coil_no, abc.weight, abc.width, abc.work, abc.heat, abc.job, abc.allocated, part_tbl.part
FROM part_tbl INNER JOIN abc ON (part_tbl.gage = abc.gage) AND (part_tbl.type = abc.material) AND (part_tbl.holes = abc.holes) AND (part_tbl.centers = abc.centers) AND (part_tbl.pattern = abc.pattern) AND (part_tbl.strip = abc.width)
WHERE (((part_tbl.part)=$partn))";
   
    $result = $db->query($query);
    $output ='';
    foreach($result as $resp){
                //$output[$key] = $resp;
        $output.= "<tr><td><input type='checkbox' name='coildataselect' value=".$resp['coil_no']." onclick='getRadio(this.value)'></td><td>".$resp['weight']."</td><td>".$resp['width']."</td><td>".$resp['width']."</td><td>".$resp['work']."</td><td>".$resp['heat']."</td><td>".$resp['job']."</td><td>".$resp['allocated']."</td></tr>";
    }
    if($result == null){
        return "<tr><td colspan='8'>No Data Found</td></tr>"; 
    }else{
       return $output; 
    }
 }
function Enter_as_Used($newval,$coil,$oldone){
    global $db;
    $total  = ((int)$oldone)-((int)$newval);
    $retRe = '';
    if(!empty($coil)){
        
        $querys = "SELECT * FROM `coil_tbl` WHERE coil_no=".$coil;
        $resultf = $db->query($querys);

        foreach($resultf as $res){
            $coil_no=$res["coil_no"];
            $work=$res["work"];
            $weight= $newval;
            $allocated=$res["allocated"];
            $job= $res["job"];
            $date_received=$res["date_received"];
            $operator= $res["operator"];

            $query = "INSERT INTO `used_coil` (`coil_no`, `work`, `weight`, `job`, `date_received`, `date_used`, `operator`) VALUES ('".$coil_no."', '".$work."','".$weight."', '".$job."', '".$date_received."', '".$date_received."', '".$operator."')";
            $result = $db->query($query);
        };
        if($total !=0){
            $query ="Update coil_tbl set weight = $total where coil_no = $coil" ;
            $result = $db->query($query);
            $retRe['query'] = 1;
        }
        else{
            $query = "DELETE FROM coil_tbl WHERE coil_no = $coil";
            $result = $db->query($query);
            $retRe['query'] = 2;
        }
    }
    else{
      $retRe['query'] = 3;
    }
    
    return $retRe;
}

function part_mat($user){
    global $db;
    $query = "SELECT part FROM `part_tbl` WHERE cust_id =$user ";
    $result = $db->query($query);
    return $result;
}

function get_part_table($user,$part){
    global $db;
    $query = "SELECT * FROM `part_tbl` WHERE cust_id = $user AND part = $part";
    $result = $db->query($query);
    return $result;
}
function get_micron_table($id){
    global $db;
    $query = "SELECT * FROM `micron` WHERE id = $id";
    $result = $db->query($query);
    return $result;
}
function job_mat_orders($user,$part){
    global $db;
    $query = "SELECT job FROM `orders_tbl` WHERE cust_id = $user AND part = $part";
    $result = $db->query($query);
    return $result;
}
function material_save($dimension, $center, $customer, $gage, $holes, $part, $patter, $quantity, $strip, $total, $lengtht, $od,$material,$po,$scrap_rate,$density,$Weight_bs){
    global $db;
    $check = '';
    $data = '';
    if($od =='true'){
        $check=1;
    }
    else{
        $check = 0;
    }
    $dimension = (double)$dimension;
    $center = (double)$center;
    $gage = (int)$gage;
    $holes = (double)$holes;
    $part = $part;
    $patter = $patter;
    $quantity = (int)$quantity;
    $strip = (double)$strip;
    $total = $total;
    $lengtht = (double)$lengtht;
    $is_od = (int)$check;
    $query = "INSERT INTO `mat_req` (`customer`, `Part`, `quantity`, `dim`, `length`, `pattern`, `holes`, `centers`, `gage`, `strip`, `is_od`,`po`, `material`, `scrap_rate`, `density` , `Weight_bs`) VALUES ('".$customer."', '".$part."','".$quantity."','".$dimension."','".$lengtht."','".$patter."','".$holes."','".$center."','".$gage."','".$strip."','".$check."','".$po."','".$material."','".$scrap_rate."','".$density."','".$Weight_bs."')" ;
    $result = $db->query($query);
    return 'Success';
}
function material_update($id,$dimension,$center,$cust_id,$gage,$holes,$part,$patter,$quantity,$strip,$total,$lengtht,$od,$material,$po,$scrap_rate,$density,$Weight_bs){
    global $db;
    $check = '';
    $data = '';
    if($od =='true'){
        $check=1;
    }
    else{
        $check = 0;
    }
    $query = "UPDATE mat_req SET customer='".$cust_id."',Part='$part',quantity='$quantity',dim='$dimension',length='$lengtht',pattern='$patter',holes='$holes',centers='$center',gage='$gage',strip='$strip',is_od='$check', po='$po', material='$material', scrap_rate='$scrap_rate', density='$density' , Weight_bs='$Weight_bs' WHERE id = $id";
    $reset=$db->query($query);
    return 'Success';
}

function save_report($customer,$po_number,$quantity,$date_ordered,$date_due,$line_item,$mill_spec,$repair_spec,$ship_date,$mill_amps,$mill_volts,$mill_speed,$repair_amps,$repair_volts,$repair_speed,$job_id){
    global $db;



$query = "UPDATE order_specs SET customer='$customer',po='$po_number',quantity='$quantity',ordered='$date_ordered',due='$date_due',item='$line_item',weld_spec_mill='$mill_spec',weld_spec_repair='$repair_spec',ship_date='$ship_date',mil_amps='$mill_amps',mil_volts='$mill_volts',mil_speed='$mill_speed',repair_amps='$repair_amps',repair_volts='$repair_volts',repair_speed='$repair_speed' WHERE job='$job_id'";
    
    $result = $db->query($query);

    return 'Success';

       
};






function delettable($id,$table){
    global $db;
    $id = (int)$id;
    $query = "DELETE FROM $table WHERE id = $id";
    $result = $db->query($query);
    return 'Success';
}
function uniscreen_save($cust_id, $part, $job,$Inner_Shroud_ID, $Inner_Shroud_Gage, $Outer_Shroud_Gage, $Outer_Shroud_Strip, $InnerShroudPattern, $Inner_Shroud_Holes, $Inner_Shroud_Centers, $OuterShroudPattern, $Outer_Shroud_Holes, $Outer_Shroud_Centers,$Gaps,$UniScreen_Length,$mesh){
    global $db;
    $cust_id==''||$cust_id =="null"? $cust_idn=0:$cust_idn= $cust_id;
    $part ==''||$part=="null"?$partn=0:$partn=$part;
    $job ==''||$job=="null"?$jobn=0:$jobn=$job;
    $Inner_Shroud_ID =="null"?$Inner_Shroud_IDn = 0:$Inner_Shroud_IDn =$Inner_Shroud_ID;
    $Inner_Shroud_Gage =="null"? $Inner_Shroud_Gagen = 0:$Inner_Shroud_Gagen=$Inner_Shroud_Gage;
    $Outer_Shroud_Gage =="null"?$Outer_Shroud_Gagen=0:$Outer_Shroud_Gagen =$Outer_Shroud_Gage;
    $Outer_Shroud_Strip =="null"?$Outer_Shroud_Stripn=0:$Outer_Shroud_Stripn =$Outer_Shroud_Strip;
    $InnerShroudPatternn = $InnerShroudPattern;
    $Inner_Shroud_Holes =="null"?$Inner_Shroud_Holesn=0:$Inner_Shroud_Holesn = $Inner_Shroud_Holes;
    $Inner_Shroud_Centers =="null"?$Inner_Shroud_Centersn=0:$Inner_Shroud_Centersn = $Inner_Shroud_Centers;
    $OuterShroudPattern =="null"?$OuterShroudPatternn=0:$OuterShroudPatternn =$OuterShroudPattern;
    $Outer_Shroud_Holes =="null"?$Outer_Shroud_Holesn=0:$Outer_Shroud_Holesn =$Outer_Shroud_Holes;
    $Outer_Shroud_Centers =="null"?$Outer_Shroud_Centersn=0:$Outer_Shroud_Centersn =$Outer_Shroud_Centers;
    $Gaps =="null"?$Gapsn=0:$Gapsn =$Gaps;
    $mesh==''||$mesh=="null"?$meshn =0:$meshn=$mesh;
    $UniScreen_Length=="null"?$UniScreen_Lengthn=0:$UniScreen_Lengthn=$UniScreen_Length;
    $query = "INSERT INTO `uniscreen`(`cust_id`, `part`, `job`, `Inner_Shroud_ID`, `Inner_Shroud_Gage`, `Outer_Shroud_Gage`, `Outer_Shroud_Strip`, `Inner_Shroud_Pattern`, `Inner_Shroud_Holes`, `Inner_Shroud_Centers`, `Outer_Shroud_Pattern`, `Outer_Shroud_Holes`, `Outer_Shroud_Centers`, `Gaps`, `UniScreen_Length`,`mesh`) VALUES ('".$cust_idn."','".$partn."','".$jobn."',".$Inner_Shroud_IDn.",".$Inner_Shroud_Gagen.",".$Outer_Shroud_Gagen.",".$Outer_Shroud_Stripn.",'".$InnerShroudPatternn."',".$Inner_Shroud_Holesn.",".$Inner_Shroud_Centersn.",'".$OuterShroudPatternn."',".$Outer_Shroud_Holesn.",".$Outer_Shroud_Centersn.",".$Gapsn.",".$UniScreen_Lengthn.",'".$meshn."')" ;
    $result = $db->query($query);
    return $db->query('SELECT LAST_INSERT_ID();');
}

function uniscreen_update($id,$cust_id, $part, $job,$Inner_Shroud_ID, $Inner_Shroud_Gage, $Outer_Shroud_Gage, $Outer_Shroud_Strip, $InnerShroudPattern, $Inner_Shroud_Holes, $Inner_Shroud_Centers, $OuterShroudPattern, $Outer_Shroud_Holes, $Outer_Shroud_Centers,$Gaps,$UniScreen_Length,$mesh){
    global $db;
    $cust_id =$cust_id;
    $Inner_Shroud_ID = (int)$Inner_Shroud_ID;
    $Inner_Shroud_Gage = (double)$Inner_Shroud_Gage;
    $Outer_Shroud_Gage = (double)$Outer_Shroud_Gage;
    $Outer_Shroud_Strip = (double)$Outer_Shroud_Strip;
    $InnerShroudPattern = $InnerShroudPattern;
    $Inner_Shroud_Holes = (double)$Inner_Shroud_Holes;
    $Inner_Shroud_Centers =(double) $Inner_Shroud_Centers;
    $OuterShroudPattern = $OuterShroudPattern;
    $Outer_Shroud_Holes =(double)$Outer_Shroud_Holes;
    $Outer_Shroud_Centers = (double)$Outer_Shroud_Centers;
    $Gaps =(double)$Gaps;
    $UniScreen_Length= (int)$UniScreen_Length;
    $query = "UPDATE uniscreen SET cust_id='".$cust_id."',part='$part',job='$job',Inner_Shroud_ID='$Inner_Shroud_ID',Inner_Shroud_Gage='$Inner_Shroud_Gage', Outer_Shroud_Gage='$Outer_Shroud_Gage', Outer_Shroud_Strip='$Outer_Shroud_Strip', Inner_Shroud_Pattern='".$InnerShroudPattern."', Inner_Shroud_Holes='$Inner_Shroud_Holes', Inner_Shroud_Centers='$Inner_Shroud_Centers', Outer_Shroud_Pattern='".$OuterShroudPattern."', Outer_Shroud_Holes='$Outer_Shroud_Holes', Outer_Shroud_Centers='$Outer_Shroud_Centers', Gaps='$Gaps',UniScreen_Length='$UniScreen_Length',mesh='".$mesh."' WHERE id = $id";
    $reset=$db->query($query);
    return 'Success';
}
function file_exist($part,$cust_id){
    global $db;
    $query = "SELECT `download_reference`, `id` FROM `download_reference` WHERE part='".$part."' AND cust_id='".$cust_id."'";
    $result = $db->query($query);
    return $result;
}
function file_existreport($part,$cust_id){
    global $db;
    $result = '';
    $query1 = "SELECT `customer` FROM `cust_tbl` WHERE cust_id=$cust_id";
    $result1 = $db->query($query1)[0]; 
    if($result1 !=''){
        $id = $result1['customer'];
        $query = "SELECT `download_reference`, `id` FROM `download_reference` WHERE part='".$part."' AND cust_id='".$id."'";
        $result = $db->query($query);
    }
    else{
        $result ="name not found";
    }
    return $result;
}
function file_delete($id){
    global $db;
    $result='';
    $query = "DELETE FROM `download_reference` WHERE id=$id";
    $result = $db->query($query)[0];
    return $result; 
}
function createInner($dir){
    if(!is_dir($dir)){
        if(mkdir($dir,0777,true)){
           $error['second_create'] = true;
        }
    }
    else{
        $error['second_create'] = true;
    }
 }
function upload_file($part,$cust_id,$file){
    $clientname = $cust_id;
    $part = $part;
    $error = ''; 
    $curdir= DIR;

    $clientdiv = $curdir.$clientname;
    $inner = $clientdiv.'/'.$part; 
    $filestorserver = $inner."/".$_FILES["uploaddrawing"]["name"];
    $filestor = $_SERVER["HTTP_ORIGIN"]."/user_files/".$clientname."/".$part."/".$_FILES["uploaddrawing"]["name"];

    global $db;
    $errorn = '';
    $ret = '';
    $query='';
    if(!is_dir($clientdiv)){
        if(mkdir($clientdiv, 0777, true)){
            $error['first_create'] = true;
            createInner($inner);
        }else{
            $error['first_dir'] = 'First Directory error!';
        }
    }else{
        createInner($inner);
    }
    $file = $_FILES["uploaddrawing"]["name"];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    if($ext =="pdf"){
         if(!($_FILES["uploaddrawing"]["error"])){
            if(move_uploaded_file($_FILES["uploaddrawing"]["tmp_name"],$filestorserver)){
                $query = "INSERT INTO `download_reference`(`part`, `cust_id`, `download_reference`) VALUES ('".$part."','".$clientname."','".$filestor."')" ;
                $result = $db->query($query);
                $ret = $db->query("SELECT LAST_INSERT_ID()");
            }else{
                $ret=$filestorserver;    
            }
        }
    }
   return  $ret;
}
// function typeGage($userid, $part_no){
//      = "SELECT `type`, `gage` FROM `part_tbl` WHERE `cust_id`=2 AND `part` = 2";
//     return $part_no;
// }
function typeGage($userid, $part_no){
    global $db;
    $query = "SELECT `type`, `gage` FROM `part_tbl` WHERE `cust_id`=$userid AND `part`=$part_no LIMIT 1";
    $result = $db->query($query);
    return $result;
}
function orderspecpartspec($jobid){
    global $db;
    
    $jobid =  filter_var($jobid,FILTER_SANITIZE_NUMBER_INT);
    $query="CALL order_startede($jobid)";
    $result = $db->query($query);
    return $result;
}


include_once('main_sub.php');
$db = new DB( $config['database'] );

$_JSON = json_decode(file_get_contents('php://input'), true);

?>
