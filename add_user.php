<?php
require_once __DIR__ . '/init.php';

if(isPost()) {
  if(postHas('username') && postHas('password')) {
    $sql = 'SELECT * FROM users WHERE username = \'' . post_string('username') . '\'';
    $r = $db->query($sql);
    $user = $r->fetch_assoc();
    if($user) {
      $message = 'Username (' . post_string('username') . ') already exist, please enter different username';
    } else {
      $sql = 'INSERT INTO users(username, password, is_admin) VALUES(\'' . post_string('username') . '\', \'' . post_string('password') . '\', 0)';
//      die($sql);
      $r = $db->query($sql);
      if(!empty($r->error)) {
        $message = 'failed to add user (' . post_string('username') . '). Error: ' . $r->error;
      } else {
        $message = 'User (' . post_string('username') . ') addedd successfully';
      }
    }
  } else {
      $message = 'Please enter username and password';
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

<?php if (isset($message)) { ?>
  <div class="alert alert-info"><?= $message ?></div>
<?php } ?>
  
<form method="post">
    <input type="text" name="username" placeholder="Username" required/>
    <input type="password" name="password" placeholder="Password" required/>

    <button class="btn btn-primary">
        Add
    </button>
</form>

<?php
// footer
include('pages/footer.html');
?>