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
if(isset($_POST['save']))
{
	include '../includes/db.php';
	
	$work_no = $_POST['work'];	
	$check_work = "SELECT * FROM steel_tbl WHERE work = '".$work_no."'";
	$result_work = mysqli_query($db , $check_work);
	echo mysqli_error($db);
	
	$nums = mysqli_num_rows($result_work);
	if($nums > 0)
	{
		$status='exists';
	}
	else
	{
		$material = $_POST['material'];
		$gage = $_POST['gage'];
		$width = $_POST['width'];
		$hole_pattern = $_POST['pattern'];
		$hole_size = $_POST['holes'];
		$hole_center = $_POST['centers'];
		$tpm_po_no = $_POST['tpm_po'];
		$tpm_job = $_POST['tpm_job'];
		
		$sql = "INSERT INTO steel_tbl(work , material , gage , width , heat , holes , centers , tpm_po , tpm_job)
				VALUES('$work_no' , '$material' , '$gage' , '$width' , '$hole_pattern' , '$hole_size' , '$hole_center' , '$tpm_po_no' , '$tpm_job')";
		//$r = $db->query($sql);
		//echo $r->error;
		$result = mysqli_query($db , $sql);
		echo mysqli_error($db);
	}
}

?>


 <link href="assets/css/jquery.searchableSelect.css" rel="stylesheet">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="assets/js/jquery.searchableSelect.js"></script>
<script type="text/javascript">
  $(function(){
    setTimeout(function(){ 

  $('#combobox').searchableSelect();
   }, 3000);
});
</script>
<?php 
	include '../includes/db.php';
	$sql = "SELECT coil_no FROM coilno_tbl ORDER BY coil_no DESC LIMIT 1";
	$result = mysqli_query($db , $sql);
	echo mysqli_error($db);
	$coil = mysqli_fetch_assoc($result);

?>
<div class="content-wrapper" style="min-height: 500px;">
    <h3>Steel receive Report
        <div style="float: right;">
			<i onclick="openReport()" title="View" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i>
        </div>
    </h3>

    <div id="filter" class="row" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div>
	
	<script>
		function openReport()
		{
			var coil_no = $("#coil_no").val();
			if(coil_no == "")
			{
				return false;
			}
			
			var no_of_items = $("#no_of_items").val();
			if(no_of_items == "" || coil_no == "")
			{
				return false;
			}
			
			var url = "NewForm.php?coil_number="+coil_no+"&no_of_items="+no_of_items;
			var win = window.open(url, '_blank');
		}
	</script>
	
    <div form="table.form" table-name="coilno_tbl" style="margin: 20px 0;">
        <div>
            <div class="column">
                <div class="input">
                    <span class="left-placeholder">Coil Number</span><input id="coil_no" var="coil_no" class="align-right" type="text" field="Coil Number seems invalid." value="<?php echo ++$coil['coil_no']; ?>" required>
                </div>
                <div class="input">
                    <span class="left-placeholder">No of Items</span><input id="no_of_items" var="no_of_items" class="align-right" type="text" field="No of Items seems invalid." required>
                </div>
				
            </div>
        </div>
    </div>

</div>




<?php if (has_message()) { ?>
  <div class="alert alert-success"><?= get_message()?></div>
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

<script type="text/javascript">
    
    $(document).ready(function(){
		var msg = $('#msg').text();
		//console.log(msg);
		
	
});
</script>