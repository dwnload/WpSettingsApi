<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\ActionHookName;
use Dwnload\WpSettingsApi\WpSettingsApi;

/** @var $this WpSettingsApi */
if (!($this instanceof WpSettingsApi)) {
    wp_die('Please don\'t load this file outside the WpSettingsApi object.');
}

?>
<div class="wrap"><!-- Default WordPress class -->
    <div class="Dwnload_WP_Settings_Api__container">

        <div class="Dwnload_WP_Settings_Api__header">
            <h3><?php echo esc_html(get_admin_page_title()); ?></h3>
            <span><?php echo esc_html($this->getPluginInfo()->getVersion()); ?></span>
            <div>
                <?php
                printf(
                    esc_html__('A %s package.', 'wp-settings-api'),
                    sprintf(
                        '<strong><a href="https://github.com/dwnload/WpSettingsApi" 
title="WP Settings API version &ldquo;%s&rdquo;" target="_blank">dwnload</a></strong>',
                        esc_attr(WpSettingsApi::VERSION)
                    )
                ); ?>
            </div>
        </div><!-- .Dwnload_WP_Settings_Api__header -->

        <div class="Dwnload_WP_Settings_Api__notices">
            <h2></h2>
        </div><!-- .Dwnload_WP_Settings_Api__notices -->

        <div class="Dwnload_WP_Settings_Api__sticky">
            <div class="wrap">
                <div class="Dwnload_WP_Settings_Api__sticky_notice">
                    <?php do_action(ActionHookName::SETTINGS_STICKY_ADMIN_NOTICE, $this); ?>
                </div>
                <div class="alignright">
                    <?php do_action(ActionHookName::SETTINGS_BEFORE_SUBMIT_BUTTON, $this); ?>
                    <?php submit_button(
                        __('Save All Changes', 'wp-settings-api'),
                        'primary',
                        'Dwnload_WP_Settings_Api__save_all',
                        false,
                        ['disabled' => 'disabled']
                    ); ?>
                </div>
                <br class="clear">
            </div>
        </div><!-- #Dwnload_WP_Settings_Api__sticky -->

        <div class="Dwnload_WP_Settings_Api__sidebar">
            <?php include __DIR__ . '/setting/nav.php'; ?>
            <?php do_action(ActionHookName::SETTINGS_SETTINGS_SIDEBARS, $this); ?>
        </div><!-- .Dwnload_WP_Settings_Api__sidebar -->

        <div class="Dwnload_WP_Settings_Api__body">
            <?php include __DIR__ . '/setting/form.php'; ?>
        </div><!-- .Dwnload_WP_Settings_Api__body -->

    </div><!-- .Dwnload_WP_Settings_Api__container -->
</div><!-- .wrap -->
