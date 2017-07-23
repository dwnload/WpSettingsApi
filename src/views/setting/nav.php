<?php

use Dwnload\WPSettingsApi\Api\SettingSection;
use Dwnload\WPSettingsApi\Settings\SectionManager;

$sections = SectionManager::getSections();

if ( count( $sections ) <= 1 ) {
	return;
}
?>
<ul class="Dwnload_WP_Settings_Api__menu">
	<?php
	/** @var SettingSection $section */
	foreach ( $sections as $section ) {
		printf(
			'<li><a href="javascript:;" data-tab-id="Dwnload_WP_Settings_Api__%s">%s</a></li>',
			esc_attr( $section->getId() ),
			esc_html( $section->getTitle() )
		);
	}
	?>
</ul>