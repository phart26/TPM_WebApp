<?php
require_once("includes/db.php");

if(isset($_GET['device'])){
    $device = $_GET['device'];

    $query = "select * from mac_add_tbl where device ='".$device."'";
    $result = mysqli_query($db , $query);
    $rowcount=mysqli_num_rows($result);
    if($rowcount == 0){
        return 0;
    }else{
        return 1;
    }
}
?>