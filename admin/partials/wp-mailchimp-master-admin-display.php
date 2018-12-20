<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       intensevisions.com
 * @since      1.0.0
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/admin/partials
 */
?>
<?php
defined( 'ABSPATH' ) or exit;
?>
<div class="wrap">
    <p class="breadcrumbs">
        <span class="prefix"><?php echo __( 'You are here: ', 'wp-mailchimp-master' ); ?></span>
        <span class="current-crumb"><strong>WP Mailchimp Master</strong></span>
    </p>
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-12">
            <h1>
                <?php _e( 'General Settings', 'wp-mailchimp-master' ); ?>
            </h1>
            <h2 style="display: none;"></h2>
            <?php
            settings_errors();
            $this->messages->show();
            ?>
            <form action="<?php echo admin_url( 'options.php' ); ?>" method="post" class="wp_mcm_settings_page">
                <?php settings_fields( 'wp_mcm_settings' ); ?>
                <div class="card" style="max-width: 100%;padding:0;">
                    <div class="card-header">
                        <h1>
                            <?php _e( 'MailChimp API Settings', 'wp-mailchimp-master' ); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <?php _e( 'Status', 'wp-mailchimp-master' ); ?>
                                </th>
                                <td>
                                    <?php if( $connected ) { ?>
                                        <span class="badge badge-success"><?php _e( 'CONNECTED' ,'wp-mailchimp-master' ); ?></span>

                                    <?php } else { ?>
                                        <span class="status neutral"><?php _e( 'NOT CONNECTED', 'wp-mailchimp-master' ); ?></span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="mailchimp_api_key"><?php _e( 'API Key', 'wp-mailchimp-master' ); ?></label></th>
                                <td>
                                    <input type="text" class="widefat" placeholder="<?php _e( 'Your MailChimp API key', 'wp-mailchimp-master' ); ?>" id="mailchimp_api_key" name="wp_mcm[api_key]" value="<?php echo esc_attr( $obfuscated_api_key ); ?>" />
                                    <p class="help">
                                        <?php _e( 'The API key for connecting with your MailChimp account.', 'wp-mailchimp-master' ); ?>
                                        <a target="_blank" href="https://admin.mailchimp.com/account/api"><?php _e( 'Get your API key here.', 'wp-mailchimp-master' ); ?></a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
                if($connected ) { ?>
                <div class="card" style="max-width: 100%;padding:0;margin-top:0">
                    <div class="card-header">
                        <h1>
                            <?php _e( 'MailChimp Data Settings', 'wp-mailchimp-master' ); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                      <?php include dirname( __FILE__ ) . '/wp-mailchimp-master-data.php';?>
                    </div>
                </div>
                <div class="card" style="max-width: 100%;padding:0;margin-top:0">
                    <div class="card-header">
                        <h1>
                            <?php _e( 'MailChimp Subscription Form Settings', 'wp-mailchimp-master' ); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">
                                        <?php _e( 'Form Heading', 'wp-mailchimp-master' ); ?>
                                    </th>
                                    <td>
                                        <input type="text" class="widefat" placeholder="<?php _e( 'Subscription Form heading', 'wp-mailchimp-master' ); ?>" id="subscription_heading" name="wp_mcm[subscription_heading]" value="<?php echo $opts['subscription_heading'];?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        Subscription Form Options
                                        <p class="help">How do you want to show the subscription pop-up form?</p>
                                    </th>
                                    <td>
                                        <fieldset><legend class="screen-reader-text"></legend>
                                            <label><input type="radio" name="wp_mcm[subscription_popup_option]" value="automatic" <?php if($opts['subscription_popup_option'] == 'automatic') { echo "checked='checked'"; };?>/>Automatic</label><br>
                                            <label><input type="radio" name="wp_mcm[subscription_popup_option]" value="specific_time" <?php if($opts['subscription_popup_option'] == 'specific_time') { echo "checked='checked'"; };?>/><span>Specific period of time(milliseconds):</span></label> <input type="text" name="wp_mcm[subscription_popup_milliseconds]" id="subscription_popup_milliseconds" value="<?php echo $opts['subscription_popup_milliseconds'];?>" class="small-text"><br>
                                            <label><input type="radio" name="wp_mcm[subscription_popup_option]" value="manual" <?php if($opts['subscription_popup_option'] == 'manual') { echo "checked='checked'"; };?> /><span>Manual (using a shortcode):</span></label> <input type="text" value="[wp_mcm_subscribe background-color='' text-color-'' font-size='']" class="regular-text code" readonly><p class="help">If you chose manual, please copy the shortcode value and paste it inside the post's/page's editor</p><br />
                                        </fieldset>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-12 card-footer"><?php submit_button(); ?></div>
            </form>
        </div>
    </div>

</div>


