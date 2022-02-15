// $(document).ready(function(){
// tables to load & cache
var tbls = {
    cust_tbl : {},
    gage_tbl : {},
    frac_tbl : {},
    pat_tbl  : {}
};


 $(function(){



    $('[form="table.form1"] [name]').on('keyup change', function(){
        // var data = parseForm('table.form')
var part_no = $('input[name="part"]').val();
        // with(data)
        // {
        //     // pre
        //     data.holes     = +selectRow(tbls.frac_tbl, {'fraction' : holes}).decimal
        //     data.centers   = +selectRow(tbls.frac_tbl, {'fraction' : centers}).decimal
        //     data.thickness = +selectRow(tbls.gage_tbl, {'gage' : gage}).thickness
        //     data.oa_factor = +selectRow(tbls.pat_tbl, {'pattern' : pattern}).oa_factor

        //     // od
        //     data.od = is_od ? dim : (dim + 2 * thickness)
            
        //     // oa
        //     data.oa = holes==0 ? 0 : (Math.pow(holes, 2) / Math.pow(centers, 2) * oa_factor)
            
        //     // tube weight
        //     data.tube_weight =  (0.29 * thickness * 4 * Math.atan(1) * od * finished_length * (100-oa))/100

        //     // weight per foot
        //     data.weight_per_foot = tube_weight / finished_length * 12
            
        //     // feet
        //     data.feet = finished_length / 12
            
        //     // hspi
        //     data.hspi = holes==0 ? 0 : (oa / (78.54 * Math.pow(holes, 2)))
            
        //     // side
        //     data.side = Math.pow( Math.pow(4 * Math.atan(1) * od, 2) - Math.pow(strip, 2), 1/2)
            
        //     // angle
        //     data.angle = 90 - Math.atan(side/strip) * 180 / ( 4 * Math.atan(1))
            
        //     // lf_ft
        //     data.lf_ft = 4 * Math.atan(1) * od/strip
            
        //     // lf_tube
        //     data.lf_tube = feet * lf_ft

        //     
            
        // }

        // fillForm('table.form', data, 'val')
    
        
        request('part-specs?part_name='+part_no, {})
            .done(function(response){
                if (response.length !=0){
                    calculation = response.done;
                    $.each(response.done,function(i,j){
                        
                                $('.'+i).val(parseFloat(j).toFixed(3));
                       console.log(i + ' :- '+ j); 
                    })
                }
                          
            });
    

    })





    $('#customer').on('click change', function(){
        var input = $(this),
            value = input.val()

        $('[var="cust_id"]').val( selectRow(tbls.cust_tbl, {'customer' : value }).cust_id )
    })

    $('[var="cust_id"]').on('change', function(){
        var input = $(this),
            value = input.val()

        $('#customer').val( selectRow(tbls.cust_tbl, {'cust_id' : value }).customer )
    })
});