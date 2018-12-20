<div class="wrap">
    <div class="row">
        <div class="col-md-12 here">
            <div class="row">
            <div class="col-md-6">
                <div class="card" style="padding: 0">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button"  aria-expanded="true" aria-controls="collapseOne">
                                Mailing Lists
                            </button>

                        </h5>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <div class="form-group">
                                <select multiple="" class="form-control" name="wp_mcm[list][]">
                                    <?php  foreach ( $lists as $list ) {

                                        $checked = "";
                                        if(in_array($list->id, $opts['list'])){
                                            $checked = "selected='selected'";
                                        }
                                    ?>
                                        <option <?php echo $checked; ?> value="<?php echo $list->id; ?>"><?php echo esc_html( $list->name ); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card" style="padding: 0;">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" aria-expanded="false" aria-controls="collapseTwo">
                                Templates
                            </button>
                        </h5>
                    </div>
                    <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            <ul class="list-group">
                                <?php foreach ( $templates as $list ) {
                                    $checked = $opts['template'] ==  $list->id ? "checked='checked'" : "";
                                    ?>
                                    <li class="list-group-item">
                                        <input <?php echo $checked; ?> type="radio" name="wp_mcm[template]" value="<?php echo $list->id; ?>" /> <?php echo esc_html( $list->name ); ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 there">
            <div class="row">
                <div class="col-md-6">
                    <div class="card" style="padding: 0">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button"  aria-expanded="true" aria-controls="collapseThree">
                                    Campaign Default Settings
                                </button>
                            </h5>
                        </div>

                        <div id="collapseThree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                            <div class="card-body">
                                    <div class="form-group">
                                        <label>From Name</label>
                                        <input type="text" class="form-control" name="wp_mcm[campaign_from_name]" value="<?php echo $opts['campaign_from_name'];?>" placeholder="From Name"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Reply-to email address</label>
                                        <input type="email" class="form-control" name="wp_mcm[campaign_reply_to]" value="<?php echo $opts['campaign_reply_to'];?>" placeholder="Reply-to email address"/>
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card" style="padding: 0">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button"  aria-expanded="true" aria-controls="collapseThree">
                                    Create Campaign Settings
                                </button>
                            </h5>
                        </div>

                        <div id="collapseFour" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                            <div class="card-body">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">
                                            Campaign Creation Options
                                            <p class="help">How do you want to create your campaign?</p>
                                        </th>
                                        <td>
                                            <fieldset><legend class="screen-reader-text"></legend>
                                                <label><input type="radio" class="campaign_create_option" name="wp_mcm[campaign_create_option]" value="automatic" <?php if($opts['campaign_create_option'] == 'automatic') { echo "checked='checked'"; };?>/>Automatic (When post/page is published )</label><br>
                                                <label><input type="radio" class="campaign_create_option" name="wp_mcm[campaign_create_option]" value="specific_datetime" <?php if($opts['campaign_create_option'] == 'specific_datetime') { echo "checked='checked'"; };?>/><span>Specific period of time:</span></label> <input type="text" name="wp_mcm[campaign_create_datetime]" id="campaign_create_datetime" value="<?php echo $opts['campaign_create_datetime'];?>" class="form-control" style="<?php if($opts['campaign_create_option'] == 'specific_datetime') { echo "display: block;"; } else { echo "display: none;"; }?>"/>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>