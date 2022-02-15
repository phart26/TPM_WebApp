<?php
require_once __DIR__ . '/init.php';

if (isPost()) {
  if (postHas('role-name')) {
    $sql = 'INSERT INTO roles(role_name) VALUES(\'' . post_string('role-name') . '\')';
    $r = $db->query($sql);
    if(!empty($r->error)) {
      $_SESSION['message'] = $r->error;
    }
    else {
      $_SESSION['message'] = 'Role (' . post_string('role-name') . ') has been added successfully';
    }
  } else {
    $_SESSION['message'] = 'Please enter role name';
  }
}

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

<?php if (has_message()) { ?>
  <div class="alert alert-success"><?= get_message() ?></div>
<?php } ?>

<form method="post">
    <input type="text" name="role-name" placeholder="Descriptive name for the role"/>
    <button class="btn btn-primary">Add</button>
</form>

<?php
// footer
include('pages/footer.html');
?>