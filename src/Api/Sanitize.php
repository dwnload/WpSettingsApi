<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\Settings\FieldManager;

/**
 * Class Sanitize
 * @package Dwnload\WpSettingsApi\Api
 */
class Sanitize
{

    /**
     * Sanitize method for possible obfuscated values.
     * If the value is obscured (meaning it contains as least 4 stars "*****" in a row, we need
     * to get the value from the database instead of the new incoming value from the settings view
     * so that the obscured setting doesn't override the actual value in the DB.
     * @param mixed $value
     * @param array $options
     * @param string $option_slug
     * @return mixed
     */
    public static function sanitizeObfuscated(mixed $value, array $options, string $option_slug): mixed
    {
        $section_id = self::getSectionId($option_slug);

        if (!empty($section_id) && Options::isObfuscated($value)) {
            return Options::getOption($option_slug, $section_id, $value);
        }

        return \sanitize_text_field($value);
    }

    /**
     * Gets the Section ID of the option.
     * @param string $option_slug
     * @return string
     */
    private static function getSectionId(string $option_slug): string
    {
        // Iterate over registered fields and see if we can find proper callback.
        foreach (FieldManager::getFields() as $fields) {
            /**
             * Filed object.
             * @var SettingField $field
             */
            foreach ($fields as $field) {
                if ($field->getName() === $option_slug) {
                    return $field->getSectionId();
                }
            }
        }

        return '';
    }
}
