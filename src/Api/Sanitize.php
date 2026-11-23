<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\Settings\FieldManager;
use WP_Error;
use function __;
use function add_settings_error;
use function esc_html;
use function esc_html__;
use function is_array;
use function is_string;
use function is_wp_error;
use function sanitize_text_field;
use function sprintf;
use function trim;

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

        if (!empty($section_id) && is_string($value) && Options::isObfuscated($value)) {
            return Options::getOption($option_slug, $section_id, $value);
        }

        return sanitize_text_field($value);
    }

    /**
     * Sanitize method for "secret" fields.
     * With the WordPress Secrets API (7.2+) the value is stored with `wp_set_secret()` and only a
     * placeholder (`SecretsApi::MARKER_PREFIX . $name`) is saved in the option. A blank submission keeps
     * the stored secret, and a plaintext value saved before the API was available is migrated on save.
     * Without the API it falls back to `sanitizeObfuscated()` (the value is stored in the option).
     * @param mixed $value Array of `value` and `delete` from the form, or a string.
     * @param array $options
     * @param string $option_slug
     * @return mixed
     */
    public static function sanitizeSecret(mixed $value, array $options, string $option_slug): mixed
    {
        $section_id = self::getSectionId($option_slug);
        $submitted = is_array($value) ? $value : ['value' => $value];

        if ($section_id === '' || !SecretsApi::isAvailable()) {
            return self::sanitizeObfuscated($submitted['value'] ?? '', $options, $option_slug);
        }

        // Already a placeholder (e.g. sanitize_option() running twice on add_option()).
        if (SecretsApi::isMarker($value)) {
            return $value;
        }

        $name = SecretsApi::getSecretName($section_id, $option_slug);
        $current = Options::getOption($option_slug, $section_id, '');
        $new = is_string($submitted['value'] ?? null) ? trim($submitted['value']) : '';
        $delete = !empty($submitted['delete']);

        if (!SecretsApi::canManage()) {
            if ($new !== '' || $delete) {
                self::addError($section_id, $option_slug, new WP_Error(
                    'secret_forbidden',
                    __('You do not have permission to manage secrets.', 'wp-settings-api')
                ));
            }

            return $current;
        }

        if ($delete) {
            $result = SecretsApi::delete($name);
            if (is_wp_error($result)) {
                self::addError($section_id, $option_slug, $result);

                return $current;
            }

            return '';
        }

        if ($new === '') {
            // Nothing submitted: migrate a plaintext value saved before the Secrets API was used, else keep it.
            if (!is_string($current) || $current === '' || SecretsApi::isMarker($current)) {
                return $current;
            }
            $new = $current;
        }

        $result = SecretsApi::set($name, $new);
        if (is_wp_error($result)) {
            // Fail closed: never write the new value to the option.
            self::addError($section_id, $option_slug, $result);

            return $current;
        }

        return SecretsApi::getMarker($name);
    }

    /**
     * Register a settings error for a secret field.
     * @param string $section_id
     * @param string $option_slug
     * @param WP_Error $error
     */
    private static function addError(string $section_id, string $option_slug, WP_Error $error): void
    {
        add_settings_error(
            $section_id,
            sprintf('secret-%s', $option_slug),
            sprintf(
                /* translators: 1: Field ID, 2: Error message. */
                esc_html__('Secret "%1$s" was not saved: %2$s', 'wp-settings-api'),
                esc_html($option_slug),
                esc_html($error->get_error_message())
            )
        );
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
