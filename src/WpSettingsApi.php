<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\Sanitize;
use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\LocalizeScripts;
use Dwnload\WpSettingsApi\Api\Script;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Api\Style;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class WpSettingsApi
 *
 * @package Dwnload\WpSettingsApi
 */
class WpSettingsApi extends AbstractApp implements WpHooksInterface
{
    const ADMIN_SCRIPT_HANDLE = 'dwnload-wp-settings-api';
    const ADMIN_STYLE_HANDLE = self::ADMIN_SCRIPT_HANDLE;
    const ADMIN_MEDIA_HANDLE = 'dwnload-wp-media-uploader';

    const VERSION = '2.6';

    /**
     * Fire away captain!
     */
    public function addHooks()
    {
        $this->getApp()->addHooks();
        $this->addAction('admin_menu', [$this, 'addAdminMenu']);
        $this->addAction('admin_init', [$this, 'adminInit']);
    }

    /**
     * Create admin menu and sub-menu items.
     */
    protected function addAdminMenu()
    {
        $hook = \add_options_page(
            \esc_html($this->getApp()->getPageTitle()),
            \esc_html($this->getApp()->getMenuTitle()),
            $this->getApp()->getAppCap(),
            \apply_filters(App::FILTER_PREFIX . 'options_page_slug', $this->getApp()->getMenuSlug()),
            [$this, 'settingsHtml']
        );
        $this->addAction('load-' . $hook, [$this, 'load'], 19);
    }

    /**
     * Render the settings html.
     */
    public function settingsHtml()
    {
        if (!\current_user_can($this->getApp()->getAppCap())) {
            \wp_die(\esc_html__('You do not have sufficient permissions to access this page.'));
        }

        include __DIR__ . '/views/settings-html.php';
    }

    /**
     * Initialize and registers the settings sections and fields to WordPress.
     */
    protected function adminInit()
    {
        // Register settings sections
        foreach (SectionManager::getSections($this->getApp()->getMenuSlug()) as $section) {
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
        foreach (SectionManager::getSections($this->getApp()->getMenuSlug()) as $section) {
            \register_setting(
                $section->getId(),
                $section->getId(),
                [$this, 'sanitizeOptionsArray']
            );
        }
    }

    /**
     * Fire hooks needed on the Settings Page (only).
     */
    protected function load()
    {
        \do_action(App::ACTION_PREFIX . 'settings_page_loaded');
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
                Script::HANDLE => self::ADMIN_SCRIPT_HANDLE,
                Script::SRC => 'src/assets/js/admin.js',
                Script::DEPENDENCIES => ['jquery'],
                Script::VERSION => $this->getApp()->getVersion(),
                Script::IN_FOOTER => true,
            ]),
            new Script([
                Script::HANDLE => self::ADMIN_MEDIA_HANDLE,
                Script::SRC => 'src/assets/js/wp-media-uploader.js',
                Script::DEPENDENCIES => ['jquery'],
                Script::VERSION => $this->getApp()->getVersion(),
                Script::IN_FOOTER => true,
                Script::INLINE_SCRIPT => 'jQuery.wpMediaUploader();',
            ]),
        ];

        $scripts = \apply_filters(App::FILTER_PREFIX . 'admin_scripts', $default_scripts);
        $this->enqueueScripts($scripts);

        /**
         * Styles
         */
        $default_styles = [
            new Style([
                Style::HANDLE => self::ADMIN_STYLE_HANDLE,
                Style::SRC => 'src/assets/css/admin.css',
                Style::DEPENDENCIES => [],
                Style::VERSION => $this->getApp()->getVersion(),
                Style::MEDIA => 'screen',
            ]),
        ];

        $styles = \apply_filters(App::FILTER_PREFIX . 'admin_styles', $default_styles);
        $this->enqueueStyles($styles);
    }

    /**
     * Localize PHP objects to pass to any page JS.
     */
    protected function localizeScripts()
    {
        $localize = new LocalizeScripts();

        $localize->add('prefix', $this->getApp()->getPrefix());
        $localize->add('nonce', \wp_create_nonce($this->getApp()->getNonce()));

        /**
         * Use this action hook to pass new objects into the script output.
         *
         * @var string Empty string value.
         * @var LocalizeScripts $localize Use this object to add new localized values to the registered output.
         */
        \do_action(App::ACTION_PREFIX . 'localize_script', '', $localize);

        // The $handle needs to match the enqueued handle.
        \wp_localize_script(self::ADMIN_SCRIPT_HANDLE, Script::OBJECT_NAME, $localize->getAllVars());
    }

    /**
     * Sanitize callback for Settings API
     *
     * @param mixed $options
     *
     * @return array
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function sanitizeOptionsArray($options): array
    {
        if (empty($options)) {
            return (array)$options;
        }

        /**
         * Hook loads before options are sanitized. Manipulate options array here.
         *
         * @var array $options The options array before getting sanitized
         */
        \do_action(App::ACTION_PREFIX . 'before_sanitize_options', $options);

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
        \do_action(App::ACTION_PREFIX . 'after_sanitize_options', $options);

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
    protected function getSanitizeCallback(string $option_slug = '')
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
                    $this->getApp()->getPluginsUrl($script->getSrc()),
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
                    $this->getApp()->getPluginsUrl($style->getSrc()),
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
