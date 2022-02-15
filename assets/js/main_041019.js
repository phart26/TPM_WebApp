function date(format, timestamp)
{
	var time = typeof timestamp=='undefined' ? (new Date()) : (new Date(typeof timestamp=='string' ? timestamp : (timestamp*1000)));

	var list = {
		// hours
		h : function(){ return time.getHours(); },
		H : function(){ return time.getHours() - (time.getHours() >= 12 ? 12 : 0); },

		// minutes
		m : function(){ return time.getMinutes(); },
		M : function(){ return (time.getMinutes() < 10 ? '0' : '') + time.getMinutes(); },

		// seconds
		s : function(){ return time.getSeconds() },
		S : function(){ return (time.getSeconds() < 10 ? '0' : '') + time.getSeconds(); },

		// date
		d : function(){ return time.getDate(); },
		D : function(){ return (time.getDate() < 10 ? '0' : '') + time.getDate(); },

		// month
		n : function(){ return time.getMonth() + 1; },
		N : function(){ return (time.getMonth() < 10 ? '0' : '') + time.getMonth(); },
		o : function(){
			var mth = 'January,February,March,April,May,Jun,July,August,September,October,November,December'.split(',');
			return mth[time.getMonth()].slice(0,3);
		},
		O : function(){
			var mth = 'January,February,March,April,May,Jun,July,August,September,October,November,December'.split(',');
			return mth[time.getMonth()];
		},
	
		// year
		y : function(){
			return time.getFullYear()%100;
		},
		Y : function(){
			return time.getFullYear();
		},

		// meridiem
		a : function(){ return time.getHours() >= 12 ? 'PM' : 'AM'; }
	};

	var equiValue = function( of )
	{
		return list[of] ? list[of]() : of;
	};

	return format.replace(/\\?(.?)/gi, equiValue);
}


var fillForm = function( name, data, selector) {

	$('[form="'+ name +'"] ['+ selector +']').each(function(){

		var input = $(this),
			name  = input.attr(selector),
			type  = input.prop('type')

		if (data[ name ] === undefined) return;

		// checkbox
		if (type=='checkbox' || type=='radio')
			input.prop('checked', data[ name ] && data[ name ]!="0" ? true : false)

		// text
		else
			input.val( data[ name ] );

		// change event
		input.trigger('change')
	})

};


var parseForm = function( name ){
	
	var data = {},
		form = $('[form="'+ name +'"] [var]'),
		radio= new Set
	
	// text
	form.filter(':not([type=radio])').filter(':not([type=checkbox])').each(function(){
		var input = $(this),
			mask  = input.attr('mask'),
			value = input.val() || input.attr('value') || ""

		if (mask=='number' || mask=='double')
			value = parseFloat(value || "0");

		data[ input.attr('var') ] = value
	})

	// checkbox
	form.filter('[type=checkbox]').each(function(){
		var input = $(this)

		data[ input.attr('var') ] = input.is(':checked') ? 1 : 0
	})
	
	// radio
	form.filter('[type=radio][checked]').each(function(){
		var input = $(this)

		data[ input.attr('var') ] = input.is(':checked') ? 1 : 0
	})

	return data
};


var resetForm = function( name ){

	var forms = $('[form="'+ name.replace(/[ ]+/g, '').replace(/\,/g, '"],[form="') +'"]')

	forms
		.find('[var]')
			.val('')
			.attr('value', '')
			.trigger('change')

	forms
		.find('.selected')
			.removeClass('selected')

	forms
		.find('[type=checkbox][var]').each(function() {
			$(this).prop('checked', false);
		})
		
		
	$('[var="work"]').attr('disabled', false );
	$('[var="coil_no"]').attr('disabled', false );

};



var json2table = function( data, events, fields )
{
// console.log(data.length);
if(typeof data !='undefined'){
	if(!data.length)
		return "No data Found";
	var table  = $('<table class="table hover row-border stripe display compact"></table>'),
        thead = $('<thead></thead>'),
        tbody = $('<tbody></tbody>'),
        field = [];
    
    table
        .append(thead)
        .append(tbody);       

	// heading
	if (data.length || 0)
	{
		var tr  = $('<tr class="head"></tr>'),
		    row = data[0],
		    i = 0;

		for (var key in row) {
			for (var key2 in fields) {
				if(key === key2)
					field[i] = fields[key2];
			}
			tr.append( $('<th></th>').text( key ) );
			i++;
		}

		thead.append(tr)
	}

	// [{}, ...]
	for (var i=0; i<data.length; i++)
	{
		var row = data[i],
			tr  = $('<tr></tr>').data( row ),
			j   = 0;


		for (var key in row)
		{
			var css = {};

			if (row[key] && row[key].length > 20)
				css = {
					'width' : '100px',
					'max-width': '100px',
					'text-overflow': 'ellipsis',
					'overflow': 'hidden',
					'white-space': 'nowrap'
				}

			var td = $('<td></td>').append($('<span></span>').text( row[key] ))
			td.css(css).prop('title', row[key]);
			
			if(field[j]) {
				if(field[j].type === "bool")
					if(row[key] == 1)
						td.addClass("bool fa fa-check");
					console.log("called");
					// else
					// 	td.addClass("bool fa fa-times");
			}
			
			tr.append(td);
			j++;
		}

        tr.on(events);

		tbody.append(tr);
	}

	return table;
}
}


var loading = function( msg, hide ) {

	var loadingElement = $('#loading');

	// input message
	loadingElement.find('[val]').text( msg || '' );

	loadingElement.visibility( !hide );
};



function request(api, data)
{

	return $.ajax({
		'url'		:	setting.server + api,
		'data'		:	JSON.stringify(data),
		'headers'	:	{
		},
		'type'	:	'POST',
		'dataType'		:	'JSON',
		'processData'	:	false,
		'contentType'	:	'application/json; UTF-8',
		'crossDomain'	:	true,
		'xhrFields'		:	{
			'withCredentials'	: true
		}
	}).done(function(response, textStatus, xhr){
		//console.log( response + ' ' + textStatus );
		if (!response) return;

		if (response.error) message(response.error, 5000);

	})
}


function message(msg, timeout) {
	var div = $('<div id="msg" style="z-index: 99999; display:none; position: fixed;text-align: center;left: 50%;mix-width: 200px;bottom: 40px;padding: 5px 20px;background: #27c24c;border: 1px solid #26be4a;border-radius: 20px;color: #fff;">Saving</div>').html( msg )

	$(document.body).append( div );

	if (timeout !== undefined)
		setTimeout(function(){
			div.hide('fade', 'slow', function(){
				$(this).remove()
			})
		}, timeout)

	return div.show('fade', 'slow');
}

function messageerror(msg, timeout) {
	var div = $('<div  style="z-index: 99999; display:none; position: fixed;text-align: center;left: 50%;mix-width: 200px;bottom: 40px;padding: 5px 20px;background: red;border: 1px solid #26be4a;border-radius: 20px;color: #fff;">Saving</div>').html( msg )

	$(document.body).append( div );

	if (timeout !== undefined)
		setTimeout(function(){
			div.hide('fade', 'slow', function(){
				$(this).remove()
			})
		}, timeout)

	return div.show('fade', 'slow');
}

var EVENTS = {};

//////////////////////////
// TABLE EVENTS
//-----------------------

EVENTS['table.filter'] = function(){
	$('#filter').slideToggle();
}



EVENTS['table.replace'] = function() {

	
	// validate input
	var invalid = $('[form="table.form"] [mask].invalid').filter(':first')

	if ( invalid.length )
		return message(invalid.attr('field'), 2000)

    var data = parseForm('table.form'),
		msg  = message('Saving...'),
		
		tbl  = $('[form="table.form"]').attr('table-name')
		
		

		if( $('#error').text() == "Work Number Exists!" ) {
			msg.remove();
			messageerror( 'Work Number Exists!', 2000 );
			
		}
		else if( $('#error').text() == "Coil Number Exists!" ) {
			msg.remove();
			messageerror( 'Coil Number Exists!', 2000 );
			
		}
		else if( data.work == "" ) {
			msg.remove();
			messageerror( 'Work Number is Empty!', 2000 );
		}
		else if( data.coil_no == "" ) {
			msg.remove();			
			messageerror( 'Coil Number is Empty!', 2000 );
		}
		
		
		else {
			
			//For Coil tbl
			if( tbl == 'coil_tbl' ) {
				console.log( data );
			
				$.ajax({
					url: 'customphp.php',
					method: 'post',
					data: data,
					dataType: 'JSON',
					success:function(data) {
						msg.remove();

						if( data == "1" ) {
							message('Saved', 2000);
						}
						else if( data == "0") {
							messageerror('Already Exists', 2000);
						}
						//EVENTS['table.list']();
					}
				});
			}
			else {
				console.log( data );
			
				request('table/'+ tbl +'/save', data)
				.always(function(){
					msg.remove()
				})
				.done(function(response){
					
					//console.log(response);
					//console.log('432' );
					message('Saved', 2000);
					
					EVENTS['table.list']();
				})
				
			}
				
				
			
			
		
			
		}	
		
    
}


EVENTS['table.delete'] = function() {

    var data = parseForm('table.form'),
		msg  = message('Deleting...'),
		tbl  = $('[form="table.form"]').attr('table-name')

    request('table/'+ tbl +'/delete', data)
		.always(function(){
			msg.remove()
		})
        .done(function(response){

			message('Deleted', 2000)

            resetForm('table.form');
            EVENTS['table.list']();

        })
}

EVENTS['table.list'] = function() {
	
    var data = {},
		tbl  = $('[form="table.form"]').attr('table-name'),
		fields = $('[form="table.form"]').attr('fields');
		
		
		
		if( !($('#filter')[0].hasAttribute('fetch-url'))){
			
			fetchtabledatapagi(select=null,tbl,fields,limit=null,page=null);
		}
		console.log($('#filter').attr('fetch-url'));
		
	
}
function fetchtabledatapagi(select=null,tbl,fields,limit,page){
	
	console.log( tbl );	
	
	
	
	if(fields)
		fields = JSON.parse(fields);
		
		url ='table/'+ tbl +'/fetchpagination';
		url +='&limit='+limit+'&page='+page;
		
	request(url)
		.done(function(response){
			console.log(response + 'abc');
			if (response)
			var insert = '<div class="bottomnavigation"';
				$('#filter').html(
					json2table(response.list.data, {
						'click' : function() {
							var data = $(this).data();

							$('#filter').slideToggle();

							$(this).closest('tbody').children('tr.active').removeClass('active')
							$(this).closest('tr').addClass('active')

							fillForm('table.form', data, 'var');
							fillForm('table.form', data, 'val');
							console.log('here');
							/*
							$('[form="table.form"] [var]').each(function(){

								var key = $(this).attr('var');
								$(this).val( data[key] ).trigger('change');

							})
							*/
							
							$('[var="work"]').attr('disabled', true );
							$('[var="coil_no"]').attr('disabled', true );
							
						}
					})
				).append(response.list.pagination);
				$('.paginationul').removeClass('paginationul').addClass('paginationulorder');
					if(typeof eventrigger != 'undefined'){
						if(eventrigger ==0){
							$('#filter tbody tr:last').trigger('click');
							eventrigger = '';
						}if(eventrigger ==1){
							$('#filter tbody tr:first').trigger('click');
							
							eventrigger = '';
						}
						$('#filter').hide();
					}
					$('#filter').prepend(response.list.searchqu);
					var field='';
						console.log( response.list.data );
						if(response.list.data.length > 0) 
						{
							$.each(response.list.data[0],function(i,j){
								if( i == "cust_id" ) 
									field += '<option value="cust_name">cust_name</option>';
								else
									field += '<option value="'+i +'">'+i+'</option>';
								console.log( i + ' - ' + j  );
							});
							$('#filter  .selectfieldsearch').append(field);
							
						}
					//field += '<option value="select">Select</option>';
				// .find('table').DataTable({
				// 	pageLength : 5,
				// 	lengthMenu : [ 5, 10, 25, 50, 75, 100 ]
				// });


		})
		$('#overlay').css('display','none');
}


//------------------------
// TABLE EVENTS - END
//////////////////////////



EVENTS['customers.filter'] = function() {
	$('#filter').slideToggle();
}
EVENTS['customers.list'] = function() {
    var data = {
        'search' : {
            'text'   : $('#search').val(),
            'fields' : 'cust_id,customer,bill_to,ship_to,contact,phone,fax,email'
        }
    }

    if (EVENTS['customers.list'].ajax) EVENTS['customers.list'].ajax.abort();

    EVENTS['customers.list'].ajax = 
        request('table/cust_tbl/fetch', data)
            .done(function(response){

                if (response.list)
                    $('#filter').html(
                        json2table(response.list, {
                            'click' : function() {
                                var data = $(this).data();

								$('#filter').slideToggle();

								$(this).closest('tbody').children('tr.active').removeClass('active')
								$(this).closest('tr').addClass('active')

                                $('[form="customers.edit"] [var]').each(function(){

                                    var key = $(this).attr('var');
                                    $(this).val( data[key] ).trigger('change');

                                })
                            }
                        })
                    )
                    .find('table').DataTable({
						pageLength : 5,
						lengthMenu : [ 5, 10, 25, 50, 75, 100 ]
					});


            })
}


EVENTS['customers.save'] = function() {

	// validate input
	var invalid = $('[form="customers.edit"] [mask].invalid').filter(':first')

	if ( invalid.length )
		return message(invalid.attr('field'), 2000)

    var data = parseForm('customers.edit'),
		msg  = message('Saving...')

    request('table/cust_tbl/save', data)
		.always(function(){
			msg.remove()
		})
        .done(function(response){

			message('Saved', 2000)

            EVENTS['customers.list']();

        })
}


EVENTS['customers.delete'] = function() {

    var data = parseForm('customers.edit'),
		msg  = message('Deleting...')

    request('table/cust_tbl/delete', data)
		.always(function(){
			msg.remove()
		})
        .done(function(response){

			message('Deleted', 2000)

            resetForm('customers.edit');
            EVENTS['customers.list']();

        })
}



EVENTS['form.reset'] = function( elem ) {
	resetForm( $(elem).attr('form-name') );
}


EVENTS['form.hide'] = function( elem ) {

    var form   = $(elem).closest('[form]'),
        parent = form.parent()

    parent.hide();
    form.hide();
}


EVENTS['activity'] = function( elem ) {
	var activity = $(elem).data('activity-name');

	showList(activity)

	$('[activity]')
		.hide()
		.filter('[activity="'+ activity +'"]')
			.show()
}

EVENTS['activity-new'] = function( elem ) {
	$('[activity="'+ $(elem).data('activity-name') +'"]').show()
}

EVENTS['activity-stop'] = function( elem ) {
	console.log('[activity="'+ $(elem).closest('[activity]').attr('activity') +'"]')
	$('[activity="'+ $(elem).closest('[activity]').attr('activity') +'"]').hide()
}

EVENTS['import'] = function( elem ) {

	var input 	 = $('<input type="file"></input>'),
		activity = $(elem).closest('[activity]').attr('activity');

	input.on('change', function(){

		var file = input[0].files[0];

		// processing
		loading();

		$.ajax({
			url		:	setting.server + activity +'/import',
			data	:	file.slice(0),
			headers	:	{
				'Content-Range' : 'bytes 0-'+ file.size +'/'+ file.size
			},
			type	:	'POST',
			processData	:	false,
			contentType	:	'application/json; name="'+ file.name +'"'
		})

		.always(function(){
			loading('', true);
		})

		.done(function(response, status, ajax) {
			if (response.done) showList(activity);
			if (response.error) alert(response.error);
			if (response.message) alert(response.message);
		});
	});

	input.click();
}

EVENTS['export'] = function( elem ) {

	var table = $(elem).closest('[activity]').attr('activity');

	window.location.href = setting.server + table + '/export';

}

EVENTS['profile.login'] = function() {
	request('users/login', parseForm('profile.login'), function(){
		resetForm('profile.login');
	});
}

EVENTS['profile.logout'] = function() {
	request('users/logout', []);
}




EVENTS['user.save'] = function() {
	request('users/save', { 'tuple' : parseForm('user.edit') }, function(){
		resetForm('user.edit');

		showList('users')
	});

	$('[activity="user.edit"]').hide()
}


EVENTS['product.save'] = function() {
	request('products/save', { 'tuple' : parseForm('product.edit') }, function(){
		resetForm('product.edit');

		showList('products')
	});

	$('[activity="product.edit"]').hide()
}


EVENTS['delete-row'] = function(elem) {

	var activity = $(elem).closest('[activity]').attr('activity');

	data = {
		'id' : $(elem).closest('tr').data('id')
	};

	request(activity + '/delete', data, function(){
		showList(activity)
	});
}


EVENTS['edit-row'] = function(elem) {
	
	var activity = $(elem).closest('[activity]').attr('activity');

	data = {
		'id' : $(elem).closest('tr').data('id'),
	};

	// fetch
	request(activity + '/detail', data, function(response){

		if (!response.table) return;

		var form_name = {
			'users' : 'user.edit',
			'products' : 'product.edit',
			'transactions' : 'transaction.edit'
		};

		$('[activity="'+ form_name[activity] +'"]')
			.show()
			.find('[var]')
				.each(function(){

					var key = $(this).attr('var');
					$(this).val( response.table[key] ).trigger('change');
				})
	});
}


var showList = function(api){

	var extraColumn = '';

	if (api=='products' || api=='users')
		extraColumn =
			'<td auth="admin">'+
				'<i class="icon-pencil" ev="edit-row" style="padding: 8px;" auth="admin"></i>'+
				'<i class="icon-cancel" ev="delete-row" style="padding: 8px;" auth="super-admin"></i>'+
			'</td>';


	request(api + '/fetch', {'search' : $('[activity="'+ api +'"]').find('.search').val()}, function(response){
		if (response.table)
			$('[activity="'+ api +'"] .body').html( json2table(response.table, extraColumn) );

		// action events
		$('[activity="'+ api +'"] table td [ev]').click(function(){
			var name = $(this).attr('ev')

			if (EVENTS[name]) EVENTS[name]( this )
		})

		// auth level
		var auths = $('[activity="'+ api +'"] table [auth]');

		auths.filter('[auth="user"]').visibility(window.profile);
		auths.filter('[auth="admin"]').visibility(window.profile.type == 'admin' || window.profile.type == 'super-admin');
		auths.filter('[auth="super-admin"]').visibility(window.profile.type == 'super-admin');
	})
}




function selectRow(table, cdn)
{
    var row, is_matched;

    for(var i=0; i<table.length; i++)
    {
        row = table[ i ]
        is_matched = true

        for(column in cdn)
            if (cdn.hasOwnProperty(column) && row[column] != cdn[column])
            {
                is_matched = false
                continue;
            }

        if (is_matched) return row;
    }

    return {};
}



$(function(){

    jQuery.fn.extend({
        centeralize : function() {
            return this.each(function() {
            var e = $(this),
                p = e.parent(),
                pW = p.width(),
                pH = p.height(),
                eW = e.width(),
                eH = e.height()

            $(this).css('top', (pH/2 - eH/2) + 'px');
            $(this).css('left', (pW/2 - eW/2) + 'px');
            });
        },

        visibility : function( condition ) {
            $(this)[ condition ? 'show' : 'hide']()
        }
    });

    // load page required tables for page
	if (window.tbls)
	{
		for(var tbl in tbls)
			(function(tbl){
				request('table/'+ tbl +'/fetch', {})
					.done(function(response){
						if (response.list)
							tbls[ tbl ] = response.list
					})
			})(tbl)
	}


    $('[ev]').click(function(){
        var name = $(this).attr('ev')

        if (EVENTS[name]) EVENTS[name]( this )
    })


    // datepicker polyfill
    $('input[type="datepicker"]').datepicker({
		dateFormat : "yy/mm/dd"
	});

    // highlight tab
    $('.left-navigation a[href="'+ window.location.href.substr( window.location.href.lastIndexOf('/') + 1 ) +'"]').parent().addClass('active');
    $('.left-sub-navigation a[href="'+ window.location.href.substr( window.location.href.lastIndexOf('/') + 1 ) +'"]').parent().addClass('active');

    // tab changing
    $('.tab-list-item').click(function(){
        var index = $(this).index()

        // highlight tab
        $(this)
            .parent()
            .children('.tab-list-item')
                .removeClass('active')
                .filter(':nth('+ index +')')
                    .addClass('active')

        // show tab content
        $(this)
            .closest('.tab')
            .children('.tab-content')
                .removeClass('active')
                .filter(':nth('+ index +')')
                    .addClass('active')

    })

//    EVENTS['customers.list']();
    EVENTS['table.list']();


	var options =  {
		onComplete: function(cep, event, currentField, options) {
			currentField.removeClass('invalid')
		},
		onKeyPress: function(cep, event, currentField, options){
			currentField.addClass('invalid')
		},
		onChange: function(cep){
		},
		onInvalid: function(cep, event, currentField, invalid, options){
		}
	};

	// only valid input chars will allowed
	var testers = {
		'email' : /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
	}


	$('[mask="double"]').on('keyup', function(){
		var val = $(this).val()

		$(this).val( val.match(/[0-9]*\.?[0-9]*/) )
	});

	$('[mask="number"]').on('keyup', function(){
		var val = $(this).val()

		$(this).val( val.match(/[0-9]*/) )
	});

	$('[mask="email"]').on('keyup', function(){
		var val = $(this).val(),
			type= $(this).attr('mask')

		if (testers[type].test( val ))
			$(this).removeClass('invalid')
		else
			$(this).addClass('invalid')

	});	
	$('[mask="mobile"]').mask('0000000000', options);
	$('[precision]').on('change', function(){
		var input = $(this),
			value = input.val(),
			precision = input.attr('precision')

		input.val( parseFloat(value || "0").toFixed( precision ) )
	})



	// select options
	$('select[table]').each(function(){

		var select = $(this),
			column = select.attr('column'),
			source = select.attr('table'),
			value = select.attr('value-field') || column,
			filter_column = select.attr('filter_column'),
			filter_value = select.attr('filter_value');

        request('table/'+ source +'/fetch', {})
            .done(function(response){
                if (!response.list) return;
                // response.list.sort();
                // console.log('response ' + response.list);

				for(var i=0; i<response.list.length; i++)
				{
					var option = response.list[i]

					if (filter_column && filter_value) {
                        if (option [ filter_column ] != filter_value) continue;
					}

                    select.append(
                        $('<option>'+ option[ column ] +'</option>').prop('value', option[ value ])
                    )
				}


//var selected = select.val(); // cache selected value, before reordering
// var opts_list = select.find('option:gt(1)');
// opts_list.sort(function(a, b) { 


// if($.isNumeric($(a).text()) && $.isNumeric($(b).text())){
// 		return $(a).text() - $(b).text();
// 	}else{
// 		return $(a).text() > $(b).text() ? 1 : -1; 
// 	}

// });
// select.find("option:gt(1)").remove();
// select.append(opts_list);
// //sel.val(selected); // set cached selected value
// select.prepend( '<option value="" selected>Please Select</option>' );
			});
	})

	// multi column select
	$('input[table]').each(function(){

		var select = $(this),
			column = select.attr('column'),
			fields = JSON.parse(select.attr('fields')),
			fieldText = select.attr('field-text'),
			fieldValue = select.attr('field-value'),
			source = select.attr('table');

        request('table/'+ source +'/fetch', {})
            .done(function(res){
                if (!res.list) return;

                select.inputpicker({
				    data: res.list,
				    fields: fields,
				    fieldText: fieldText,
				    fieldValue: fieldValue,
				    headShow: true,
				    autoOpen: true
				})

			});
	})

	// load table with data from url
	$('[fetch-url]').each(function() {
		var select = $(this),
			url = select.attr('fetch-url'),
			fields = $('[form="table.form"]').attr('fields');
			fetchtabledata(select,url,fields,null,null);
	})

});


	function fetchtabledata(select,url,fields,limit=null,page=null){
		// console.log(limit);
	
		if(fields)
				fields = JSON.parse(fields);
		
			select.html('Loading...');
		url +='&limit='+limit+'&page='+page;
		request(url,{})
			.done(function(res) {
				if(!res.list) {
					select.html('<tbody>No data Found !</tbody>');
					return;
				}

				if(typeof window[select.attr('callback')] === "function")
					window[select.attr('callback')](res);

					select.html(
						json2table(res.list.data, {
							'click': function() {
								var data = $(this).data();
	
								$(this).closest('tbody').children('tr.active').removeClass('active')
								$(this).closest('tr').addClass('active')
	
								fillForm('table.form', data, 'var');
								fillForm('table.form', data, 'val');
								$('#filter').slideUp();
							}
						}, fields)
					);
					$('#filter').prepend(res.list.searchqu);
					var field='';
						$.each(res.list.data[0],function(i,j){
							field += '<option value="'+i +'">'+i+'</option>';
						})
						$('#filter .selectfieldsearch').append(field);

					var insert = '<div class="bottomnavigation"'
					select.append(res.list.pagination);
					
					if(typeof eventrigger != 'undefined'){
						if(eventrigger ==0){
							$('#filter tbody tr:last').trigger('click');
							eventrigger = '';
						}if(eventrigger ==1){
							$('#filter tbody tr:first').trigger('click');
							
							eventrigger = '';
						}
						$('#filter').hide();
					}

				// .find('table').DataTable({
				// 	pageLength : 5,
				// 	lengthMenu : [ 5, 10, 25, 50, 75, 100 ]
				// });
			})
			$('#overlay').css('display','none');
}


// EVENTS['MasterSteel.filter'] = function() {
	// $('#filter').slideToggle();
// }
// EVENTS['MasterSteel.list'] = function() {
    // var data = {
        // 'search' : {
            // 'text'   : $('#search').val(),
            // 'fields' : 'id,work_no,coil,material,gage,width,weight,tpm_job'
        // }
    // }

    // if (EVENTS['MasterSteel.list'].ajax) EVENTS['MasterSteel.list'].ajax.abort();

    // EVENTS['MasterSteel.list'].ajax = 
        // request('table/new_materials/fetch', data)
            // .done(function(response){

                // if (response.list)
                    // $('#filter').html(
                        // json2table(response.list, {
                            // 'click' : function() {
                                // var data = $(this).data();

								// $('#filter').slideToggle();

								// $(this).closest('tbody').children('tr.active').removeClass('active')
								// $(this).closest('tr').addClass('active')

                                // $('[form="MasterSteel.edit"] [var]').each(function(){

                                    // var key = $(this).attr('var');
                                    // $(this).val( data[key] ).trigger('change');

                                // })
                            // }
                        // })
                    // )
                    // .find('table').DataTable({
						// pageLength : 5,
						// lengthMenu : [ 5, 10, 25, 50, 75, 100 ]
					// });


            // })
// }


// EVENTS['MasterSteel.save'] = function() {

	// // validate input
	// var invalid = $('[form="MasterSteel.edit"] [mask].invalid').filter(':first')

	// if ( invalid.length )
		// return message(invalid.attr('field'), 2000)

    // var data = parseForm('MasterSteel.edit'),
		// msg  = message('Saving...')

    // request('table/new_materials/save', data)
		// .always(function(){
			// msg.remove()
		// })
        // .done(function(response){

			// message('Saved', 2000)

            // EVENTS['MasterSteel.list']();

        // })
// }

// EVENTS['MasterSteel.delete'] = function() {

    // var data = parseForm('MasterSteel.edit'),
		// msg  = message('Deleting...')

    // request('table/new_materials/delete', data)
		// .always(function(){
			// msg.remove()
		// })
        // .done(function(response){

			// message('Deleted', 2000)

            // resetForm('MasterSteel.edit');
            // EVENTS['MasterSteel.list']();

        // })
// }

// EVENTS['form.reset'] = function( elem ) {
	// resetForm( $(elem).attr('form-name') );
// }



$(document).ready(function(){
	$('body').on('mousewheel',function(){
		return false;
	});
	$('body').append('<div class="overlayl"><div class="centermade"><div class="loader"></div>Loading...</div></div>');
	$(document).ajaxStop(function() {
		$('.overlayl').remove();
		$('body').off('mousewheel');
    });
	$(document).on('click','.paginationul a',function(e){
		e.preventDefault();
		var hre = $(this).attr('href'),
			limit = $(this).attr('data-limit'),
		parent = $(this).parents('#filter'),
		fields = $('[form="table.form"]').attr('fields');
		$('#overlay').css('display','block');
		fetchtabledata(parent,parent.attr('fetch-url'),fields,limit,hre);

		
	})
	$(document).on('click','.paginationulorder a',function(e){
		e.preventDefault();
		var hre = $(this).attr('href'),
			limit = $(this).attr('data-limit'),
		parent = $(this).parents('#filter'),
		tbl  = $('[form="table.form"]').attr('table-name'),
		fields = $('[form="table.form"]').attr('fields');
		$('#overlay').css('display','block');
		fetchtabledatapagi(select=null,tbl,fields,limit,hre)
	
	})
	$(document).ajaxError(function(){
		$('.overlayl').hide();
		$('body').off('mousewheel');
	});

	$(document).on('click','.search_filter .searchbtn',function(e){
		e.preventDefault();
		if($('.search_filter input[type="text"]').val() == ''){
			$('.search_filter input[type="text"]').css('border','1px solid red');
		}else{
			$('.search_filter input[type="text"]').css('border','');
			var filterval = $('.search_filter input[type="text"]').val(),
			field =  $('.search_filter .selectfieldsearch').val(),
			table = $('.search_filter').attr('data-connector');
			
			if( field == "cust_name" ) {
				$.post(setting.server+"index.php?url=filtersearch&filterval="+filterval+'&field=customer&table=cust_tbl', function(data){
					
					if( data.list.length > 0 ) {
						for( var i = 0 ; i < data.list.length; i++ ) {
							var customer = data.list[i].customer;
							var cust_id = data.list[i].cust_id;
							$.post(setting.server+"index.php?url=filtersearch&filterval="+cust_id+'&field=cust_id&table='+table, function(data){
								$('#filter table.table').remove();
								$('#filter .pagicommon').remove();
								$('#filter .searchshow').text('"Search : '+filterval+'"');
								$('#filter .cancelthissearch').show();
								
								//Change Customer ID To Customer Name
								if( data.list.length > 0 ) {
									for( var i = 0 ; i < data.list.length; i++ ) {
										
										if( cust_id == data.list[i].cust_id ) {
											data.list[i].cust_id = customer;
										}
										else 
											data.list[i] = [];
										
										
									}
								}
								
								
								$('#filter').append(json2table(data.list,{
									'click': function() {
										var data = $(this).data();

										$(this).closest('tbody').children('tr.active').removeClass('active')
										$(this).closest('tr').addClass('active')

										fillForm('table.form', data, 'var');
										fillForm('table.form', data, 'val');
										$('#filter').slideUp();
									}
								}));
								
							});
							
						}
					}
					
				
				});
			}
			else {
				$.post(setting.server+"index.php?url=filtersearch&filterval="+filterval+'&field='+field+'&table='+table, function(data){
					$('#filter table.table').remove();
					$('#filter .pagicommon').remove();
					$('#filter .searchshow').text('"Search : '+filterval+'"');
					$('#filter .cancelthissearch').show();
					
					//Change Customer ID To Customer Name
					if( data.list.length > 0 ) {
						for( var i = 0 ; i < data.list.length; i++ ) {
							
							var cust_id = data.list[i].cust_id;
							
							
							data.list[i].cust_id = "Temp";
						}
					}
					
					
					$('#filter').append(json2table(data.list,{
						'click': function() {
							var data = $(this).data();

							$(this).closest('tbody').children('tr.active').removeClass('active')
							$(this).closest('tr').addClass('active')

							fillForm('table.form', data, 'var');
							fillForm('table.form', data, 'val');
							$('#filter').slideUp();
						}
					}));
					
				});
			}
			
			

		}
	});

	$(document).on('click','.cancelthissearch',function(){
		EVENTS['table.list']();
		$('[fetch-url]').each(function() {
			var select = $(this),
				url = select.attr('fetch-url'),
				fields = $('[form="table.form"]').attr('fields');
				fetchtabledata(select,url,fields,null,null);
		});

	})

	$(document).on('click','#filter table tbody tr',function(){
		if($('.pagicommon .lastpagina').hasClass('active')){
            if($(this).is(':last-child')){
				$('#next-page').hide();
				$('#prev-page').show();
            }
		}
		if($('.pagicommon .firstpagina').hasClass('active')){
            if($(this).index()==0){
				$('#prev-page').hide();
				$('#next-page').show();
            }
        }
	})




});