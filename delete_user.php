<?php
require_once __DIR__ . '/init.php';

if (isPost()) {
  $user_id = post_int('user-id');

  $sql = 'DELETE FROM users_permissions WHERE user_id = ' . $user_id;
  $r = $db->query($sql);

  $sql = 'DELETE FROM users_roles WHERE user_id = ' . $user_id;
  $r = $db->query($sql);

  $sql = 'DELETE FROM users WHERE id = ' . $user_id;
  $r = $db->query($sql);

  $_SESSION['message'] = 'User deleted';

  redirect_to('users.php');
}

$sql = 'SElECT * FROM users WHERE id = ' . get_int('user_id');
$r = $db->query($sql);
$user = $r->fetch_assoc();

$pages = [
    'customers' => [
        'title' => 'Customers',
        'content' => 'customers.html'
    ],
    'orders' => [
        'title' => 'Orders',
        'content' => 'orders.html'
    ],
    'inventory' => [
        'title' => 'Inventory',
        'content' => 'inventory.html'
    ],
    'reports' => [
        'title' => 'Reports',
        'content' => 'reports.html'
    ],
    'quotes' => [
        'title' => 'Quotes',
        'content' => 'quotes.html'
    ],
    'fax' => [
        'title' => 'Fax',
        'content' => 'fax.html'
    ],
    'material-requirments' => [
        'title' => 'Material Requirments',
        'content' => 'material-requirments.html'
    ],
    'uniscreen' => [
        'title' => 'UniScreen',
        'content' => 'uniscreen.html'
    ]
];


// heading
include('pages/header.html');
include('check_perm.php');
// page content
//include('pages/' . $_GET['page']);

if (is_admin($_SESSION['user_id'])) {
  ?>
  <div class="content-wrapper">
      <div class="row">
          <a href="<?= HTTP_ROOT ?>add_user.php">Delete User: <?= $user['username'] ?></a>
      </div>

      <form method="post">
          <input type="hidden" name="user-id" value="<?= $user['id'] ?>" />
          <p>Do you want to delete user: <?= $user['username'] ?>, this acion cannot be reverted</p>

          <div class="row">
              <button type="submit" class="btn btn-danger">Delete</button>
              <a href="<?= HTTP_ROOT ?>users.php" class="btn btn-success">Cancel</a>
          </div>
      </form>
  </div>
  <?php
}
// footer
include('pages/footer.html');
