<?php

declare(strict_types=1);

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
use WP_Http;
use function add_option;
use function add_options_page;
use function add_query_arg;
use function add_settings_error;
use function add_settings_field;
use function add_settings_section;
use function add_site_option;
use function add_submenu_page;
use function admin_url;
use function array_map;
use function call_user_func;
use function check_admin_referer;
use function current_user_can;
use function defined;
use function do_action;
use function esc_html;
use function esc_html__;
use function get_option;
use function get_settings_errors;
use function get_site_option;
use function in_array;
use function is_array;
use function is_callable;
use function is_network_admin;
use function is_string;
use function map_deep;
use function method_exists;
use function network_admin_url;
use function register_setting;
use function sanitize_text_field;
use function set_transient;
use function sprintf;
use function update_site_option;
use function wp_die;
use function wp_get_referer;
use function wp_safe_redirect;
use function wp_unslash;

/**
 * Class WpSettingsApi
 * @package Dwnload\WpSettingsApi
 */
class WpSettingsApi extends AbstractHookProvider
{
    use HooksTrait;

    public const string ADMIN_SCRIPT_HANDLE = 'dwnload-wp-settings-api';
    public const string ADMIN_STYLE_HANDLE = self::ADMIN_SCRIPT_HANDLE;
    public const string ADMIN_MEDIA_HANDLE = 'dwnload-wp-media-uploader';
    public const string FILTER_PREFIX = 'dwnload/wp_settings_api/';
    public const string ACTION_PREFIX = self::FILTER_PREFIX;
    public const string HOOK_INIT = self::ACTION_PREFIX . 'init';
    public const string HOOK_INIT_SLUG__S = self::HOOK_INIT . '-%s';
    public const int HOOK_PRIORITY = 999;
    public const string VERSION = '4.0.0';
    public const string CAPABILITY = 'manage_options';
    public const string NETWORK_CAPABILITY = 'manage_network_options';
    public const string NETWORK_PARENT_SLUG = 'settings.php';
    public const string NETWORK_SAVE_ACTION__S = 'dwnload_wp_settings_api_%s';
    public const string NETWORK_EDIT_PAGE = 'edit.php';
    public const string OPTIONS_PAGE = 'options.php';
    public const string SETTINGS_ERRORS_TRANSIENT = 'settings_errors';
    public const int SETTINGS_ERRORS_EXPIRATION = 30;

    /**
     * WpSettingsApi constructor.
     * @param PluginSettings $plugin_info
     */
    public function __construct(private readonly PluginSettings $plugin_info)
    {
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
            do_action(self::HOOK_INIT, (new SectionManager($this)), (new FieldManager()), $this);
        }, self::HOOK_PRIORITY);
        $this->addAction(self::HOOK_INIT, [$this, 'initMenuSlug'], 10, 3);
        if ($this->plugin_info->isNetwork()) {
            $this->addAction('network_admin_menu', [$this, 'addAdminMenu']);
            $this->addAction('network_admin_edit_' . $this->getNetworkSaveAction(), [$this, 'saveNetworkSettings']);
        } else {
            $this->addAction('admin_menu', [$this, 'addAdminMenu']);
        }
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
        do_action(
            sprintf(self::HOOK_INIT_SLUG__S, $this->getPluginInfo()->getMenuSlug()),
            $section_manager,
            $field_manager,
            $wp_settings_api
        );
    }

    /**
     * Get the URL the settings forms post to: options.php, or Network Admin edit.php for network pages.
     * @return string
     */
    public function getFormAction(): string
    {
        $scheme = defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ? 'https' : 'admin';

        if ($this->plugin_info->isNetwork()) {
            return add_query_arg(
                'action',
                $this->getNetworkSaveAction(),
                network_admin_url(self::NETWORK_EDIT_PAGE, $scheme)
            );
        }

        return admin_url(self::OPTIONS_PAGE, $scheme);
    }

    /**
     * Get the Network Admin edit.php action used to save a network settings page.
     * @return string
     */
    public function getNetworkSaveAction(): string
    {
        return sprintf(self::NETWORK_SAVE_ACTION__S, $this->plugin_info->getMenuSlug());
    }

    /**
     * Create admin menu and sub-menu items.
     */
    protected function addAdminMenu(): void
    {
        $args = [
            esc_html($this->plugin_info->getPageTitle()),
            esc_html($this->plugin_info->getMenuTitle()),
            $this->getAppCap(),
            apply_filters(self::FILTER_PREFIX . 'options_page_slug', $this->plugin_info->getMenuSlug()),
            function (): void {
                $this->settingsHtml();
            },
        ];
        $hook = $this->plugin_info->isNetwork() ?
            add_submenu_page(self::NETWORK_PARENT_SLUG, ...$args) :
            add_options_page(...$args);
        if (is_string($hook)) {
            $this->addAction('load-' . $hook, [(new AdminSettingsPage($this)), 'load'], 19);
        }
    }

    /**
     * Render the settings HTML.
     */
    protected function settingsHtml(): void
    {
        if (!current_user_can($this->getAppCap())) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wp-settings-api'));
        }

        include __DIR__ . '/views/settings-html.php';
    }

    /**
     * Initialize and registers the settings sections and fields to WordPress.
     */
    protected function adminInit(): void
    {
        // Network pages are only registered (and saved) in Network Admin.
        if ($this->plugin_info->isNetwork() && !is_network_admin()) {
            return;
        }

        // Register settings sections.
        foreach (SectionManager::getSection($this->plugin_info->getMenuSlug()) as $section) {
            if ($this->plugin_info->isNetwork()) {
                if (get_site_option($section->getId(), false) === false) {
                    add_site_option($section->getId(), []);
                }
            } elseif (get_option($section->getId(), false) === false) {
                add_option($section->getId(), []);
            }

            add_settings_section(
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
                        !is_callable($callback_array) ||
                        !method_exists($classObject, $field->getType())
                    ) {
                        $callback_array = $getCallbackArray();
                    }
                }

                add_settings_field(
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
            register_setting(
                $section->getId(),
                $section->getId(),
                fn($options): array => $this->sanitizeOptionsArray($options)
            );
        }
    }

    /**
     * Save a network settings page section (Network Admin `edit.php?action=…`).
     * Mirrors wp-admin/options.php: the nonce from settings_fields(), the page capability, and
     * update_site_option(), which runs the register_setting() sanitize callback via sanitize_option().
     */
    protected function saveNetworkSettings(): void
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified below for the requested section.
        $option_page = isset($_POST['option_page']) ? sanitize_text_field(wp_unslash($_POST['option_page'])) : '';
        $section_ids = array_map(
            static fn(SettingSection $section): string => $section->getId(),
            SectionManager::getSection($this->plugin_info->getMenuSlug())
        );

        if (!in_array($option_page, $section_ids, true)) {
            wp_die(
                esc_html__('Invalid settings section.', 'wp-settings-api'),
                '',
                ['response' => WP_Http::BAD_REQUEST]
            );
        }

        check_admin_referer($option_page . '-options');

        if (!current_user_can($this->getAppCap())) {
            wp_die(
                esc_html__('You do not have sufficient permissions to access this page.', 'wp-settings-api'),
                '',
                ['response' => WP_Http::FORBIDDEN]
            );
        }

        // Sanitized by the register_setting() callback (sanitizeOptionsArray) when update_site_option() runs.
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $value = isset($_POST[$option_page]) && is_array($_POST[$option_page]) ? wp_unslash($_POST[$option_page]) : [];
        update_site_option($option_page, $value);

        if (get_settings_errors() === []) {
            add_settings_error(
                'general',
                'settings_updated',
                esc_html__('Settings saved.', 'wp-settings-api'),
                'success'
            );
        }
        set_transient(self::SETTINGS_ERRORS_TRANSIENT, get_settings_errors(), self::SETTINGS_ERRORS_EXPIRATION);

        $referer = wp_get_referer();
        $redirect = is_string($referer) && $referer !== '' ?
            $referer :
            add_query_arg('page', $this->plugin_info->getMenuSlug(), network_admin_url(self::NETWORK_PARENT_SLUG));
        wp_safe_redirect(add_query_arg('settings-updated', 'true', $redirect));
        exit;
    }

    /**
     * Returns the allowed admin capability to modify or view settings.
     * @link https://codex.wordpress.org/Roles_and_Capabilities
     * @return string
     */
    private function getAppCap(): string
    {
        $capability = $this->plugin_info->isNetwork() ? self::NETWORK_CAPABILITY : self::CAPABILITY;

        return (string)apply_filters(self::FILTER_PREFIX . 'capability', $capability, $this->plugin_info);
    }

    /**
     * Sanitize callback for Settings API
     * @param mixed $options
     * @return array
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
        do_action(self::ACTION_PREFIX . 'before_sanitize_options', $options);

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
                $options[$option_slug] = call_user_func($sanitize_callback, $option_value, $options, $option_slug);
                continue;
            }

            // Treat everything as a string; sanitize array values (multicheck, multiselect, text_array) deeply.
            $options[$option_slug] = is_array($option_value) ?
                map_deep($option_value, 'sanitize_text_field') :
                sanitize_text_field($option_value);
        }

        /**
         * Hook loads after options are sanitized.
         * @param array $options The options array after getting sanitized
         */
        do_action(self::ACTION_PREFIX . 'after_sanitize_options', $options);

        return $options;
    }

    /**
     * Get sanitation callback for given option slug
     * @param string $option_slug option slug
     * @return callable|bool|string Boolean if no callback exists or Callable method
     */
    private function getSanitizeCallback(string $option_slug = ''): callable|bool|string
    {
        if (empty($option_slug)) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback.
        foreach (FieldManager::getFields() as $fields) {
            /**
             * Field object.
             * @var SettingField $field
             */
            foreach ($fields as $field) {
                if ($field->getName() !== $option_slug) {
                    continue;
                }

                // Secret fields are stored with the WordPress Secrets API when available (7.2+).
                if ($field->getType() === FieldTypes::FIELD_TYPE_SECRET) {
                    return Sanitize::class . '::sanitizeSecret';
                }

                // Call our obfuscated setting sanitizer so stars (****) don't get saved.
                if (
                    $field->isObfuscated() &&
                    method_exists(Sanitize::class, 'sanitizeObfuscated')
                ) {
                    return Sanitize::class . '::sanitizeObfuscated';
                }

                // Return the callback name.
                return !empty($field->getSanitizeCallback()) && is_callable($field->getSanitizeCallback()) ?
                    $field->getSanitizeCallback() : false;
            }
        }

        return false;
    }
}
