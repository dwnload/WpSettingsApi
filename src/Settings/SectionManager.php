<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Settings;

use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\WpSettingsApi;

/**
 * Class SectionManager
 * @package Dwnload\WpSettingsApi\Settings
 */
class SectionManager
{

    /**
     * WpSettingsApi object.
     * @var WpSettingsApi $wp_settings_api
     */
    private WpSettingsApi $wp_settings_api;

    /**
     * SectionManager constructor.
     * @param WpSettingsApi $wp_settings_api
     */
    public function __construct(WpSettingsApi $wp_settings_api)
    {
        $this->wp_settings_api = $wp_settings_api;
    }

    /**
     * Array of SettingSection objects.
     * @var array<SettingSection> $settings_sections
     */
    protected static array $settings_sections = [];

    /**
     * Set settings sections
     * @param SettingSection[] $sections An array of SettingSection objects.
     */
    public function setSections(array $sections): void
    {
        self::$settings_sections[$this->wp_settings_api->getPluginInfo()->getMenuSlug()] = $sections;
    }

    /**
     * Add a new SettingSection object.
     * @param SettingSection $section Array of SettingSection objects.
     * @return string The registered section ID
     */
    public function addSection(SettingSection $section): string
    {
        self::$settings_sections[$this->wp_settings_api->getPluginInfo()->getMenuSlug()][] = $section;

        return $section->getId();
    }

    /**
     * Get all settings SettingSection objects as an array.
     * @param string $menu_slug The WpSettingsApi->getPluginInfo()->getMenuSlug() instance id.
     * @return SettingSection[] Array of SettingSection objects.
     */
    public static function getSection(string $menu_slug): array
    {
        return self::$settings_sections[$menu_slug] ?? [];
    }

    /**
     * Get all sections.
     * @return array
     */
    public static function getSections(): array
    {
        return self::$settings_sections;
    }
}
