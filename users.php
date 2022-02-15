<?php
require_once __DIR__ . '/init.php';

//phpinfo();

$sql = 'SELECT * FROM users';
$r = $db->query($sql);
//$users = $r->fetch_all(MYSQLI_ASSOC);
//var_dumpt($users);
//die();

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
      <?php if (has_message()) { ?>
        <div class="alert alert-success"><?= get_message() ?></div>
      <?php } ?>
      <div class="row">
          <a class="btn btn-success" href="<?= HTTP_ROOT ?>add_user.php">
              <i class="icon-plus fa-lg"></i>
              Add User
          </a>
      </div>

      <table class="table table-bordered">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Actions</th>
              </tr>
          </thead>
          <tbody>
              <?php // foreach($users as $user) {  ?>
              <?php while ($user = $r->fetch_assoc()) { ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td>
                        <a class="btn btn-info" href="<?= HTTP_ROOT ?>view_user.php?id=<?= $user['id'] ?>">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="<?= HTTP_ROOT ?>delete_user.php?user_id=<?= $user['id'] ?>" class="btn btn-danger delete-user-btn">
                            <i class="fa fa-remove"></i>
                        </a>
                    </td>
                </tr>
              <?php } ?>
          </tbody>
      </table>
  </div>
  <?php
}
// footer
include('pages/footer.html');
