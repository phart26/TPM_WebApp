<?php

require_once __DIR__ . '/init.php';

unset($_SESSION['message']);
unset($_SESSION['user_id']);
unset($_SESSION['username']);

session_destroy();

redirect_to('index.php');
      