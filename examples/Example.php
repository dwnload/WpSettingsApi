<?php

namespace Vendor\Package; // CHANGE ME

use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\SettingsApiFactory;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\WpUtilities\Plugin\PluginFactory;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class ExampleSettings
 * @package Vendor\Package
 */
class ExampleSettings implements WpHooksInterface
{

    /**
     * Register our callback to the WP Settings API action hook
     * `WpSettingsApi::ACTION_PREFIX . 'init'`. This custom action passes three parameters (two prior to version 2.7)
     * so you have to register a priority and the parameter count.
     */
    public function addHooks(): void
    {
        \add_action(WpSettingsApi::ACTION_PREFIX . 'init', [$this, 'init'], 10, 3);
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
     * @param WpSettingsApi $wp_settings_api
     */
    public function init(SectionManager $section_manager, FieldManager $field_manager, WpSettingsApi $wp_settings_api)
    {
        // Check for current app instance in case using more than once (slug set below in `SettingsApiFactory::create()`).
        if ($wp_settings_api->getPluginInfo()->getMenuSlug() !== 'vendor-domain-settings') {
            return;
        }

        /**
         * Checkout Settings Section
         * `$section_manager->addSection` returns the ID of the new section to pass
         * to any fields that are registered to this section.
         */
        $section_id = $section_manager->addSection(
            new SettingSection([
                SettingSection::SECTION_ID => 'plugin_checkout', // Unique section ID
                SettingSection::SECTION_TITLE => 'Checkout Settings',
            ])
        );

        // Passing Field settings as an Array
        $field_manager->addField(
            new SettingField([
                SettingField::NAME => 'text1',
                SettingField::LABEL => \esc_html__('I\'m a label, bro!', 'text-domain'),
                SettingField::DESC => \esc_html__('An example text field', 'text-domain'),
                SettingField::TYPE => 'text',
                SettingField::SECTION_ID => $section_id,
            ])
        );

        /**
         * Random Settings Section
         */
        $section_id = $section_manager->addSection(
            new SettingSection([
                SettingSection::SECTION_ID => 'plugin_random',
                SettingSection::SECTION_TITLE => 'Random Settings',
            ])
        );

        // Passing Field settings as setters to the field object
        $field = new SettingField();
        $field->setName('text1');
        $field->setLabel(\esc_html__('I\'m a label, bro!', 'text-domain'));
        $field->setDescription(\esc_html__('An example text field', 'text-domain'));
        $field->setType('text'); // @see \Settings\FieldTypes for default types
        $field->setSectionId($section_id);

        $field_manager->addField($field);
    }
}

$app = SettingsApiFactory::create([
    'domain' => 'vendor-domain',
    'file' => __FILE__, // Path to WpSettingsApi file (not required, see README for more info).
    'menu-slug' => 'vendor-domain-settings',
    'menu-title' => 'Vendor Settings', // Title found in menu
    'page-title' => 'Vendor Settings Api', // Title output at top of settings page
    'prefix' => 'vendor_',
    'version' => '0.7.9',
]);
$plugin = PluginFactory::create('vendor-domain')
    ->add(new WpSettingsApi($app))
    ->add(new ExampleSettings());

\add_action('plugins_loaded', function () use ($plugin) {
    $plugin->initialize();
    unset($plugin);
});
