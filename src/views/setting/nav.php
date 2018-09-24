<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\SectionManager;

/** @var $this \Dwnload\WpSettingsApi\WpSettingsApi */
$sections = SectionManager::getSections($this->getApp()->getMenuSlug());

if (count($sections) <= 1) {
    return;
}
?>
<ul class="Dwnload_WP_Settings_Api__menu">
    <?php
    /** @var SettingSection $section */
    foreach ($sections as $section) {
        printf(
            '<li><a href="javascript:;" data-tab-id="Dwnload_WP_Settings_Api__%s">%s</a></li>',
            esc_attr($section->getId()),
            esc_html($section->getTitle())
        );
    }
    ?>
</ul>
