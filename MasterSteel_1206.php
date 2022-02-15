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
	$tpm_po_no = $_POST['tpm_po'];
	$work_no = $_POST['work'];
	$weight = $_POST['weight'];
	$material = $_POST['material'];
	$gage = $_POST['gage'];
	$width = $_POST['width'];
	$hole_pattern = $_POST['pattern'];
	$hole_size = $_POST['holes'];
	$hole_center = $_POST['centers'];
	$heat = $_POST['heat'];
	$date_received = $_POST['date_received'];
	$allocated = $_POST['allocated'];	
	$tpm_job = $_POST['job'];
	$cycles = $_POST['cycles'];
	
	

	$sql = "INSERT INTO new_materials(coil , tpm_po_no , work_no , weight , material , gage , width , hole_pattern , hole_size , hole_centers , heat_number , date_received , allocated , tpm_job , cycles)
							VALUES('$coil' , '$tpm_po_no' , '$work_no' , '$weight' , '$material' , '$gage' , '$width' , '$hole_pattern' , '$hole_size' , '$hole_center' , '$heat' , '$date_received' , '$allocated' , '$tpm_job' , '$cycles')";
	//$r = $db->query($sql);
	//echo $r->error;
	$result = mysqli_query($db , $sql);
	echo mysqli_error($db);
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
    <h3>MasterSteel<div style="float: right;"><i title="Filter" ev="table.filter" class="fa fa-filter" style="font-size: 18px;padding: 0 15px;"></i><i title="Save Record" ev="table.replace" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i><i title="Delete Record" ev="table.delete"  class="fa fa-trash" style="font-size: 18px;padding: 0 15px;"></i><i title="Reset Form" ev="form.reset" form-name="table.form" class="fa fa-undo" style="font-size: 18px;padding: 0 15px;"></i></div></h3>

    <div id="filter" class="row clicktoid" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div>
	
    <div class="row" form="table.form" table-name="new_materials" fields='{"allocated": {"type": "bool"}}' style="margin: 20px 0;">
        <form action="" method="post" >
			<div class="col-lg-12">
				<div class="column">
				  
					
					<div class="input">
						
						<span class="left-placeholder">Coil</span><select class="a1" name="coil_no" var="coil_no" table="coil_tbl" column="coil_no"><option value="">Select Coil No</option></select>					
						<!--<span class="left-placeholder">Coil</span><input var="coil" class="align-right" type="text" mask="number"> -->
					</div>
					<div class="input">
						<span class="left-placeholder">TPM PO#</span><input var="tpm_po_no" class="align-right" type="text" mask="number">
					</div>
					<div class="input">						
						<span class="left-placeholder">Work number</span><select var="work_no" table="steel_tbl" column="work"><option value="">Select Work No</option></select>					
					</div>
					<div class="input">
						<span class="left-placeholder">Weight</span><input var="weight" class="align-right" type="text" mask="number">
					</div>
					<div class="input">
						<span class="left-placeholder">Material</span><select var="material" table="mat_tbl" column="material"></select>
					</div>

					<div class="input">
						<span class="left-placeholder">Thickness</span><select var="gage" table="gage_tbl" column="gage"></select>
					</div>

					<div class="input">
						<span class="left-placeholder">Width</span><input var="width" class="align-right" type="text" mask="double">                    
					</div>

					<div class="input">
						<span class="left-placeholder">Hole Pattern</span><select var="hole_pattern" table="pat_tbl" column="pattern"></select>
					</div>

					
				</div>

				<div class="column">
					<div class="input">
						<span class="left-placeholder">Hole Size</span><select var="hole_size" table="frac_tbl" column="holes" value-field="decimal"><option value="">Select Holes</option></select>
					</div>

					 <div class="input">
						<span class="left-placeholder">Hole Centers</span><select var="hole_centers" table="frac_tbl" column="centers" value-field="decimal"><option value="">Select Centers</option></select>
					</div>

					<div class="input">
						<span class="left-placeholder">Heat Number</span><input var="heat_number" class="align-right" type="text" >
					</div>

					<div class="input">
						<span class="left-placeholder">Date Received</span><input var="date_received" class="align-right" type="datepicker">
					</div>

					<div class="input">
						<span class="left-placeholder">Allocated</span><input var="allocated" type="checkbox">
					</div>

					<div class="input">
						<span class="left-placeholder">Job</span><input var="tpm_job" class="align-right" type="text" mask="number">
					</div>

					<div class="input">
						<span class="left-placeholder">Cycles</span><input var="cycles" class="align-right" type="text" mask="number">
					</div>



					<!-- <div class="input">
						<span class="left-placeholder">Operator</span><select var="operator" table="operators" column="operator"></select>
					</div> -->
					
					
					
					
					

					
				   
					
					<!-- <div class="input">
						<span class="left-placeholder">TPM Job</span><input var="tpm_job" type="checkbox">
					</div> -->
				</div>
				
				<!-- </div>
				</div> -->


				<!-- <div class="row" form="table.form" table-name="steel_tbl" fields='{"tpm_job": {"type": "bool"}}' style="margin: 20px 0;"> -->
				<!-- <div class="col-lg-12"> -->
				  
				<!-- </div>-->
			</div> 
		</form>

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
		
		$('.a1').on('change', function() {
		//$('[var="coil"]').change(function() {
			var coil = $('.a1').val();
			
			
			$.ajax({
				type: "GET",				
				url: "getdata.php", // replace 'PHP-FILE.php with your php file
				data: {coil: coil},
				success: function(data) {
					data = $.parseJSON( data );
					
					$('[var="tpm_po_no"]').val(data["tpm_po"]);
					$('[var="work_no"]').val(data["awork"]);
					$('[var="weight"]').val(data["weight"]);
					$('[var="material"]').val(data["material"]);
					$('[var="gage"]').val(data["gage"]);
					$('[var="width"]').val(data["width"]);
					$('[var="hole_pattern"]').val(data["pattern"]);
					$('[var="hole_size"]').val(data["holes"]);
					$('[var="hole_centers"]').val(data["centers"]);
					$('[var="heat_number"]').val(data["heat"]);
					$('[var="date_received"]').val(data["date_received"]);
					if( data["allocated"] == "1" ) {
						$('[var="allocated"]').attr('checked', true );
					}
					$('[var="tpm_job"]').val(data["job"]);
					$('[var="cycles"]').val(data["cycles"]);
				  
				},
				error : function(){
				   alert('Some error occurred!');
				}
			});
		});

		/*setTimeout(function(){ 
            
            $('[var="holes"]').html($('[var="holes"]').children('option').sort(function (x, y) {
                return $(x).val().toUpperCase() < $(y).val().toUpperCase() ? -1 : 1;
                
            }));
            console.log('hellooo');
            $('[var="holes"]').get(0).selectedIndex = 0;
            
            $('[var="holes"] option').each(function() {
                if ( $(this).text() == '' ) {
                    $(this).remove();
                }
            });
            
            $('[var="centers"]').html($('[var="centers"]').children('option').sort(function (x, y) {
                return $(x).val().toUpperCase() < $(y).val().toUpperCase() ? -1 : 1;
                
            }));
            console.log('hellooo');
            $('[var="centers"]').get(0).selectedIndex = 0;
            $('[var="centers"] option').each(function() {
                if ( $(this).text() == '' ) {
                    $(this).remove();
                }
            });
            $('[var="centers"]').children('option').text().empty().remove();

            }, 3000); */
			
			
			
			
});
</script>