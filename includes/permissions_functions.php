<?php

function get_page_id($page_name) {
  global $db;
  $sql = 'SELECT * FROM pages WHERE page_name = \'' . $page_name . '\'';
  $r = $db->query($sql);
  $page = $r->fetch_assoc();
  return $page['id'];
}

function get_permission_id($page_name, $operation) {
  global $db;
  $page_id = get_page_id($page_name);
  if(empty($page_id)) {
    return false;
  }
  $sql = 'SELECT * FROM permissions WHERE page_id = ' . $page_id . ' AND operation = \'' . $operation . '\'';
  $r = $db->query($sql);
  $permission = $r->fetch_assoc();
  return $permission['id'];
}

function is_admin($user_id) {
  global $db;
  
  $sql = 'SELECT * FROM users WHERE id = ' . $user_id . ' AND is_admin = 1';
  $r = $db->query($sql);
  $perm = $r->fetch_assoc();
  
  return !empty($perm);
}

function user_has_permission($page_name, $operation, $user_id, $user_only = false) {
  if(is_admin($user_id)) {
    return true;
  }
  global $db;

  $permission_id = get_permission_id($page_name, $operation);
  if(!$permission_id) {
    return false;
  }

  if (!$user_only) {
    $sql = 'SELECT * FROM roles_permissions rp WHERE permission_id = ' . $permission_id . ' AND role_id IN
             (SELECT role_id FROM users_roles WHERE user_id = ' . $user_id . ')';
//  die($sql);
    $r = $db->query($sql);
    $permissions_array = [];
    while($perm = $r->fetch_assoc()) {
      $permissions_array[] = $perm;
    }
//    $permissions_array = $r->fetch_all(MYSQLI_ASSOC);
    if (!empty($permissions_array)) {
      return true;
    }
  }

  $sql = 'SELECT * FROM users_permissions up WHERE permission_id = ' . $permission_id . ' AND user_id = ' . $user_id;
  $r = $db->query($sql);
//  $permissions_array = $r->fetch_all(MYSQLI_ASSOC);
  $permissions_array = array();
  while ($p = $r->fetch_assoc()) {
    $permissions_array[] = $p;
  }
  if (!empty($permissions_array)) {
    return true;
  }


  return false;
}

function current_has_permission($page_name, $operation) {
  if (isset($_SESSION['user_id'])) {
    return user_has_permission($page_name, $operation, $_SESSION['user_id']);
  } else {
    return false;
  }
}

//function current_has_permission($page_name, $operation) {
//  global $db;
//  $permission_id = get_permission_id($page_name, $operation);
//
//  $sql = 'SELECT * FROM roles_permissions rp WHERE permission_id = ' . $permission_id . ' AND role_id IN
//             (SELECT role_id FROM users_roles WHERE user_id = ' . $_SESSION['user_id'] . ')';
////  die($sql);
//  $r = $db->query($sql);
//  $permissions_array = $r->fetch_all(MYSQLI_ASSOC);
//  if (!empty($permissions_array)) {
//    return true;
//  }
//  
//  $sql = 'SELECT * FROM users_permissions up WHERE permission_id = ' . $permission_id . ' AND user_id = ' . $_SESSION['user_id'];
//  $r = $db->query($sql);
//  $permissions_array = $r->fetch_all(MYSQLI_ASSOC);
//  if (!empty($permissions_array)) {
//    return true;
//  }
//  
//  return false;
//}

function role_has_permission($page_name, $operation, $role_id) {
  global $db;

  $permission_id = get_permission_id($page_name, $operation);

  $sql = 'SELECT * FROM roles_permissions WHERE permission_id = ' . $permission_id . ' AND role_id = ' . $role_id;
//  die($sql);
  $r = $db->query($sql);
  $permissions_array = [];
    while($perm = $r->fetch_assoc()) {
      $permissions_array[] = $perm;
    }
//  $permissions_array = $r->fetch_all(MYSQLI_ASSOC);
  if (!empty($permissions_array)) {
    return true;
  }

  return false;
}
