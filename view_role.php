<?php
require_once __DIR__ . '/init.php';

// Update the role name only
if (isPost() && postHas('role-name')) {
  $sql = 'UPDATE roles SET role_name = \'' . post_string('role-name') . '\' WHERE id = ' . post_int('role-id');
  $r = $db->query($sql);

  redirect_to('view_role.php?id=' . post_int('role-id'));
}

if (isPost() && postHas('permissions')) {
  $sql = 'DELETE FROM roles_permissions WHERE role_id = ' . post_int('role-id');
  $r = $db->query($sql);
  
  $sql = 'SELECT * FROM pages';
  $r = $db->query($sql);
  $site_pages = $r->fetch_all(MYSQLI_ASSOC);

  $role_id = post_int('role-id');
  foreach ($site_pages as $page) {
//    var_dump($_POST);
//    var_dump($_POST[$page['page_name']] );
    if (isset($_POST[$page['page_name']])) {

      foreach ($_POST[$page['page_name']] as $op) {
        $permission_id = get_permission_id($page['page_name'], $op);
        $sql = "INSERT INTO roles_permissions(role_id, permission_id) VALUES({$role_id}, {$permission_id})";
        $db->query($sql);
      }
    }
  }
}

$sql = 'SELECT * FROM roles WHERE id = ' . get_int('id');
$r = $db->query($sql);
$role = $r->fetch_assoc();

$sql = 'SELECT * FROM pages';
$r = $db->query($sql);
$site_pages = $r->fetch_all(MYSQLI_ASSOC);

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
    <input type="hidden" name="role-id" placeholder="Descriptive name for the role" value="<?= $role['id'] ?>"/>
    <input type="text" name="role-name" placeholder="Descriptive name for the role" value="<?= $role['role_name'] ?>"/>
    <button class="btn btn-primary">Save</button>
</form>

<form method="post">
    <input type="hidden" name="permissions" placeholder="Descriptive name for the role" value="1"/>
    <input type="hidden" name="role-id" placeholder="Descriptive name for the role" value="<?= $role['id'] ?>"/>
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
                      $permissions = $r->fetch_all(MYSQLI_ASSOC);
                      foreach ($permissions as $p) {
                        ?>
                        <label>
                            <input type="checkbox" name="<?= $page['page_name'] ?>[]" value="<?= $p['operation'] ?>" <?= role_has_permission($page['page_name'], $p['operation'], get_int('id')) ? 'checked' : '' ?> /> <?= $p['description'] ?>
                        </label>
                      <?php } ?>
                  </td>
              </tr>
            <?php } ?>
        </tbody>
    </table>
    <button class="btn btn-primary">Update Permissions</button>
</form>

<?php
// footer
include('pages/footer.html');
?>