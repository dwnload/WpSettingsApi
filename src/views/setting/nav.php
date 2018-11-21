<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\Settings\SectionManager;

/** @var $this Dwnload\WpSettingsApi\WpSettingsApi */
$sections = SectionManager::getSections($this->getApp()->getMenuSlug());

add_action(App::ACTION_PREFIX . 'settings_sidebars', function () {
    echo '<ul class="Dwnload_WP_Settings_Api__menu">';
}, 0);

/** @var SettingSection $section */
foreach ($sections as $key => $section) {
    ++$key;
    add_action(App::ACTION_PREFIX . 'settings_sidebars', function () use ($section) {
        printf(
            '<li><a href="javascript:;" data-tab-id="Dwnload_WP_Settings_Api__%s">%s</a></li>',
            esc_attr($section->getId()),
            esc_html($section->getTitle())
        );
    }, ++$key * 2 + 1);
}

add_action(App::ACTION_PREFIX . 'settings_sidebars', function () {
    echo '</ul>';
}, 199);
