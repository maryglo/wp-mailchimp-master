<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       intensevisions.com
 * @since      1.0.0
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/includes
 * @author     intensevision <info@intensevision.com>
 */
class Wp_Mailchimp_Master_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-mailchimp-master',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
