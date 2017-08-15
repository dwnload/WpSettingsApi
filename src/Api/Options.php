<?php

namespace Dwnload\WPSettingsApi\Api;

/**
 * Class Options
 *
 * @package Dwnload\WPSettingsApi\Api
 */
class Options {

    /**
     * Get the value of a settings field
     *
     * @param string $option_key Settings field key name in the section option array.
     * @param string $section_id The Section object ID the option belongs too.
     * @param mixed $default (Optional) Default value if option is not found.
     *                          Defaults to an empty string.
     *
     * @return mixed
     */
    public static function getOption( string $option_key, string $section_id, $default = '' ) {
        $options = Options::getOptions( $section_id );

        return $options[ $option_key ] ?? $default;
    }

    /**
     * Get the full settings Section ID array.
     *
     * @param string $section_id The Section object ID the option belongs too.
     *
     * @return array
     */
    public static function getOptions( string $section_id ) : array {
        return get_option( $section_id, [] );
    }

    /**
     * @param string $option_key Settings field key name in the section option array.
     * @param string $section_id The Section object ID the option belongs too.
     * @param mixed $default (Optional) Default value if option is not found.
     *                          Defaults to an empty string.
     * @param int $len (Optional) The Length of the un-obfuscated string. Defaults to `6`.
     *
     * @return mixed|string
     */
    public static function getObfuscatedOption( string $option_key, string $section_id, $default = '', int $len = 6 ) {
        $value = self::getOption( $option_key, $section_id, $default );

        if ( ! empty( $value ) ) {
            return str_repeat( '*', strlen( $value ) - $len ) . substr( $value, - $len, $len );
        }

        return $value;
    }

    /**
     * Is the value in question an obfuscated string?
     *
     * @param string $value
     *
     * @return bool
     */
    public static function isObfuscated( string $value ): bool {
        return strpos( $value, '****' ) !== false;
    }
}
