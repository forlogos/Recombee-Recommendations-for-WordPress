<?php //footer js code

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
//only proceed if we are looking at a post type and it is singular
if( is_singular($do_these_types) ) {
	//continue
} else {
	die();//let's stop	
}

//get user id
if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	$user = $current_user->user_email;
} else {//use IP address if user not logged in
	//$user = get_user_ip();
	//use an external API
	echo '<script type="application/javascript">
		var user;
		function getIP(json) {
			user = json.ip;
		}
	</script>
	<script type="application/javascript" src="https://api.ipify.org?format=jsonp&callback=getIP"></script>';
}

//get some vars
$itemId = ( !empty( $o['item-prefix'] ) ? $o['item-prefix'] : '' ).get_the_ID();
$dbname = ( !empty( $o['db-title'] ) ? $o['db-title'] : '' );
$publickey = ( !empty($o['db-pu-token']) ? $o['db-pu-token'] : '' );
$purchase = $o['interactions']['purchase'];
$how = ( !empty( $purchase['how'] ) ? $purchase['how'] : '' );
$scrollpx = ( !empty( (int) $purchase['scrollpx'] ) ? (int) $purchase['scrollpx'] : '0' );
$timeseconds = ( !empty( (int) $purchase['timeseconds'] ) ? (int) $purchase['timeseconds'] : '60' );
$custompujs = ( !empty( $purchase['custompujs'] ) ? $purchase['custompujs'] : '' );
$customjs = ( !empty( $o['interactions']['customjs'] ) ? $o['interactions']['customjs'] : '' );
$purchasejs = "client.send( new recombee.AddPurchase(user, itemid));";
?>


<script>
var client = new recombee.ApiClient('<?php echo $dbname; ?>', '<?php echo $publickey; ?>');

<?php if ( is_user_logged_in() ) {
	echo 'var user = "' . $user . '";';
} ?>
var itemid = '<?php echo $itemId; ?>';
//send the view
client.send( new recombee.AddDetailView( user, itemid ) );

	jQuery(document).ready(function($) {

<?php if( $how == 'scroll') { ?>
	var counter = 1;
	$(window).scroll( function() {
		toScroll = $(document).height() - $(window).height() - <?php echo $scrollpx; ?>;
		if ( $(this).scrollTop() > toScroll && counter == 1 ) {
			<?php echo $purchasejs; ?>
			counter++;
		}
	});
<?php }elseif( $how == 'time' ) { ?>
	setTimeout( function() { 
		<?php echo $purchasejs; ?>
	}, <?php echo $timeseconds; ?>000);
<?php }elseif( $how == 'custom' ) {
	echo $custompujs;
} ?>

$('h2 a').css( 'color', 'red' );
	});

</script>

<pre>
<?php echo print_r($o);
echo '$customjs: ' . $customjs .'<br/>';

?>
</pre>