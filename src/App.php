<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\PluginInfo;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class Bootstrap
 *
 * @package Dwnload\WpSettingsApi
 */
class App extends PluginInfo implements WpHooksInterface
{
    use HooksTrait;

    const FILTER_PREFIX = 'dwnload/wp_settings_api/';
    const ACTION_PREFIX = self::FILTER_PREFIX;
    const HOOK_PRIORITY = 999;

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        /**
         * "Out of the frying pan, and into the fire!"
         */
        $this->addAction('init', function () {
            if (\did_action(self::ACTION_PREFIX . 'init')) {
                return;
            }

            /**
             * Fires when this plugin is loaded!
             *
             * @var SectionManager Instance of the SectionManager object.
             * @var FieldManager Instance of the FieldManager object.
             */
            \do_action(self::ACTION_PREFIX . 'init', (new SectionManager($this)), (new FieldManager()));
        }, self::HOOK_PRIORITY);
    }

    /**
     * Returns the allowed admin capability to modify or view settings.
     *
     * @link https://codex.wordpress.org/Roles_and_Capabilities
     * @return string
     */
    public function getAppCap(): string
    {
        return (string)\apply_filters(self::FILTER_PREFIX . 'capability', 'manage_options');
    }

    /**
     * Return the URL of from our plugin root directory.
     *
     * @param string $src The file path relative to the root of the plugin directory.
     *
     * @return string
     */
    public function getPluginsUrl(string $src): string
    {
        return \plugins_url($src, __DIR__);
    }
}
