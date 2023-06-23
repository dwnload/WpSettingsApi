<?php
declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\Settings\SectionManager;
use function absint;
use function array_key_exists;
use function get_option;
use function is_array;
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
        $options = Options::getOptions($section_id);

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
            return str_repeat('*', absint(strlen($value) - $len)) . substr($value, -$len, $len);
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
        foreach (SectionManager::getSections() as $section) {
            if (!($section instanceof SettingSection) && is_array($section)) {
                foreach ($section as $setting) {
                    if (!($setting instanceof SettingSection)) {
                        continue;
                    }
                    $options = self::getOptions($setting->getId());
                    if (array_key_exists($option_key, $options)) {
                        return $setting->getId();
                    }
                }
            }
            $options = self::getOptions($section->getId());
            if (array_key_exists($option_key, $options)) {
                return $section->getId();
            }
        }

        return '';
    }
}
