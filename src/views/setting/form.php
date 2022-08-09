<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\ActionHookName;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;

$scheme = defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ? 'https' : 'admin';
$action = admin_url('options.php', $scheme);

/** @var $this WpSettingsApi */
foreach (SectionManager::getSection($this->getPluginInfo()->getMenuSlug()) as $section) {
    ?>
    <div id="Dwnload_WP_Settings_Api__<?php echo esc_attr($section->getId()); ?>"
         class="Dwnload_WP_Settings_Api__group">
        <form action="<?php echo esc_url($action); ?>" method="post">
            <?php
            /**
             * Action hook before settings section loads.
             * @param SettingSection $section
             */
            do_action(ActionHookName::FORM_TOP, $section, $this);

            settings_fields($section->getId());
            do_settings_sections($section->getId());

            /**
             * Action hook after settings section loads (before submit button).
             * @param SettingSection $section
             */
            do_action(ActionHookName::FORM_BOTTOM, $section, $this);

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
do_action(ActionHookName::AFTER_SETTINGS_SECTIONS_FORM, $this);
