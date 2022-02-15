var ctype = {
    testers : {
        otp		: /^([\d]*)/,
        alnum	: /^([\w\d]*)/i,
        alpha	: /^([\w]*)/i,
        number	: /^([\d]*)/,
        mobile	: /^([7-9][\d]*)/
    },

    types : {
        'password'	: 'password',
        'otp'		: 'tel',
        'mobile'	: 'tel',
        'number'	: 'tel'
    },

    lengths : {
        mobile	: 10
    }
}


$(function(){

	// c-type
	$('[ctype]').on('keydown', function(e){

        // non printable keys
        if (e.keyCode < 32) return;

        var input  = $(this),
            key    = e.keyCode,
            mask   = input.attr('ctype'),
            value  = input.val() + String.fromCharCode( key ),
            length = input.attr('length') || ctype.lengths[ mask ]

        var matched = ctype.testers[ mask ].test( value ) ? value.match(ctype.testers[ mask ])[0] : '';

        // length
        if (length && matched.length > length) matched = matched.substr(0, length);

        // filtered value
        input.val( matched )

        return false;
    })

});