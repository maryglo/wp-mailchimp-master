<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function wp_mcm_autoload( $class_name ) {
    $class     = str_replace( '\\', DIRECTORY_SEPARATOR, str_replace( '_', '-', strtolower($class_name) ) );
    $file_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $class . '.php';
    //var_dump($file_path);
    if ( file_exists( $file_path ) ) {

        require_once $file_path;
    }
}

spl_autoload_register('wp_mcm_autoload');