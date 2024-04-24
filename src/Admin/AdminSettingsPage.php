<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Admin;

use Dwnload\WpSettingsApi\ActionHookName;
use Dwnload\WpSettingsApi\Api\LocalizeScripts;
use Dwnload\WpSettingsApi\Api\Script;
use Dwnload\WpSettingsApi\Api\Style;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use function apply_filters;
use function defined;
use function did_action;
use function do_action;
use function sprintf;
use function str_replace;
use function wp_add_inline_script;
use function wp_enqueue_media;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;
use function wp_register_script;
use function wp_register_style;
use function wp_script_is;
use function wp_style_is;
use const SCRIPT_DEBUG;

/**
 * Class AdminSettingsPage
 * @package Dwnload\WpSettingsApi\Admin
 */
class AdminSettingsPage
{

    use HooksTrait;

    /**
     * WpSettingsApi object.
     * @var WpSettingsApi $wp_settings_api
     */
    private WpSettingsApi $wp_settings_api;

    /**
     * AdminSettingsPage constructor.
     * @param WpSettingsApi $wp_settings_api
     */
    public function __construct(WpSettingsApi $wp_settings_api)
    {
        $this->wp_settings_api = $wp_settings_api;
    }

    /**
     * Fire hooks needed on the Settings Page (only).
     */
    public function load(): void
    {
        do_action(WpSettingsApi::ACTION_PREFIX . 'settings_page_loaded');
        $this->addAction('admin_enqueue_scripts', [$this, 'adminEnqueueScripts'], 99);
        $this->addAction('admin_footer', [$this, 'localizeScripts'], 99);
    }

    /**
     * Enqueue scripts and styles for the Settings page.
     */
    protected function adminEnqueueScripts(): void
    {
        /** WordPress Core */
        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }

        $use_local = apply_filters(WpSettingsApi::FILTER_PREFIX . 'use_local_scripts', false);
        $get_src = function (string $path) use ($use_local): string {
            if ($use_local) {
                return $this->wp_settings_api->getPlugin()->getUrl($path);
            }

            $debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

            return sprintf(
                'https://cdn.jsdelivr.net/gh/dwnload/wpSettingsApi@%s/%s',
                apply_filters(WpSettingsApi::FILTER_PREFIX . 'scripts_version', WpSettingsApi::VERSION),
                $debug === true ? $path : str_replace(['.css', '.js'], ['.min.css', '.min.js'], $path)
            );
        };

        /**
         * Scripts
         */
        $default_scripts = [
            new Script([
                Script::HANDLE => 'wp-color-picker-alpha',
                Script::SRC => $get_src('src/assets/js/wp-color-picker-alpha.js'),
                Script::DEPENDENCIES => ['jquery', 'wp-color-picker'],
                Script::VERSION => '3.0.3',
                Script::IN_FOOTER => true,
            ]),
            new Script([
                Script::HANDLE => WpSettingsApi::ADMIN_SCRIPT_HANDLE,
                Script::SRC => $get_src('src/assets/js/admin.js'),
                Script::DEPENDENCIES => ['jquery', 'wp-color-picker-alpha'],
                Script::VERSION => $this->wp_settings_api->getPluginInfo()->getVersion(),
                Script::IN_FOOTER => true,
            ]),
            new Script([
                Script::HANDLE => WpSettingsApi::ADMIN_MEDIA_HANDLE,
                Script::SRC => $get_src('src/assets/js/wp-media-uploader.js'),
                Script::DEPENDENCIES => ['jquery'],
                Script::VERSION => $this->wp_settings_api->getPluginInfo()->getVersion(),
                Script::IN_FOOTER => true,
            ]),
        ];

        $scripts = apply_filters(ActionHookName::ADMIN_SETTINGS_ADMIN_SCRIPTS, $default_scripts);
        $this->enqueueScripts($scripts);

        /**
         * Styles
         */
        $default_styles = [
            new Style([
                Style::HANDLE => WpSettingsApi::ADMIN_STYLE_HANDLE,
                Style::SRC => $get_src('src/assets/css/admin.css'),
                Style::DEPENDENCIES => ['wp-color-picker'],
                Style::VERSION => $this->wp_settings_api->getPluginInfo()->getVersion(),
                Style::MEDIA => 'screen',
            ]),
        ];

        $styles = apply_filters(ActionHookName::ADMIN_SETTINGS_ADMIN_STYLES, $default_styles);
        $this->enqueueStyles($styles);
    }

    /**
     * Localize PHP objects to pass to any page JS.
     */
    protected function localizeScripts(): void
    {
        $localize = new LocalizeScripts();

        $localize->add('prefix', $this->wp_settings_api->getPluginInfo()->getPrefix());
        $localize->add('nonce', \wp_create_nonce($this->wp_settings_api->getPluginInfo()->getNonce()));

        /**
         * Use this action hook to pass new objects into the script output.
         * @var string Empty string value.
         * @var LocalizeScripts $localize Use this object to add new localized values to the registered output.
         */
        do_action(ActionHookName::ADMIN_SETTINGS_LOCALIZE_SCRIPT, '', $localize);

        // The $handle needs to match the enqueued handle.
        wp_localize_script(WpSettingsApi::ADMIN_SCRIPT_HANDLE, Script::OBJECT_NAME, $localize->getAllVars());
    }

    /**
     * Helper to enqueue scripts via proper registration.
     * @param Script[] $scripts An array af Script objects.
     * @uses wp_enqueue_script()
     * @uses wp_register_script()
     */
    protected function enqueueScripts(array $scripts): void
    {
        foreach ($scripts as $script) {
            if (!wp_script_is($script->getHandle(), 'registered')) {
                wp_register_script(
                    $script->getHandle(),
                    $script->getSrc(),
                    $script->getDependencies(),
                    $script->getVersion(),
                    $script->getInFooter()
                );
                wp_enqueue_script($script->getHandle());
                $this->addInlineScript($script);
                continue;
            }
            wp_enqueue_script($script->getHandle());
            $this->addInlineScript($script);
        }
    }

    /**
     * Helper to enqueue styles via proper registration.
     * @param Style[] $styles An array af Style objects.
     * @uses wp_enqueue_style()
     * @uses wp_register_style()
     */
    protected function enqueueStyles(array $styles): void
    {
        foreach ($styles as $style) {
            if (!wp_style_is($style->getHandle(), 'registered')) {
                wp_register_style(
                    $style->getHandle(),
                    $style->getSrc(),
                    $style->getDependencies(),
                    $style->getVersion(),
                    $style->getMedia()
                );
                wp_enqueue_style($style->getHandle());
                continue;
            }
            wp_enqueue_style($style->getHandle());
        }
    }

    /**
     * Add an inline script.
     * @param Script $script
     */
    private function addInlineScript(Script $script): void
    {
        if (!empty($script->getInlineScript())) {
            wp_add_inline_script($script->getHandle(), $script->getInlineScript());
        }
    }
}
