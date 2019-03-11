<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;

/** @var $this Dwnload\WpSettingsApi\WpSettingsApi */
$sections = SectionManager::getSection($this->getPluginInfo()->getMenuSlug());

add_action(WpSettingsApi::ACTION_PREFIX . 'settings_sidebars', function () {
    echo '<ul class="Dwnload_WP_Settings_Api__menu">';
}, 0);

foreach ($sections as $key => $section) {
    ++$key;
    add_action(WpSettingsApi::ACTION_PREFIX . 'settings_sidebars', function () use ($section) {
        printf(
            '<li><a href="javascript:;" data-tab-id="Dwnload_WP_Settings_Api__%s">%s</a></li>',
            esc_attr($section->getId()),
            esc_html($section->getTitle())
        );
    }, ++$key * 2 + 1);
}

add_action(WpSettingsApi::ACTION_PREFIX . 'settings_sidebars', function () {
    echo '</ul>';
}, 199);
