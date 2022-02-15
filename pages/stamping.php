
<?php
    function getCustomer($cust_id){
        $conn = new mysqli("localhost", "root", "anuj", "demo_tpm");
        $sql = "SELECT * FROM cust_tbl WHERE cust_id = '".$cust_id."'";

        //make query & get result
        if ($result= $conn -> query($sql)) {
        
        }
        
        //fetch resulting rows as an array
        $orderACT = mysqli_fetch_assoc($result);

        return $orderACT['customer'];
    }
?>

<script>
        var data, cur = 0;

        function saveMaster() {

            // validate input
            var invalid = $('[form="table.form"] [mask].invalid').filter(':first')

            if ( invalid.length )
                return message(invalid.attr('field'), 2000)

            var data = parseForm('table.form'),
                msg  = message('Saving...'),
                tbl  = $('[form="table.form"]').attr('table-name')
                //console.log(data);

                var curret = $('#filter tbody tr.active');
                
                curret.find('td:eq(0)').text(data.job).attr('title',data.job);
                curret.find('td:eq(1)').text(data.cust_id).attr('title',data.cust_id);
                curret.find('td:eq(3)').text(data.part).attr('title',data.part);
                curret.find('td:eq(4)').text(data.rouselle).attr('title',data.rouselle);
                curret.find('td:eq(4)').text(data.rouselleAlt).attr('title',data.rouselleAlt);
                curret.find('td:eq(5)').text(data.niagara).attr('title',data.niagara);
                curret.find('td:eq(6)').text(data.cycles).attr('title',data.cycles);
                curret.find('td:eq(7)').text(data.linear_feet).attr('title',data.linear_feet);
                curret.find('td:eq(8)').text(data.strip).attr('title',data.strip);
                curret.find('td:eq(9)').text(data.stripAlt).attr('title',data.stripAlt);
                curret.find('td:eq(10)').text(data.millJob).attr('title',data.millJob);
                curret.find('td:eq(11)').text(data.millCycles).attr('title',data.millCycles);
                curret.find('td:eq(12)').text(data.remarks).attr('title',data.remarks);
                curret.find('td:eq(13)').text(data.testCycles).attr('title',data.testCycles);
                curret.find('td:eq(14)').text(data.press).attr('title',data.press);





// alert(JSON.stringify(data));
// alert('table/'+ tbl +'/save');

            request('table/'+ tbl +'/save', data)
                .always(function(){
                    msg.remove()
                })
                .done(function(res){
                    //alert(JSON.stringify(res));

                    //console.log(res);
                    message('Saved', 2000)

                    EVENTS['table.list']();

                })
        }

        function resetEverything() {
            resetForm('table.form');
            setTimeout(function() {
                resetDetail();
                $('.detail').hide();
            }, 1000);
        }

        function deleteEverything() {
            var job = $('[var=job]').val(),
                msg  = message('Deleting...');
            request('table/stamping_orders_tbl/delete', {'job': job})
            .always(function() {
                msg.remove()
            })
                     .done(function(response){

                        message('Deleted', 2000)

                        resetForm('table.form');
                        EVENTS['table.list']();

                    })
        }
        var calculation = [];
        $(document).ready(function() {

        $('.detail').hide();        
        getid();

        $(document).on('click','[ev="form.reset"]',function(){
            getid();
        });

           
        });//ready close here
    </script>

<script>
    $(document).ready(function(){
        $('select[var="cust_id"]').on('change',function(e){           
                onCustomerChange(this); 
        });


         $.post(setting.server+"index.php?url=custorderwise", 
            function(response){
                //console.log(response);
                var cust = $('[var="cust_id"]');
                for(var i=0; i<response.list.length; i++)
                {
                    cust.append(
                        $('<option>'+ response.list[i].customer +'</option>').prop('value', response.list[i].cust_id)
                    )
                   
                }
                onCustomerChange(cust);

            });

            $.post(setting.server+"index.php?url=activeJobs", 
            function(response){
                //console.log(response);
                var millJob = $('[var="millJob"]');
                millJob.append($('<option>    </option>').prop('value', ""));
                for(var i=0; i<response.list.length; i++)
                {
                    millJob.append(
                        $('<option>'+ response.list[i].job +'</option>').prop('value', response.list[i].job)
                    )
                   
                }
                

            });




       
    });
    function onCustomerChange(select) {
       
        var part = $('.part_id');
        // change value of filter for part_id
       
        part.attr('filter_value', select.value);
        
        // remove all options from part_id
        part.find('option').remove();
       
        
        // append new options for part_id
        var column = part.attr('column'),
            source = part.attr('data-tablen'),
            value = part.attr('value-field') || column,
            filter_column = part.attr('data-filtercolumn'),
            filter_value = part.attr('filter_value');

            var cust = $('[var="cust_id"]').val();

    // if(e ==1){

    

        request('table/'+ source +'/fetch', { "cust_id":cust })
            .done(function(response){ 
                if (!response.list) return;
                
                for(var i=0; i<response.list.length; i++)
                {
                    var option = response.list[i];

                    if (filter_column && filter_value) {
                        if (option [ filter_column ] != filter_value) continue;
                    }

                    if (!saved_i) var saved_i = i;

                    part.append(
                        $('<option>'+ option[ column ] +'</option>').prop('value', option[ value ])
                    )
                }
                $('.inputWeightParent').hide();
                    if(response.list.length !=0){
                        getPart();

                        
                    }
                
            });
    }   

    function onPartNoChange(part_no) {
        if(part_no != ''){
        request('part-specs?part_name='+part_no, {})
            .done(function(response){
                if (response.length !=0){
                    calculation = response.done;
                    $.each(response.done,function(i,j){
                        if ($('.quantity').val()!=''){
                                $('.'+i).val((parseFloat(j)*parseFloat($('.quantity').val())).toFixed(3));
                        }
                        else{
                            $('.'+i).val(j.toFixed(3));
                        }
                    })

                }
            });
        }
    }


</script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript">
    /*
* File: jquery.simplePopup.js
* Version: 1.0.0
* Description: Create a simple popup to display content
* Author: 9bit Studios
* Copyright 2012, 9bit Studios
* http://www.9bitstudios.com
* Free to use and abuse under the MIT license.
* http://www.opensource.org/licenses/mit-license.php
*/

(function ($) {

    $.fn.simplePopup = function (options) {

    var defaults = $.extend({
            centerPopup: true,
            open: function() {},
            closed: function() {}
    }, options);
        
    /******************************
    Private Variables
    *******************************/         
        
    var object = $(this);
    var settings = $.extend(defaults, options);
        
    /******************************
    Public Methods
    *******************************/         
        
    var methods = {

        init: function() {
        return this.each(function () {
            methods.appendHTML();
            methods.setEventHandlers();
            methods.showPopup();
        });
        },

        /******************************
        Append HTML
        *******************************/            
 
        appendHTML: function() {

        // if this has already been added we don't need to add it again
        if ($('.simplePopupBackground').length === 0) {
            var background = '<div class="simplePopupBackground"></div>';
            $('body').prepend(background);
        }

        if(object.find('.simplePopupClose').length === 0) {
            var close = '<div title="Close Form" class="fa fa-remove simplePopupClose"></div>';
            object.prepend(close);
        }
        },

        /******************************
        Set Event Handlers
        *******************************/            

        setEventHandlers: function() {

        $(".simplePopupClose, .simplePopupBackground").on("click", function (event) {
            methods.hidePopup();
        });

        $(window).on("resize", function(event){
                    
                    if(settings.centerPopup) {
                        methods.positionPopup();
                    }
        });             

        },

            removeEventListners: function() {
        $(".simplePopupClose, .simplePopupBackground").off("click");                
            },

        showPopup: function() {
        $(".simplePopupBackground").css({
            "opacity": "0.7"
        });
        
                $(".simplePopupBackground").fadeIn("fast");
                
        object.fadeIn("slow", function(){
                    settings.open();
                });
                
                if(settings.centerPopup) {
                    methods.positionPopup();
                }
        },

        hidePopup: function() {
        $(".simplePopupBackground").fadeOut("fast");
        object.fadeOut("fast", function(){
                    methods.removeEventListners();
                    settings.closed();
                });
        },          

        positionPopup: function() {
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();
        var popupWidth = object.width();                
        var popupHeight = object.height();

        var topPos = (windowHeight / 2) - (popupHeight / 2);
        var leftPos = (windowWidth / 2) - (popupWidth / 2);
        if(topPos < 30) topPos = 30;
        
        object.css({
            "position": "absolute",
            "top": topPos,
            "left": leftPos
        });
        },          

    };
        
    if (methods[options]) { // $("#element").pluginName('methodName', 'arg1', 'arg2');
        return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof options === 'object' || !options) {   // $("#element").pluginName({ option: 1, option:2 });
        return methods.init.apply(this);  
    } else {
        $.error( 'Method "' +  method + '" does not exist in simple popup plugin!');
    } 
    };

})(jQuery);
</script>

<style type="text/css">
    .simplePopup {
  display: none;
  position: fixed;
  border: 4px solid #23b7e5;
  background: #23b7e5;
  z-index: 999;
  color:#fff;
  padding: 12px;
  width: 70%;
  min-width: 70%;
}

.simplePopupBackground {
  display: none;
  background: #000;
  position: fixed;
  height: 100%;
  width: 100%;
  top: 0;
  left: 0;
  z-index: 112;
}
.simplePopupClose {
  float: right;
  cursor: pointer;
/*  margin-left: 10px;
  margin-bottom: 10px;*/
  padding: 7px;
  font-size: 18px;
}
.clear{
    clear: both;
}
#start_tube, #end_tube{
    color:#000;
}

.report_pop{
   display: none;
  position: fixed;
  border: 4px solid #23b7e5;
  background: #fff;
  z-index: 999;
  
  padding: 12px;
  width: 70%;
  min-width: 70%;
}

.choice_save{
  display: none;
  position: fixed;
  border: 4px solid #23b7e5;
  background:#76b0f9;
  z-index: 9999;  
  padding: 12px;
  width: 70%;
  min-width: 70%;
  text-align: center;
}
.choice_save .simplePopupClose{
    display: none;
}
.checkedbox{
    display: none;
    color:#5d9cec !important;
    cursor: pointer;
    font-size: 12px;
}
.no-margin-right{
    margin-right: 0px !important;
    font-size: 12px;
}
</style>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src = "https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src = "https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
</head>

<div class="content-wrapper">

<input type="hidden" id="coildata_get">
<input type="hidden" id="layer_get" value="1">


    <h3>Stamping Jobs<div style="float: right;">
            <i title="print" class="fa fa-print clickhere" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Filter" ev="table.filter" class="fa fa-filter" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Save Record" onclick="saveMaster()" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Delete Record" onclick="deleteEverything()"  class="fa fa-trash" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Reset Form" ev="form.reset" form-name="table.form" class="fa fa-undo" style="font-size: 18px;padding: 0 15px;"></i>
        </div></h3>
<div id="filter" class="row clicktoid" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div>

    
    <div class="row">
        <div class="col-lg-12">

        <div form="table.form" table-name="stamping_orders_tbl">
            <form action="" method="post" >
                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Stamping Job No.</span><input type="text" class="align-right" name="job" var="job" id="job" data-hold="stamping_orders_tbl" data-colla="job">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Customer</span>
                        <select class="customer_id" var="cust_id" name="cust_id" value-field="cust_id" column="customer"></select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Part No.
                            <i title="view part information" class="fa fa-info" id="part_info" style="font-size: 16px;margin: 0px 0 0 13px;vertical-align: middle;color: #09c;cursor: pointer;"></i>
                        </span>
                        <select  class="part_id" data-tablen="part_tbl"  id="part_id" var="part" value-field="part" column="part" onchange="getPartSpec(this.value)" name="part_no"></select>
                    </div>
                    <div>
                        <div class="inline input hit_depth" >
                            <span class="top-placeholder">Rouselle</span><input type="text" mask="number" name='rouselle' var='rouselle' id='rouselle'>
                        </div>
                        <div class="inline input hit_depth" >
                            <span class="top-placeholder">Niagara</span><input type="text" mask="number" name="niagara" var='niagara' id='niagara'>
                        </div> 
                        <div class="inline input hit_depth" >
                            <span class="top-placeholder">Rouselle Alt</span><input type="text" mask="number" name='rouselleAlt' var='rouselleAlt' id='rouselleAlt'>
                        </div>
                        <div class="inline input hit_depth" >
                            <span class="top-placeholder">Niagara Alt</span><input type="text" mask="number" name="niagaraAlt" var='niagaraAlt' id='niagaraAlt'>
                        </div>                      
                    </div>
                    <div>
                        <div class="inline input blank_depth" >
                            <span class="top-placeholder">Blank</span><input type="text" mask="number" name='blank_area_input' var='blank_area' id='blank_area'>
                        </div> 
                        <div class="inline input blank_depth" >
                            <span class="top-placeholder">Blank Alt</span><input type="text" mask="number" name='blank_area_inputAlt' var='blank_areaAlt' id='blank_areaAlt'>               
                        </div>
                    </div>
                    <div>
                        <div class="inline input cycle_depth" >
                            <span class="top-placeholder">Cycles</span><input type="text" mask="number" name='cycles_input' var='cycles'>
                        </div>
                    </div>
                    <div>
                        <div class="inline input linear_depth" >
                        <span class="top-placeholder">Linear Feet</span><input type="text" mask="number" name='linear_feet_input' var='linear_feet'>
                        </div>
                    </div>
                    <div>
                        <div class="inline input">
                            <span class="top-placeholder">Strip width</span><input var="strip" name="strip_width"  id="strip" type="text" mask="double">
                        </div>
                        <div class="inline input">
                            <span class="top-placeholder">Strip width Alt.</span><input var="stripAlt" name="strip_width_alt"  id="stripAlt" type="text" mask="double">
                        </div>
                    </div>
                    <div>
                        <div class="inline input">
                            <span class="top-placeholder">Die</span><input id="die" type="text">
                        </div>
                        <div class="inline input">
                            <span class="top-placeholder">Progression</span><input id="progression" type="text" mask="number">
                        </div>
                    
                    </div>
                    <div>
                        <div class="inline input">
                            <span class="top-placeholder">Mill Job</span>
                            <select var="millJob" name="millJob" value-field="millJob"></select>
                        </div>
                        <div class="inline input">
                            <span class="top-placeholder">Cycles to allocate</span><input var="millCycles" name="millCycles"  id="millCycles" type="text" mask="double">
                        </div>
                    </div>
                    <div>
                        <div class="inline input">
                            <span class="top-placeholder">3 Test Cycles?</span><input class="testCycles" var="testCycles" name="testCycles" type="checkbox">
                        </div>
                    </div>
                    <div>
                        <div class="inline input">
                            <span class="left-placeholder">Remarks</span><textarea var="remarks" name="remarks" style="width: 554px;"></textarea>
                        </div>
                    </div>
                    <div>
                        <div class="inline input">
                            <span class="left-placeholder">Press</span>
                            <select var="press" name="press" id="press">
                                <option value="rouselle">Rouselle</option>
                                <option value="niagara">Niagara</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>


<style>
span.deletefile {
    display: inline-block;
    margin-left: 10px;
    cursor: pointer;
}
span.deletefile:hover {
    color:red
}
.downloadbtn {
    width: 180px;
    float: right;
    background: #fff;
    box-sizing: border-box;
    height: 28px;
    border-left: 1px solid #cccccc;
}
.downloadbtn:after,.clear{
    content:'';
    clear: both;
    display:table
}
.uploadbtn {    width: 180px;
    float: right;
    background: #fff;
    box-sizing: border-box;
    height: 28px;
}
.clear>span{
    width: 84px;
}
#overlay {
    position: fixed; 
    display: none;
    width: 100%; 
    height: 100%; 
    top: 0; 
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5); 
    z-index: 9999; 
    cursor: pointer;
}
.loadersmall {
    border: 5px solid #19a2cc;
    -webkit-animation: spin 1s linear infinite;
    animation: spin 1s linear infinite;
    border-top: 5px solid #fff8f8;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    position: absolute;
    left: calc(50% - 25px);
    top: calc(50% - 25px);
    }
@-webkit-keyframes spin {
  0% { 
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    transform: rotate(0deg);
  }

  100% {
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@keyframes spin {
  0% { 
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    transform: rotate(0deg);
  }

  100% {
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
div#text {
    color: #fff;
    position: absolute;
    left: 47%;
    top: 62%;
}
</style>

<script type="text/javascript">




$(".report_pop input[name='job']").val($("#job").val());
// changeData('');
$(".checkedbox").click(function(){
    $(".report_pop input[name='job']").trigger('change');
});

 $("#save_repo").click(function ()
        {



var $job_id = $('input[name="job"]').val();
var $customer= $('input[name="customer"]').val();
var $cycles= $('input[name="cycles_input"]').val();
var $linFeet= $('input[name="linear_feet_input"]').val();
var $blank= $('input[name="blank_area_input"]').val();
var $blankAlt= $('input[name="blank_area_inputAlt"]').val();
var $niagara= $('input[name="niagara"]').val();
var $niagaraAlt= $('input[name="niagaraAlt"]').val();
var $rouselle= $('input[name="rouselle"]').val();
var $rouselleAlt= $('input[name="rouselleAlt"]').val();
var $part=  $('input[name="part_no"]').val();
var $strip=$('input[name="strip_width"]').val();
var $stripAlt=$('input[name="strip_width_alt"]').val();
var $meshJob=  $('input[name="meshJob"]').val();
var $meshCycles=  $('input[name="meshCycles"]').val();
var $testCycles=  $('input[name="testCycles"]').val();
var $remarks=  $('input[name="remarks"]').val();
var $press=  $('input[name="press"]').val();


$('.choice_save').simplePopup({
                centerPopup: true,
                
});

        $("#save_for_part").click(function ()
                {

            var url = "/TPM-master/api/test.php?view=ajax_repo_update&customer="+$customer+"&po_number="+$po_number+"&quantity="+$quantity+"&date_ordered="+$date_ordered+"&date_due="+$date_due+"&line_item="+$line_item+"&mill_spec="+$mill_spec+"&repair_spec="+$repair_spec+"&ship_date="+$ship_date+"&mill_amps="+$mill_amps+"&mill_volts="+$mill_volts+"&mill_speed="+$mill_speed+"&repair_amps="+$repair_amps+"&repair_volts="+$repair_volts+"&repair_speed="+$repair_speed+"&job_id="+$job_id+"&part="+$part+"&description="+$description+"&type="+$type+"&gage="+$gage+"&holes="+$holes+"&centers="+$centers+"&pattern="+$pattern+"&od="+$od+"&finished_length="+$finished_length+"&dim_plus="+$dim_plus+"&dim_minus="+$dim_minus+"&angle="+$angle+"&cutoff_length="+$cutoff_length+"&length_plus="+$length_plus+"&length_minus="+$length_minus+"&drift="+$drift+"&mill="+$mill+"&die="+$die+"&strip="+$strip+"&ring_min="+$ring_min+"&ring_max="+$ring_max+"&started="+$started;


            $.get(url, function (data, status)
            {
                if (status == "success")
                {
                    
                   // alert(status);

                    setTimeout(function() {
                        $('.choice_save').hide();
                        $('.report_pop').hide();
                        $(".simplePopupBackground").fadeOut("fast");
                    }, 2000);

                    message('Saved', 2000);
                    EVENTS['table.list']();
                
                } 
                else
                {
                    alert("failed to save details");
                }
            });

        });//end click 

        $("#save_for_order").click(function ()
         {

            var url = "/TPM-master/api/test.php?view=ajax_only_repo_update&customer="+$customer+"&po_number="+$po_number+"&quantity="+$quantity+"&date_ordered="+$date_ordered+"&date_due="+$date_due+"&line_item="+$line_item+"&mill_spec="+$mill_spec+"&repair_spec="+$repair_spec+"&ship_date="+$ship_date+"&mill_amps="+$mill_amps+"&mill_volts="+$mill_volts+"&mill_speed="+$mill_speed+"&repair_amps="+$repair_amps+"&repair_volts="+$repair_volts+"&repair_speed="+$repair_speed+"&job_id="+$job_id+"&part="+$part+"&description="+$description+"&type="+$type+"&gage="+$gage+"&holes="+$holes+"&centers="+$centers+"&pattern="+$pattern+"&od="+$od+"&finished_length="+$finished_length+"&dim_plus="+$dim_plus+"&dim_minus="+$dim_minus+"&angle="+$angle+"&cutoff_length="+$cutoff_length+"&length_plus="+$length_plus+"&length_minus="+$length_minus+"&drift="+$drift+"&mill="+$mill+"&die="+$die+"&strip="+$strip+"&ring_min="+$ring_min+"&ring_max="+$ring_max+"&started="+$started;

            $.get(url, function (data, status)
            {
                if (status == "success")
                {
                    
                    //alert(data);

                    setTimeout(function() {
                        
                        $('.choice_save').hide();
                        $('.report_pop').hide();

                        $(".simplePopupBackground").fadeOut("fast");
                    }, 2000);

                    message('Saved', 2000);
                   

                     EVENTS['table.list']();


                    
                } 
                else
                {
                    alert("failed to save details");
                }
            });

        });//end click button1
        $('#cancel_save').click(function(){
            $('.choice_save').hide();
        });


   
});


    $(document).ready(function ()
    {

        $(".report_pop input[name='job']").val($("#job").val());

        $("#job").change(function (){
            $(".report_pop input[name='job']").val($(this).val());
            // changeData('');

        });




        $(".report_pop input[name='job']").keypress(function (e)
        {
            if (e.which == 13)
            {
                $(this).trigger("change");
            }
        });

        $("#show_pdf").click(function ()
        {
            var view = "merged_pdf";
            
            var pages = [];

            $(".report-pdf").each(function(){
                    //alert($(this).text())
                if($(this).is(':checked')){
                    pages.push($(this).data("view"));
                }
             });


            if (typeof view == "undefined" || view == "")
            {
                return false;
            }

            var job = $(".report_pop input[name='job']").val();

            var from = $("[var=from]").val();
            var to = $("[var=to]").val();
            var url = setting.server + 'test.php?view=' + view + "&job=" + job +"&pages=" + pages + "&from=" + from + "&to=" + to;

            var win = window.open(url, '_blank');
            win.focus();

            return false;
        });

        $(".report_pop input[name='job']").change(function ()
        {
            var v = $(this).val();

            var url = '/TPM-master/api/test.php?view=ajax_job_detail&job=' + v;

            $.get(url, function (data, status)
            {
                if (status == "success")
                {
                    var jsonResult;
                    try
                    {
                        jsonResult = JSON.parse(data);
                        // console.log(jsonResult);

                        var len = $.map(jsonResult, function (n, i) {
                            return i;
                        }).length;

                        if (len > 0)
                        {
                            for (var i in jsonResult)
                            {
                                $(".report_pop input[name='" + i + "']").val(jsonResult[i]);
                                $(".report_pop textarea[name='" + i + "']").val(jsonResult[i]);
                                $(".report_pop select[name='" + i + "']").val(jsonResult[i]);
                            }
                        }
                        else
                        {
                            alert("No Data found");

                            $(".report_pop input").not("[name='job']").val("");
                        }
                    } 
                    catch (e)
                    {
                        //console.log("error: " + e);
                        alert(data);
                    }
                } 
                else
                {
                    alert("failed to get details");
                }
            });
        });
        $('body').append('<div id="overlay" style="display:none"><div class="loadersmall"></div><div id="text">We are processing...</div></div>');
        $(document).on('click','.clicktoid [role="grid"] tr:not(.head)',function(){
            buttonToggle($(this).find('td').eq(1).text(),$(this).find('td').eq(3).text());
            
        });
        function buttonToggle(cust_id,part){
            console.log(cust_id+','+part);
            if(part !='' && cust_id != ''){
                $('#overlay').css('display','block');
                $.post(setting.server+"index.php?url=file_existreport&part="+part+"&cust_id="+cust_id, function(data){
                if(data.done !=''){
                        $('#download_file').css('display','block').attr('href',data.done[0]['download_reference']).attr('data-files',data.done[0]['id']);
                        $('#overlay').css('display','none');
                    }
                else{
                        $('#download_file').css('display','none');
                    }
                    $('#overlay').css('display','none');
                });//aj
            }
            else{
                $('#download_file').css('display','none');
            }
        }
    });
   
</script>
<style>
.mesh_length{
    margin-left: 23px;
}
.weight_show{
    display: inline-block;
    width: 260px;
    background:lightblue;
    padding:10px
}
.weight_show span{
    display: block;
}
.column.refresh_weight {
    display: block;
}
.inputWeightParent {
    margin-top: 7px;
    width: 388px;
}
.inputWeightParent .input-group-addon {
    width: 134px;
    float: left;
    border-radius: 0;
    padding: 9.5px;
}
.inputWeightParent .form-control{
    width:220px;
    float:left;
    border-radius: 0;
}
.inputlengthParent {
    margin-top: 7px;
    width: 388px;
}
.inputlengthParent .input-group-addon {
    width: 134px;
    float: left;
    border-radius: 0;
    padding: 9.5px;
}
.inputlengthParent .form-control{
    width:220px;
    float:left;
    border-radius: 0;
}
.tab .tab-content {
    padding-left: 0;
    padding-right: 0;
}
.info_pop{
   display: none;
  position: fixed;
  border: 4px solid #23b7e5;
  background: #fff;
  z-index: 999;
  
  padding: 12px;
  width: 70%;
  min-width: 70%;
}
</style>  



<script type="text/javascript">


function getid(){
            $.ajax({
                method:"POST",
                url:"/TPM-master/api/getid?&getidnew="+$('#job').attr('data-hold')+"&coll="+$('#job').attr('data-colla'),
                success:function(res){
                    if(res['list'].length !=0){
                        let id = parseInt(res['list'][0][$('#job').attr('data-colla')].substring(0, res['list'][0][$('#job').attr('data-colla')].indexOf('S')));
                        $('#job').val(((id+1).toString()).concat("S"));
                    }
                }
            });
        }

function getPartSpec(part_no){
    $.ajax({
        method:"POST",
        url:"/TPM-master/api/getStrip?&partnum="+part_no,
        success:function(res){
            if(res['list'].length !=0){
                let strip = res['list'][0]['strip'];
                $('#strip').val(strip);
                // $('#rouselle').val(res['list'][0]['rouselle']);
                // $('#niagara').val(res['list'][0]['niagara']);
                // $('#blank_area').val(res['list'][0]['blank_area_input']);
                $('#die').val(res['list'][0]['die_stamp']);
                $('#progression').val(res['list'][0]['progression']);
            }
        }
    });

}

function getPart(){
    $.ajax({
        method:"POST",
        url:"/TPM-master/api/getPart?&job="+$('#job').val(),
        success:function(res){
            if(res['list'].length !=0){
                let part_id = res['list'][0]['part'];
                $('#part_id').val(part_id);
                getPartSpec(part_id);
            }
        }
    });
}



</script>
<script src="assets/js/page.part-information-popup.js"></script>


          