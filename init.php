<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

defined('HTTP_ROOT') ? null : define('HTTP_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/TPM-master/app/');
//defined('HTTP_ROOT') ? null : define('HTTP_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/newdev/app/');
//defined('HTTP_ROOT') ? null : define('HTTP_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/acme/appn/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/permissions_functions.php';
