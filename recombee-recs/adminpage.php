<div class="wrap">
	<h1>Recombee Recommendations</h1>


	<p>Syncing to an empty Recombee database almost always results in an error. Do another sync after.</p>
	<p><span id="sync_all" class="spanlink">Sync all content with Recombee.</span></p>

	<p><span id="sync_all_status"></span></p>

	<p><span id="rem_all" class="spanlink">Remove all items and user interactions from Recombee</span></p>

	<form method="post" action="options.php">
		<?php settings_fields( 'recombee_recs_opts' );
		$o=get_option('recombee_recs_options'); ?>
		<h2>Sync Options</h2>
		 <table class="form-table" role="presentation"><tbody>
			<tr>
				<th scope="row">Sync Batch Size*</th>
				<td>
					<input id="batch_size" name="recombee_recs_options[batch_size]" size="40" type="text" value="<?php echo ( !empty( $o['batch_size'] ) ? $o['batch_size'] : '' ); ?>"><br/>
					* optional. Items/posts are synced with Recombee in batches. This is how many posts at a time are sent to Recombee as items. You can change the batch size to suit your sever. The default size (if left blank) is 25. 
				</td>
			</tr>
		</tbody></table>

		<h2>Recombee Database</h2>
		<p>These should match the data found in the Recombee admin under settings for the database you want to use.</p>
		<table class="form-table" role="presentation"><tbody>
			<tr>
				<th scope="row">Database Title</th>
				<td><input id="db_title" name="recombee_recs_options[db-title]" size="40" type="text" value="<?php echo ( !empty( $o['db-title'] ) ? $o['db-title'] : '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row">Database Private token</th>
				<td><input id="db_pr_token" name="recombee_recs_options[db-pr-token]" size="40" type="text" value="<?php echo ( !empty($o['db-pr-token']) ? $o['db-pr-token'] : '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row">Database Public token</th>
				<td><input id="db_pu_token" name="recombee_recs_options[db-pu-token]" size="40" type="text" value="<?php echo ( !empty($o['db-pu-token']) ? $o['db-pu-token'] : '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row">Site Name*</th>
				<td><input id="sitename" name="recombee_recs_options[sitename]" size="40" type="text" value="<?php echo ( !empty($o['sitename']) ? $o['sitename'] : '' ); ?>"><br/>* optional. If you want to add a field for sitename. Useful if you are using your Recombee database for multiple sites and want to get recomendations for other sites.</td>
			</tr>
			<tr>
				<th scope="row">Item ID Prefix*</th>
				<td><input id="item_prefix" name="recombee_recs_options[item-prefix]" size="40" type="text" value="<?php echo ( !empty($o['item-prefix']) ? $o['item-prefix'] : '' ); ?>"><br/>* optional. If you want to add a prefix to your item IDs. If left blank, item IDs will be the Post IDs. Will be helpful if you are sending items from multiple WordPress installs to one Recombee database.</td>
			</tr>
		</tbody></table>
		<p class="submit">
			<input type="submit" class="button-primary" value="Save" />
		</p>

		<h2>Post Types to include</h2>
		<p>All taxonomies for the selected post types will be included</p>
		<ul>
			<?php $post_types = get_post_types( array('public' => true) ); 
			foreach ( $post_types  as $post_type ) {
				if( !empty( $o['posttype'][$post_type]['include'] ) && $o['posttype'][$post_type]['include'] == 'yes') {
					$checked = 'checked';
				}else{
					$checked = '';
				}
				$postmeta = ( !empty( $o['posttype'][$post_type]['postmeta'] ) ? $o['posttype'][$post_type]['postmeta'] : '');
				$customname = ( !empty( $o['posttype'][$post_type]['customname'] ) ? $o['posttype'][$post_type]['customname'] : '');
				$customID = ( !empty( $o['posttype'][$post_type]['customID'] ) ? $o['posttype'][$post_type]['customID'] : '');
				echo '<li>
					<input type="checkbox" name="recombee_recs_options[posttype][' . $post_type . '][include]" value="yes" id="pt' . $post_type . '" ' . $checked . '>
					<label for="pt' . $post_type . '"><strong>' . $post_type . '</strong></label>

					<span class="showcustom">Post Meta/Custom Fields options</span>
					<div class="customoptions" style="display:none;">
						Post Meta (Custom Fields) to include
						<input type="text" name="recombee_recs_options[posttype][' . $post_type . '][postmeta]" value="' . $postmeta . '" class="wideinput">
						enter a comma separated list of post meta (meta keys) to include 
					</div>';
				echo '</li>';
			} ?>
		</ul>

		<p class="submit">
			<input type="submit" class="button-primary" value="Save" />
		</p>

		<h2>Interaction Settings</h2>

		<p><strong>Detail Views.</strong> Every single page view is sent as a Detail View interaction.</p>

		<p><strong>Purchases.</strong> How should a single page be considered a purchase?</p>
		<ul>
			<li>
				<?php $checked = ( !empty( $o['interactions']['purchase']['how'] ) && $o['interactions']['purchase']['how'] == 'scroll' ? 'checked' : '' ); 

				$scrollpx = ( !empty( (int) $o['interactions']['purchase']['scrollpx'] ) ? (int) $o['interactions']['purchase']['scrollpx'] : '' );

				$timeseconds = ( !empty( (int) $o['interactions']['purchase']['timeseconds'] ) ? (int) $o['interactions']['purchase']['timeseconds'] : '' );

				$custompujs = ( !empty( $o['interactions']['purchase']['custompujs'] ) ? $o['interactions']['purchase']['custompujs'] : '' );

				$customjs = ( !empty( $o['interactions']['customjs'] ) ? $o['interactions']['customjs'] : '' );

				 ?>

				<input type="radio" name="recombee_recs_options[interactions][purchase][how]" value="scroll" id="int-pur-scroll" <?php echo $checked; ?>>
				By scroll position: when the user scrolls to the bottom of the page. Offset this by this many pixels: 
				<input type="text" name="recombee_recs_options[interactions][purchase][scrollpx]" value="<?php echo $scrollpx; ?>" id="int-pur-scrollpx">
			</li>
			<li>
				<?php $checked = ( !empty( $o['interactions']['purchase']['how'] ) && $o['interactions']['purchase']['how'] == 'time' ? 'checked' : '' ); ?>
				<input type="radio" name="recombee_recs_options[interactions][purchase][how]" value="time" id="int-pur-time" <?php echo $checked; ?>>
				By time: after the user views the page for 
				<input type="text" name="recombee_recs_options[interactions][purchase][timeseconds]" value="<?php echo $timeseconds; ?>" id="int-pur-timeseconds"> 
				seconds. Leaving this blank will set it to the default of 60 seconds
			</li>
			<li>
				<?php $checked = ( !empty( $o['interactions']['purchase']['how'] ) && $o['interactions']['purchase']['how'] == 'custom' ? 'checked' : '' ); ?>
				<input type="radio" name="recombee_recs_options[interactions][purchase][how]" value="custom" id="int-pur-custom" <?php echo $checked; ?>>
				Custom JS. You can use your own custom javascript for implementing your own purchase interaction. Refer to the <a href="https://docs.recombee.com/api.html#purchases" target="blank">Recombee API Reference for purchases</a>.<br/>
				<textarea name="recombee_recs_options[interactions][purchase][custompujs]" value="" id="int-pur-custompujs"><?php echo $custompujs; ?></textarea></li>
		</ul>

		<p><strong>Custom Interactions.</strong> You can add your own custom js for other interactions. Refer to the <a href="https://docs.recombee.com/api.html#user-item-interactions" target="blank">Recombee API Reference for Interactions</a>.</p>
		<textarea name="recombee_recs_options[interactions][customjs]" value="" id="int-customjs"><?php echo $customjs; ?></textarea>

		<p>Note: When using custom js, use the var 'user' for the user ID and the var 'itemid' for the itemid. So your code that sends interactions to Recombee will look like:<br/><br/>
		<em>client.send( new recombee.AddPurchase(user, itemid));</em>
		</p>

		<p class="submit">
			<input type="submit" class="button-primary" value="Save" />
		</p>
	</form>
	<style type="text/css">
		.spanlink {cursor:pointer;text-decoration:underline;color:blue;}
		.showcustom {cursor:pointer;text-decoration:underline;}
		.customoptions {border:1px solid black;margin:5px;padding:5px 10px;}
		.customoptions input {display:block;}
		.wideinput {width:100%;}
	</style>
</div>