<?php global $wpdb;

//get saved options of this plugin
$o=get_option('recombee_recs_options');

//set array of post types to include
$do_these_types = array();

//loop thru saved options and get the post types we are going to do
foreach( $o['posttype'] as $pt=>$vals ) {
	if( !empty($vals['include']) && $vals['include'] == 'yes' ) {
		$do_these_types[] = $pt;
	}
}

//set placeholders - from https://coderwall.com/p/zepnaw/sanitizing-queries-with-in-clauses-with-wpdb-on-wordpress
$placeholders = array_fill(0, count($do_these_types), '%s');
$format = implode(', ', $placeholders);

//now get the post IDs
$postids = $wpdb->get_col( $wpdb->prepare( 
	"
	SELECT      ID
	FROM        $wpdb->posts
	WHERE       post_status = 'publish' 
   	        AND post_type IN ( $format )
	",
	$do_these_types
) );

if( $postids ) {
	//get the batch size
	$chunksize = ( !empty( $o['batch_size'] ) ? $o['batch_size'] : 25 );
	$chunksize = (int) $chunksize;
	$chunksize = ( !empty( $chunksize ) && $chunksize!= 0 ? $chunksize : 25 );

	//make a bunch of smaller arrays
	$postids = array_chunk( $postids, $chunksize);

	//add the array count to the array
	$postids['ct'] = count($postids);

	echo json_encode($postids);
} else {
	echo 'none';
}
die();//needed