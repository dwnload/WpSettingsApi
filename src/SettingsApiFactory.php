<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\PluginSettings;
use function json_encode;
use function md5;

/**
 * Class SettingsApiFactory
 * @package Dwnload\WpSettingsApi
 */
class SettingsApiFactory
{
    /**
     * Array of PluginSettings instances.
     * @var PluginSettings[] $instance
     */
    private static array $instance = [];

    /**
     * Create a new instance for the settings api.
     * @param array $fields
     * @return PluginSettings
     */
    public static function create(array $fields): PluginSettings
    {
        if (
            !isset(self::$instance[self::getId($fields)]) ||
            !(self::$instance[self::getId($fields)] instanceof PluginSettings)
        ) {
            self::$instance[self::getId($fields)] = new PluginSettings($fields);
        }

        return self::$instance[self::getId($fields)];
    }

    /**
     * Get the field ID.
     * @param array $fields
     * @return string
     */
    private static function getId(array $fields): string
    {
        return md5(json_encode($fields));
    }
}
