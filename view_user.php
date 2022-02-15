<?php
require_once __DIR__ . '/init.php';

if (isPost()) {
//  var_dump($_POST);
//  die();
  if (postHas('username') || postHas('password')) {
    $sql = 'SELECT * FROM users WHERE username = \'' . post_string('username') . '\'';
    $r = $db->query($sql);
    $user = $r->fetch_assoc();
    if ($user) {
      $message = 'Username (' . post_string('username') . ') already exist, please enter different username';
    } else {
      $sql = 'UPDATE users SET username = \'' . post_string('username') . '\'';
      if (postHas('password')) {
        $sql .= ', password = \'' . post_string('password') . '\' ';
      }
      $sql .= ' WHERE id = ' . get_int('id') . ' ';
//      die($sql);
      $r = $db->query($sql);
      if (!empty($r->error)) {
        $message = 'failed to update user (' . post_string('username') . '). Error: ' . $r->error;
      } else {
        $message = 'User (' . post_string('username') . ') updated successfully';
      }
    }
  } else {
    $message = 'Please enter username and password';
  }

  $sql = 'DELETE FROM users_permissions WHERE user_id = ' . post_int('user-id');
  $r = $db->query($sql);

  $sql = 'SELECT * FROM pages';
  $r = $db->query($sql);
  $site_pages = array();
  while ($site_page = $r->fetch_assoc()) {
    $site_pages[] = $site_page;
  }

  $user_id = post_int('user-id');
  foreach ($site_pages as $page) {
//    var_dump($_POST);
//    var_dump($_POST[$page['page_name']] );
    if (isset($_POST[$page['page_name']])) {

      foreach ($_POST[$page['page_name']] as $op) {
        $permission_id = get_permission_id($page['page_name'], $op);
        $sql = "INSERT INTO users_permissions(user_id, permission_id) VALUES({$user_id}, {$permission_id})";
        $db->query($sql);
      }
    }
  }
//  die();

  redirect_to('view_user.php?id=' . post_int('user-id'));
}

if (getHas('id')) {
  $sql = 'SELECT * FROM users WHERe id = ' . get_int('id');
  $r = $db->query($sql);
  $user = $r->fetch_assoc();
}

$sql = 'SELECT * FROM pages';
$r = $db->query($sql);
$site_pages = array();
while ($site_page = $r->fetch_assoc()) {
  $site_pages[] = $site_page;
}
//$site_pages = $r->fetch_all(MYSQLI_ASSOC);

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
?>

<?php if (isset($message)) { ?>
  <div class="alert alert-info"><?= $message ?></div>
<?php } ?>

<form method="post">
    <input type="hidden" name="user-id" value="<?= $user['id'] ?>"/>
    <input type="text" name="username" placeholder="Username" value="<?= $user['username'] ?>" required/>
    <input type="password" name="password" placeholder="Empty to leave it the same"/>

    <button class="btn btn-primary">
        Update
    </button>


    <table class="table table-bordered">
        <thead>
            <tr>
                <td>Page Name</td>
                <td>Operations</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($site_pages as $page) { ?>
              <tr>
                  <td><?= $page['description'] ?></td>
                  <td><?php
                      $sql = 'SELECT * FROM permissions WHERE page_id = ' . $page['id'];
                      $r = $db->query($sql);
                      $permissions = array();
                      while ($permission = $r->fetch_assoc()) {
                        $permissions[] = $permission;
                      }
//                      $permissions = $r->fetch_all(MYSQLI_ASSOC);
                      foreach ($permissions as $p) {
                        ?>
                        <label>
                            <input type="checkbox" name="<?= $page['page_name'] ?>[]" value="<?= $p['operation'] ?>" <?= user_has_permission($page['page_name'], $p['operation'], get_int('id'), true) ? 'checked' : '' ?> /> <?= $p['description'] ?>
                        </label>
                      <?php } ?>
                  </td>
              </tr>
            <?php } ?>
        </tbody>
    </table>
</form>
<?php
// footer
include('pages/footer.html');
?>