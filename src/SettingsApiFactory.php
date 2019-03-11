<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

/**
 * Class AppFactory
 *
 * @package Dwnload\WpSettingsApi
 */
class AppFactory
{
    /**
     * PluginInfo field settings array.
     *
     * @var array $fields
     */
    private static $fields = [];

    /**
     * @var App[] $app;
     */
    private static $app;

    /**
     * Create the App instance.
     *
     * @param array $fields
     *
     * @return App
     */
    public static function createApp(array $fields): App
    {
        if (!(self::$app[self::getId()] instanceof App)) {
            self::$fields = $fields;
            self::$app[self::getId()] = new App($fields);
        }

        return self::$app[self::getId()];
    }

    /**
     * Get the App instance.
     *
     * @return App
     */
    public static function getApp(): App
    {
        return self::$app[self::getId()];
    }

    /**
     * Get the field settings array.
     *
     * @return array
     */
    public static function getFields(): array
    {
        return self::$fields;
    }

    /**
     * Get the field ID.
     *
     * @return string
     */
    public static function getId(): string
    {
        return \serialize(self::getFields());
    }
}
