
<?php
    if(isset($_POST['save']))
    {   

        include '../includes/db.php';
        
        // run through each ship_date item and add to db
        for($i = 0; $i < count($_POST['ship_dates']); $i++){
            
            $job = $_POST['job'];
            $cust = $_POST['cust_id'];
            $ship_date = $_POST['ship_dates'][$i];
            $quan = $_POST['ship_quan'][$i];
            
            $sql = "INSERT INTO partial_ship (job, quantity, cust_id, ship_date) VALUES('$job', '$quan', '$cust', '$ship_date')";
                
            $result = mysqli_query($db , $sql);
            echo mysqli_error($db);

            


        }
    }
?>
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
    function request1(type) {
        //alert(setting.server+ api);
            $('#coildata_get').val(type);
            var part_id = $('#part_id').val();
            var job = $('#job').val();

            $('#coil_data').html('<tr><td colspan="7">Loading....</td></tr>');

            $.post(setting.server+"index.php?url=order_list_coil&type="+type+"&part_id="+part_id+"&job="+job, function(data){
                if (data['done'] == null) {
                  $('#coil_data').html('<tr><td colspan="7">No Matching Records Found</td></tr>');  
                } else {
                 $('#coil_data').html(data['done']);
                }
                //refresh right weight
                 get_weight($('#job').val());
            })
        
   }
    $(document).ready(function() {

       request1(4);
    
    //weight
    $('.inputweight input[type="radio"]').on('change',function(){
       
        
        $(document).off('change','#coil_data input[type="checkbox"]');
        
        if($(this).hasClass('Allocated')){
            $(document).on('change','#coil_data input[type="checkbox"]',function(){
                let $this = $(this);
                $('#coil_data input[type="checkbox"]').not($this).attr('checked',false);
                if($this.is(':checked')){
                    let $this = $(this),valuehold = $this.parents('tr').find('td').eq(2).text(),coilno = $this.parents('tr').find('td').eq(1).text();
                    $('.inputWeightParent input[type="text"]').css('border-color','');
                    $('.inputWeightParent').css('display','inline-block');
                    $('.inputWeightParent input[type="text"]').val('0').attr('data-vall',valuehold).attr('data-coilno', coilno).attr('data-updateable','false');
                }else{
                    $('.inputWeightParent').css('display','none');
                }
                
            });
           
       }
       else{
            $('.inputWeightParent').hide();
            
       }
    });//change close here
   
    $('.inputWeightParent input[type="text"]').on('keyup',function(){
        $(this).css('border-color','');
        let $this = $(this);
        this.value = this.value.replace(/[^0-9\.]/g,''); 
        if(parseInt($this.val()) > parseInt($this.attr('data-vall'))){
            $(this).css('border-color','red');
            $(this).attr('data-updateable','false');
        }
        else{
            $(this).attr('data-updateable','true');
        }
        
    });//input update
    $('#Enter_as_Used').on('click',function(){
        let textf = $('.inputWeightParent input[type="text"]');
        if(textf.attr('data-updateable') == 'true'){
            $.post(setting.server+"index.php?url=Enter_as_Used&newval="+textf.val()+"&coil="+textf.attr('data-coilno')+"&oldone="+textf.attr('data-vall'), 
            function(data){
                if(data.done.query ==1){
                    let old = textf.attr('data-vall'),newone = textf.val();
                    let radio = $('#coil_data input[type="radio"]:checked');
                    radio.parents('tr').find('td').eq(2).text(old-newone);
                    let valuehold =radio.parents('tr').find('td').eq(2).text(),coilno = radio.parents('tr').find('td').eq(1).text();
                     $('.inputWeightParent input[type="text"]').val('0').attr('data-vall',valuehold).attr('data-coilno', coilno).attr('data-updateable','false');;
                     get_weight($('#job').val());
                }else if(data.done.query ==2){
                    $('#coil_data input[type="radio"]:checked').parents('tr').remove();
                    get_weight($('#job').val());
                }
                else {
                    alert("something wrong!");
                }
            })
        }
        else{
            alert('Please Fill Value it does not same or not greater than last vlaue!');
        }
    });


    })

     function request2(type)
    {

        //console.log(setting.server);
    
        $('#layer_get').val(type);
        var part_id = $('#part_id').val();
        var rdchecked = $("input[name='meshdata']:checked").val();
        var job = $('#job').val();

        $('#mesh_data'+type).html('<tr><td colspan="8">Loading....</td></tr>');

        if (rdchecked != undefined) {
          $.post(setting.server+"index.php?url=order_list_mesh&type="+type+"&part_id="+part_id+"&rdchecked="+rdchecked+"&job="+job, 
            function(data1){
              //console.log(data1);
              if (data1['done'] == null) {
                $('#mesh_data'+type).html('<tr><td colspan="9">No Matching Records Found</td></tr>');
              } else {
                $('#mesh_data'+type).html(data1['done']);
              }
             preview_length();
            })
            // $('.mesh_length input[type="radio"]').trigger( "click" );
        }
      
        

    }
    $(document).ready(function() {
       
      

       $('.mesh_length input[type="radio"]').on('click',function(){

    var id_data1 = $('#layer_get').val();
    // console.log('aaa-------->',id_data1)
                        request2(id_data1);

      
       //length
       var id_data1 = $('#layer_get').val();
         $(document).on('change','#layer_get',function(){
           var id_data1 = $(this).val();
        });
       // var selector = "#mesh_data" +id_data1+ ' input[type="'+'checkbox"'+ "]";
       var selector = '.mesh_datas input[type="checkbox"]';
       //console.log(id_data1);
        
        $(document).off('change',selector);
        
        if($(this).hasClass('Allocated')){
            $(document).on('change',selector,function(){
                let $this = $(this);
                $(selector).not($this).attr('checked',false);
                if($this.is(':checked')){
                    let $this = $(this),valuehold = $this.parents('tr').find('td').eq(5).text(),mesh_no = $this.val();
                    $('.inputlengthParent input[type="text"]').css('border-color','');
                    $('.inputlengthParent').css('display','inline-block');
                    $('.inputlengthParent input[type="text"]').val('0').attr('data-vall',valuehold).attr('data-mesh_no', mesh_no).attr('data-updateable','false');
                }else{
                    $('.inputlengthParent').css('display','none');
                }
                
            });
//event.preventDefault();
       }
       else{
            $('.inputlengthParent').hide();
            
       }
    });//change close here
   
    $('.inputlengthParent input[type="text"]').on('keyup',function(){
        $(this).css('border-color','');
        let $this = $(this);
        this.value = this.value.replace(/[^0-9\.]/g,''); 
        if(parseInt($this.val()) > parseInt($this.attr('data-vall'))){
            $(this).css('border-color','red');
            $(this).attr('data-updateable','false');
        }
        else{
            $(this).attr('data-updateable','true');
        }
        
    });//input update length



    })

       function request3(type)
    {

    if (type==1) {
         if($('.startedcls').is(":checked")){
             $("#databegan").show();
             $(".checkedbox").show();

         }else{
             $("#databegan").hide();
             $(".checkedbox").hide();
         }                    
       
    }else if (type==2) {
         if($('.finishedcls').is(":checked")){
             $("#datefinish").show();
         }else{
             $("#datefinish").hide();
         }
    } else if (type==3) {
         if($('.shippedcls').is(":checked")){
             $("#dateship").show();
         }else{
             $("#dateship").hide();
         }
    }      
       
    }


    function update_allocation(type,self)
    {
        
        if (type == 1 || type == 2 || type == 3) {
            if ($('#coil_data input[type="checkbox"]:checked').length != 0){

                let allfiled =[];   
                if($('#coil_data input[type="checkbox"]:checked').length !=0){
                    //console.log(type);
                    var job = $('#job').val();
                    $('#coil_data input[type="checkbox"]:checked').each(function(i,j){
                        allfiled[i] = $(j).val();
                    });
                  $.post(setting.server+"index.php?url=update_data&type="+type+"&id="+JSON.stringify(allfiled)+
                    "&job="+job, function(data){
                        var id_data = $('#coildata_get').val();
                        request1(id_data);
                        get_weight($('#job').val());
                    });
                }else{
                    alert('At least 1 item should be selected !');
                }
            }
        }

        if (type == 4 || type == 5) {
           
           let $this = $(self).attr('data-body');
           


                let allfiled =[];
                if($('#'+$this+' input[type="checkbox"]:checked').length !=0){
                    var job = $('#job').val();
                    $('#'+$this+' input[type="checkbox"]:checked').each(function(i,j){
                        allfiled[i] = $(j).val();
                    });

                    $.post(setting.server+"index.php?url=update_data&type="+type+"&id="+JSON.stringify(allfiled)+"&job="+job, function(data){
                        var id_data1 = $('#layer_get').val();
                        request2(id_data1);
                        preview_length();//mesh preview legth
                    });
                   
                }else{
                    alert('At least 1 item should be selected !');
                }
             
            
        }


    }
    function getRadio(id){
        $('#radio_id').val(id);
    }

    function getMeshRadio(id){
        $('#mesh_radio_id').val(id);
    }






function ReverseProg()
{
    var Alloc=document.getElementById("AllocRadio");
    Alloc.checked=false;
}
function ReverseEver()
{
    var Alloc=document.getElementById("AllocRadio");
    Alloc.checked=false;
    Alloc.disabled=true;
}

</script>
<script>
        var data, cur = 0;
        // function quoteChanged() {
        //     var quote = $('[var=quote]').val();
        //     request('table/orders_tbl/fetch', {'quote': quote})
        //     .done(function(res) {
        //         if(!quote)
        //             return;
        //         resetForm('detail.form');
        //         $('.detail').slideDown();
        //         $('#detail-quote').val(quote);
        //         data = res.list;
        //         if(!data.length) {
        //             $('#page-btn').hide();
        //             return;
        //         }
        //         fillForm('detail.form', data[0], 'var');
        //         fillForm('detail.form', data[0], 'val');
        //         cur = 0;

        //         $('#page-btn').show();
        //         $('#first-page').show();
        //         $('#prev-page').show();
        //         $('#next-page').show();
        //         $('#last-page').show();

        //         $('#page-no').html(cur +1);

        //         $('#first-page').hide();
        //         $('#prev-page').hide();

        //         if(cur == data.length - 1) {
        //             $('#next-page').hide();
        //             $('#last-page').hide();
        //         }
        //     });

        // }

        // function show(page) {
        //     cur = page;
        //     resetForm('detail.form');
        //     fillForm('detail.form', data[cur], 'var');
        //     fillForm('detail.form', data[cur], 'val');
            
        //     $('#first-page').show();
        //     $('#prev-page').show();
        //     $('#next-page').show();
        //     $('#last-page').show();

        //     $('#page-no').html(cur +1);

        //     if(cur == 0) {
        //         $('#first-page').hide();
        //         $('#prev-page').hide();
        //     }
        //     if(cur == data.length - 1) {
        //         $('#next-page').hide();
        //         $('#last-page').hide();
        //     }
        // }

        function deleteDetail() {

            var form_data = parseForm('detail.form'),
                msg  = message('Deleting...');

            request('table/orders_tbl/delete', {id: form_data.id})
            .always(function() {
                msg.remove()
            })
            .done(function(res) {
                message('Deleted', 2000);
                //quoteChanged();
            })
        }

        function resetDetail() {
            resetForm('detail.form');
            var quote = $('[var=quote]').val();
            $('#detail-quote').val(quote);
        }

        function saveDetail() {

            // validate input
            var invalid = $('[form="detail.form"] [mask].invalid').filter(':first')

            if ( invalid.length )
                return message(invalid.attr('field'), 2000)

            var form_data = parseForm('detail.form'),
                msg  = message('Saving...');

            request('table/orders_tbl/save', form_data)
                .always(function(){
                    msg.remove()
                })
                .done(function(res){
                    message('Saved', 2000);
                    //quoteChanged();
                })
                
        }

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
                // curret.find('td:eq(2)').text(data.po);
                curret.find('td:eq(3)').text(data.part).attr('title',data.part);
                curret.find('td:eq(4)').text(data.quantity).attr('title',data.quantity);
                curret.find('td:eq(5)').text(data.ordered).attr('title',data.ordered);
                curret.find('td:eq(6)').text(data.due).attr('title',data.due);
                curret.find('td:eq(7)').text(data.has_started).attr('title',data.has_started);
                curret.find('td:eq(8)').text(data.began).attr('title',data.began);
                curret.find('td:eq(9)').text(data.has_finished).attr('title',data.has_finished);
                curret.find('td:eq(10)').text(data.finished).attr('title',data.finished);
                curret.find('td:eq(11)').text(data.has_shipped).attr('title',data.has_shipped);
                curret.find('td:eq(12)').text(data.shipped).attr('title',data.shipped);
                curret.find('td:eq(14)').text(data.item).attr('title',data.item);
                curret.find('td:eq(23)').text(data.inspector).attr('title',data.inspector);
                curret.find('td:eq(20)').text(data.mill_operator).attr('title',data.mill_operator);
                curret.find('td:eq(21)').text(data.cutoff_operator).attr('title',data.cutoff_operator);
                curret.find('td:eq(24)').text(data.weld_spec_mill).attr('title',data.weld_spec_mill);
                curret.find('td:eq(25)').text(data.weld_spec_repair).attr('title',data.weld_spec_repair);
                curret.find('td:eq(26)').text(data.ship_date).attr('title',data.ship_date);
                curret.find('td:eq(33)').text(data.cont_type).attr('title',data.cont_type);
                curret.find('td:eq(33)').text(data.ship_method).attr('title',data.ship_method);
                curret.find('td:eq(22)').text(data.repair_welder).attr('title',data.repair_welder);





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
            request('table/orders_tbl/delete', {'job': job})
            .always(function() {
                msg.remove()
            })
            // .done(function(res) {
            //     message('Deleted', 2000);
            //     var data = parseForm('table.form'),
            //         msg  = message('Deleting...'),
            //         tbl  = $('[form="table.form"]').attr('table-name')

            //     request('table/'+ tbl +'/delete', data)
            //         .always(function(){
            //             msg.remove()
            //         })
                     .done(function(response){

                        message('Deleted', 2000)

                        resetForm('table.form');
                        EVENTS['table.list']();
                        //setTimeout(function() {
                          //  $('.detail').hide();
                        //}, 1000);

                    })
            //})
        }
        var calculation = [];
        $(document).ready(function() {


            // $(document).on('click','.Enter_asUsedmesh',function(){

            //     Enter_as_Used_mesh($(this));
            //     console.log($(this));
            // });
$('.Enter_asUsedmesh').on('click',function(){
        let textf = $('.inputlengthParent input[type="text"]');
        if(textf.attr('data-updateable') == 'true'){
            $.post(setting.server+"index.php?url=Enter_as_Used_mesh&newval="+textf.val()+"&mesh_n="+textf.attr('data-mesh_no')+"&oldone="+textf.attr('data-vall'), 
            function(data){
                if(data.done.query ==1){
                    let old = textf.attr('data-vall'),newone = textf.val();
                    let radio = $('.mesh_datas input[type="radio"]:checked');
                    radio.parents('tr').find('td').eq(5).text(old-newone);
                    let valuehold =radio.parents('tr').find('td').eq(5).text(),mesh_no = radio.parents('tr').find('td').eq(0).val();
                     $('.inputlengthParent input[type="text"]').val('0').attr('data-vall',valuehold).attr('data-mesh_no', mesh_no).attr('data-updateable','false');;
                     preview_length();
                }else if(data.done.query ==2){
                    $('.mesh_datas input[type="radio"]:checked').parents('tr').remove();
                    preview_length();
                }
                else {
                    alert("something wrong!");
                }
            })
        }
        else{
            alert('Please Fill Value it does not same or not greater than last vlaue!');
        }
    });
            preview_length();//mesh weight preview




            $('.detail').hide();
            // part detail on change
            // $('#part_id').on('click',function(){
            //     part_update($(this).val());
            // });

            
        getid();

        $(document).on('click','[ev="form.reset"]',function(){
            getid();
        });

        //calculation based on quantity 
            $('.quantity').on('keyup',function(){
                let $thisval = $(this).val();
                if (calculation.length !=0){
                    $.each(calculation,function(i,j){
                        $('.'+i).val((parseFloat(j.toFixed(3))*parseFloat($thisval)).toFixed(3));
                    });
                }
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
                get_weight($('#job').val());
                preview_length();
                part_update($('#part_id').val());
                // if(e==1){
                    if(response.list.length !=0){
                        onPartNoChange(response.list[saved_i].part);
                    }
                // }
                
            });
           
        // }
    }   
         //weight label
         function get_weight(job){
            let jobid = job ;
            jobid = parseInt(jobid);
            $.post(setting.server+"index.php?url=weight_show&job="+jobid, function(data){
                $('#allocated b').text(data.done.tw);
                $('#already b').text(data.done.used);
                $('#dedicated b').text((parseInt(data.done.tw) + parseInt(data.done.used)));
            });
          }
          function part_update(partno){
            let partn = partno;
            if(partn != ''){
                $.post(setting.server+"index.php?url=part_show&partn="+partn, function(data){
                    $(".resetRadio input[type='radio']").prop('checked',false);
                    $('#coil_data').empty();
                    // $('#coil_data').html(data.done);
                });
            }
          }
          //partupdate end
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

    function onPriceChange(price) {
        var revenue = $('.quantity').val() * price;
        $('.PO_total').prop('value', revenue);
    }

    function onQuantityChange(quantity) {
        var revenue = $('.price').val() * quantity;
        $('.PO_total').prop('value', revenue);
        $("[name=quantity]").val(quantity);
        $("[var=from]").attr("max", quantity);
        $("[var=to]").attr("max", quantity);
        $("[var=to]").val(quantity);
    }

    // function Enter_as_Used_mesh(self){
    //     let textf = $('.inputlengthParent input[type="text"]');
    //     let tbody  = self.attr('data-body');
    //     let allcheckbox  = $('#'+tbody).find('input[type="checkbox"]:checked');
    //     let meshlist =[];
    //     if (allcheckbox.length !=0){
    //         allcheckbox.each(function(i,j){
    //             meshlist[i] = $(j).val();
    //         });
    //         $.post(setting.server+"index.php?url=Enter_as_Used_mesh&jobno="+$('#job').val()+"&meshlist="+JSON.stringify(meshlist),        
    //         function(response){
    //             preview_length();//mesh preview legth
    //             if (response.done.length !=0){
    //                 if(response.done['INSERT']==true){
    //                     $('#'+tbody+' input[type="checkbox"]').attr('checked',false);
    //                 }
    //             }
    //         });
    //     }else{
    //         alert('Select atleast one field !');

    //     }
    // }


    function preview_length(){
        $.post(setting.server+"index.php?url=meshtotal_show&jobno="+$('#job').val(),        
            function(r){
                if (r.done.length !=0){
                    $('#allocatedmesh b').text(r.done.ML);
                    $('#alreadymesh b').text(r.done.EL);
                    $('#dedicatedmesh b').text(parseInt(r.done.ML) + parseInt(r.done.EL));
            }
        });
    }
function validateForPdf()
      {
        var job_value = parseFloat($("#job").val());
        var quantity = parseFloat($('input[var="quantity"]').val());
        //var no_of_items = parseFloat($("#no_of_items").val());
        var start_tube = parseFloat($('#start_tube').val());
        var end_tube = parseFloat($('#end_tube').val());
        if(!job_value || !quantity){
            $(".msg").html('<span>Please fill the Job/Quantity fields</span>');
            return false;
        }
        if(start_tube >= quantity || !start_tube){
            $(".msg").html('<span>start tube value is not valid</span>');
            return false;
        }
        if (end_tube > quantity || !end_tube || end_tube < start_tube){
            $(".msg").html('<span>end tube value is not valid</span>');
            return false;
        }
              var url = "/order_dev.php?job_number="+job_value+"&start="+start_tube+"&end="+end_tube+"&quantity="+quantity;
          var win = window.open(url, '_blank');
          win.focus();
            $(".simplePopupBackground").hide();
            $(".simplePopup").hide();
        


        // if(job_value == "")
        // {
        //   return false;
        // }
        // else
        // {
        //   var url = "/order_dev.php??coil_number="+job_value+"&no_of_items="+no_of_items;
        //   var win = window.open(url, '_blank');
        //   win.focus();
        // }
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
<div id="demo" class="simplePopup">
            <div class="clear"></div>
           
            <div class="input">
                    <span class="left-placeholder">Start tube</span><input id="start_tube" type="number" class="align-right">
            </div>
            <div class="input">
                    <span class="left-placeholder">End tube</span><input type="number" id="end_tube" class="align-right">
            </div>
             <div class="msg"></div>
            <div class="button" onclick="validateForPdf()">Okay</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
    $('.startedcls').click(function () {
        if ($(this).is(':checked')) {
            $(".report_pop input[name='job']").val($("#job").val());
            var jobno  = $('#job').val();
                if(jobno !=''){
                    $('#overlay').show();

                    //changeData(jobno);

                    //var v = $(this).val();

            var url = '/TPM-master/api/test.php?view=ajax_job_detail&job=' + jobno;

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
                        // console.log("error: " + e);
                        alert(data);
                    }
                } 
                else
                {
                    alert("failed to get details");
                }
            });





                    // $.post(setting.server+"index.php?url=orderspecpartspec"+"&jobid="+jobno,
                    // function(res){
                    //     if(res.done.length !=0){               
                    //     var d = res.done[0];
                    //     console.log(d);
                    //     $('input[name="customer"]').val(d.ocustomer);
                    //     $('input[name="po_number"]').val(d.opo);
                    //     $('input[name="quantity"]').val(d.oquantity);
                    //     $('input[name="date_ordered"]').val(d.odate);
                    //     $('input[name="date_due"]').val(d.odue);
                    //     $('input[name="line_item"]').val();
                    //     $('select[name="mill_spec"]').val(d.oweld_spec_mill);
                    //     $('select[name="repair_spec"]').val(d.oweld_spec_repair);
                    //     $('input[name="ship_date"]').val(d.oship_date);
                    //     $('input[name="mill_amps"]').val(d.omil_amps);
                    //     $('input[name="mill_volts"]').val(d.omil_volts);
                    //     $('input[name="mill_speed"]').val(d.omil_speed);
                    //     $('input[name="repair_amps"]').val(d.orepair_amps);
                    //     $('input[name="repair_volts"]').val(d.orepair_volts);
                    //     $('input[name="repair_speed"]').val(d.orepair_speed);
                    //     $('input[name="part_no"]').val(d.opart);
                    //     $('textarea[name="part_desc"]').val(d.pdescription); 
                    //     $('select[name="material"]').val(d.pmaterial);
                    //     $('select[name="gage"]').val(d.pgage); 
                    //     $('input[name="hole_size"]').val(d.pholes);
                    //     $('input[name="hole_centers"]').val(d.pcenters);
                    //     $('select[name="pattern"]').val(d.ppattern);
                    //     $('input[name="od"]').val(d.pod);
                    //     $('input[name="finished_length"]').val(d.pfinished_length);
                    //     $('input[name="od_postive"]').val(d.pdim_plus);
                    //     $('input[name="od_negtive"]').val(d.pdim_minus);
                    //     $('input[name="angle"]').val(d.pangle);
                    //     $('input[name="cutoff_length"]').val(d.pcutoff_length);
                    //     $('input[name="finished_length_postive"]').val(d.plength_minus);
                    //     $('input[name="finished_length_negtive"]').val(d.plength_plus);
                    //     $('input[name="id_drift"]').val(d.pdrift);
                    //     $('input[name="mill"]').val(d.pmill);
                    //     $('input[name="die"]').val(d.pdie);
                    //     $('input[name="strip_width"]').val(d.pstrip);
                    //     $('input[name="ring_min"]').val(d.pring_min);
                    //     $('input[name="ring_max"]').val(d.pring_max);
                        $('.report_pop').simplePopup({
                            centerPopup: true,
                            // closed: function () {
                            //     $('.startedcls').prop('checked', false);
                            //     $(".checkedbox").hide();
                            // }
                        }); 
                        $('#overlay').hide();
                        //}else{
                          //  alert('No record found!');
                           
                        //$(".checkedbox").hide();
                        //$('#overlay').hide();
                        //}
                    //});


                }else{
                    alert('enter job number !');
                }
        } 
    });
$('.checkedbox').click(function () {
        
            $('.report_pop').simplePopup({
                centerPopup: true,
                // closed: function () {
                //     $('.startedcls').prop('checked', false);
                // }
            });
        
    });

    $('.clickhere').click(function(){
    $('#demo').simplePopup(
//     {

// // always center the popup
// centerPopup: true,

// // callbacks
// open: function() {},
// closed: function() {}

// }
 );
    });

   
});

</script>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src = "https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src = "https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
</head>

<div class="content-wrapper">

<input type="hidden" id="coildata_get">
<input type="hidden" id="layer_get" value="1">


    <h3>All Orders<div style="float: right;">
            <i title="print" class="fa fa-print clickhere" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Filter" ev="table.filter" class="fa fa-filter" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Save Record" onclick="saveMaster()" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Delete Record" onclick="deleteEverything()"  class="fa fa-trash" style="font-size: 18px;padding: 0 15px;"></i>
            <i title="Reset Form" ev="form.reset" form-name="table.form" class="fa fa-undo" style="font-size: 18px;padding: 0 15px;"></i>
        </div></h3>
<div id="filter" class="row clicktoid" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div>

<div id="page-btn">
                    <i id="prev-page" class="button fa fa-angle-left"></i>
                    <span id="page-no" class="button"></span>
                    <i id="next-page" class="button fa fa-angle-right"></i>
                </div>
    
    <div class="row">
        <div class="col-lg-12">

        <div form="table.form" table-name="orders_tbl">
            <form action="" method="post" >
                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Job No.</span><input type="text" class="align-right" name="job" var="job" id="job" data-hold="orders_tbl" data-colla="job">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Customer</span>
                        <select class="customer_id" var="cust_id" name="cust_id" value-field="cust_id" column="customer">
                    
                        </select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">PO Number</span><input type="text" class="align-right" var="po" value-field="po">
                    </div>
                    <div class="input">
                
                        <span class="left-placeholder">Part No.
                            <i title="view part information" class="fa fa-info" id="part_info" style="font-size: 16px;margin: 0px 0 0 13px;vertical-align: middle;color: #09c;cursor: pointer;"></i></span>
                    
                            <select  class="part_id" data-filtercolumn="cust_id" data-tablen="part_tbl"  id="part_id" var="part" value-field="part" column="part" onchange="onPartNoChange(this.value)" name="part_no"></select>



                    </div>
                    <div class="input">
                        <span class="left-placeholder">Quantity</span><input type="text" class="quantity align-right" var="quantity" onchange="onQuantityChange(this.value)">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Date Ordered</span><input type="datepicker" class="align-right" var="ordered">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Date Due</span><input type="datepicker" class="align-right" var="due">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Shipping Date</span><input type="datepicker" class="align-right" var="ship_date">
                    </div>
                </div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">LF mat</span><input class="lf_req align-right" type="number" placeholder="0.000" step="0.001">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Weight (pre)</span><input class="wbs align-right" type="number" placeholder="0.000" step="0.001">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Weight (post)</span><input class="was align-right" type="number" placeholder="0.000" step="0.001">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Total tube (ft)</span><input class="tf align-right" type="number" placeholder="0.000" step="0.001">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Price</span><input class="price align-right" type="number" placeholder="0.00" step="0.01" onchange="onPriceChange(this.value)">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Revenue total</span><input class="PO_total align-right" type="number" placeholder="0.00" step="0.01">
                    </div>
                </div>

                <div class="column">
                        <div class="input">
                                <span class="left-placeholder">Mill</span>
                                <select id="mac_add_tbl" var="device" table="mac_add_tbl" value-field="device" column="device">
                                    <option value="" selected>Select Mill</option>
                            </select>
                            </div>
                    <div class="input">
                        <span class="left-placeholder">Mill Operator</span>
                        <select var="mill_operator" table="employee"   value-field="ID" column="name" filter_column="mill_operator" filter_value="1">
                        </select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Cut Off Operator</span>
                        <select var="cutoff_operator" table="employee"   value-field="ID" column="name" filter_column="cutoff_operator" filter_value="1">
                        </select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Repair Welder</span>
                        <select var="repair_welder" table="employee"   value-field="ID" column="name" filter_column="repair_welder" filter_value="1">
                        </select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Inspector</span>
                        <select var="inspector" table="employee"   value-field="ID" column="name" filter_column="inspector" filter_value="1">
                        </select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Container Type</span>
                        <select var="cont_type" table="cont"   value-field="ID" column="cont_type">
                        </select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Shipping Method</span>
                        <select var="ship_method" table="ship_method"   value-field="ID" column="ship_method">
                        </select>
                    </div>
                </div>

                
                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Mill Weld Spec</span>
                        <select var="weld_spec_mill" table="weld_spec_mill"   value-field="weld_spec" column="weld_spec">
                        </select>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Repair Weld Spec</span>
                        <select var="weld_spec_repair" table="weld_spec_repair"   value-field="weld_spec" column="weld_spec">
                        </select>
                    </div>
                </div>

                
                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Ship Date</span><input name="ship_dates[]" class="align-right" type="date">
                    </div>
                    
                    <div class="input">
                        <span class="left-placeholder">Quantity</span><input name="ship_quan[]" class="align-right" type="number">
                    </div>    
                </div>
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
            </form>
            

<?php $current_date= date('Y/m/d'); ?>
            <div class="column">
                <div class="input" id="databegan" style="display:none;">
                    <span class="left-placeholder">Date Began</span>
                  
                    <input type="datepicker" class="align-right" var="began" value="<?php echo  $current_date; ?>">
                </div>
                <div class="input" id="datefinish" style="display:none;">
                    <span class="left-placeholder">Date Finished</span>
                    
                    <input type="datepicker" class="align-right" value="<?php echo  $current_date; ?>" var="finished">
                </div>
                <div class="input" id="dateship" style="display:none;">
                    <span class="left-placeholder">Date Shipped</span>
                  
                    <input type="datepicker" class="align-right"  value="<?php echo  $current_date; ?>" var="shipped">
                </div>

                <div class="button" style="display:none;">Advance Job</div>
            </div>
            <div class="column">
                
                <a title="Open order info" class="checkedbox left-placeholder">Show Order Info.</a>
               
            </div>
            
        </div>

        <div class="inline resetRadio inputweight">

            

            <br>
             <input type="radio" name="coildata" value="1" onclick="request1(1)" class="Allocated">Allocated
            <input type="radio" name="coildata" value="2" onclick="request1(2)" class="allocateable">All material widths
            <input type="radio" name="coildata" value="3" onclick="request1(3)" class="allocateable">Part specific widths
             <input type="hidden" id="radio_id">
            <br>
            <div class="inline">
                <div class="inline overflow" style="font-size: 80%; border: 1px solid #ccc; height: 150px; background: #fff; width: 390px;">
                    <table class="table row-border stripe display compact">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Coil</th>
                                <th>Weight</th>
                                <th>Width</th>
                                <th>Worker#</th>
                                <th>Heat#</th>
                                <th>Job</th>
                                <th>Allocated</th>
                            </tr>
                           
                        </thead>
                         <tbody id="coil_data">
                        </tbody>
                    </table>
                </div>

                <div class="weight_show">
                    <span class="weight_Allocated"  id="allocated">Weight Allocated: <b>..</b></span>
                    <span class="weight_Allocated" id="already">Weight already used: <b>..</b></span>
                    <span class="weight_Allocated"  id="dedicated">Total Weight dedicated to job:<b>..</b></span>
                    </div>

                <div class="column refresh_weight">
                    <div class="button" onclick="update_allocation(1,this)" id="allocat_sub">Allocate</div>
                    <div class="button" onclick="update_allocation(2,this)">Deallocate</div>
                    <div class="button"  id ="Enter_as_Used">Enter as Used</div>
                    <div class="inputWeightParent" style="display:none">
                        <div class="input-group-addon" >Enter used weight</div>
                        <input type="text" class="form-control" placeholder="Update Weight" data-updateable="false">
                    </div>
                </div>
                <!-- </form> -->
                            
            </div>


            <br>

        </div>
        
         <br>
        <br>
        <div class="mesh_length">
         <input type="radio" name="meshdata" value="1" class="Allocated" >Allocated
            <input type="radio" name="meshdata" value="2" >All Mesh
            <input type="radio" name="meshdata" value="3"  >Part Specific Mesh
             </div>
        <div class="inline tab" style="margin-left: 23px; width: 500px; font-size: 80%;">

             
<input type="hidden" id="mesh_radio_id">
            <div class="tab-list">
                <div class="tab-list-item active" onclick="request2(1)" >Layer 1 Mesh</div>
                <div class="tab-list-item" onclick="request2(2)" >Layer 2 Mesh</div>
                <div class="tab-list-item" onclick="request2(3)">Drainage 1 Mesh</div>
                <div class="tab-list-item" onclick="request2(4)">Drainage 2 Mesh</div>
            </div>
            <div class="tab-content active" style="padding: 0px;">
                <div class="overflow" style="height: 300px;">
                    <table class="table row-border stripe display compact">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Mesh Type</th>
                                <th>Type</th>
                                <th>Recieved</th>
                                <th>Width</th>
                                <th>Length</th>
                                <th>Allocated</th>
                                <th>TPM JOB</th>
                                <th>Heat#</th>
                            </tr>
                        </thead>
                        <tbody id="mesh_data1" class="mesh_datas">
                        </tbody>
                    </table>
                </div>
                <div class="button" onclick="update_allocation(4,this)" data-body="mesh_data1">Allocate</div>
                <div class="button" onclick="update_allocation(5,this)" data-body="mesh_data1">Deallocate</div>
                <div class="button Enter_asUsedmesh" data-body="mesh_data1">Enter as Used</div>
                
            </div>
            <div class="tab-content">
               <div class="overflow" style="height: 300px;">
                    <table class="table row-border stripe display compact">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Mesh Type</th>
                                <th>Type</th>
                                <th>Recieved</th>
                                <th>Width</th>
                                <th>Length</th>
                                <th>Allocated</th>
                                <th>TPM JOB</th>
                                <th>Heat#</th>
                            </tr>
                        </thead>
                        <tbody id="mesh_data2" class="mesh_datas">
                        </tbody>
                    </table>
                </div>
                <div class="button" onclick="update_allocation(4,this)" data-body="mesh_data2">Allocate</div>
                <div class="button" onclick="update_allocation(5,this)" data-body="mesh_data2">Deallocate</div>
                <div class="button Enter_asUsedmesh" data-body="mesh_data2">Enter as Used</div>
                
            </div>
            <div class="tab-content">
                <div class="overflow" style="height: 300px;">
                    <table class="table row-border stripe display compact">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Mesh Type</th>
                                <th>Type</th>
                                <th>Recieved</th>
                                <th>Width</th>
                                <th>Length</th>
                                <th>Allocated</th>
                                <th>TPM JOB</th>
                                <th>Heat#</th>
                            </tr>
                        </thead>
                        <tbody id="mesh_data3" class="mesh_datas">
                        </tbody>
                    </table>
                </div>
                <div class="button" onclick="update_allocation(4,this)" data-body="mesh_data3">Allocate</div>
                <div class="button" onclick="update_allocation(5,this)" data-body="mesh_data3">Deallocate</div>
                <div class="button Enter_asUsedmesh"  data-body="mesh_data3">Enter as Used</div>
                
            </div>
            <div class="tab-content">
                <div class="overflow" style="height: 300px;">
                    <table class="table row-border stripe display compact">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Mesh Type</th>
                                <th>Type</th>
                                <th>Recieved</th>
                                <th>Width</th>
                                <th>Length</th>
                                <th>Allocated</th>
                                <th>TPM JOB</th>
                                <th>Heat#</th>
                            </tr>
                        </thead>
                        <tbody id="mesh_data4" class="mesh_datas">
                        </tbody>
                    </table>
                </div>
                <div class="button" onclick="update_allocation(4,this)" data-body="mesh_data4">Allocate</div>
                <div class="button" onclick="update_allocation(5,this)" data-body="mesh_data4">Deallocate</div>
                <div class="button Enter_asUsedmesh"  data-body="mesh_data4">Enter as Used</div>
                
            </div>
            <div class="inputlengthParent" style="display:none">
                        <div class="input-group-addon" >Enter used length</div>
                        <input type="text" class="form-control" placeholder="Update length" data-updateable="false">
            </div>
        </div>
        <div class="weight_show">
            <span class="weight_Allocated" id="allocatedmesh">Length Allocated: <b>0</b></span>
            <span class="weight_Allocated" id="alreadymesh">Length already used: <b>0</b></span>
            <span class="weight_Allocated" id="dedicatedmesh">Total Length dedicated to job:<b>0</b></span>
        </div>

        </div>
    </div>
</div>
<!-- Pop up code reports -->

<div class="content-wrapper report_pop">
    <h3>Order setup information
         <div style="float: right;">
             <i title="Save Record" id="save_repo" class="fa fa-save" style="font-size: 18px;padding: 0 15px;"></i>
<!--             <i title="Filter" ev="table.filter" class="fa fa-filter" style="font-size: 18px;padding: 0 15px;"></i>
            <i class="fa fa-print" style="font-size: 18px;padding: 0 15px;" onclick="window.print();"></i> -->
        </div> 
    </h3>
    
   
    
    <!-- <div id="filter" class="row clicktoid" style="display: none; padding: 20px; background: #fff; border: 1px solid #eee;"></div> -->
    
    <div form="table.form2" table-name="order_specs" style="margin: 20px 0;">
        <div class="col-lg-12">
            <div class="section">
                <div class="section-title">Part Specification</div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Job No.</span>
                        <input var="job" id="job_rep" onChange="changeData(this.value);" type="text" name="job" >
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Quantity</span>
                        <input type="text" name="quantity">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Material</span>
                        <select name="material" table="mat_tbl" column="material"></select>
                        <!-- <input type="text" var="material" name="material" /> -->
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Gage</span>
                        <select name="gage" table="gage_tbl" column="gage" ></select>
                        <!-- <input type="text" var="gage" name="gage" /> -->
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Pattern</span>
                        <select  table="pat_tbl" column="pattern" name="pattern"></select>
                        <!-- <input type="text" var="pattern" name="pattern" /> -->
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Hole Size</span>
                        <input type="text" name="hole_size" />
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Hole centers</span>
                        <input type="text" name="hole_centers" />
                    </div>

                    <div class="inline input">
                        <div class="input">
                            <span class="left-placeholder">OD</span>
                            <input type="text" name="od" style="width: 128px; border-width: 0 0 0 1px;" disabled>
                        </div>
                    </div>

                    <div class="inline input" style="margin-bottom: 10px;">
                        <div class="input">
                            <span class="left-placeholder">+</span>
                            <input type="text" name="od_postive" style="border-width: 0 0 0 1px; width: 60px;">
                        </div>
                        <div class="input">
                            <span class="left-placeholder">-</span>
                            <input type="text" name="od_negtive" style="border-width: 0 0 0 1px; width: 60px">
                        </div>
                    </div>

                    <div class="input clear">
                        <span class="left-placeholder">Drawing</span>
                        <div class="downloadbtn">
                            <a style="display:none" href="download.pdf" id="download_file" target="_blank">Download File</a>
                        </div>
                    </div>
            
                </div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Customer</span>
                        <input type="text"  name="customer" disabled>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">PO Number</span>
                        <input type="text"  name="po_number">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Line item</span>
                        <input type="text"  name="line_item">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Part No.</span>
                        <input type="text"  name="part_no" disabled>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Part Desc.</span>
                        <textarea  name="part_desc" disabled></textarea>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Date ordered</span>
                        <input type="datepicker"  name="date_ordered">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Date Due</span>
                        <input type="datepicker"  name="date_due">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Ship Date</span>
                        <input type="datepicker"  name="ship_date">
                    </div>
                    <div class="inline input">
                        <div class="input">
                            <span class="left-placeholder">Finished length</span>
                            <input type="text"  name="finished_length" style="width: 70px; border-width: 0 0 0 1px;">
                        </div>
                    </div>

                    <div class="inline input">
                        <div class="input">
                            <span class="left-placeholder">+</span>
                            <input type="text"  name="finished_length_postive" style="border-width: 0 0 0 1px; width: 60px;">
                        </div>
                        <div class="input">
                            <span class="left-placeholder">-</span>
                            <input type="text"  name="finished_length_negtive" style="border-width: 0 0 0 1px; width: 60px;">
                        </div>
                    </div>
                </div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Mill Spec</span>
                        <select  table="weld_spec_mill"   value-field="weld_spec" column="weld_spec" name="mill_spec">
                    </select>
                        <!-- <input type="text" var="mill_spec" name="mill_spec" /> -->
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Repair Spec</span>
                        <select  table="weld_spec_repair"   value-field="weld_spec" column="weld_spec" name="repair_spec">
                    </select>
                        <!-- <input type="text" var="repair_spec" name="repair_spec" /> -->
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Mfg. Specification</div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Strip width</span>
                        <input type="text" name="strip_width">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Angle</span>
                        <input type="text" name="angle">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Cutoff length</span>
                        <input type="text" name="cutoff_length">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Die</span>
                        <select class="align-right die1" name="die" table="die_tbl" value-field="die" column="die_id"></select>
                    </div>
                </div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Mill</span>
                        <input type="text" name="mill" />
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Ring Min</span>
                        <input type="text" name="ring_min">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Ring Max</span>
                        <input type="text" name="ring_max">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">ID drift</span>
                        <input type="text" name="id_drift">
                    </div>
                </div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Mill amps</span>
                        <input type="text" name="mill_amps" disabled>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Mill volts</span>
                        <input type="text" name="mill_volts" disabled>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Mill speed</span>
                        <input type="text" name="mill_speed" disabled>
                    </div>
                </div>

                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Repair amps</span>
                        <input type="text" var="repair_amps" name="repair_amps" disabled>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Repair volts</span>
                        <input type="text" name="repair_volts" disabled>
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Repair speed</span>
                        <input type="text" name="repair_speed" disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="section">
                <div class="section-title">Report Options</div>

                <div class="column no-margin-right">
                    <div class="input">
                        <span class="left-placeholder" >Mill Log</span>
                        <input type="checkbox" data-view="tube_mill_log" class="report-pdf">
                    </div>

                    <div class="input">
                        <span class="left-placeholder">Mill Setup</span>
                        <input type="checkbox" data-view="tube_mill_setup" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Welding Sheets</span>
                        <input type="checkbox" data-view="welding_station_check_list" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Worksheet</span>
                        <input type="checkbox" data-view="worksheet" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Direct Pack</span>
                        <input type="checkbox" data-view="dp_inspection" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">First Part Drift Confirmation</span>
                        <input type="checkbox" data-view="first_part_drift_confirmation" class="report-pdf">
                    </div>

                </div>
                
                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">Cutoff Sheets</span>
                        <input type="checkbox" data-view="cutoff_station_check_sheet" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Inspection Sheets</span>
                        <input type="checkbox" data-view="inspection_rpt" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Allocation</span>
                        <input type="checkbox" data-view="alloc" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Allocation Mesh</span>
                        <input type="checkbox" data-view="alloc_mesh" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Ring Inspection</span>
                        <input type="checkbox" data-view="ring_station_check_list" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">GeoForm Ring Inspection</span>
                        <input type="checkbox" data-view="geo_form_ring_inspection" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">GeoForm Ring Concentric Inspection</span>
                        <input type="checkbox" data-view="geo_form_ring_concentric_inspection" class="report-pdf">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">Final Inspection Geo Form</span>
                        <input type="checkbox" data-view="final_inspection_geo_form" class="report-pdf">
                    </div>

                     
                    
                    
                    
                    <!--
                    <div class="input no-border">
                        <a href="#" class="report-pdf">Drawing</a>
                    </div>-->
                </div>             


                <div class="column">
                    <div class="input">
                        <span class="left-placeholder">From</span>
                        <input type="number" min="1" max="10" class="align-right" value="1" var="from">
                    </div>
                    <div class="input">
                        <span class="left-placeholder">To</span>
                        <input type="number" min="1" max="10" class="align-right" value="10" var="to">
                    </div>

                    <div class="section" style="margin: 54px;">
                        <div class="section-title">Print Options</div>
                        <div class="input no-border">
                            <input type="checkbox"><span class="left-placeholder">Print</span>
                        </div>
                        <div class="input no-border">
                            <input type="checkbox"><span class="left-placeholder">Preview</span>
                        </div>
                        <button id="show_pdf">View Reports</button>
                    </div>
                </div>       

            </div>
        </div>
    </div>
</div>


<div class="choice_save">
     <button id="save_for_order">Save Status for this job only</button>
     <button id="save_for_part">Save for the Part Only</button>
     <button id="cancel_save">Cancel</button>
</div>


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
var $po_number= $('input[name="po_number"]').val();
var $quantity= $('input[name="quantity"]').val();
var $date_ordered= $('input[name="date_ordered"]').val();
var $date_due= $('input[name="date_due"]').val();
var $line_item= $('input[name="line_item"]').val();
var $mill_spec= $('select[name="mill_spec"]').val();
var $repair_spec= $('select[name="repair_spec"]').val();
var $ship_date= $('input[name="ship_date"]').val();
var $mill_amps= $('input[name="mill_amps"]').val();
var $mill_volts= $('input[name="mill_volts"]').val();
var $mill_speed= $('input[name="mill_speed"]').val();
var $repair_amps= $('input[name="repair_amps"]').val();
var $repair_volts= $('input[name="repair_volts"]').val();
var $repair_speed= $('input[name="repair_speed"]').val();
var $started= $('input[var="has_started"]').val();

var $part=  $('input[name="part_no"]').val();
var $description=$('textarea[name="part_desc"]').val(); 
var $type=$('select[name="material"]').val();
var $gage=$('select[name="gage"]').val(); 
var $holes=$('input[name="hole_size"]').val();
var $centers=$('input[name="hole_centers"]').val();
var $pattern=$('select[name="pattern"]').val();
var $od=$('input[name="od"]').val();
var $finished_length=$('input[name="finished_length"]').val();
var $dim_plus=$('input[name="od_postive"]').val();
var $dim_minus=$('input[name="od_negtive"]').val();
var $angle=$('input[name="angle"]').val();
var $cutoff_length=$('input[name="cutoff_length"]').val();
var $length_plus=$('input[name="finished_length_postive"]').val();
var $length_minus=$('input[name="finished_length_negtive"]').val();
var $drift=$('input[name="id_drift"]').val();
var $mill=$('input[name="mill"]').val();
var $die=$('input[name="die"]').val();
var $strip=$('input[name="strip_width"]').val();
var $ring_min=$('input[name="ring_min"]').val();
var $ring_max=$('input[name="ring_max"]').val();


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






    
    function changeData(id)
    {
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
                    //console.log(data);
                    var jsonResult;
                    try
                    {
                        jsonResult = JSON.parse(data);
                         //console.log('jsonResult :-  '+jsonResult);

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
                        // console.log("error: " + e);
                        alert(data);
                    }
                } 
                else
                {
                    alert("failed to get details");
                }
            });
        });
    }

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





<div class="row info_pop" id="part_popup" form="table.form1" fields='{"is_od": {"type": "bool"}}' table-name="part_tbl" style="margin: 20px 0;">
    <h3>Part information</h3>
        <div class="column">
            <div class="input">
                <span class="left-placeholder">Part#</span><input type="text" id="part_rep" onChange="changePart(this.value);" type="text" name="part" />
            </div>
            <div class="input">
                <input var="cust_id" type='text' style="display: none;"/>
                <span class="left-placeholder">Customer</span><select id="customer" table="cust_tbl" column="customer" disabled="disabled"></select>
            </div>
            <div class="input">
                <span class="left-placeholder">Part Description</span><textarea name="description"></textarea>
            </div>

            <div>
                <div class="inline input">
                    <span class="top-placeholder">Dimension</span><input name="dim" type="text" mask="double">
                </div>
                <div class="inline input">
                <span class="top-placeholder">ID ?</span><input class="id_not_od" type="checkbox">
                </div>
                <div class="input" style="display: none;">
                    <span class="top-placeholder">OD ?</span><input class="od_not_id" name="is_od" type="checkbox" checked="checked">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Dim +</span><input name="dim_plus" type="text" mask="double">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Dim -</span><input name="dim_minus" type="text" mask="double">
                </div>
            </div>


            <div>
                <div class="inline input">
                    <span class="top-placeholder">Material</span><select name="type" table="mat_tbl" column="material" style="width: 152px;"></select>
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Gage</span><select name="gage" table="gage_tbl" column="gage" style="width: 152px;"></select>
                </div>
            </div>

            <div>
                <div class="inline input">
                    <span class="top-placeholder">Pattern</span><select name="pattern" table="pat_tbl" column="pattern"></select>
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Hole Size</span><select name="holes" table="frac_tbl" column="holes" value-field="decimal" var="holes"><option value="">Select Holes</option></select>
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Hole Center</span><select name="centers" table="frac_tbl" column="centers" value-field="decimal" var="centers"><option value="">Select Centers</option></select>
                </div>
            </div>

            <div>
                <div class="inline input">
                    <span class="top-placeholder">Cutoff length</span><input name="cutoff_length" type="text" mask="double">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Strip width</span><input name="strip" type="text" mask="double">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">ID Drift</span>
                    <input name="drift" type="text">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Mill</span><select name="mill" style="width: 50px;"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select>
                </div>
            </div>
            <div>
                <div class="inline input">
                <span class="top-placeholder">Blank ring?</span><input class="blank_ring" type="checkbox">
                </div>
                <div class="inline input show_blank" style="display: none">
                    <span class="top-placeholder">Blank Ring Length +</span><input type="text" var='blank_length_plus' mask="double" name="blank_length_plus">
                </div>
                <div class="inline input show_blank" style="display: none">
                    <span class="top-placeholder">Blank Ring Length -</span><input type="text" mask="double" var='blank_length_minus' name="blank_length_minus">
                </div>
            </div>

             <div>
                <div class="inline input">
                <span class="top-placeholder">Dimple depth?</span><input class="depth_of_dimple" type="checkbox" >
                </div>
                <div class="inline input show_depth" style="display: none">
                    <span class="top-placeholder">Depth of dimple</span><input type="text" mask="double" var='depth_of_dimple' name="depth_of_dimple">
                </div>
                <div class="inline input show_depth" style="display: none">
                    <span class="top-placeholder">Plus +</span><input type="text" mask="double" var='depth_of_dimple_plus' name="depth_of_dimple_plus">
                </div>
                <div class="inline input show_depth" style="display: none">
                    <span class="top-placeholder">Minus -</span><input type="text" mask="double" var='depth_of_dimple_minus' name="depth_of_dimple_minus">
                </div>
            </div>

             <div>
                <div class="inline input">
                <span class="top-placeholder">Blank end ring?</span><input class="blank_end" type="checkbox">
                </div>
                <div class="inline input show_end" style="display: none">
                    <span class="top-placeholder">Width of ring</span><input type="text" mask="double" var='blank_end' name="blank_end">
                </div>
                <div class="inline input show_end" style="display: none">
                    <span class="top-placeholder">Plus +</span><input type="text" mask="double" var='blank_end_plus' name="blank_end_plus">
                </div>
                <div class="inline input show_end" style="display: none">
                    <span class="top-placeholder">Minus -</span><input type="text" mask="double" var='blank_end_minus' name="blank_end_minus">
                </div>
            </div>

            <div>
                <div class="inline input">
                    <span class="top-placeholder">Finished</span><input name="finished_length" type="text" mask="double">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Length +</span><input name="length_plus" type="text" mask="double">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Length -</span><input name="length_minus" type="text" mask="double">
                </div>
                <div class="inline input finished_length_rings">
                     <span class="top-placeholder"></span>
                    <p></p>
                </div>
                
            </div>

            <div>
                <div class="inline input">
                    <span class="top-placeholder">Min. Ring</span><input name="ring_min" type="text" mask="double">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Max. Ring</span><input name="ring_max" type="text" mask="double">
                </div>
                <div class="inline input">
                    <span class="top-placeholder">Die</span>
                    <select class="align-right die" name="die" table="die_tbl" value-field="die" column="die_id"></select>
                    <!-- <input name="die" type="text" mask="double"> -->
                </div>
            </div>

        </div>


        <div class="column">
            <div class="input">
                <span class="left-placeholder">OA</span><input class="oa" precision="3" val="oa" class="align-right" type="number" disabled>
            </div>
            <div class="input">
                <span class="left-placeholder">Tube Weight</span><input class="tube_weight" precision="3" val="tube_weight" class="align-right" type="number" disabled>
            </div>
            <div class="input">
                <span class="left-placeholder">Tube length in feet</span><input class="feet" precision="3" val="feet" class="align-right" type="number" disabled>
            </div>
            <div class="input">
                <span class="left-placeholder">weight/foot</span><input class="weight_per_foot" precision="3" val="weight_per_foot" class="align-right" type="number" disabled>
            </div>
            <div class="input">
                <span class="left-placeholder">hspi</span><input class="hspi" precision="3" val="hspi" class="align-right" type="number" disabled>
            </div>
            <div class="input">
                <span class="left-placeholder">Angle</span><input class="angle" precision="3" val="angle" class="align-right" type="number" disabled>
            </div>
            <div class="input">
                <span class="left-placeholder">lf per foot</span><input class="lf_ft" precision="3" val="lf_ft" class="align-right" type="number" disabled>
            </div>
            <div class="input">
                <span class="left-placeholder">lf per tube</span><input class="lf_tube" precision="3" val="lf_tube" class="align-right" type="number" disabled>
            </div>
            <div class="input clear">
                        <span class="left-placeholder">Drawing</span>
                        <div class="downloadbtn">
                            <a style="display:none" href="download.pdf" id="download_file" target="_blank">Download File</a>
                        </div>
                    </div>
        </div>

        <br>

        <div class="column">
            <div class="input">
                <span class="left-placeholder">Layer 1 Mesh</span><select table="mesh" column="mesh" name="layer_1_mesh"></select>
            </div>
            <div class="input">
                <span class="left-placeholder">Layer 1 Width</span><input name="layer_1_width" type="text" mask="double">
            </div>
            <div class="input">
                <span class="left-placeholder">Drainage 1 Mesh</span><select table="mesh" column="mesh" name="drainage_1_mesh"></select>
            </div>
            <div class="input">
                <span class="left-placeholder">Drainage 1 Width</span><input name="drainage_1_width" type="text" mask="double">
            </div>
        </div>

        <div class="column">
            <div class="input">
                <span class="left-placeholder">Layer 2 Mesh</span><select table="mesh" column="mesh" name="layer_2_mesh"></select>
            </div>
            <div class="input">
                <span class="left-placeholder">Layer 2 Width</span><input name="layer_2_width" type="text" mask="double">
            </div>
            <div class="input">
                <span class="left-placeholder">Drainage 2 Mesh</span><select table="mesh" column="mesh" name="drainage_2_mesh"></select>
            </div>
            <div class="input">
                <span class="left-placeholder">Drainage 2 Width</span><input name="drainage_2_width" type="text" mask="double">
            </div>
        </div>

        <br>

        <div class="column">
            <div class="input">
                <span class="left-placeholder">MFG Notes</span><textarea name="notes" style="width: 554px;"></textarea>
            </div>
            <div class="input">
                <span class="left-placeholder">INSP. Notes</span><textarea name="insp_notes" style="width: 554px;"></textarea>
            </div>
        </div>
        
    </div>



<script type="text/javascript">

   


    var data, cur = 0;
function changePart(id)
    {
        $(".info_pop input[name='part']").keypress(function (e)
        {
            if (e.which == 13)
            {
                $(this).trigger("change");
            }
        });

        

        $(".info_pop input[name='part']").change(function ()
        {
            var v = $(this).val();

            var url = '/TPM-master/api/test.php?view=ajax_part_detail&part=' + v;
                
            $.get(url, function (data, status)
            {
                if (status == "success")
                {
                    var jsonResult;
                    try
                    {
                        jsonResult = JSON.parse(data);
                        //console.log(jsonResult);

                        var len = $.map(jsonResult, function (n, i) {
                            return i;
                        }).length;

                        if (len > 0)
                        {
                            for (var i in jsonResult)
                            {
                                $(".info_pop input[name='" + i + "']").val(jsonResult[i]);
                                $(".info_pop textarea[name='" + i + "']").val(jsonResult[i]);
                                $(".info_pop select[name='" + i + "']").val(jsonResult[i]);
                            }
                        }
                        else
                        {
                            alert("No Data found");

                            $(".info_pop input").not("[name='part']").val("");
                        }
                    } 
                    catch (e)
                    {
                        console.log("error: " + e);
                        alert(data);
                    }
                } 
                else
                {
                    alert("failed to get details");
                }
               
            });
        });
    }
function runfunction(){
    $('.quantity').trigger('keyup');
}
$(document).ready(function(){
    getid();
    
$('#page-btn').hide();
            
$('#job').on('change',function(){
  $('#page-no').html($(this).val());  
});
    $('#page-no').html($('#job').val());
    $('#part_info').click(function(){


        $(".info_pop input[name='part']").val($('#part_id').val());
        $(".info_pop input[var='cust_id']").val($('[var="cust_id"]').val());
        //console.log($('[var="cust_id"] option:selected').text());
        $(".info_pop select#customer").val($('[var="cust_id"] option:selected').text());
        $(".info_pop input[name='part']").trigger("change");

setTimeout(function(){        
        if( jQuery( "input[var='blank_length_plus']" ).val() !='0' && jQuery( "input[var='blank_length_plus']" ).val() !='' || jQuery( "input[var='blank_length_minus']" ).val() !='0'  && jQuery( "input[var='blank_length_minus']" ).val() !=''){
     jQuery('.finished_length_rings p').text('Finished length includes rings');
     jQuery('input[type="checkbox"].blank_ring').prop('checked', true);
    }
     else{
    jQuery('.finished_length_rings p').text('');
    jQuery('input[type="checkbox"].blank_ring').prop('checked', false);
   }
if ( jQuery('input.blank_ring').prop('checked') ) {
    jQuery( ".inline.input.show_blank" ).show();
}

if( jQuery( "input[var='depth_of_dimple']" ).val() !='0' && jQuery( "input[var='depth_of_dimple']" ).val() !='' || jQuery( "input[var='depth_of_dimple_plus']" ).val() !='0'  && jQuery( "input[var='depth_of_dimple_plus']" ).val() !='' || jQuery( "input[var='depth_of_dimple_minus']" ).val() !='0'  && jQuery( "input[var='depth_of_dimple_minus']" ).val() !=''){
     //jQuery('.finished_length_rings p').text('Finished length includes rings');
     jQuery('input[type="checkbox"].depth_of_dimple').prop('checked', true);
    }
     else{
    //jQuery('.finished_length_rings p').text('');
    jQuery('input[type="checkbox"].depth_of_dimple').prop('checked', false);
   }
if ( jQuery('input.depth_of_dimple').prop('checked') ) {
    jQuery( ".inline.input.show_depth" ).show();
}

if( jQuery( "input[var='blank_end']" ).val() !='0' && jQuery( "input[var='blank_end']" ).val() !='' || jQuery( "input[var='blank_end_plus']" ).val() !='0'  && jQuery( "input[var='blank_end_plus']" ).val() !='' || jQuery( "input[var='blank_end_minus']" ).val() !='0'  && jQuery( "input[var='blank_end_minus']" ).val() !=''){
     //jQuery('.finished_length_rings p').text('Finished length includes rings');
     jQuery('input[type="checkbox"].blank_end').prop('checked', true);
    }
     else{
    //jQuery('.finished_length_rings p').text('');
    jQuery('input[type="checkbox"].blank_end').prop('checked', false);
   }
if ( jQuery('input.blank_end').prop('checked') ) {
    jQuery( ".inline.input.show_end" ).show();
}






if ( jQuery('input.blank_ring').prop('checked') ) {
    jQuery( ".inline.input.show_blank" ).show();
}

jQuery('input.blank_ring').click(function(){
    if ( jQuery('input.blank_ring').prop('checked') ) {
        //alert('test');
        jQuery( ".inline.input.show_blank" ).show();
    }else{
        jQuery( ".inline.input.show_blank" ).hide();
        jQuery('.finished_length_rings p').text('');
        jQuery( "input[var='blank_length_plus']" ).val('') ;
        jQuery( "input[var='blank_length_minus']" ).val('');
        //alert('test');
    }
});

jQuery( "input[var='blank_length_plus'] , input[var='blank_length_minus']" ).keyup(function() {
    if( jQuery( "input[var='blank_length_plus']" ).val() !='' || jQuery( "input[var='blank_length_minus']" ).val() !=''  ){
     jQuery('.finished_length_rings p').text('Finished length includes rings');
    }
     else{
    jQuery('.finished_length_rings p').text('');
   }
});


// depth of dimple

if ( jQuery('input.depth_of_dimple').prop('checked') ) {
    jQuery( ".inline.input.show_depth" ).show();
}

jQuery('input.depth_of_dimple').click(function(){
    if ( jQuery('input.depth_of_dimple').prop('checked') ) {
        //alert('test');
        jQuery( ".inline.input.show_depth" ).show();
    }else{
        jQuery( ".inline.input.show_depth" ).hide();
        
        jQuery( "input[var='depth_of_dimple_plus']" ).val('') ;
        jQuery( "input[var='depth_of_dimple_minus']" ).val('');
        jQuery( "input[var='depth_of_dimple']" ).val('');
        //alert('test');
    }
});

// blank end ring of dimple

if ( jQuery('input.blank_end').prop('checked') ) {
    jQuery( ".inline.input.show_end" ).show();
}

jQuery('input.blank_end').click(function(){
    if ( jQuery('input.blank_end').prop('checked') ) {
        //alert('test');
        jQuery( ".inline.input.show_end" ).show();
    }else{
        jQuery( ".inline.input.show_end" ).hide();
        
        jQuery( "input[var='blank_end_plus']" ).val('') ;
        jQuery( "input[var='blank_end_minus']" ).val('');
        jQuery( "input[var='blank_end']" ).val('');
        //alert('test');
    }
});


}, 3000);







        $('.info_pop').simplePopup({
                centerPopup: true,
                closed: function () {
                    runfunction();
                }
         });
         calldata($('#part_id'));

    });

    function calldata(d){
        var v = $(d).val();

            var url = '/TPM-master/api/test.php?view=ajax_part_detail&part=' + v;
                
            $.get(url, function (data, status)
            {
                if (status == "success")
                {
                    var jsonResult;
                    try
                    {
                        jsonResult = JSON.parse(data);
                        //console.log(jsonResult);

                        var len = $.map(jsonResult, function (n, i) {
                            return i;
                        }).length;

                        if (len > 0)
                        {
                            for (var i in jsonResult)
                            {
                                $(".info_pop input[name='" + i + "']").val(jsonResult[i]);
                                $(".info_pop textarea[name='" + i + "']").val(jsonResult[i]);
                                $(".info_pop select[name='" + i + "']").val(jsonResult[i]);
                            }
                        }
                        else
                        {
                            alert("No Data found");

                            $(".info_pop input").not("[name='part']").val("");
                        }
                    } 
                    catch (e)
                    {
                        console.log("error: " + e);
                        alert(data);
                    }
                } 
                else
                {
                    alert("failed to get details");
                }
               
            });
    }
//$('#overlay').css('display','block');
    //quoteChanged();
    // $(".info_pop input[name='part']").trigger("change");




$('#prev-page').click(function(e){
    
    if($('#filter tbody tr:first').hasClass('active')){
       var ne = $('#filter .paginationulorder li.active').prev().find('a');
       ne.trigger('click');
       eventrigger = 0;
   }else{
    $('#filter').hide();
    var da = $('#filter tbody tr.active').prev().trigger('click');
   
    $('#filter').hide();
        if($('.pagicommon .firstpagina').hasClass('active')){
            if(da.index()==0){
                $(this).hide();
            }
        }
   }
   $('#next-page').show();
});

$('#next-page').click(function(e){
    
    

    if($('#filter tbody tr:last').hasClass('active')){
        var ne = $('#filter .paginationulorder li.active').next().find('a');
        ne.trigger('click');
        eventrigger = 1;
     }else{
         $('#filter').hide();
         var lasat = $('#filter tbody tr.active').next().trigger('click');
         $('#filter').hide();
         if($('.pagicommon .lastpagina').hasClass('active')){
            if(lasat.is(':last-child')){
                $(this).hide();
            }
        }
    }
    $('#prev-page').show();
});
var pageno,rowno;
$(document).on('click','#filter tbody tr',function(){
    var $this = $(this);
    let current = 0;
        $('select[var="cust_id"] option').each(function(){
                if($(this).val() == $this.find('td:eq(1)').text() ){
                    current = $(this).attr('value');
                }
        });
            let selectionpar = $this.find('td:eq(3)').text();
                oldvalue1 = current;
            oldvalue2 = selectionpar;
            
           if(current!=null){
            $.post(setting.server+"index.php?url=part_mat"+"&userid="+current,
                function(res){
                    var output =[];
                    output.push('<option value="">Select Part</option>');
                    $.each(res.list, function(key, value){
                        if(value['part'] == selectionpar){
                            output.push('<option selected value="'+ value['part'] +'">'+ value['part'] +'</option>');
                        }
                        else{
                            output.push('<option value="'+ value['part'] +'">'+ value['part'] +'</option>');
                        }
                    });
                        $('.part_id').html(output.join(''));
                        //console.log('counter1');
                }); 
                $('select[var="cust_id"]').val(current);
           }  
           $('#page-btn').show();  
           
           var part_no = $this.find("td:eq(3)").text();
            
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

         $('.mesh_length input').prop('checked', false);
         $('#mesh_data1').html('');  


       });
});


function quoteChanged() {
    
            var job = '';
            request('table/orders_tbl/fetch')
            .done(function(res) {
                // if(!job)
                //     return;
                             
                
                data = res.list;
                if(!data.length) {
                    $('#page-btn').hide();
                    return;
                }
                
                
                    fillForm('table.form', data[data.length-1], 'var');
                    fillForm('table.form', data[data.length-1], 'val');
                
                cur = 0;

                $('#page-btn').show();
                $('#prev-page').show();
                $('#first-page').show();
                $('#next-page').hide();
                $('#last-page').hide();

                $('#page-no').html($('#job').val());

                //$('#first-page').hide();
                //$('#prev-page').hide();

                if(cur == data.length - 1) {
                    $('#next-page').hide();
                    $('#last-page').hide();
                }

                //calcRateInfo();
                $('#overlay').css('display','none');
            });
        }

function show(page) {
            cur = page;
            resetForm('table.form');   
            $('#overlay').css('display','block');
            fillForm('table.form', data[cur], 'var');
            fillForm('table.form', data[cur], 'val');
            
            $('#first-page').show();
            $('#prev-page').show();
            $('#next-page').show();
            $('#last-page').show();

            $('#page-no').html($('#job').val());
$('#overlay').css('display','none');
            if(cur == 0) {
                $('#first-page').hide();
                $('#prev-page').hide();
            }
            if(cur == data.length - 1) {
                $('#next-page').hide();
                $('#last-page').hide();
            }
            //calcRateInfo();
        }
function getid(){
            $.ajax({
                method:"POST",
                url:"/TPM-master/api/getid?&getidnew="+$('#job').attr('data-hold')+"&coll="+$('#job').attr('data-colla'),
                success:function(res){
                    if(res['list'].length !=0){
                        let id = parseInt(res['list'][0][$('#job').attr('data-colla')]);
                        $('#job').val(id+1);

                        //call when job id need
                        $('.mesh_length input').prop('checked', false);
                        $('#mesh_data1').html('');

                        preview_length();//mesh preview length
                    }
                }
            });
        }

</script>
<script src="assets/js/page.part-information-popup.js"></script>

<script type="text/javascript">

    // adds a new line item for coils to be entered
    $(document).ready(function(){
        var maxField = 50; //Input fields increment limitation
        var addButton = $('.add_button'); //Add button selector
        var wrapper = $('.field_wrapper'); //Input field wrapper
        var fieldHTML = '<div class="column"><div class="input"><span class="left-placeholder">Ship Date</span><input name="ship_dates[]" class="align-right" type="date"><a href="javascript:void(0);" class="remove_button"><button type="button">-</button></a></div><div class="input"><span class="left-placeholder">Quantity</span><input name="ship_quan[]" class="align-right" type="number"></div></div>'; //New input field html 
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
    
</script>

<script type="text/javascript">
    
    $(document).ready(function(){







setTimeout(function(){ 

$('.die').html($('.die').children('option').sort(function (x, y) {
                return +$(x).text().toUpperCase() > +$(y).text().toUpperCase() ? -1 : 1;
                
            }));
            console.log('die');

$('.die1').html($('.die1').children('option').sort(function (x, y) {
                return +$(x).text().toUpperCase() > +$(y).text().toUpperCase() ? -1 : 1;
                
            }));
            console.log('die1');
            



            
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

           �     �                    �=            �;    h�             �    �        �                                                                                     ��