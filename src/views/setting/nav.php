<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\ActionHookName;
use Dwnload\WpSettingsApi\Settings\SectionManager;

$sections = SectionManager::getSection($this->getPluginInfo()->getMenuSlug());

add_action(
    ActionHookName::SETTINGS_SETTINGS_SIDEBARS,
    static function (): void {
        echo '<ul class="Dwnload_WP_Settings_Api__menu">';
    },
    0
);

static $count = 0;
foreach ($sections as $key => $section) {
    $count += $key;
    add_action(ActionHookName::SETTINGS_SETTINGS_SIDEBARS, static function () use ($section): void {
        printf(
            '<li><a href="javascript:;" data-tab-id="Dwnload_WP_Settings_Api__%s">%s</a></li>',
            esc_attr($section->getId()),
            esc_html($section->getTitle())
        );
    }, $count + 2);
}

add_action(
    ActionHookName::SETTINGS_SETTINGS_SIDEBARS,
    static function (): void {
        echo '</ul>';
    },
    205
);
