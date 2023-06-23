<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Settings;

use Dwnload\WpSettingsApi\Api\SettingField;
use function array_push;

/**
 * Class FieldManager
 * @package Dwnload\WpSettingsApi\Settings
 */
class FieldManager
{

    /**
     * Array of `SettingField` objects.
     * @var SettingField[]
     */
    protected static array $settings_fields = [];

    /**
     * Set an array of new SettingField objects.
     * @param SettingField[] $fields An array of `SettingField` objects.
     */
    public function setFields(array $fields): void
    {
        array_push(self::$settings_fields, ...$fields);
    }

    /**
     * Add a new SettingField object.
     * @param SettingField $field The `SettingField` object.
     */
    public function addField(SettingField $field): void
    {
        self::$settings_fields[$field->getSectionId()][] = $field;
    }

    /**
     * Get all SettingField objects as an array.
     * @return SettingField[] An array of `SettingField` objects.
     */
    public static function getFields(): array
    {
        return self::$settings_fields;
    }
}
