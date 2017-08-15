<?php

namespace Dwnload\WPSettingsApi\Api;

/**
 * Class Sanitize
 *
 * @package Dwnload\WPSettingsApi\Api
 */
class Sanitize {

    /**
     * @param $value
     * @param array $options
     * @param string $option_slug
     *
     * @return string
     */
    public static function sanitizeObfuscated( $value, array $options, string $option_slug ) {
        return sanitize_text_field( $options[ $option_slug ] );
    }
}
