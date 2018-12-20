<?php
require WP_MCM_PLUGIN_DIR . 'class-wp-mcm-container.php';
require WP_MCM_PLUGIN_DIR . '/api/class-api-v3.php';

function wp_mcm_get_options()
{
    static $options;

    if (!$options) {
        $defaults = require WP_MCM_PLUGIN_DIR . 'config/default_settings.php';
        $options = (array)get_option('wp_mcm', array());
        $options = array_merge($defaults, $options);
    }

    /**
     * Filters the WordPress - Mailchimp Master settings (general).
     *
     * @param array $options
     */
    return apply_filters('wp_mcm_settings', $options);
}

/**
 * @param null $service
 * @return mixed|Wp_MCM_Container
 */
function wp_mcm($service = null)
{
    static $wp_mcm;

    if (!$wp_mcm) {
        $wp_mcm = new Wp_MCM_Container();
    }

    if ($service) {
        return $wp_mcm->get($service);
    }

    return $wp_mcm;
}

/**
 * @return Wp_Mailchimp_Master_API_v3
 */
function wp_mcm_get_api_v3()
{
    $opts = wp_mcm_get_options();
    $instance = new Wp_Mailchimp_Master_API_v3($opts['api_key']);
    return $instance;
}

/**
 * @param $string
 * @return string
 */
function wp_mcm_obfuscate_string($string)
{
    $length = strlen($string);
    $obfuscated_length = ceil($length / 2);
    $string = str_repeat('*', $obfuscated_length) . substr($string, $obfuscated_length);
    return $string;
}