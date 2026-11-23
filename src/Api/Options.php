<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\Settings\SectionManager;
use function absint;
use function array_key_exists;
use function get_option;
use function is_array;
use function is_string;
use function str_contains;
use function str_repeat;
use function strlen;
use function substr;

/**
 * Class Options
 * @package Dwnload\WpSettingsApi\Api
 */
class Options
{

    /**
     * Get the value of a settings field
     * @param string $option_key Settings field key name in the section option array.
     * @param string|null $section_id The Section object ID the option belongs too.
     * @param mixed $default (Optional) Default value if option is not found.
     *                          Defaults to an empty string.
     * @return mixed
     */
    public static function getOption(string $option_key, ?string $section_id = null, mixed $default = ''): mixed
    {
        if (empty($section_id)) {
            $section_id = self::getSectionId($option_key);
        }

        $options = self::getOptions($section_id);

        return $options[$option_key] ?? $default;
    }

    /**
     * Get the full settings Section ID array.
     * @param string $section_id The Section object ID the option belongs too.
     * @return mixed Value set for the option. Defaults to an empty array.
     */
    public static function getOptions(string $section_id): mixed
    {
        return get_option($section_id, []);
    }

    /**
     * Get the plaintext value of a "secret" field.
     * Reads from the WordPress Secrets API (7.2+) when available, falling back to the option value
     * (pre-7.2, or a plaintext value not yet migrated). Returns null when there is no usable value.
     * @param string $option_key Settings field key name in the section option array.
     * @param string $section_id The Section object ID the option belongs too.
     * @return string|null
     */
    public static function getSecret(string $option_key, string $section_id): ?string
    {
        if (SecretsApi::isAvailable()) {
            $secret = SecretsApi::reveal(SecretsApi::getSecretName($section_id, $option_key));
            if ($secret !== null) {
                return $secret;
            }
        }

        $value = self::getOption($option_key, $section_id, '');

        return is_string($value) && $value !== '' && !SecretsApi::isMarker($value) ? $value : null;
    }

    /**
     * Get the obfuscated option value.
     * @param string $option_key Settings field key name in the section option array.
     * @param string $section_id The Section object ID the option belongs too.
     * @param mixed $default (Optional) Default value if option is not found.
     *                          Defaults to an empty string.
     * @param int $len (Optional) The Length of the un-obfuscated string. Defaults to `6`.
     * @return mixed|string
     */
    public static function getObfuscatedOption(
        string $option_key,
        string $section_id,
        mixed $default = '',
        int $len = 6
    ): mixed {
        $value = self::getOption($option_key, $section_id, $default);

        if (!empty($value)) {
            return str_repeat('*', absint(strlen((string)$value) - $len)) . substr((string)$value, -$len, $len);
        }

        return $value;
    }

    /**
     * Is the value in question an obfuscated string?
     * @param string $value
     * @return bool
     */
    public static function isObfuscated(string $value): bool
    {
        return str_contains($value, '****');
    }

    /**
     * Get the Section ID by option key.
     * @param string $option_key
     * @return string
     */
    protected static function getSectionId(string $option_key): string
    {
        // Sections are stored per menu slug: [menu_slug => SettingSection[]].
        foreach (SectionManager::getSections() as $sections) {
            $sections = $sections instanceof SettingSection ? [$sections] : (array)$sections;
            foreach ($sections as $section) {
                if (!$section instanceof SettingSection) {
                    continue;
                }

                $options = self::getOptions($section->getId());
                if (is_array($options) && array_key_exists($option_key, $options)) {
                    return $section->getId();
                }
            }
        }

        return '';
    }
}
