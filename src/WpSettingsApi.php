<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Admin\AdminSettingsPage;
use Dwnload\WpSettingsApi\Api\PluginSettings;
use Dwnload\WpSettingsApi\Api\Sanitize;
use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;
use TheFrosty\WpUtilities\Plugin\HooksTrait;

/**
 * Class WpSettingsApi
 *
 * @package Dwnload\WpSettingsApi
 */
class WpSettingsApi extends AbstractHookProvider
{
    use HooksTrait;

    public const ADMIN_SCRIPT_HANDLE = 'dwnload-wp-settings-api';
    public const ADMIN_STYLE_HANDLE = self::ADMIN_SCRIPT_HANDLE;
    public const ADMIN_MEDIA_HANDLE = 'dwnload-wp-media-uploader';
    public const FILTER_PREFIX = 'dwnload/wp_settings_api/';
    public const ACTION_PREFIX = self::FILTER_PREFIX;
    public const HOOK_PRIORITY = 999;
    public const VERSION = '3.2.3';

    /**
     * The current plugin instance.
     * @var PluginSettings $plugin_info
     */
    private $plugin_info;

    /**
     * WpSettingsApi constructor.
     * @param PluginSettings $info
     */
    public function __construct(PluginSettings $info)
    {
        $this->plugin_info = $info;
    }

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction('init', function () {
            if (\did_action(self::ACTION_PREFIX . 'init')) {
                return;
            }

            /**
             * Fires when this plugin is loaded!
             *
             * @param SectionManager Instance of the SectionManager object.
             * @param FieldManager Instance of the FieldManager object.
             * @param WpSettingsApi $this
             */
            \do_action(self::ACTION_PREFIX . 'init', (new SectionManager($this)), (new FieldManager()), $this);
        }, self::HOOK_PRIORITY);
        $this->addAction('admin_menu', [$this, 'addAdminMenu']);
        $this->addAction('admin_init', [$this, 'adminInit']);
    }

    /**
     * Get the current PluginInfo object.
     * @return PluginSettings
     */
    public function getPluginInfo(): PluginSettings
    {
        return $this->plugin_info;
    }

    /**
     * Create admin menu and sub-menu items.
     */
    protected function addAdminMenu()
    {
        $hook = \add_options_page(
            \esc_html($this->plugin_info->getPageTitle()),
            \esc_html($this->plugin_info->getMenuTitle()),
            $this->getAppCap(),
            \apply_filters(self::FILTER_PREFIX . 'options_page_slug', $this->plugin_info->getMenuSlug()),
            function () {
                $this->settingsHtml();
            }
        );
        if (\is_string($hook)) {
            $this->addAction('load-' . $hook, [(new AdminSettingsPage($this)), 'load'], 19);
        }
    }

    /**
     * Render the settings html.
     */
    protected function settingsHtml()
    {
        if (!\current_user_can($this->getAppCap())) {
            \wp_die(\esc_html__('You do not have sufficient permissions to access this page.'));
        }

        include __DIR__ . '/views/settings-html.php';
    }

    /**
     * Initialize and registers the settings sections and fields to WordPress.
     */
    protected function adminInit() // phpcs:ignore
    {
        // Register settings sections
        foreach (SectionManager::getSection($this->plugin_info->getMenuSlug()) as $section) {
            /** @var SettingSection $section */
            if (\get_option($section->getId(), false) === false) {
                \add_option($section->getId(), []);
            }

            \add_settings_section(
                $section->getId(),
                $section->getTitle(),
                '__return_false',
                $section->getId()
            );
        }

        // Register settings fields
        foreach (FieldManager::getFields() as $section_id => $fields) {
            foreach ($fields as $field) {
                /** @var SettingField $field */
                $args = [
                    SettingField::ID => $field->getId(),
                    SettingField::DEFAULT => $field->getDefault(),
                    SettingField::DESC => $field->getDescription(),
                    SettingField::NAME => $field->getLabel(),
                    SettingField::OPTIONS => $field->getOptions(),
                    SettingField::SANITIZE => $field->getSanitizeCallback(),
                    SettingField::SECTION_ID => $field->getSectionId(),
                    SettingField::SIZE => $field->getSize(),
                    SettingField::FIELD_OBJECT => $field,
                ];

                $callback_array = [$field->getClassObject(), $field->getType()];

                if (!\is_callable($callback_array) ||
                    !\class_exists(\get_class($field->getClassObject())) ||
                    !\method_exists($field->getClassObject(), $field->getType())
                ) {
                    $callback_array = [(new FieldTypes()), $field->getType()];
                }

                // @todo double check `$callback_array` fallback is callable.

                \add_settings_field(
                    $section_id . '[' . $field->getName() . ']',
                    $field->getLabel(),
                    $callback_array,
                    $section_id,
                    $section_id,
                    $args
                );
            }
        }

        // Register settings setting
        foreach (SectionManager::getSection($this->plugin_info->getMenuSlug()) as $section) {
            \register_setting(
                $section->getId(),
                $section->getId(),
                function ($options): array { // phpcs:ignore
                    return $this->sanitizeOptionsArray($options);
                }
            );
        }
    }

    /**
     * Returns the allowed admin capability to modify or view settings.
     * @link https://codex.wordpress.org/Roles_and_Capabilities
     * @return string
     */
    private function getAppCap(): string
    {
        return (string)\apply_filters(self::FILTER_PREFIX . 'capability', 'manage_options');
    }

    /**
     * Sanitize callback for Settings API
     *
     * @param mixed $options
     *
     * @return array
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    private function sanitizeOptionsArray($options): array
    {
        if (empty($options)) {
            return (array)$options;
        }

        /**
         * Hook loads before options are sanitized. Manipulate options array here.
         *
         * @var array $options The options array before getting sanitized
         */
        \do_action(self::ACTION_PREFIX . 'before_sanitize_options', $options);

        foreach ($options as $option_slug => $option_value) {
            $sanitize_callback = $this->getSanitizeCallback($option_slug);

            // If callback is set, call it
            if (!empty($sanitize_callback)) {
                /**
                 * Sanitize Callback accepted args.
                 *
                 * @param mixed $option_value
                 * @param array $options
                 * @param string $option_slug
                 */
                $options[$option_slug] = \call_user_func($sanitize_callback, $option_value, $options, $option_slug);
                continue;
            }

            // Treat everything that's not an array as a string
            if (!\is_array($option_value)) {
                $options[$option_slug] = \sanitize_text_field($option_value);
                continue;
            }
        }

        /**
         * Hook loads after options are sanitized.
         *
         * @var array $options The options array after getting sanitized
         */
        \do_action(self::ACTION_PREFIX . 'after_sanitize_options', $options);

        return $options;
    }

    /**
     * Get sanitation callback for given option slug
     *
     * @param string $option_slug option slug
     *
     * @return bool|callable Boolean if no callback exists or Callable method
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    private function getSanitizeCallback(string $option_slug = '')
    {
        if (empty($option_slug)) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback
        foreach (FieldManager::getFields() as $section_id => $fields) {
            /** @var SettingField $field */
            foreach ($fields as $field) {
                if ($field->getName() !== $option_slug) {
                    continue;
                }

                // Call our obfuscated setting sanitizer so stars (****) don't get saved.
                if ($field->isObfuscated() &&
                    \method_exists(Sanitize::class, 'sanitizeObfuscated')
                ) {
                    return Sanitize::class . '::sanitizeObfuscated';
                }

                // Return the callback name
                return !empty($field->getSanitizeCallback()) &&
                \is_callable($field->getSanitizeCallback()) ?
                    $field->getSanitizeCallback() : false;
            }
        }

        return false;
    }
}
