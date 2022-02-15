<?php
//ini_set('error_reporting', E_ALL);
//error_reporting(E_ALL);
require_once('core/main.php');
define('DIR',dirname(__DIR__).'/user_files/');

$url = $_GET['url'];

// table data processing
if (preg_match("/^table\/([a-zA-Z0-9\_\-]+)\/(fetch|delete|save|newrecord|update)$/", $url, $match))
{
    list(, $table, $action) = $match;

    $$table = new Table( $table );

    switch ($action)
    {
        case 'fetch':
        {
            done(array(
                'list' => $$table->fetch( $_JSON )
            ));
        }
        break;

        case 'delete' :
        {
            done(array(
                'done' => $$table->delete( $_JSON )
            ));
        }
        break;

        case 'save':
        {
            done(array(
                'done' => $$table->replace( $_JSON )
            ));
        }
        break;
        case 'update':
        {
            $f = fopen('log.txt', "a");
            fwrite($f, "-----\n" . "update\n-----\n");
            fclose($f);
            done(array(
                'done' => $$table->update( $_JSON )
            ));
        }
        break;
        case 'newrecord':
        {

            done(array(
                'done' => $$table->newrecord( $_JSON)
            ));
        }
        break;
    }
}


// select options
if (preg_match("/^selectOptions\/([a-zA-Z0-9\_\-]+)\/(fetch|delete|save)$/", $url, $match))
{
    list(, $name, $action) = $match;

    $selectTable = new Table('select_options');

    switch ($action)
    {
        case 'fetch':
        {
            done(array(
                'list' => $selectTable->fetch(array(
                    'fieldname' => $name
                ))
            ));
        }
        break;
    }
}

if (preg_match("/^table\/([a-zA-Z0-9\_\-]+)\/(fetchpagination)$/", $url, $match))
{
    list(, $name, $action) = $match;
    done(array(
        'list' => tablepagination($name,$_GET['limit'],$_GET['page'])
    ));
        
}
if ($url == "custorderwise")
{
     done(array(
        'list' => custorderwise()
    ));
}
if ($url == "activeJobs")
{
     done(array(
        'list' => activeJobs()
    ));
}

if ($url == "filtersearch")
{
     done(array(
        'list' => filtersearch($_GET['filterval'],$_GET['field'],$_GET['table'])
    ));
}
if ($url == "ship-info-table")
{
    // done([
    //     'list' => $$table->fetch( $_JSON )
    // ]);
    done(array(
        'list' => shipInfoTable()
    ));
}
if ($url == "order_list_coil")
{
   
    done(array(
                'done' => order_list_coil($_GET['type'],$_GET['part_id'],$_GET['job'])
            ));
}
if ($url == "order_list_mesh")
{
   
    done(array(
                'done' => order_list_mesh($_GET['type'],$_GET['part_id'],$_GET['rdchecked'],$_GET['job'])
            ));
}
if($url == "order_yet_to_start")
{
    done(array(
            'list' => order_yet_to_start($_GET['limit'],$_GET['page'])
        ));
}
if($url == "order_started")
{
    done(array(
            'list' => order_started($_GET['limit'],$_GET['page'])
        ));
}
if($url == "order_shipped")
{
    done(array(
            'list' => order_shipped($_GET['limit'],$_GET['page'])
        ));
}
if($url == "order_finished")
{
    done(array(
            'list' => order_finished($_GET['limit'],$_GET['page'])
        ));
}
if ($url == "update_data")
{
   
    done(array(
                'done' => update_data($_GET['type'],$_GET['id'],$_GET['job'])
            ));
}
if ($url == "part-specs")
{

    done(array(
        'done' => part_specs($_GET['part_name'])
    ));
}

if ($url == "die-stamp")
{

    done(array(
        'done' => get_die_stamp($_GET['die_id'])
    ));
}

if($url == "weight_show")
{
    done(array(
        'done' => weight_listing($_GET['job'])
    ));
}
if($url == "meshweight_show")
{
    done(array(
        'done' => meshweight_show($_GET['job'])
    ));
}

if($url =="part_show"){
    done(array(
        'done'=>part_show($_GET['partn'])
    ));
}
if($url == "mat_value")
{
    done(array(
        'done' => mat_value($_GET['material'])
    ));
}
if($url == "quote_id")
{
    done(array(
        'done' => quote_id()
    ));
}
if($url == "uni_quote_id")
{
    done(array(
        'done' => uni_quote_id()
    ));
}
if($url == "Enter_as_Used")
{
    done(array(
        'done' => Enter_as_Used($_GET['newval'],$_GET['coil'],$_GET['oldone'])
    ));
}
if($url == "quote")
{
    done(array(
        'list' => quote($_GET['limit'],$_GET['page'])
    ));
}
if($url=="part_mat"){
    done(array(
        'list' =>part_mat($_GET['userid'])
    ));
}
if($url=="get_part_table"){
    done(array(
        'list' =>get_part_table($_GET['userid'],$_GET['part'])
    ));
}
if($url=="get_micron_table"){
    done(array(
        'list' =>get_micron_table($_GET['id'])
    ));
}
if($url=="job_mat_orders"){
    done(array(
        'list' =>job_mat_orders($_GET['userid'],$_GET['part'])
    ));
}
if($url == "material_save")
{
    done(array(
        'done'=>material_save($_GET['dimension'], $_GET['center'], $_GET['cust_id'], $_GET['gage'], $_GET['holes'], $_GET['part'], $_GET['patter'], $_GET['quantity'], $_GET['strip'], $_GET['total'], $_GET['lengtht'],$_GET['od'],$_GET['material'],$_GET['po'],$_GET['scrap_rate'],$_GET['density'],$_GET['Weight_bs'])
    ));
}
if($url == "uniscreen_save")
{
    done(array(
        'done'=>uniscreen_save($_GET['cust_id'],
                                $_GET['part'],$_GET['job'],
                                $_GET['Inner_Shroud_ID'],
                                $_GET['Inner_Shroud_Gage'],
                                $_GET['Outer_Shroud_Gage'],
                                $_GET['Outer_Shroud_Strip'],
                                $_GET['InnerShroudPattern'],
                                $_GET['Inner_Shroud_Holes'],
                                $_GET['Inner_Shroud_Centers'],
                                $_GET['OuterShroudPattern'],
                                $_GET['Outer_Shroud_Holes'],
                                $_GET['Outer_Shroud_Centers'],
                                $_GET['Gaps'],
                                $_GET['UniScreen_Length'],
                                $_GET['mesh'])
    ));
}
if($url == "uniscreen_update")
{
    done(array(
        'done'=>uniscreen_update($_GET['id'],$_GET['cust_id'],
                                $_GET['part'],$_GET['job'],
                                $_GET['Inner_Shroud_ID'],
                                $_GET['Inner_Shroud_Gage'],
                                $_GET['Outer_Shroud_Gage'],
                                $_GET['Outer_Shroud_Strip'],
                                $_GET['InnerShroudPattern'],
                                $_GET['Inner_Shroud_Holes'],
                                $_GET['Inner_Shroud_Centers'],
                                $_GET['OuterShroudPattern'],
                                $_GET['Outer_Shroud_Holes'],
                                $_GET['Outer_Shroud_Centers'],
                                $_GET['Gaps'],
                                $_GET['UniScreen_Length'],
                                $_GET['mesh'])
    ));
}
if($url == "material_update")
{
    done(array(
        'done'=>material_update($_GET['id'],$_GET['dimension'], $_GET['center'], $_GET['cust_id'], $_GET['gage'], $_GET['holes'], $_GET['part'], $_GET['patter'], $_GET['quantity'], $_GET['strip'], $_GET['total'], $_GET['lengtht'],$_GET['od'],$_GET['material'],$_GET['po'],$_GET['scrap_rate'],$_GET['density'],$_GET['Weight_bs'])
    ));
}
if($url == "save_report")
{
    done(array(
        'done'=>save_report($_GET['customer'],$_GET['po_number'], $_GET['quantity'], $_GET['date_ordered'], $_GET['date_due'], $_GET['line_item'], $_GET['mill_spec'], $_GET['repair_spec'], $_GET['ship_date'], $_GET['mill_amps'], $_GET['mill_volts'], $_GET['mill_speed'],$_GET['repair_amps'],$_GET['repair_volts'],$_GET['repair_speed'],$_GET['job_id'])
    ));




}
if($url == "deleterow"){
    done(array(
        'done'=>delettable($_GET['id'],$_GET['table'])
    ));
}
if($url == "file_exist"){
    done(array(
        'done'=>file_exist($_GET['part'],$_GET['cust_id'])
    ));
}
if($url == "file_existreport"){
    done(array(
        'done'=>file_existreport($_GET['part'],$_GET['cust_id'])
    ));
}
if($url == "file_delete"){
    done(array(
        'done'=>file_delete($_GET['deletfile'])
    ));
}
if($url == "upload_file"){
      done(array(
        'done'=>upload_file($_POST['part'],$_POST['client_name'],$_FILES['uploaddrawing'])
    ));
}

if($url == "delete_quote_detail")
{
    done(array(
        'done' => delete_quote_detail($_GET['quote'])
    ));
}

if($url == "uni_quote")
{
    done(array(
        'list' => uni_quote($_GET['limit'],$_GET['page'])
    ));
}
if($url == "excess_part")
{
    done(array(
        'list' => excess_part($_GET['limit'],$_GET['page'])
    ));
}
if($url == "typeGage")
{
    done(array(
        'list' => typeGage($_GET['userid'],$_GET['part_no'])
    ));
}
if($url == "getid")
{
    done(array(
        'list' => get_latest($_GET['getidnew'],$_GET['coll'])
    ));
}
if($url == "getStrip")
{
    done(array(
        'list' => getStrip($_GET['partnum'])
    ));
}
if($url == "getPart")
{
    done(array(
        'list' => getPart($_GET['job'])
    ));
}
if($url == "matsearch")
{
    done(array(
        'list' => matsearch($_GET)
    ));
}
if($url == "user_setting_save")
{
    done(array(
        'list' => user_setting_save($_POST)
    ));
}
if($url == "postDieStamp")
{
    done(array(
        'list' => die_stamp($_POST['die_id'], $_POST['progression'])
    ));
}
if($url == "odlength")
{
    done(array(
        'list' => od_length($_GET['from'])
    ));
}
if($url == "odlengthpart")
{
    done(array(
        'list' => odlengthpart($_GET['from'])
    ));
}
if($url=='employee'){
    done(array(
        'list'=>employee()
    ));
}
if($url == 'training_report'){
    done(array(
        'list'=>training_report($_GET)
    ));
}
if ($url=='standard_prices'){
    done(array(
        'list'=>standard_prices($_GET)
    ));
}
if ($url=='allocate_all_part'){
    done(array(
        'done'=>allocate_all_part($_POST)
    ));
}
if ($url=='Enter_as_Used_mesh'){
    done(array(
        'done'=>Enter_as_Used_mesh($_GET['newval'],$_GET['mesh_n'],$_GET['oldone'])
    ));
}
if ($url=='meshtotal_show'){
    done(array(
        'done'=>meshtotal_show($_GET['jobno'])
    ));
}
if ($url=='orderspecpartspec'){
    done(array(
        'done'=>orderspecpartspec($_GET['jobid'])
    ));
}
?>

  