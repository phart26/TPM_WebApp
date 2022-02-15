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
		$heat = $_POST['heat'];
		
		$sql = "INSERT INTO steel_tbl(work , material , gage , width , heat , holes , centers , pattern, tpm_po , tpm_job)
				VALUES('$work_no' , '$material' , '$gage' , '$width' , '$heat', '$hole_size' , '$hole_center' ,'$hole_pattern', '$tpm_po_no' , '$tpm_job')";
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
<div class="content-wrapper">
    <h3>Inventory - Steel Work Number<div style="float: right;"><i title="Filter" ev="table.filter" class="fa fa-filter" style="font-size: 18px;padding: 0 15px;"></i><i title="Save Record" ev="table.replace" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i><i title="Delete Record" ev="table.delete"  class="fa fa-trash" style="font-size: 18px;padding: 0 15px;"></i><i title="Reset Form" ev="form.reset" form-name="table.form" class="fa fa-undo" style="font-size: 18px;padding: 0 15px;"></i></div></h3>

    <div id="filter" class="row" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div>
	<h3 id='error'></h3>
	
    <div class="row"  form="table.form" table-name="steel_tbl" fields='{"tpm_job": {"type": "bool"}}' style="margin: 20px 0;">
        <form action="" method="post" >
			<div class="col-lg-12">
				<div class="column">
					<div class="input">
						<span class="left-placeholder">Work Number</span><input name="work" var="work" class="align-right" type="text" id="work_no" required="required"> 
					</div>
					<div class="input">
						<span class="left-placeholder">Material</span><select name="material" var="material" table="mat_tbl" column="material"></select>
					</div>
					<div class="input">
						<span class="left-placeholder">Gage</span><select name="gage" var="gage" table="gage_tbl" column="gage"></select>
					</div>
					<div class="input">
						<span class="left-placeholder">Width</span><input name="width" var="width" class="align-right" type="text" mask="double">
					</div>
					<div class="input">
						<span class="left-placeholder">Heat Number</span><input name="heat" var="heat" class="align-right" type="text" mask="double">
					</div>
				</div>

				<div class="column">
					<div class="input">
						<span class="left-placeholder">Hole Pattern</span><select name="pattern" var="pattern" table="pat_tbl" column="pattern"></select>
					</div>
					<div class="input">
						<span class="left-placeholder">Hole Size</span><select name="holes" var="holes" table="frac_tbl" column="holes" value-field="decimal"><option value="">Select Holes</option></select>
					</div>
					<div class="input">
						<span class="left-placeholder">Hole Centers</span><select name="centers" var="centers" table="frac_tbl" column="centers" value-field="decimal"><option value="">Select Centers</option></select>
					</div>
					<div class="input">
						<span class="left-placeholder">TPM PO#</span><input name="tpm_po" var="tpm_po" class="align-right" type="text" mask="number">
					</div>
					<div class="input">
						<span class="left-placeholder">TPM Job</span><input name="tpm_job" var="tpm_job" type="checkbox">
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
		
		
		$('#work_no').mouseleave(function()
		{
			
			if( $('#work_no').val() ) 
			{
				var work_no = $('#work_no').val();
				$.ajax({
					type: "GET",				
					url: "checkdata.php", // replace 'PHP-FILE.php with your php file
					data: {work_no: work_no , action : 'work_no_check'},
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

		setTimeout(function(){ 
            
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

            }, 3000);
});
</script>