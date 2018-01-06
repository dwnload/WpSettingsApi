<?php

namespace Dwnload\WpSettingsApi;

/**
 * Class AppFactory
 *
 * @package Dwnload\WpSettingsApi
 */
class AppFactory {

    /**
     * PluginInfo field settings array.
     *
     * @var array $fields
     */
    public static $fields = [];

    /**
     * @var App $app;
     */
    private static $app;

    /**
     * Create the App instance.
     *
     * @param array $fields
     *
     * @return App
     */
    public static function createApp( array $fields ): App {
        if ( ! ( self::$app instanceof App ) ) {
            self::$fields = $fields;
            self::$app = new App( $fields );
        }

        return self::$app;
    }

    /**
     * Get the App instance.
     *
     * @return App
     */
    public static function getApp(): App {
        return self::$app;
    }

    /**
     * Get the field settings array.
     *
     * @return array
     */
    public static function getFields(): array {
        return self::$fields;
    }
}
