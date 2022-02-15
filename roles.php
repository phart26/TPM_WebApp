<?php
require_once __DIR__ . '/init.php';

$sql = 'SELECT * FROM roles';
$r = $db->query($sql);
$roles = $r->fetch_all(MYSQLI_ASSOC);

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

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Role Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($roles as $role) { ?>
            <tr>
                <td><?= $role['role_name'] ?></td>
                <td><a class="btn btn-info" href="<?= HTTP_ROOT ?>view_role.php?id=<?= $role['id'] ?>"><i class="fa fa-eye"></i></a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php
// footer
include('pages/footer.html');
?>