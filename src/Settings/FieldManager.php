<?php

namespace Dwnload\WPSettingsApi\Settings;

use Dwnload\WPSettingsApi\Api\SettingField;

/**
 * Class FieldManager
 *
 * @package Dwnload\WPSettingsApi\Settings
 */
class FieldManager {

    /**
     * Array of SettingField objects.
     *
     * @var array
     */
    protected static $settings_fields = [];

    /**
     * Set an array of new SettingField objects.
     *
     * @param array $fields An array of Field objects.
     */
    public function setFields( array $fields ) {
        array_push( self::$settings_fields, ...$fields );
    }

    /**
     * Add a new SettingField object.
     *
     * @param SettingField $field The SettingField object.
     */
    public function addField( SettingField $field ) {
        self::$settings_fields[ $field->getSectionId() ][] = $field;
    }

    /**
     * Get all SettingField objects as an array.
     *
     * @return array Array of SettingField objects.
     */
    public static function getFields() : array {
        return self::$settings_fields;
    }
}
