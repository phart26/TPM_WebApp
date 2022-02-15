$(function(){

    // Recieve Multiple Coils
    $('#recieve_multiple_coils').on('click', function(){

        // value of part no will be auto_incremented
        var number = parseInt( prompt('Enter number of mesh') )

        if (confirm('Ready to save '+ number +' records.\n\nDo you want to procced ?'))
        {
            $('[var="mesh_no"]').val('')

            for (; number > 0; number--)
                EVENTS['table.replace']();
        }
    })

})