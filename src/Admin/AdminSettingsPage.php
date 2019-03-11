<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Admin;

use Dwnload\WpSettingsApi\Api\LocalizeScripts;
use Dwnload\WpSettingsApi\Api\Script;
use Dwnload\WpSettingsApi\Api\Style;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\WpUtilities\Plugin\HooksTrait;

/**
 * Class AdminSettingsPage
 *
 * @package Dwnload\WpSettingsApi\Admin
 */
class AdminSettingsPage
{
    use HooksTrait;

    /**
     * WpSettingsApi object.
     * @var WpSettingsApi $wp_settings_api
     */
    private $wp_settings_api;

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
    public function load()
    {
        \do_action(WpSettingsApi::ACTION_PREFIX . 'settings_page_loaded');
        $this->addAction('admin_enqueue_scripts', [$this, 'adminEnqueueScripts'], 99);
        $this->addAction('admin_footer', [$this, 'localizeScripts'], 99);
    }

    /**
     * Enqueue scripts and styles for the Settings page.
     */
    protected function adminEnqueueScripts()
    {
        /** WordPress Core */
        \wp_enqueue_media();
        \wp_enqueue_script('wp-color-picker');
        \wp_enqueue_style('wp-color-picker');

        /**
         * Scripts
         */
        $default_scripts = [
            new Script([
                Script::HANDLE => WpSettingsApi::ADMIN_SCRIPT_HANDLE,
                Script::SRC => 'src/assets/js/admin.js',
                Script::DEPENDENCIES => ['jquery'],
                Script::VERSION => $this->wp_settings_api->getPluginInfo()->getVersion(),
                Script::IN_FOOTER => true,
            ]),
            new Script([
                Script::HANDLE => WpSettingsApi::ADMIN_MEDIA_HANDLE,
                Script::SRC => 'src/assets/js/wp-media-uploader.js',
                Script::DEPENDENCIES => ['jquery'],
                Script::VERSION => $this->wp_settings_api->getPluginInfo()->getVersion(),
                Script::IN_FOOTER => true,
                Script::INLINE_SCRIPT => 'jQuery.wpMediaUploader();',
            ]),
        ];

        $scripts = \apply_filters(WpSettingsApi::FILTER_PREFIX . 'admin_scripts', $default_scripts);
        $this->enqueueScripts($scripts);

        /**
         * Styles
         */
        $default_styles = [
            new Style([
                Style::HANDLE => WpSettingsApi::ADMIN_STYLE_HANDLE,
                Style::SRC => 'src/assets/css/admin.css',
                Style::DEPENDENCIES => [],
                Style::VERSION => $this->wp_settings_api->getPluginInfo()->getVersion(),
                Style::MEDIA => 'screen',
            ]),
        ];

        $styles = \apply_filters(WpSettingsApi::FILTER_PREFIX . 'admin_styles', $default_styles);
        $this->enqueueStyles($styles);
    }

    /**
     * Localize PHP objects to pass to any page JS.
     */
    protected function localizeScripts()
    {
        $localize = new LocalizeScripts();

        $localize->add('prefix', $this->wp_settings_api->getPluginInfo()->getPrefix());
        $localize->add('nonce', \wp_create_nonce($this->wp_settings_api->getPluginInfo()->getNonce()));

        /**
         * Use this action hook to pass new objects into the script output.
         *
         * @var string Empty string value.
         * @var LocalizeScripts $localize Use this object to add new localized values to the registered output.
         */
        \do_action(WpSettingsApi::ACTION_PREFIX . 'localize_script', '', $localize);

        // The $handle needs to match the enqueued handle.
        \wp_localize_script(WpSettingsApi::ADMIN_SCRIPT_HANDLE, Script::OBJECT_NAME, $localize->getAllVars());
    }

    /**
     * Helper to enqueue scripts via proper registration.
     *
     * @uses wp_register_script()
     * @uses wp_enqueue_script()
     *
     * @param Script[] $scripts An array af Script objects.
     */
    protected function enqueueScripts(array $scripts)
    {
        /** @var Script $script */
        foreach ($scripts as $script) {
            if (!\wp_script_is($script->getHandle(), 'registered')) {
                \wp_register_script(
                    $script->getHandle(),
                    $script->getSrc(),
                    $script->getDependencies(),
                    $script->getVersion(),
                    $script->getInFooter()
                );
                \wp_enqueue_script($script->getHandle());
                $this->addInlineScript($script);
                continue;
            }
            \wp_enqueue_script($script->getHandle());
            $this->addInlineScript($script);
        }
    }

    /**
     * Helper to enqueue styles via proper registration.
     *
     * @uses wp_register_style()
     * @uses wp_enqueue_style()
     *
     * @param Style[] $styles An array af Style objects.
     */
    protected function enqueueStyles(array $styles)
    {
        /** @var Style $style */
        foreach ($styles as $style) {
            if (!\wp_style_is($style->getHandle(), 'registered')) {
                \wp_register_style(
                    $style->getHandle(),
                    $style->getSrc(),
                    $style->getDependencies(),
                    $style->getVersion(),
                    $style->getMedia()
                );
                \wp_enqueue_style($style->getHandle());
                continue;
            }
            \wp_enqueue_style($style->getHandle());
        }
    }

    /**
     * Add an inline script.
     * @param Script $script
     */
    private function addInlineScript(Script $script)
    {
        if (!empty($script->getInlineScript())) {
            \wp_add_inline_script($script->getHandle(), $script->getInlineScript());
        }
    }
}
