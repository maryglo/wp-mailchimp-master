<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       intensevisions.com
 * @since      1.0.0
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/includes
 * @author     intensevision <info@intensevision.com>
 */
class WP_Mailchimp_Master {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Mailchimp_Master_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-mailchimp-master';

        // Define constants
        $this->define_contants();

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();


	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Mailchimp_Master_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Mailchimp_Master_i18n. Defines internationalization functionality.
	 * - Wp_Mailchimp_Master_Admin. Defines all hooks for the admin area.
	 * - Wp_Mailchimp_Master_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
        //require_once WP_MCM_PLUGIN_DIR . '/autoloader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';

        global $wp_mcm;

        $wp_mcm = wp_mcm();
        $wp_mcm['api'] = 'wp_mcm_get_api_v3';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-mailchimp-master-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-mailchimp-master-i18n.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-mcm-admin-messages.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-mcm-mailchimp.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-mailchimp-master-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-mailchimp-master-public.php';

		$this->loader = new Wp_Mailchimp_Master_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Mailchimp_Master_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Mailchimp_Master_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
        $messages = new Wp_MCM_Admin_Messages();
        $mailchimp = new Wp_MCM_MailChimp();

		$plugin_admin = new Wp_Mailchimp_Master_Admin( $this->get_plugin_name(), $this->get_version(), $messages, $mailchimp );

        // Initialize cron
        $this->loader->add_action( 'init', $plugin_admin, 'wp_mcm_set_cron' );

        //register settings
        $plugin_admin->register_general_settings();
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        //add admin menu
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_plugin_custom_box' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'wp_mcm_save_postdata' );

        //on publish post
        $this->loader->add_action( 'publish_post', $plugin_admin, 'wp_mcm_publish_post', 10, 2 );

        //cron
        $this->loader->add_action( 'wp_mcm_cron_create_campaign', $plugin_admin, 'wp_mcm_cron_create_campaign', 10,1 );

        //ajax hooks
        $this->loader->add_action('wp_ajax_create_campaign',$plugin_admin, 'wp_mcm_process_campaign');


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
        $mailchimp = new Wp_MCM_MailChimp();

		$plugin_public = new Wp_Mailchimp_Master_Public( $this->get_plugin_name(), $this->get_version(), $mailchimp );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        $this->loader->add_action('init',$plugin_public, 'create_button');

        //ajax hooks
        $this->loader->add_action('wp_ajax_subscribe_to_list',$plugin_public, 'subscribe_to_list');
        $this->loader->add_action('wp_ajax_nopriv_subscribe_to_list',$plugin_public, 'subscribe_to_list');

        //update footer
        $this->loader->add_action( 'wp_footer', $plugin_public, 'add_popup' );


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Mailchimp_Master_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    public function define_contants(){
        define( 'WP_MCM_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
    }

}
