<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\SectionManager;

$scheme = defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ? 'https' : 'admin';
$action = admin_url('options.php', $scheme);

/** @var $this \Dwnload\WpSettingsApi\WpSettingsApi */
/** @var SettingSection $section */
foreach (SectionManager::getSections($this->getApp()->getMenuSlug()) as $section) {
    ?>
    <div id="Dwnload_WP_Settings_Api__<?php echo esc_attr($section->getId()); ?>"
         class="Dwnload_WP_Settings_Api__group">
        <form action="<?php echo esc_url($action); ?>" method="post">
            <?php
            /**
             * Action hook before settings section loads.
             *
             * @var SettingSection $section
             */
            do_action(App::ACTION_PREFIX . 'form_top', $section);

            settings_fields($section->getId());
            do_settings_sections($section->getId());

            /**
             * Action hook after settings section loads (before submit button).
             *
             * @var SettingSection $section
             */
            do_action(App::ACTION_PREFIX . 'form_bottom', $section);

            submit_button(
                sprintf(esc_attr__('Save &ldquo;%s&rdquo;', 'dwnload-wp-settings-api'), $section->getTitle()),
                'secondary'
            );
            ?>
        </form>
    </div>
    <?php
}

/** Action hook after all settings sections load. */
do_action(App::ACTION_PREFIX . 'after_settings_sections_form');
