<?php
    include '../Connections/connectionTPM.php';
?>


<?php
    require_once 'createJWT.php';
    require './vendor/autoload.php';
    use \Firebase\JWT\JWT;
    if(isset($_GET['userToken'])){

        //checking authentication
        $userToken = $_GET['userToken'];
        $mac_add = $_GET['mac_add'];
        try{
        $secret_key = 'Acfd5xy4!';
        $decoded_data = JSON_encode(JWT::decode($userToken, $secret_key, array('HS512')));
        
        //get mill device is linked to
        $sql = "SELECT * FROM mac_add_tbl WHERE MAC_address = '".$mac_add."'";

        if ($resultConn= $conn -> query($sql)) {
        
        }

        $dbResult = mysqli_fetch_array($resultConn);
        $press = $dbResult['device'];

        //get job number linked to that mill
        $sql = "SELECT * FROM stamping_orders_tbl WHERE press = '$press' AND has_finished = 0";

        if ($resultConn= $conn -> query($sql)) {
        
        }

        $dbResult = array();
        //fetch resulting rows as an array
        while($order = mysqli_fetch_assoc($resultConn)){
            $dbResult[] = $order;
        }
        $currentJob = "";
        if(empty($dbResult)){
            $currentJob = "";
        }else{
            foreach($dbResult as $jobNum){

                if($jobNum['has_started'] == 1){
                    $currentJob = strval($jobNum['job']);
                }

            }

            if($currentJob == ''){
                $currentJob = strval($dbResult[0]['job']);
            }
        }

        //JSON Output
        $jobOutput = array('job' => $currentJob);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = JSON_encode($jobOutput);
        print_r($response);
        

        }catch(Exception $ex){
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => $ex->getMessage()
            ));
        }
    }
?>