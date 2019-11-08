<?php //to send a single item to Recombee -
//use within send_item_to_recombee( $id )
//$id can be a single ID or an array of IDs

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;

if( !empty( $_POST['what'] ) && $_POST['what'] != '' ) {
	$id = $_POST['what'];
}

if( is_array( $id) ) {
	//do nothing
} else {
	//let's make it an array!
	$id = array( $id );
}

//get saved options of this plugin
$o=get_option('recombee_recs_options');

//start the recombee client
//$client = new Recombee\RecommApi\Client( $o['db-title'], $o['db-pr-token']);
$client = new Client( $o['db-title'], $o['db-pr-token']);

//array where we will add all post data to send to Recombee
$requests = array();

//process each $id, $sid = single id
foreach( $id as $sid) {

	//get the post type of the item
	$post_type = get_post_type( $sid );

	//make sure we want to send this post type to Recombee
	if( !empty( $o['posttype'][$post_type]['include'] ) && $o['posttype'][$post_type]['include'] == 'yes') {
		//let's proceed

		//get all post info
		$info = get_post( $sid, 'ARRAY_A' );

		//array of individual post data
		$postdata = array(
			'post_author' => $info['post_author'],
			'post_date' => $info['post_date'],
			'post_content' => $info['post_content'],
			'post_title' => $info['post_title'],
			'post_type' => $info['post_type'],
			'post_status' => $info['post_status'],
			'permalink' => get_permalink( $sid )
		);

		//include sitename 
		if( !empty( $o['sitename'] ) && $o['sitename'] != '' ) {
			$postdata['sitename'] = $o['sitename'];
		}

		//include post featured image
		$img = get_the_post_thumbnail( $sid );
		$img = apply_filters('recombee_recs_img', $img, $sid );
		if( !empty( $img ) && $img != '' ) {
			$postdata['img'] = $img;
		}

		//get taxonomies of this post
		$taxonomies = get_post_taxonomies( $sid );

		foreach( $taxonomies as $t ) {

			//get terms for each taxonomy
			$terms = wp_get_post_terms( $sid, $t, array("fields" => "names") );

			//get the terms into json
			$terms = json_encode($terms);

			//save it to array of individual post data
			$postdata[$t] = $terms;
		}

		//check if sending custom post meta
		if( !empty( $o['posttype'][$post_type]['postmeta'] ) && $o['posttype'][$post_type]['postmeta'] != '') {

			//get custom post meta keys to be sent
			$custompm = explode( ',' , $o['posttype'][$post_type]['postmeta'] ); 

			foreach( $custompm as $pm ) {
				//get the value for each post meta key found above
				$pmdata = get_post_meta( $sid, $pm, true );

				//save it to array of individual post data
				$postdata[$pm] = $pmdata;	
			}
		}

		//make the itemID
		$itemId = ( !empty( $o['item-prefix'] ) ? $o['item-prefix'] : '' ) . $info['ID'];
		
		//setup all item data
		$r = new Reqs\SetItemValues(
			$itemId,
			//values:
			$postdata,
			//optional parameters:
			['cascadeCreate' => true] // Use cascadeCreate for creating item
                                 // with given itemId, if it doesn't exist]
		);

		//add item to the array of items
		array_push($requests, $r);

		//let's reset the $postdata array
		$postdata = array();

	} else {
		echo '"' . get_the_title( $sid ) . '" has a post of type "'. $post_type . '" and will not be sent to Recombee.';
	}
}

//now send all the items to Recombee
try {
	$result = $client->send(new Reqs\Batch($requests));
	//output results to console, for debugging
	foreach( $result as $r=>$rr ) {
		echo "item " . $r . " send " . $rr['json'] . "\n\r";
	}
}
catch(Ex\ApiTimeoutException $e) {
   	//Handle timeout => use fallback
   	echo 'error: timeout error';
   	echo print_r($e);
}
catch(Ex\ResponseException $e) {
   	//Handle errorneous request => use fallback
   	echo 'error: ResponseException';
   	echo print_r($e);
}
catch(Ex\ApiException $e) {
   	//ApiException is parent of both ResponseException and ApiTimeoutException
   	echo print_r($e);
}

die();//required