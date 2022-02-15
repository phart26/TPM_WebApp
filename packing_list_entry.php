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
use Dompdf\Dompdf;
// page content
if(isset($_POST['save']))
{   

	include '../includes/db.php';
    date_default_timezone_set("US/Central");
    
    // run through each packing item and add to db
    for($i = 0; $i < count($_POST['no_coil']); $i++){
        $po = $_POST['po'];
        $heat_no = $_POST['heat_no'];	
        $type_mat = $_POST['type_mat'];
        $type_mesh = $_POST['type_mesh'];
        $tot_len = $_POST['totLen'];
        $allocated = isset($_POST['allocated1']) ? 1 : 0;
        $tpm_job = $_POST['job'];		
        $date_entered = date("Y-m-d H:i:s");
        $length = $_POST['length'][$i];
        $width = $_POST['width'];
        $no_coil = $_POST['no_coil'][$i];
        $crate = $_POST['crate'][$i];
        
        $sql = "INSERT INTO packing_list_entry (po, heat_num, type_mat, mesh, tot_len, width, length, quantity, crate, allocated, job, date_entered)
                VALUES('$po' , '$heat_no' , '$type_mat' , '$type_mesh' , '$tot_len', '$width' , '$length' , '$no_coil' , '$crate','$allocated','$tpm_job', '$date_entered')";
            
        $result = mysqli_query($db , $sql);
        echo mysqli_error($db);

        


    }

    $command = escapeshellcmd('/opt/bitnami/apache2/htdocs/coil_combo_alg.py');
    shell_exec($command);

    $po = $_POST['po'];
    $date_entered = date("Y-m-d H:i:s");
    // update started flag in db
    $sql = "UPDATE mesh_jobs SET started = 1, start_time = '$date_entered' WHERE po = '$po'";
            
    $result = mysqli_query($db , $sql);
    echo mysqli_error($db);

    // //get all the newely entered coils
    // $sql = "SELECT * FROM mesh_tbl WHERE tpm_po = '$po'";

    // if ($resultConn= $db -> query($sql)) {
        
    // }

    // $meshes = array();
    // //fetch resulting rows as an array
    // while($order = mysqli_fetch_assoc($resultConn)){
    //     $meshes[] = $order;
    // }

    // //get the mesh job
    // $sql = "SELECT * FROM mesh_jobs WHERE po = '$po'";

    // if ($resultConn= $db -> query($sql)) {
        
    // }

    // $meshJob = mysqli_fetch_assoc($resultConn);
    
    // ob_start();

    // require_once 'newMesh.php';
    
    // require_once 'dompdf/autoload.inc.php';
    
    // $fileName = strval($po)."_mesh.pdf";


    // $html = ob_get_clean();


    // // instantiate and use the dompdf class
    // $dompdf = new Dompdf();
    // $dompdf->set_option('isHtml5ParserEnabled', true);
    // $dompdf->loadHtml($html);

    // // (Optional) Setup the paper size and orientation
    // $dompdf->setPaper('A4', 'landscape');

    // // Render the HTML as PDF
    // $dompdf->render();

    // // $dompdf->output();

    // $dompdf->stream($fileName, array("Attachment"=>1));
	
}

?>


 <link href="assets/css/jquery.searchableSelect.css" rel="stylesheet">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="assets/js/jquery.searchableSelect.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
//   $(function(){
//     setTimeout(function(){ 

//   $('#combobox').searchableSelect();
//    }, 3000);
// });
</script>
<div class="content-wrapper">
    <h3>Inventory - Packing List Entry<div style="float: right;"><i title="Filter" ev="table.filter" class="fa fa-filter" style="font-size: 18px;padding: 0 15px;"></i><i title="Save Record" ev="table.replace" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i><i title="Delete Record" ev="table.delete"  class="fa fa-trash" style="font-size: 18px;padding: 0 15px;"></i><i title="Reset Form" ev="form.reset" form-name="table.form" class="fa fa-undo" style="font-size: 18px;padding: 0 15px;"></i></div></h3>

    <div id="filter" class="row clicktoid" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div>
	
	<h3 id='error'></h3>
	
    <div class="row" form="table.form" table-name="packing_tbl" fields='{"allocated": {"type": "bool"}}' style="margin: 20px 0;">
		<form action="" method="post" >
			<div class="col-lg-12">
				<div class="column">
				  
				    <div class="input">
						<span class="left-placeholder">Heat number</span><input name="heat_no" var="heat_no"  class="align-right" type="text">
                    </div>
                    <div class="input">
						<span class="left-placeholder">PO</span><input name="po" var="po"  class="align-right" type="text">
                    </div>
					<div class="input">
						<span class="left-placeholder">Material Type</span><input name="type_mat" var="type_mat"  class="align-right" type="text">
                    </div>
                    <div class="input">
						<span class="left-placeholder">Mesh Type</span><input name="type_mesh" var="type_mesh"  class="align-right" type="text">
                    </div>
                    <div class="input">
						<span class="left-placeholder">Width</span><input name="width" var="width" class="align-right" type="number" step="0.01">
                    </div>
                    <div class="input">
						<span class="left-placeholder">Total Length</span><input name="totLen" var="totLen" class="align-right" type="number" step="1">
                    </div>

                </div>

                <div class="column">

					<div class="input">
						<span class="left-placeholder">Allocated</span><input name="allocated0" var="allocated0" type="hidden" value="0"><input name="allocated1" var="allocated1" type="checkbox" value="1">
					</div>
					<div class="input">
						<span class="left-placeholder">Job</span><input name="job" var="job" class="align-right" type="number">
                    </div>
                    
                </div>

                <div class="column">

					<div class="input">
						<span class="left-placeholder">Number of coils</span><input name="no_coil[]" class="align-right" type="number">
					</div>
					
					<div class="input">
                        <span class="left-placeholder">Length (linear ft.)</span><input name="length[]" class="align-right" type="number" step="0.01">
					</div>
					<div class="input">
						<span class="left-placeholder">Crate</span><input name="crate[]" class="align-right" type="number">
                    </div>
                    
                </div>

                <!-- dynamic add button for multiple entries on the packing list -->
                <div class="column">
                    <div class="field_wrapper">
                        <div>
                            <a href="javascript:void(0);" class="add_button" title="Add field"><button type="button">+</button></a>
                        </div>

                        <br>
                    </div>

                </div>

                <div class="column">
                    <div>
                        <span class="left-placeholder"></span><input type="submit" name="save" value="save">
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

    // adds a new line item for coils to be entered
    $(document).ready(function(){
        var maxField = 1000; //Input fields increment limitation
        var addButton = $('.add_button'); //Add button selector
        var wrapper = $('.field_wrapper'); //Input field wrapper
        var fieldHTML = '<div class="column"><div class="input"><span class="left-placeholder">Number of coils</span><input name="no_coil[]" class="align-right" type="number"><a href="javascript:void(0);" class="remove_button"><button type="button">-</button></a></div><div class="input"><span class="left-placeholder">Length (linear ft.)</span><input name="length[]" class="align-right" type="number" step="0.01"></div><div class="input"><span class="left-placeholder">Crate</span><input name="crate[]" class="align-right" type="number"></div></div>'; //New input field html 
        var x = 1; //Initial field counter is 1
        
        //Once add button is clicked
        $(addButton).click(function(){
            //Check maximum number of input fields
            if(x < maxField){ 
                x++; //Increment field counter
                $(wrapper).append(fieldHTML); //Add field html
            }
        });
        
        //Once remove button is clicked
        $(wrapper).on('click', '.remove_button', function(e){
            e.preventDefault();
            $(this).parent('div').parent('div').remove(); //Remove field html
            x--; //Decrement field counter
        });
    });
    
//     $(document).ready(function(){
// 		// chnage by vishal
// 		$('#coilno').change(function()  
// 		{
// 			var coil_no=$('#coilno').val();
// 			$.ajax({
// 				type: "GET",				
// 				url: "checkdata.php", // replace 'PHP-FILE.php with your php file
// 				data: {coil_no: coil_no , action : 'get_wo_no'},
// 				success: function(data) {
// 					$('#combobox100').val(data);
// 				}
// 			});
			
// 		});
		
// 		$('#coil_no').mouseleave(function()
// 		{
// 			if( $('#coil_no').val() ) 
// 			{
// 				var coil_no = $('#coil_no').val();
// 				$.ajax({
// 				type: "GET",				
// 				url: "checkdata.php", // replace 'PHP-FILE.php with your php file
// 				data: {coil_no: coil_no , action : 'coil_no_check'},
// 				success: function(data) {
// 					//data = $.parseJSON( data );
				
// 				//	if(data > 0)
// 					//{
// 						$('#error').html(data);
// 						$('#error').css({"color": "red"});
// 					//}
					
				  
// 				},
// 				error : function(){
// 				   alert('Some error occurred!');
// 				}
// 			});
				
// 				//$('#error').text('Work Number Exists!');
// 			}
// 		});
// });
</script>