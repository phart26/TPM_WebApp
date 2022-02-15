<?php

function isPost() {
  if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    return true;
  } else {
    return false;
  }
}

function post_int($var) {
  return filter_input(INPUT_POST, $var, FILTER_SANITIZE_NUMBER_INT);
}

function post_string($var) {
  return filter_input(INPUT_POST, $var, FILTER_SANITIZE_STRING);
}

function postHas($var) {
  if (empty(filter_input(INPUT_POST, $var)) == true) {
    return false;
  } else {
    return true;
  }
}

function getHas($var) {
  if (empty(filter_input(INPUT_GET, $var)) == true) {
    return false;
  } else {
    return true;
  }
}

function get_int($var) {
  return filter_input(INPUT_GET, $var, FILTER_SANITIZE_NUMBER_INT);
}

function get_string($var) {
  return filter_input(INPUT_GET, $var, FILTER_SANITIZE_STRING);
}

function redirect_to($path) {
  header('Location: ' . HTTP_ROOT . $path);
  die();
}

function has_message() {
  return isset($_SESSION['message']);
}

function get_message() {
  if (isset($_SESSION['message'])) {
    $return_value = $_SESSION['message'];
    unset($_SESSION['message']);
    return $return_value;
  } else {
    return '';
  }
}

function check_login() {
  if(!isset($_SESSION['user_id'])) {
    redirect_to('login.php');
  }
}