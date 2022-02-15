<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*$hostname = 'localhost';
$db_name = 'final';
$db_username = 'root';
$db_password = 'anuj';*/

/*$hostname = '31.220.110.218';
$db_name = 'u886168621_tpmv2';
$db_username = 'u886168621_tpmv2';
$db_password = 'Reset123!';*/

$hostname = 'localhost';
$db_name = 'demo_tpm';
$db_username = 'root';
$db_password = 'anuj';
$db = new mysqli($hostname, $db_username, $db_password, $db_name);
