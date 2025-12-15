<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

/**
 * Interface WpSettingsApiHooks
 * @package Dwnload\WpSettingsApi
 */
interface ActionHookName
{

    public const string ADMIN_SETTINGS_ADMIN_SCRIPTS = WpSettingsApi::FILTER_PREFIX . 'admin_scripts';
    public const string ADMIN_SETTINGS_ADMIN_STYLES = WpSettingsApi::FILTER_PREFIX . 'admin_styles';
    public const string ADMIN_SETTINGS_LOCALIZE_SCRIPT = WpSettingsApi::FILTER_PREFIX . 'localize_script';
    public const string AFTER_SETTINGS_SECTIONS_FORM = WpSettingsApi::ACTION_PREFIX . 'after_settings_sections_form';
    public const string FORM_BOTTOM = WpSettingsApi::ACTION_PREFIX . 'form_bottom';
    public const string FORM_TOP = WpSettingsApi::ACTION_PREFIX . 'form_top';
    public const string SETTINGS_BEFORE_SUBMIT_BUTTON = WpSettingsApi::ACTION_PREFIX . 'before_submit_button';
    public const string SETTINGS_SETTINGS_SIDEBARS = WpSettingsApi::ACTION_PREFIX . 'settings_sidebars';
    public const string SETTINGS_STICKY_ADMIN_NOTICE = WpSettingsApi::ACTION_PREFIX . 'sticky_admin_notice';
}
