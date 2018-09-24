<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Settings;

use Dwnload\WpSettingsApi\AbstractApp;
use Dwnload\WpSettingsApi\Api\SettingSection;

/**
 * Class SectionManager
 *
 * @package Dwnload\WpSettingsApi\Settings
 */
class SectionManager extends AbstractApp
{

    /**
     * Array of SettingSection objects.
     *
     * @var array SettingSection[]
     */
    protected static $settings_sections = [];

    /**
     * Set settings sections
     *
     * @param SettingSection[] $sections An array of SettingSection objects.
     */
    public function setSections(array $sections)
    {
        self::$settings_sections[$this->getApp()->getMenuSlug()] = $sections;
    }

    /**
     * Add a new SettingSection object.
     *
     * @param SettingSection $section Array of SettingSection objects.
     *
     * @return string The registered section ID
     */
    public function addSection(SettingSection $section): string
    {
        self::$settings_sections[$this->getApp()->getMenuSlug()][] = $section;

        return $section->getId();
    }

    /**
     * Get all settings SettingSection objects as an array.
     *
     * @param string $menu_slug The App->getMenuSlug() instance id.
     *
     * @return SettingSection[] Array of SettingSection objects.
     */
    public static function getSections(string $menu_slug = null): array
    {
        return self::$settings_sections[$menu_slug] ?? self::$settings_sections;
    }
}
