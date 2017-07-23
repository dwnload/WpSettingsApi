<?php

namespace Dwnload\WPSettingsApi\Settings;

use Dwnload\WPSettingsApi\Api\SettingSection;

/**
 * Class SectionManager
 *
 * @package Dwnload\WPSettingsApi\Settings
 */
class SectionManager {

    /**
     * Array of SettingSection objects.
     *
     * @var SettingSection[]
     */
    protected static $settings_sections = [];

    /**
     * Set settings sections
     *
     * @param SettingSection[] $sections An array of SettingSection objects.
     */
    public function setSections( array $sections ) {
        array_push( self::$settings_sections, ...$sections );
    }

    /**
     * Add a new SettingSection object.
     *
     * @param SettingSection $section Array of SettingSection objects.
     *
     * @return string The registered section ID
     */
    public function addSection( SettingSection $section ) : string {
        self::$settings_sections[] = $section;

        return $section->getId();
    }

    /**
     * Get all settings SettingSection objects as an array.
     *
     * @return SettingSection[] Array of SettingSection objects.
     */
    public static function getSections() : array {
        return self::$settings_sections;
    }
}
