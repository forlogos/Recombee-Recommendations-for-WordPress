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

//Clear everything from the database
$client->send(new Reqs\ResetDatabase());

die();//required