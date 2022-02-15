<?php
require_once __DIR__ . '/init.php';



if (isPost()) {
  if (postHas('username') && postHas('password')) {
    $sql = 'SELECT * FROM users WHERE username = \'' . post_string('username') . '\' AND password = \'' . post_string('password') . '\'';
    $result = $db->query($sql);
    $user = $result->fetch_assoc();
    if(!$user) {
      $message = 'Invalid username/password, please check entered data';
    } else {
      $_SESSION['message'] = 'Welcome ' . $user['username'];
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      redirect_to('index.php');
    }
  } else {
    $message = 'Please eter username and password';
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
?>


<form method="post">
    <input type="text" name="username" placeholder="Username" required/>
    <input type="password" name="password" placeholder="Password" required/>

    <button class="btn btn-primary">
        Login
    </button>
</form>

    <div>
  <?= isset($message) ? $message : 'No message' ?>
</div>



<?php
// footer
include('pages/footer.html');
?>