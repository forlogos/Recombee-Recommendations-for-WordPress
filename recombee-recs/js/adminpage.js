	$( document ).ready(function() {

$( ".showcustom" ).click(function() {
	$(this).next().toggle();
});

//get all the ids in groups
$('#sync_all').click(function() {
    $('#sync_all_status').html('Processing...');
    var data = {
        'action': 'get_IDs_to_send_to_recombee'
    };
    jQuery.post(ajaxurl, data, function(response) {
        process_ID_array( response );
    });
});

function process_ID_array(response) {
	var obj = jQuery.parseJSON( response );

	var i = 1, status;
	$.each( obj, function( key, value ) {

		if( $.isNumeric(key)) {
			var data = {
	        	'action': 'send_item_to_recombee',
        		'what': value
    		};

    		setTimeout( function() {

    			jQuery.post(ajaxurl, data, function(response) {
    				console.log( response );
    				status = i / obj.ct;
    				status = status * 100;
    				status = parseInt(status);
         			$('#sync_all_status').html('Processing... ' + status + '% completed.');
	         		i++;
	         		if( $('#sync_all_status').html() == 'Processing... 100%') {
    					$('#sync_all_status').html('Complete. 100% Success. Done!');
    				}
    			}).fail(function(xhr, status, error) {
        			console.log(JSON.stringify(xhr,null,1).replace(/[^\w\s]/g,''));

        			$('#sync_all_status').parent().append( '<br/>' + xhr.status + ' ' + status + ': ' + error + '. ' + xhr.responseText );
        		});
    		},
    		500 );
    	}
	});
}

//clear the whole Recombee db
$('#rem_all').click(function() {
	var txt;
	var r = confirm("Do you really want to reset the Recombee database?");
	if (r == true) {
		var data = {
        	'action': 'reset_items_interactions'
    	};
    	jQuery.post(ajaxurl, data, function(response) {
    		alert( 'Recombee database cleared' );
    	});
	}
});

//clear the whole Recombee db
$('#sync_item_props').click(function() {
    var data = {
        'action': 'sync_item_props'
    };
    jQuery.post(ajaxurl, data, function(response) {
        alert( 'Properties synced.' );
        console.log( response );
    });
});


	});