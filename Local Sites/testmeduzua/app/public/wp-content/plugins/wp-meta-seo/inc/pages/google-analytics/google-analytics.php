<div class="wrap">
    <?php
    require_once(WPMETASEO_PLUGIN_DIR . 'inc/pages/google-analytics/menu.php');
    wp_enqueue_script('jquery');
    ?>
    <h2 class="wpms_uppercase"><?php esc_html_e('Google Analytics tracking & report', 'wp-meta-seo') ?></h2>
    <p class="ju-description"><?php esc_html_e('Enable Google Analytics tracking and reports using a Google Analytics direct connection. Require free Google Cloud credentials', 'wp-meta-seo') ?></p>
    <p class="wpms-ga-link-document">
        <a class="ju-link-classic" href="<?php echo esc_url('https://www.joomunited.com/documentation/wp-meta-seo-documentation#toc-7-1-activate-analytics-tracking') ?>"
           target="_blank"><?php esc_html_e('DOCUMENTATION', 'wp-meta-seo') ?></a>
        <a class="ju-link-classic" href="<?php echo esc_url('https://console.cloud.google.com/apis/dashboard') ?>" style="margin-left: 15px"
           target="_blank"><?php esc_html_e('GET GOOGLE CREDENTIALS >>', 'wp-meta-seo') ?></a>
    </p>
    <?php
    $display_body_info = '';
    $display_body_access = 'style=display:none;';
    if (!empty($this->google_alanytics['wpmsga_dash_clientid']) && !empty($this->google_alanytics['wpmsga_dash_clientsecret'])) {
        $display_body_info = 'style=display:none;';
        $display_body_access = '';
    }
    ?>
    <form name="input" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" style="margin-top: 20px">
        <?php wp_nonce_field('gadash_form', 'gadash_security'); ?>
        <input type="hidden" name="wpms_nonce" value="<?php echo esc_attr(wp_create_nonce('wpms_nonce')) ?>">

        <div class="ju-settings-option wpms_ga_info background-none" <?php echo esc_attr($display_body_info) ?>>
            <div class="wpms_row_full">
                <label class="ju-setting-label"><?php esc_html_e('Client ID', 'wp-meta-seo') ?></label>
                <div class="ju-switch-button">
                    <input type="text" name="wpmsga_dash_clientid" class="wpms-large-input wpmsga_dash_input" size="60"
                           value="<?php echo esc_attr((!empty($this->google_alanytics['wpmsga_dash_clientid'])) ? $this->google_alanytics['wpmsga_dash_clientid'] : '') ?>">
                </div>
            </div>
            <div class="wpms_row_full">
                <label class="ju-setting-label"><?php esc_html_e('Client Secret', 'wp-meta-seo') ?></label>
                <div class="ju-switch-button">
                    <input type="text" name="wpmsga_dash_clientsecret" class="wpms-large-input wpmsga_dash_input" size="60"
                           value="<?php echo esc_attr((!empty($this->google_alanytics['wpmsga_dash_clientsecret'])) ? $this->google_alanytics['wpmsga_dash_clientsecret'] : '') ?>">
                </div>
            </div>
            <div class="wpms_row_full save-ga-field">
                <input type="button" class="ju-button save-ga-infomation orange-button" <?php echo esc_attr($display_body_access)?>
                       value="<?php esc_html_e('Save and Continue', 'wp-meta-seo') ?>" />
                <img class="save-ga-loader" src="<?php echo esc_url(WPMETASEO_PLUGIN_URL . '/assets/images/ajax-loader1.gif') ?>" width="50px"
                     style="display:none;margin-left:10px;vertical-align: middle" />
            </div>
        </div>
        <div class="ju-settings-option wpms_ga_access background-none" <?php echo esc_attr($display_body_access) ?>>
            <div class="wpms_row_full">
                <input type="button" class="ju-button wpms-ga-back-information"
                       value="<?php esc_html_e('Back to credentials', 'wp-meta-seo') ?>">
            </div>
            <div class="wpms_row_full">
                <label class="ju-setting-label" title="<?php esc_attr_e('Use the generate access code to get your access code!', 'wp-meta-seo') ?>">
                    <?php esc_html_e('Access Code', 'wp-meta-seo'); ?>
                </label>
                <div class="ju-switch-button">
                    <input type="text" class="wpms-large-input" id="ga_dash_code" name="wpms_ga_code" value="" size="60" />
                </div>
                <button type="submit" class="ju-button orange-button" data-href="<?php echo esc_url($authUrl) ?>" id="wpms-ga-get-accesscode"
                   data-type="link"><?php esc_html_e('Generate Access Code', 'wp-meta-seo') ?></button>
            </div>
        </div>

        <script>
            jQuery('body').on('click', '.wpmsga-advanced-config-header', function (e) {
                if (jQuery(this).hasClass('down')) {
                    jQuery(this).removeClass('down').addClass('up');
                } else {
                    jQuery(this).removeClass('up').addClass('down');
                }

                jQuery.cookie('wpmsga-advanced-box', jQuery('.wpmsga-advanced-box .content-box').is(':hidden').toString());
                jQuery(this).closest('.wpmsga-advanced-box').find('.content-box').stop().slideToggle();
            });

            jQuery(document).ready(function ($) {
                if ($.cookie('wpmsga-advanced-box') === 'true') {
                    $('.wpmsga-advanced-box .content-box').show();
                    $('.wpmsga-advanced-config-header').removeClass('down').addClass('up');
                } else {
                    $('.wpmsga-advanced-box .content-box').hide();
                    $('.wpmsga-advanced-config-header').removeClass('up').addClass('down');
                }

                $('input.wpmsga_dash_input').on('keyup', function () {
                    if ($('input[name="wpmsga_dash_clientid"]').val() !== '' && $('input[name="wpmsga_dash_clientsecret"]').val()) {
                        $('.save-ga-infomation').show();
                    } else {
                        $('.save-ga-infomation').hide();
                    }
                });

                $('input#ga_dash_code').on('keyup', function () {
                    if ($(this).val() !== '') {
                        $('button#wpms-ga-get-accesscode').html('Save and Apply');
                        $('button#wpms-ga-get-accesscode').attr('name', 'ga_dash_authorize');
                        $('button#wpms-ga-get-accesscode').attr('data-type', 'save');
                    } else {
                        $('button#wpms-ga-get-accesscode').html('Generate Access Code');
                        $('button#wpms-ga-get-accesscode').removeAttr('name');
                        $('button#wpms-ga-get-accesscode').attr('data-type', 'link');
                    }
                });

                $('button#wpms-ga-get-accesscode').on('click', function (e) {
                    if ($(this).attr('data-type') === 'link') {
                        e.preventDefault();
                        window.open($(this).attr('data-href'),"_blank");
                        return false;
                    }
                });

                $('input.save-ga-infomation').click(function () {
                    var wpmsga_dash_clientid = $('input[name="wpmsga_dash_clientid"').val();
                    var wpmsga_dash_clientsecret = $('input[name="wpmsga_dash_clientsecret"').val();
                    $('.save-ga-field .save-ga-loader').show();
                    if (wpmsga_dash_clientid !== '' && wpmsga_dash_clientsecret !== '') {
                        $.ajax({
                            url: ajaxurl,
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                'action': 'wpms_ga_save_information',
                                'wpmsga_dash_clientid': wpmsga_dash_clientid,
                                'wpmsga_dash_clientsecret': wpmsga_dash_clientsecret,
                                'wpms_nonce': $('input[name="wpms_nonce"]').val()
                            },
                            success: function (res) {
                                if (res.status) {
                                    $('#wpms-ga-get-accesscode').attr('data-href', res.authUrl);
                                    $('.wpms_ga_info').hide();
                                    $('.save-ga-field .save-ga-loader').hide();
                                    $('.wpms_ga_access').show('fade');
                                }
                            }
                        });
                    } else {
                        if (wpmsga_dash_clientid === '') {
                            $('input[name="wpmsga_dash_clientid"').focus();
                        }
                        if (wpmsga_dash_clientsecret === '') {
                            $('input[name="wpmsga_dash_clientsecret"').focus();
                        }
                    }
                });

                $('.wpms-ga-back-information').click(function () {
                    $('.wpms_ga_access').hide();
                    $('.wpms_ga_info').show('fade');
                })
            });
        </script>
        <div class="wpmsga-advanced-configuration">
            <div class="wpmsga-advanced-box">
                <h2 class="wpmsga-advanced-config-header down">
                    <?php esc_html_e('Advanced configuration', 'wp-meta-seo') ?>
                </h2>
                <div class="content-box">
                    <h4 class="wpms_uppercase"><?php esc_html_e('Google analytics tracking only', 'wp-meta-seo') ?></h4>
                    <p class="ju-description"><?php esc_html_e('Enable Google Analytics tracking only. You won\'t be
     able to display statistics in your', 'wp-meta-seo') ?></p>
                    <p class="ju-description"><?php esc_html_e('Wordpress admin, only on Google Analytics website', 'wp-meta-seo') ?></p>

                    <table class="wpms-settings-options">
                        <tr>
                            <td class="wpms-settings-title"><label for="wpms_ga_uax_reference" class="wpms-text"
                                                                   title="<?php esc_attr_e('Analytics UA-X reference', 'wp-meta-seo') ?>">
                                    <?php esc_html_e('Analytics UA-X reference:', 'wp-meta-seo'); ?></label>
                            </td>
                            <td>
                                <input type="text" class="wpms-large-input" id="wpms_ga_uax_reference"
                                       name="_metaseo_ga_disconnect[wpms_ga_uax_reference]"
                                       value="<?php echo esc_attr($this->gaDisconnect['wpms_ga_uax_reference']) ?>"
                                       size="61">
                                <input type="hidden" name="wpms_nonce"
                                       value="<?php echo esc_attr(wp_create_nonce('wpms_nonce')) ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="wpms-settings-title"><label for="wpms_ga_uax_reference" class="wpms-text"
                                                                   title="<?php esc_attr_e('Analytics tracking type', 'wp-meta-seo') ?>">
                                    <?php esc_html_e('Analytics tracking type', 'wp-meta-seo'); ?></label>
                            </td>
                            <td>
                                <label>
                                    <select id="wpmsga_dash_tracking_type" class="wpms-large-input"
                                            name="_metaseo_ga_disconnect[wpmsga_dash_tracking_type]">
                                        <option value="classic"
                                            <?php selected($this->gaDisconnect['wpmsga_dash_tracking_type'], 'classic') ?>>
                                            <?php esc_html_e('Classic Analytics', 'wp-meta-seo') ?>
                                        </option>
                                        <option value="universal"
                                            <?php selected($this->gaDisconnect['wpmsga_dash_tracking_type'], 'universal') ?>>
                                            <?php esc_html_e('Universal Analytics', 'wp-meta-seo') ?>
                                        </option>
                                    </select>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <p class="ju-description">
                        <?php esc_html_e('If you are using Universal Analytics make sure you have changed your account to a Universal Analytics', 'wp-meta-seo') ?>
                    </p>
                    <p class="ju-description">
                        <?php esc_html_e('property in Google Analytics Read more about Universal Analytics ', 'wp-meta-seo') ?>
                        <a target="_blank" href="https://developers.google.com/analytics/devguides/collection/upgrade/">here</a>
                    </p>
                    <p class="ju-description"><?php esc_html_e('OR use Analytics JS code', 'wp-meta-seo') ?></p>
                    <label>
                    <textarea name="_metaseo_ga_disconnect[wpmsga_code_tracking]" class="wpmsga_code_tracking">
                        <?php echo esc_textarea($this->gaDisconnect['wpmsga_code_tracking']); ?>
                    </textarea>
                    </label>
                </div>
            </div>
        </div>

        <p>
            <button type="submit" class="ju-button orange-button wpmsga_authorize" name="ga_dash_authorize"
            ><?php esc_html_e('Save Changes', 'wp-meta-seo'); ?></button>
        </p>
    </form>
</div>