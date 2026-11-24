<?php

declare(strict_types=1);

use Dwnload\WpSettingsApi\ActionHookName;
use Dwnload\WpSettingsApi\Settings\SectionManager;

/** @var $this Dwnload\WpSettingsApi\WpSettingsApi */
// options.php for site pages, Network Admin edit.php for network pages.
$action = $this->getFormAction();

foreach (SectionManager::getSection($this->getPluginInfo()->getMenuSlug()) as $section) {
    ?>
    <div id="Dwnload_WP_Settings_Api__<?php echo esc_attr($section->getId()); ?>"
         class="Dwnload_WP_Settings_Api__group">
        <form action="<?php echo esc_url($action); ?>" method="post">
            <?php
            /**
             * Action hook before settings section loads.
             * @param Dwnload\WpSettingsApi\Api\SettingSection $section
             */
            do_action(ActionHookName::FORM_TOP, $section, $this);

            settings_fields($section->getId());
            do_settings_sections($section->getId());

            /**
             * Action hook after settings section loads (before submit button).
             * @param Dwnload\WpSettingsApi\Api\SettingSection $section
             */
            do_action(ActionHookName::FORM_BOTTOM, $section, $this);

            submit_button(
                sprintf(esc_attr__('Save &ldquo;%s&rdquo;', 'wp-settings-api'), $section->getTitle()),
                'secondary'
            );
            ?>
        </form>
    </div>
    <?php
}

/** Action hook after all settings sections load. */
do_action(ActionHookName::AFTER_SETTINGS_SECTIONS_FORM, $this);
