<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       intensevisions.com
 * @since      1.0.0
 *
 * @package    Wp_Mailchimp_Master
 * @subpackage Wp_Mailchimp_Master/public/partials
 */
?>
<div id="dialog">
    <div class="form_wrapper">
        <h3 class="subscribe_heading"><?php echo $opts['subscription_heading'];?></h3>
        <form method="POST" class="mailchimp-form">
            <div class="wp_mcm_checkbox">
                <label>Mailing Lists</label>
                <ul class="checkbox">
                    <?php  foreach($lists as $list) { ?>
                        <li><input name="mailchimp_list[]" type="checkbox" id="list_<?php echo $list->id; ?>" value="<?php echo $list->id; ?>" /><label><?php echo esc_html( $list->name ); ?></label></li>
                    <?php } ?>
                </ul>
            </div>
            <!--<div class="Form-row">
                <div class="FormSelect">
                    <label></label>
                    <select class="mailchimp_list FormSelect-input" name="mailchimp_list[]" required multiple>
                        <?php  //foreach($lists as $list) {?>
                            <option value="<?php //echo $list->id; ?>" /> <?php //echo esc_html( $list->name ); ?></option>
                        <?php //} ?>
                    </select>
                    <small>hint: multiple items can be selected</small>
                </div>
            </div>-->
            <div class="Form-row">
                <div class="FormText Login-formItem--name">
                    <label class="firstName_label">
                        <input required type="text" class="FormText-input" name="firstName" maxlength="100" placeholder="*First name" value="" />
                    </label>
                </div>
                <div class="FormText Login-formItem--name">
                    <label class="lastName_label">
                        <input required type="text" class="FormText-input" name="lastName" maxlength="100" placeholder="*Last name" value="">
                    </label>
                </div>
            </div>
            <div class="Form-row">
                <div class="FormText">
                    <label class="mailchimp-email_label">
                        <input type="email" name="mailchimp-email" class="mailchimp-email FormText-input" placeholder="*Your email address" required />
                    </label>
                </div>
            </div>
            <button class="Btn Btn--filled Btn--large subscribe_me" type="submit">
                <div class="Btn-content">
                    <span class="Btn-label">Subscribe</span>
                </div>
            </button>
        </form>
    </div>
</div>
<div id="success_dialog">
    <p class="text" style="text-align: center"></p>
</div>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
