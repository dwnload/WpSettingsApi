<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Settings;

use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\WpSettingsApi;
use function method_exists;

/**
 * Class SectionManager
 * @package Dwnload\WpSettingsApi\Settings
 */
class SectionManager
{

    /**
     * SectionManager constructor.
     * @param WpSettingsApi $wp_settings_api
     */
    public function __construct(private readonly WpSettingsApi $wp_settings_api)
    {
    }

    /**
     * Array of SettingSection objects.
     * @var array<SettingSection> $settings_sections
     */
    protected static array $settings_sections = [];

    /**
     * IDs of sections registered on a network settings page, stored as network options.
     * @var array<string, true> $network_sections
     */
    protected static array $network_sections = [];

    /**
     * Set settings sections
     * @param SettingSection[] $sections An array of SettingSection objects.
     */
    public function setSections(array $sections): void
    {
        self::$settings_sections[$this->wp_settings_api->getPluginInfo()->getMenuSlug()] = $sections;
        foreach ($sections as $section) {
            if ($section instanceof SettingSection) {
                $this->registerNetworkSection($section);
            }
        }
    }

    /**
     * Add a new SettingSection object.
     * @param SettingSection $section Array of SettingSection objects.
     * @return string The registered section ID
     */
    public function addSection(SettingSection $section): string
    {
        self::$settings_sections[$this->wp_settings_api->getPluginInfo()->getMenuSlug()][] = $section;
        $this->registerNetworkSection($section);

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

    /**
     * Is the section registered on a network settings page (stored as a network option)?
     * @param string $section_id
     * @return bool
     */
    public static function isNetworkSection(string $section_id): bool
    {
        return isset(self::$network_sections[$section_id]);
    }

    /**
     * Track sections added from a network settings page.
     * @param SettingSection $section
     */
    private function registerNetworkSection(SettingSection $section): void
    {
        $plugin_info = $this->wp_settings_api->getPluginInfo();
        // PluginSettings may come from an older copy of this package (bundled by another plugin) without it.
        if (method_exists($plugin_info, 'isNetwork') && $plugin_info->isNetwork()) {
            self::$network_sections[$section->getId()] = true;
        }
    }
}
