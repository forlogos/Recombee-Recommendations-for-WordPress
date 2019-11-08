<?php /**
 * Plugin Name: Recombee Recommendations
 * Description: Use Recombee in your WordPress install for post content
 * Version:     dev
 * Author:      Jason Jalbuena
 * Author URI:  https://jasonjalbuena.com
 * License:     GPLv2 or later
 * Text Domain: recomee-recs
 */

class recombee_rec_plugin {
	private static $instance = null;
	private $plugin_path;
	private $plugin_url;
    private $text_domain = '';
	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	/**
	 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
	 */
	private function __construct() {
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		//load_plugin_textdomain( $this->text_domain, false, $this->plugin_path . '\lang' );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
		//register_activation_hook( __FILE__, array( $this, 'activation' ) );
		//register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		add_action('admin_init', array( $this,'recombee_recs_admin_init'));
		add_action('admin_menu',array( $this,'add_recombeemenu'));

		add_action( 'wp_ajax_get_IDs_to_send_to_recombee', 'get_IDs_to_send_to_recombee' );
		add_action( 'wp_ajax_send_item_to_recombee', 'send_item_to_recombee' );
		add_action( 'wp_ajax_reset_items_interactions', 'reset_items_interactions' );
		add_action( 'wp_ajax_sync_item_props', 'sync_item_props' );
		add_action( 'transition_post_status', 'save_send_to_recombee', 10, 3 );

		add_action('wp_footer', 'footer_js_code', 10 );


		$this->run_plugin();
	}
	public function get_plugin_url() {
		return $this->plugin_url;
	}
	public function get_plugin_path() {
		return $this->plugin_path;
	}
    /**
     * Place code that runs at plugin activation here.
     */
    public function activation() {
	}
    /**
     * Place code that runs at plugin deactivation here.
     */
    public function deactivation() {
	}
    /**
     * Enqueue and register JavaScript files here.
     */
    public function register_scripts($page) {
    	wp_enqueue_script('jquery');
    	wp_enqueue_script('recombee-js-api', 'https://cdn.jsdelivr.net/gh/recombee/js-api-client@2.4.0/dist/recombee-api-client.min.js');

    	//enqueue admin js only on this plugin's admin page
    	if( $page == 'tools_page_recombee-recs' ) {
    		wp_enqueue_script('recombee-recs-adminjs', $this->plugin_url.'js/adminpage.js', null, null, true);
    	}
	}
    /**
     * Enqueue and register CSS files here.
     */
    public function register_styles() {
	}

	//
	public function recombee_recs_admin_init() {
		register_setting('recombee_recs_opts','recombee_recs_options');//set settings for the management page		
	}

	//add a page to the tools admin menu
    public function add_recombeemenu() {
 	   add_management_page( 'Recombee Recommendations', 'Recombee', 'manage_options', 'recombee-recs', 'recombee_recs_page' );
	}

    /**
     * Place code for your plugin's functionality here.
     */
    private function run_plugin() {
    	function recombee_recs_page() {
    		require_once( 'adminpage.php');
    	}

    	//function to add a new item - $id can be a single ID or an array of IDs
    	function send_item_to_recombee( $id ) {
    		require_once( 'vendor/autoload.php');
    		require_once( 'includes/send-items.php');
    	}

    	//function to add a new item - not thru ajax. Same as above, but no echos and no die();
    	function send_item_to_recombee_not_ajax( $id ) {
    		require_once( 'vendor/autoload.php');
    		require_once( 'includes/send-items-not-ajax.php');
    	}

    	//run when a post is saved/updated/changed
    	function save_send_to_recombee(  $new_status, $old_status, $post ) {
	    	if ( 'trash' === $new_status ) {
	    		remove_item_from_recombee( $post->ID );
	    	} else {
	    		send_item_to_recombee_not_ajax( $post->ID );
	    	}
    	}

    	function remove_item_from_recombee( $id ) {
    		require_once( 'vendor/autoload.php');
    		require_once( 'includes/remove-item.php');
    	}

    	//get all IDs to send to recombee
    	function get_IDs_to_send_to_recombee() {
    		require_once( 'includes/get-ids.php');	
    	}

    	//reset all items and interactions
    	function reset_items_interactions() {
    		require_once( 'vendor/autoload.php');
    		require_once( 'includes/reset-items.php');
    	}

    	//reset all items and interactions
    	function sync_item_props() {
    		require_once( 'vendor/autoload.php');
    		require_once( 'includes/sync-props.php');
    	}

    	function footer_js_code() {
    		require_once( 'includes/footer-js.php');
    	}

    	function get_user_ip() {
    		require_once( 'includes/get-user-ip.php');
    	}

        require_once( 'includes/get-recs-shortcodes.php');

	}
}
recombee_rec_plugin::get_instance();