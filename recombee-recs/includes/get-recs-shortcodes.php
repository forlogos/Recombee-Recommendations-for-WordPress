<?php //shortcodes to get recommendations

function recombee_recs_fn( $atts ) {
	$args = shortcode_atts(
		array(
			'type' => 'items to user',
			'scenario' => '',
			'logic' => '',
			'count' => 5,
			'filter' => '',
			'booster' => '',
			'properties' => ''
		),
		$atts,
		'recombee-recs'
	);

	if( !empty( $atts['type'] ) && $atts['type'] == 'items to user' ) {
		$type = 'RecommendItemsToUser(';
	} else {
		$type = 'RecommendItemsToItem( itemid, ';
	}

	$count = ( !empty( $atts['count'] ) && !empty( $atts['count'] ) != '' ? !empty( $atts['count'] ) : '5');

	$scenario = ( !empty( $atts['scenario'] ) && !empty( $atts['scenario'] ) != '' ? !empty( $atts['scenario'] ) : '');

	$logic = ( !empty( $atts['logic'] ) && !empty( $atts['logic'] ) != '' ? !empty( $atts['logic'] ) : '');

	$booster = ( !empty( $atts['booster'] ) && !empty( $atts['booster'] ) != '' ? !empty( $atts['booster'] ) : '');

	if( !empty( $atts['filter'] ) ) {
		$filter = $atts['filter'];
	} else {
		$filter = ' \'post_title\' != null AND \'post_status\' == \"publish\" ';
	}

	$filter = apply_filters('recombee_recs_filter', $filter, $scenario );

	if( !empty( $atts['properties'] ) ) {
		$properties = $atts['properties'];
	} else {
		$properties = "['post_title', 'permalink', 'img']";
	}

	$properties = apply_filters('recombee_recs_properties', $properties, $scenario );

	$script = '<script type="text/javascript">
function showRec' . $scenario . '(post_title, permalink, img){
		return [
			\'<li><a href="\' + permalink + \'">\' + img + post_title + \'</a></li>\'
		].join("\n");
	}

	// Request recommended items
	client.send(new recombee.'. $type .' user, '. $count .',
      {
        returnProperties: true,
        includedProperties: ' . $properties . ',
        filter: "'. $filter .'",
        '. ( $logic != '' ? 'filter: "'. $filter .'", ' : '') .'
        '. ( $booster != '' ? 'filter: "'. $booster .'", ' : '') .'
        '. ( $scenario != '' ? 'scenario: "'. $scenario .'" ' : '') .'
      }),
      (err, resp) => {
        if(err) {
          console.log("Could not load recomms: ", err);
          return;
        }
        // Show recommendations
        var recomms_html = resp.recomms.map(r => r.values).
                    map(vals => showRec' . $scenario . '( vals[\'post_title\'], vals[\'permalink\'],
                        vals[\'img\'] ));
        document.getElementById("recombee_recs_' . $scenario . '").innerHTML = recomms_html.join("\n");
        console.log("shouldwork");
      }
    );
	</script>';

	$return = 'output shorthoce here
	<ul id="recombee_recs_' . $scenario . '"></ul>';

	

	add_action( 'wp_footer', function() use( $script ){
		echo $script;
	}, 10, 999);

	return $return;

}
add_shortcode('recombee-recs','recombee_recs_fn');