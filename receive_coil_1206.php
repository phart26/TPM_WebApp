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
	
	$coil = $_POST['coil_no'];	
	$check_coil = "SELECT * FROM coil_tbl WHERE coil = '".$coil."'";
	$result_coil = mysqli_query($db , $check_coil);
	echo mysqli_error($db);
	
	$nums = mysqli_num_rows($result_coil);
	if($nums > 0)
	{
		$status='exists';
	}
	else
	{
		$weight = $_POST['weight'];
		$work_no = $_POST['work'];	
		$operator = $_POST['operator'];
		$cycles = $_POST['cycles'];
		$allocated = $_POST['allocated'];
		$tpm_job = $_POST['job'];		
		$date_received = $_POST['date_received'];
	
		$sql = "INSERT INTO coil_tbl (coil_no , weight , work , operator , cycles , allocated , job , date_received)
				VALUES('$coil' , '$weight' , '$work_no' , '$operator' , '$cycles' , '$allocated' , '$tpm_job' , '$date_received')";
		
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
<div class="content-wrapper">
    <h3>Inventory - Receive Steel<div style="float: right;"><i title="Filter" ev="table.filter" class="fa fa-filter" style="font-size: 18px;padding: 0 15px;"></i><i title="Save Record" ev="table.replace" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i><i title="Delete Record" ev="table.delete"  class="fa fa-trash" style="font-size: 18px;padding: 0 15px;"></i><i title="Reset Form" ev="form.reset" form-name="table.form" class="fa fa-undo" style="font-size: 18px;padding: 0 15px;"></i></div></h3>

    <div id="filter" class="row clicktoid" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div>
	
	<h3 id='error'></h3>
	
    <div class="row" form="table.form" table-name="coil_tbl" fields='{"allocated": {"type": "bool"}}' style="margin: 20px 0;">
		<form action="" method="post" >
			<div class="col-lg-12">
				<div class="column">
				  
					<div class="input">
						<span class="left-placeholder">Coil</span><input name="coil_no" var="coil_no" class="align-right" type="text" mask="number" id='coil_no'>
					</div>
					<div class="input">
						<span class="left-placeholder">Weight</span><input name="weight" var="weight" class="align-right" type="text" mask="number">
					</div>
					<div class="input">
						
						<span class="left-placeholder">Work number</span><select name="work" var="work" table="steel_tbl" column="work" id="combobox"></select>
					
					</div>
					<div class="input">
						<span class="left-placeholder">Operator</span><select name="operator" var="operator" table="operators" column="operator"></select>
					</div>
					<div class="input">
						<span class="left-placeholder">Cycles</span><input name="cycles" var="cycles" class="align-right" type="text" mask="number">
					</div>
				</div>

				<div class="column">
					<div class="input">
						<span class="left-placeholder">Allocated</span><input name="allocated" var="allocated" type="checkbox">
					</div>
					<div class="input">
						<span class="left-placeholder">Job</span><input name="job" var="job" class="align-right" type="text" mask="number">
					</div>
					<div class="input">
						<span class="left-placeholder">Received</span><input name="date_received" var="date_received" class="align-right" type="datepicker">
					</div>
				</div>
				
			</div>
			
		</form>
    </div>
</div>




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
 
<script type="text/javascript">
    
    $(document).ready(function(){

		$('#coil_no').mouseleave(function()
		{
			
			if( $('#coil_no').val() ) 
			{
				var coil_no = $('#coil_no').val();
				$.ajax({
				type: "GET",				
				url: "checkdata.php", // replace 'PHP-FILE.php with your php file
				data: {coil_no: coil_no , action : 'coil_no_check'},
				success: function(data) {
					//data = $.parseJSON( data );
				
				//	if(data > 0)
					//{
						$('#error').html(data);
						$('#error').css({"color": "red"});
					//}
					
				  
				},
				error : function(){
				   alert('Some error occurred!');
				}
			});
				
				//$('#error').text('Work Number Exists!');
			}
		});
		
		/*setTimeout(function(){ 
            
            $('[var="holes"]').html($('[var="holes"]').children('option').sort(function (x, y) {
                return $(x).val().toUpperCase() < $(y).val().toUpperCase() ? -1 : 1;
                
            }));
            //console.log('hellooo');
            $('[var="holes"]').get(0).selectedIndex = 0;
            
            $('[var="holes"] option').each(function() {
                if ( $(this).text() == '' ) {
                    $(this).remove();
                }
            });
            
            $('[var="centers"]').html($('[var="centers"]').children('option').sort(function (x, y) {
                return $(x).val().toUpperCase() < $(y).val().toUpperCase() ? -1 : 1;
                
            }));
            //console.log('hellooo');
            $('[var="centers"]').get(0).selectedIndex = 0;
            $('[var="centers"] option').each(function() {
                if ( $(this).text() == '' ) {
                    $(this).remove();
                }
            });
            //$('[var="centers"]').children('option').text().empty().remove();

            }, 3000); */
});
</script>