<?php //to remove a single item from Recombee
//use within remove_item_from_recombee( $id )

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;

//get saved options of this plugin
$o=get_option('recombee_recs_options');

//start the recombee client
$client = new Recombee\RecommApi\Client( $o['db-title'], $o['db-pr-token']);

//get the $itemId
$itemId = ( !empty( $o['item-prefix'] ) ? $o['item-prefix'] : '' ) . $id;

//remove item from Recombee database
try {
	$result = $client->send(new Reqs\DeleteItem($itemId));
}
catch(Ex\ApiException $e) {
   	//do nothing
}