<?php

namespace Dwnload\WPSettingsApi\Api;

/**
 * Class LocalizeScripts
 * This class helps localize settings variables passed from PHP to JS.
 *
 * @package Dwnload\WPSettingsApi\Api
 */
class LocalizeScripts {

    /**
     * Localized array to pass from PHP to JS.
     *
     * @var array $_vars
     */
    protected static $_vars = [];

    /**
     * Add a key/value to the Array.
     *
     * @param string $key
     * @param string $value
     */
    public function add( string $key, string $value ) {
        self::$_vars[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
    }

    /**
     * Get the Array.
     *
     * @return array
     */
    public function getAllVars() : array {
        return self::$_vars;
    }
}
