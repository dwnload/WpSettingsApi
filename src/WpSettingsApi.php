<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Admin\AdminSettingsPage;
use Dwnload\WpSettingsApi\Api\PluginSettings;
use Dwnload\WpSettingsApi\Api\Sanitize;
use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;
use TheFrosty\WpUtilities\Plugin\HooksTrait;

/**
 * Class WpSettingsApi
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
    public const HOOK_INIT = self::ACTION_PREFIX . 'init';
    public const HOOK_INIT_SLUG__S = self::HOOK_INIT . '-%s';
    public const HOOK_PRIORITY = 999;
    public const VERSION = '3.11.1';

    /**
     * The current plugin instance.
     * @var PluginSettings $plugin_info
     */
    private PluginSettings $plugin_info;

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
        $this->addAction('init', function (): void {
            /**
             * Fires when this plugin is loaded!
             * @param SectionManager Instance of the SectionManager object.
             * @param FieldManager Instance of the FieldManager object.
             * @param WpSettingsApi $this
             */
            \do_action(self::HOOK_INIT, (new SectionManager($this)), (new FieldManager()), $this);
        }, self::HOOK_PRIORITY);
        $this->addAction(self::HOOK_INIT, [$this, 'initMenuSlug'], 10, 3);
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
     * Is the current instance's menu slug equal to the passed slug.
     * @param string $menu_slug
     * @return bool
     * @since 3.6.1
     */
    public function isCurrentMenuSlug(string $menu_slug): bool
    {
        return $this->getPluginInfo()->getMenuSlug() === $menu_slug;
    }

    /**
     * Add a custom action for the current WpSettingsApi based on the instance menu slug.
     * @param SectionManager $section_manager Instance of the SectionManager object.
     * @param FieldManager $field_manager Instance of the FieldManager object.
     * @param WpSettingsApi $wp_settings_api
     */
    protected function initMenuSlug(
        SectionManager $section_manager,
        FieldManager $field_manager,
        WpSettingsApi $wp_settings_api
    ): void {
        \do_action(
            \sprintf(self::HOOK_INIT_SLUG__S, $this->getPluginInfo()->getMenuSlug()),
            $section_manager,
            $field_manager,
            $wp_settings_api
        );
    }

    /**
     * Create admin menu and sub-menu items.
     */
    protected function addAdminMenu(): void
    {
        $hook = \add_options_page(
            \esc_html($this->plugin_info->getPageTitle()),
            \esc_html($this->plugin_info->getMenuTitle()),
            $this->getAppCap(),
            \apply_filters(self::FILTER_PREFIX . 'options_page_slug', $this->plugin_info->getMenuSlug()),
            function (): void {
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
    protected function settingsHtml(): void
    {
        if (!\current_user_can($this->getAppCap())) {
            \wp_die(\esc_html__('You do not have sufficient permissions to access this page.', 'wp-settings-api'));
        }

        include __DIR__ . '/views/settings-html.php';
    }

    /**
     * Initialize and registers the settings sections and fields to WordPress.
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    protected function adminInit(): void
    {
        // Register settings sections.
        foreach (SectionManager::getSection($this->plugin_info->getMenuSlug()) as $section) {
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

        // Register settings fields.
        foreach (FieldManager::getFields() as $section_id => $fields) {
            /**
             * Field object.
             * @var SettingField[] $fields
             */
            foreach ($fields as $field) {
                $args = [
                    SettingField::ID => $field->getId(),
                    SettingField::DEFAULT => $field->getDefault(),
                    SettingField::DESC => $field->getDescription(),
                    SettingField::NAME => $field->getLabel(),
                    SettingField::OPTIONS => $field->getOptions(),
                    SettingField::SANITIZE => $field->getSanitizeCallback(),
                    SettingField::SECTION_ID => $field->getSectionId(),
                    SettingField::SIZE => $field->getSize(),
                    SettingField::REPEATER_FIELDS => $field->getFields(),
                    SettingField::FIELD_OBJECT => $field,
                ];

                $classObject = $field->getClassObject();
                $getCallbackArray = static fn(): array => [(new FieldTypes()), $field->getType()];
                $callback_array = $getCallbackArray();
                if ($classObject !== null) {
                    $callback_array = [$classObject, $field->getType()];

                    if (
                        !\is_callable($callback_array) ||
                        !\method_exists($classObject, $field->getType())
                    ) {
                        $callback_array = $getCallbackArray();
                    }
                }

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

        // Register settings setting.
        foreach (SectionManager::getSection($this->plugin_info->getMenuSlug()) as $section) {
            \register_setting(
                $section->getId(),
                $section->getId(),
                fn($options): array => $this->sanitizeOptionsArray($options)
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
     * @param mixed $options
     * @return array
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    private function sanitizeOptionsArray(mixed $options): array
    {
        if (empty($options)) {
            return (array)$options;
        }

        /**
         * Hook loads before options are sanitized. Manipulate options array here.
         * @var array $options The options array before getting sanitized
         */
        \do_action(self::ACTION_PREFIX . 'before_sanitize_options', $options);

        foreach ($options as $option_slug => $option_value) {
            $sanitize_callback = $this->getSanitizeCallback($option_slug);

            // If callback is set, call it.
            if (!empty($sanitize_callback)) {
                /**
                 * Sanitize Callback accepted args.
                 * @param mixed $option_value
                 * @param array $options
                 * @param string $option_slug
                 */
                $options[$option_slug] = \call_user_func($sanitize_callback, $option_value, $options, $option_slug);
                continue;
            }

            // Treat everything that's not an array as a string.
            if (!\is_array($option_value)) {
                $options[$option_slug] = \sanitize_text_field($option_value);
            }
        }

        /**
         * Hook loads after options are sanitized.
         * @param array $options The options array after getting sanitized
         */
        \do_action(self::ACTION_PREFIX . 'after_sanitize_options', $options);

        return $options;
    }

    /**
     * Get sanitation callback for given option slug
     * @param string $option_slug option slug
     * @return callable|bool|string Boolean if no callback exists or Callable method
     *
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    private function getSanitizeCallback(string $option_slug = ''): callable|bool|string
    {
        if (empty($option_slug)) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback.
        foreach (FieldManager::getFields() as $section_id => $fields) {
            /**
             * Field object.
             * @var SettingField $field
             */
            foreach ($fields as $field) {
                if ($field->getName() !== $option_slug) {
                    continue;
                }

                // Call our obfuscated setting sanitizer so stars (****) don't get saved.
                if (
                    $field->isObfuscated() &&
                    \method_exists(Sanitize::class, 'sanitizeObfuscated')
                ) {
                    return Sanitize::class . '::sanitizeObfuscated';
                }

                // Return the callback name.
                return !empty($field->getSanitizeCallback()) && \is_callable($field->getSanitizeCallback()) ?
                    $field->getSanitizeCallback() : false;
            }
        }

        return false;
    }
}
