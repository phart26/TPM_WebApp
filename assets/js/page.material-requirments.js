// tables to load & cache
var tbls = {
    gage_tbl : {},
    pat_tbl  : {},
    frac_tbl : {}
};


$(function(){

    $('[form="table.form"] [var]').on('keyup change', function(){
        var data = parseForm('table.form')
        
        with(data)
        {
        	//alert("Alok")
            // pre
            data.holes     = +selectRow(tbls.frac_tbl, {'decimal' : holes}).decimal
            data.centers   = +selectRow(tbls.frac_tbl, {'decimal' : centers}).decimal
            data.thickness = +selectRow(tbls.gage_tbl, {'gage' : gage}).thickness
            data.oa_factor = +selectRow(tbls.pat_tbl, {'pattern' : pattern}).oa_factor

            //alert(data.holes + ' - ' + data.centers + ' - ' + data.thickness + ' - ' + data.oa_factor )

            // od
            data.od = is_od ? dim : (dim + 2 * thickness)
             
            // oa
            data.oa = holes==0 ? 0 : (Math.pow(holes, 2) / Math.pow(centers, 2) * oa_factor)
            
            //alert(thickness + ' - ' + od + ' - ' + length + ' - ' + data.oa)
            // tube weight
            data.tube_weight =  0.29 * thickness * 4 * Math.atan(1) * od * length * (100-oa)/100
           // alert(data.tube_weight)
            // weight per foot
            data.weight_per_foot = length * 12 * thickness * 0.29
            
            // feet
            data.feet = length / 12
            
            // hspi
            data.hspi = holes==0 ? 0 : (oa / (78.54 * Math.pow(holes, 2)))
            
            // side
            data.side = Math.pow( Math.pow(4 * Math.atan(1) * od, 2) - Math.pow(strip, 2), 1/2)
            
            // angle
            data.angle = 90 - Math.atan(side/strip) * 180 / ( 4 * Math.atan(1))
            
            // lf_ft
            data.lf_ft = 4 * Math.atan(1) * od/strip
            
            // lf_tube
            data.lf_tube = feet * lf_ft
            
            // wbs
            data.wbs = 1.1 * 100 * quantity * tube_weight / (100 - oa)
            
            // lf_total
            data.lf_total = 1.1 * lf_tube * quantity
        }
          console.log(data);

        fillForm('table.form', data, 'val')
    })

})