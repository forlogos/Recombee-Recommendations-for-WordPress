<?php //to send a single item to Recombee -
//use within send_item_to_recombee( $id )
//$id can be a single ID or an array of IDs

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;

//get saved options of this plugin
$o=get_option('recombee_recs_options');

//start the recombee client
$client = new Recombee\RecommApi\Client( $o['db-title'], $o['db-pr-token']);

// Add properties of items - these are the standard properties per the wp posts table and a few others to make getting recommendations easier
//first make an array of property names and their types
$properties = array(
	'post_author' => 'string',
	'post_date' => 'timestamp',
	'post_content' => 'string',
	'post_title' => 'string',
	'post_type' => 'string',
	'post_status' => 'string',
	'permalink' => 'string',
	'img' => 'string'
);

//check if we are also using sitename and include it if so
if( !empty( $o['sitename'] ) && $o['sitename'] != '' ) {
	$properties['sitename'] = 'string';
}

//loop thru every posttype
foreach( $o['posttype'] as $name=>$pt ) {
	if( !empty( $pt['include'] ) && $pt['include'] == 'yes' ) {

		//get taxonomies for this posttype
		$taxonomies = get_object_taxonomies( $name );

		foreach( $taxonomies as $t ) {
			//set this taxonomy as a property for Recombee
			$properties[$t] = 'set';
		}

		//check if sending custom post meta
		if( !empty( $pt['postmeta'] ) && $pt['postmeta'] != '') {

			//get custom post meta keys to be sent
			$custompm = explode( ',' , $pt['postmeta'] ); 

			foreach( $custompm as $pm ) {
				//set this post meta as a property for Recombee
				$properties[$pm] = 'string';
			}
		}
	}

}

foreach( $properties as $p=>$type ) {
	//$client->send(new Reqs\DeleteItemProperty($p));
	$client->send(new Reqs\AddItemProperty( $p, $type ) );
}
echo 'done';
die();//required