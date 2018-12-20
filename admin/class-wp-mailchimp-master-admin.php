<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       intensevisions.com
 * @since      1.0.0
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/admin
 * @author     intensevision <info@intensevision.com>
 */

class Wp_Mailchimp_Master_Admin {

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
    protected $messages;

    /**
     * @var
     */
    protected $mailchimp;

    /**
     * @var
     */
    protected $post_page_fields;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version, $messages, $mailchimp ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->messages = $messages;
        $this->mailchimp = $mailchimp;

    }

    /**
     * Register the stylesheets for the admin area.
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
        wp_enqueue_style( $this->plugin_name."-bootstrap", plugin_dir_url( __FILE__ ) . 'css/bootstrap/css/bootstrap.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-mailchimp-master-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name."-datetimepicker", plugin_dir_url( __FILE__ ) . 'css/bootstrap-datetimepicker.min.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
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
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_script( $this->plugin_name.'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap/js/bootstrap.min.js', array('jquery'), $this->version, 'all' );
        wp_enqueue_script( $this->plugin_name.'datetimepicker', plugin_dir_url( __FILE__ ) . 'js/bootstrap-datetimepicker.min.js', array( 'jquery',$this->plugin_name.'bootstrap'), $this->version, false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-mailchimp-master-admin.js', array( 'jquery',$this->plugin_name.'bootstrap'), $this->version, false );

    }

    /**
     * general settings of the plugin
     */
    public function register_general_settings(){
        // register settings
        register_setting( 'wp_mcm_settings', 'wp_mcm', array( $this, 'save_general_settings' ) );
    }

    /**
     * @param array $settings
     * @return array
     */
    public function save_general_settings( array $settings ) {

        $current = wp_mcm_get_options();

        // merge with current settings to allow passing partial arrays to this method
        $settings = array_merge( $current, $settings );

        // Make sure not to use obfuscated key
        if( strpos( $settings['api_key'], '*' ) !== false ) {
            $settings['api_key'] = $current['api_key'];
        }

        // Sanitize API key
        $settings['api_key'] = sanitize_text_field( $settings['api_key'] );

        // if API key changed, empty MailChimp cache
        if ( $settings['api_key'] !== $current['api_key'] ) {
            // $this->mailchimp->empty_cache();
        }


        /**
         * Runs right before general settings are saved.
         *
         * @param array $settings The updated settings array
         * @param array $current The old settings array
         */
        do_action( 'wp_mcm_save_settings', $settings, $current );
        return $settings;
    }

    public function add_plugin_admin_menu(){
        add_options_page(
            'WP Mailchimp Master',
            'WP Mailchimp Master',
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page'),
            'none'
        );
    }

    /**
     * plugin setting page setup
     */
    public function display_plugin_setup_page() {
        $opts = wp_mcm_get_options();

        $connected = ! empty( $opts['api_key'] );

        if($connected){
            try {
                $connected = $this->get_api()->is_connected();
            } catch( WP_MCM_API_Connection_Exception $e ) {
                $message = sprintf( "<strong>%s</strong> %s %s ", __( "Error connecting to MailChimp:", 'mailchimp-for-wp' ), $e->getCode(), $e->getMessage() );

                if( is_object( $e->data ) && ! empty( $e->data->ref_no ) ) {
                    $message .= '<br />' . sprintf( __( 'Looks like your server is blocked by MailChimp\'s firewall. Please contact MailChimp support and include the following reference number: %s', 'wp-mailchimp-master' ), $e->data->ref_no );
                }

                $this->messages->flash( $message, 'error' );
                $connected = false;
            } catch( Wp_Mcm_Exception $e ) {
                $this->messages->flash( sprintf( "<strong>%s</strong><br /> %s", __( "MailChimp returned the following error:", 'wp-mailchimp-master' ), $e ), 'error' );
                $connected = false;
            }
        }

        $lists = $this->mailchimp->get_mailchimp_lists();
        $templates = $this->mailchimp->get_templates();
        $obfuscated_api_key = wp_mcm_obfuscate_string( $opts['api_key'] );
        include_once( 'partials/wp-mailchimp-master-admin-display.php' );
    }

    /**
     * @return MC4WP_API_v3
     */
    protected function get_api() {
        return wp_mcm('api');
    }

    /**
     * custom metabox
     */
    public function add_plugin_custom_box(){
        $screens = ['post', 'page'];
        foreach ($screens as $screen) {
            add_meta_box(
                'wp_mcm_box_id',           // Unique ID
                'Mailchimp Options',  // Box title
                array($this, 'wp_mcm_custom_box_html'),
                $screen,                   // Post type
                'normal',
                'high'
            );
        }
    }

    /**
     * @param $postID
     * @return array
     *  get meta values for mailchimp master post/page meta fields
     */
    public function wp_mcm_post_page_meta($postID) {
        $default  = $this->get_default_settings();
        $opts = wp_mcm_get_options();
        $meta_values = get_post_meta($postID, 'wp_mcm_settings', true);
        $meta_fields = array();

        foreach($default as $key=>$value){
            if(!empty($meta_values) && isset($meta_values[$key])){
                $meta_fields[$key] = $meta_values[$key];
            } else {
                $default_key = $value['default'];
                $meta_fields[$key] =  isset($value['default']) && !empty($default_key) ? $opts[$default_key] : "";
            }

        }

        return $meta_fields;
    }

    /**
     * @param $post
     */
    public function wp_mcm_custom_box_html($post){
        $lists = $this->mailchimp->get_mailchimp_lists();
        $templates = $this->mailchimp->get_templates();
        $mcm_meta_values = $this->wp_mcm_post_page_meta($post->ID);

        ?>
        <div class="inside">
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_templatefield">Template</label></p>
            <select name="wp_mcm_templatefield" id="wp_mcm_templatefield" class="postbox">
                <option value="">Mailchimp Options</option>
                <?php
                foreach ( $templates as $list ) {
                    ?>
                    <option value="<?php echo $list->id;?>" <?php selected($mcm_meta_values['wp_mcm_templatefield'],$list->id); ?>><?php echo esc_html( $list->name ); ?></option>
                <?php } ?>
            </select>
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_listfield">List</label></p>
            <select name="wp_mcm_listfield[]" id="wp_mcm_listfield" class="postbox" multiple>
                <option value="">Select a List</option>
                <?php
                foreach ( $lists as $list ) {
                       $selected = in_array($list->id, $mcm_meta_values['wp_mcm_listfield']) ? "selected='selected'": "";
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $list->id;?>"><?php echo esc_html( $list->name ); ?></option>
                <?php } ?>
            </select>
            <hr>
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_listfield">Campaign Settings</label></p>
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_templatefield">From Name</label></p>
            <input type="text" name="wp_mcm_from_name" id="wp_mcm_from_name" size="25" autocomplete="off" value="<?php echo $mcm_meta_values['wp_mcm_from_name']; ?>" style="margin-bottom: 12px;">
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_templatefield">Reply-to email address</label></p>
            <input type="text" name="wp_mcm_reply_to" id="wp_mcm_reply_to" size="25" autocomplete="off" value="<?php echo $mcm_meta_values['wp_mcm_reply_to']; ?>" style="margin-bottom: 12px;">
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_templatefield">Subject Line</label></p>
            <input type="text" name="wp_mcm_subject_line" id="wp_mcm_subject_line" size="25" autocomplete="off" value="<?php echo $mcm_meta_values['wp_mcm_subject_line'];?>" style="margin-bottom: 12px;">
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_templatefield">Campaign Title</label></p>
            <input type="text" name="wp_mcm_campaign_title" id="wp_mcm_campaign_title" size="25" autocomplete="off" value="<?php echo $mcm_meta_values['wp_mcm_campaign_title']; ?>" style="margin-bottom: 12px;">
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="wp_mcm_templatefield">Update campaign's content?</label></p>
            <select name="wp_mcm_overrridecontent" id="wp_mcm_overrridecontent" class="postbox" style="margin-bottom: 0">
                <option value="yes" <?php selected($mcm_meta_values['wp_mcm_overrridecontent'],"yes"); ?>>YES</option>
                <option value="no" <?php selected($mcm_meta_values['wp_mcm_overrridecontent'],"no"); ?>>NO</option>
            </select>
            <p class="howto" id="new-tag-post_tag-desc">If you use a 'user template', be sure that the template's content section is called 'main' so that your post's content can be substituted in the template.</p>
            <p class="post-attributes-label-wrapper" style="margin-top:20px"><label class="post-attributes-label" for="wp_mcm_templatefield">Automatically send the campaign?</label></p>
            <fieldset><legend class="screen-reader-text"></legend>
                <label><input type="radio" class="campaign_create_option" name="wp_mcm_automatic_send" value="automatic" <?php checked($mcm_meta_values['wp_mcm_automatic_send'],"automatic"); ?>/>Automatic (When post/page is published )</label><br>
                <label><input type="radio" class="campaign_create_option" name="wp_mcm_automatic_send" value="specific_datetime" <?php checked($mcm_meta_values['wp_mcm_automatic_send'],"specific_datetime"); ?>/><span>Specific period of time:</span></label>&nbsp;<input type="text" name="wp_mcm_campaign_datetime" id="wp_mcm_campaign_datetime" value="<?php echo $mcm_meta_values['wp_mcm_campaign_datetime'];?>" style="<?php if($mcm_meta_values['wp_mcm_automatic_send'] != 'specific_datetime') { ?>display: none<?php } ?>"/>
            </fieldset>
        </div>
    <?php
    }

    /**
     * get defaults settings
     * @return mixed
     */
    public function get_default_settings(){
        $defaults = require WP_MCM_PLUGIN_DIR . 'config/post_page_settings.php';
        return $defaults;
    }

    /**
     * @param $post_id
     */
    public function wp_mcm_save_postdata($post_id) {
        $defaults = $this->get_default_settings();
        $options = array();

        foreach($defaults as $key=>$value){
            if (array_key_exists($key, $_POST)) {
                $options[$key] = $_POST[$key];
            }
        }
        //$options = array_merge($defaults, $options);
        update_post_meta(
            $post_id,
            'wp_mcm_settings',
            $options
        );

    }

    /**
     * @param $data
     * @param $sent
     * @param $post
     */
    public function save_campaign_data($data, $sent, $post){
        $meta_values = get_post_meta($post->ID, 'wp_mcm_campaign_data', true);
        $new_data = array(
            'campaign_id'=>$data->id,
            'sent'=>$sent
        );
        if(empty($meta_values)){
            $meta_values = $new_data;
        } else {
            $meta_values[] = $new_data;
        }

        update_post_meta(
            $post->ID,
            'wp_mcm_campaign_data',
            $meta_values
        );
    }

    /**
     * set the cron schedule based on settings
     */
    public function wp_mcm_set_cron(){
        $first_run = "";
        wp_clear_scheduled_hook('wp_mcm_cron_create_campaign');
        wp_schedule_single_event($first_run, 'wp_mcm_cron_create_campaign');
    }

    /**
     * @param $ID
     * @param $post
     */
    public function wp_mcm_publish_post($ID, $post){
        $this->wp_mcm_save_postdata($ID);
        $mcm_meta_values = $this->wp_mcm_post_page_meta($ID);

        //check if automatic creation of campaign or not
        if($mcm_meta_values['wp_mcm_automatic_send'] == 'automatic'){
            $this->wp_mcm_process_campaign($post);
        } else {
            //get selected datetime
            $date_time = $mcm_meta_values['wp_mcm_campaign_datetime'];
            //clear previous cron
            wp_clear_scheduled_hook('wp_mcm_cron_create_campaign', $post);
            //setup cron
            wp_schedule_single_event(strtotime($date_time), 'wp_mcm_cron_create_campaign', $post);
        }

    }

    public function wp_mcm_cron_create_campaign($post) {
        $this->wp_mcm_process_campaign($post);
    }

    public function wp_mcm_process_campaign($post){
        $api = $this->get_api();
        $mcm_meta_values = $this->wp_mcm_post_page_meta($post->ID);
        $subject_line = empty($mcm_meta_values['wp_mcm_subject_line']) ? $post->post_title : $mcm_meta_values['wp_mcm_subject_line'];
        $title = empty($mcm_meta_values['wp_mcm_campaign_title']) ? $post->post_title : $mcm_meta_values['wp_mcm_campaign_title'];

        foreach($mcm_meta_values['wp_mcm_listfield'] as $list_id){
                $args = array(
                    'type'=>'regular',
                    'tracking'=>array(
                        'opens'=>true,
                        'html_clicks'=>true
                    ),
                    'settings'=>array(
                        'subject_line'=>$subject_line,
                        'title'=>$title,
                        'template_id'=>intval($mcm_meta_values['wp_mcm_templatefield']),
                        'from_name'=>$mcm_meta_values['wp_mcm_from_name'],
                        'reply_to'=>$mcm_meta_values['wp_mcm_reply_to'],
                        'to_name'=>'*|FNAME|*'
                    ),
                    'recipients'=>array(
                        'list_id'=> $list_id
                    )
                );

                $sent = false;
                $response = $api->add_campaign($args);
                $campaign_id = $response->id;
                $campaigns[] = $response->id;

                if($mcm_meta_values['wp_mcm_overrridecontent'] == 'yes'){
                    $contents = array(
                        'template'=>array(
                            'id'=>intval($mcm_meta_values['wp_mcm_templatefield']),
                            'sections'=>array(
                                'main'=> $post->post_content
                            )
                        )
                    );


                    $api->update_campaign_content($response->id, $contents);
                }

                if($mcm_meta_values['wp_mcm_automatic_send'] == 'yes'){
                    $sent = true;
                    $api->campaign_action($campaign_id,'send', array());
                }

                $this->save_campaign_data($response, $sent, $post);
            }

        }
}
