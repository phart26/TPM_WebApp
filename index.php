<?php
require_once __DIR__ . '/init.php';
error_reporting(0);
check_login();

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
    ],

];


// heading
include('pages/header.html');
include('check_perm.php');
// page content
include('pages/' . $_GET['page']);
?>

<?php if (has_message()) { ?>
  <div class="alert alert-success"><?= get_message() ?></div>
<?php } ?>

<?php if (current_has_permission('/index_php', 'add')) { ?>
  <div>Guarded by "add" permission on /index.php page >> in roles permissions</div>
<?php } ?>

<?php if (current_has_permission('/index_php', 'delete')) { ?>
  <div>Guarded by "delete" permission on /index.php page >> in users permissions</div>
<?php } ?>

<?php
// footer
include('pages/footer.html');
?>