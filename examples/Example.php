<?php

namespace Vendor\Package;

use Dwnload\WPSettingsApi\Api\SettingField;
use Dwnload\WPSettingsApi\Api\SettingSection;
use Dwnload\WPSettingsApi\App;
use Dwnload\WPSettingsApi\Settings\FieldManager;
use Dwnload\WPSettingsApi\Settings\SectionManager;
use Dwnload\WPSettingsApi\WPSettingsApi;
use TheFrosty\WP\Utils\Init;
use TheFrosty\WP\Utils\WpHooksInterface;

class ExampleSettings implements WpHooksInterface {

	/**
	 * Register our callback to the BB WP Settings API action hook
	 * `App::ACTION_PREFIX . 'init'`. This custom action passes two parameters
	 * so you have to register a priority and the parameter count.
	 */
	public function addHooks() {
		add_action( App::ACTION_PREFIX . 'init', [ $this, 'init' ], 10, 2 );
	}

	/**
	 * Initiate our setting to the Section & Field Manager classes.
	 *
	 * SettingField requires the following settings (passes as an array or set explicitly):
	 * [
	 *  SettingField::NAME
	 *  SettingField::LABEL
	 *  SettingField::DESC
	 *  SettingField::TYPE
	 *  SettingField::SECTION_ID
	 * ]
	 *
	 * @see SettingField for additional options for each field passed to the output
	 *
	 * @param SectionManager $section_manager
	 * @param FieldManager $field_manager
	 */
	public function init( SectionManager $section_manager, FieldManager $field_manager ) {
		/**
		 * Checkout Settings Section
		 * `$section_manager->addSection` returns the ID of the new section to pass
		 * to any fields that are registered to this section.
		 */
		$section_id = $section_manager->addSection(
			new SettingSection( [
				SettingSection::SECTION_ID    => 'plugin_checkout', // Unique section ID
				SettingSection::SECTION_TITLE => 'Checkout Settings',
			] )
		);

		// Passing Field settings as an Array
		$field_manager->addField(
			new SettingField( [
				SettingField::NAME       => 'text1',
				SettingField::LABEL      => esc_html__( 'I\'m a label, bro!', 'text-domain' ),
				SettingField::DESC       => esc_html__( 'An example text field', 'text-domain' ),
				SettingField::TYPE       => 'text',
				SettingField::SECTION_ID => $section_id,
			] )
		);

		/**
		 * Random Settings Section
		 */
		$section_id = $section_manager->addSection(
			new SettingSection( [
				SettingSection::SECTION_ID    => 'plugin_random',
				SettingSection::SECTION_TITLE => 'Random Settings',
			] )
		);

		// Passing Field settings as setters to the field object
		$field = new SettingField();
		$field->setName( 'text1' );
		$field->setLabel( esc_html__( 'I\'m a label, bro!', 'text-domain' ) );
		$field->setDescription( esc_html__( 'An example text field', 'text-domain' ) );
		$field->setType( 'text' ); // @see \Settings\FieldTypes for default types
		$field->setSectionId( $section_id );

		$field_manager->addField( $field );
	}
}

$app = new App( [
	'domain'  => 'vendor-domain',
	'file'    => __FILE__,
	'prefix'  => 'vendor_',
	'version' => '0.7.8',
] );
( new Init() )
	->add( $app )
	->add( new WPSettingsApi( $app ) )
	->add( new ExampleSettings() )
	->initialize();
