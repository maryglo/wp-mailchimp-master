<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       intensevisions.com
 * @since      1.0.0
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/public
 * @author     intensevision <info@intensevision.com>
 */
class Wp_Mailchimp_Master_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * @var
     */
    private $mailchimp;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $mailchimp ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->mailchimp = $mailchimp;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Mailchimp_Master_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Mailchimp_Master_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-mailchimp-master-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Mailchimp_Master_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Mailchimp_Master_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        global $wp_query;
        $post_id = $wp_query->post->ID;
        $opts = wp_mcm_get_options();

        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_script( $this->plugin_name."_jquery_form", plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array('jquery'),$this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-mailchimp-master-public.js', array( 'jquery','jquery-ui-dialog', $this->plugin_name."_jquery_form"), $this->version, false );
        wp_localize_script( $this->plugin_name, 'wc_mcm_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
                   'site_url' => get_site_url(),
                   'subscription_form_option'=>$opts['subscription_popup_option'],
                   'milliseconds'=>$opts['subscription_popup_milliseconds']
            )

        );
	}

    /**
     * @return mixed|Wp_MCM_Container
     */
    protected function get_api() {
        return wp_mcm('api');
    }

    /**
     * add the pop up contents for subscription
     */
    public function add_popup(){
        $lists = $this->mailchimp->get_cached_lists();
        $opts = wp_mcm_get_options();
        include_once( 'partials/wp-mailchimp-master-public-display.php' );
    }

    public function subscribe_to_list() {
        $api = $this->get_api();
        $response = "";
        foreach($_POST['data']['mailchimp_list'] as $list){
            $args = [
                'email_address' => $_POST['data']['mailchimp-email'],
                'status'        => 'subscribed',
                'merge_fields'  => [
                    'FNAME'     => $_POST['data']['firstName'],
                    'LNAME'     => $_POST['data']['lastName']
                ]
            ];
            //$response = $api->add_list_member($list, $args);
        }
        //wp_send_json($response);
    }

    public function create_button(){

        add_shortcode('wp_mcm_subscribe',array($this, 'wp_mcm_shortcode'));
    }

    public function wp_mcm_shortcode($atts = [], $content = null, $tag = ''){
        $atts = array_change_key_case((array)$atts, CASE_LOWER);
        $wporg_atts = shortcode_atts([
           'background-color' => '#000000',
            'text-color'=>'#ffffff',
            'text'=> 'Subscribe'
        ], $atts, $tag);

        $o = '<button class="wp_mcm_wrapper_btn wp_mcm_subscription" type="button" style="padding:15px 25px;border:none;background-color:'.$wporg_atts['background-color'].';color:'.$wporg_atts['text-color'].'">';
        $o.= '<div class="Btn-content">';
        $o.= '<span class="Btn-label">'.$wporg_atts['text'].'</span>';
        $o.= '</div>';
        $o.= '</button>';

        return $o;
    }

}
