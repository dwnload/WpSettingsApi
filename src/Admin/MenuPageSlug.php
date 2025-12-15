<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Admin;

/**
 * Interface MenuPageSlug.
 *
 * This class defines constants to use as the parent slug for attaching submenu
 * pages to built-in WordPress admin pages.
 * @since 2.2.0
 * @package Dwnload\WpSettingsApi\Admi
 */
interface MenuPageSlug
{

    /**
     * Slugs to use as parents for the built-in WordPress menu pages.
     */
    public const string DASHBOARD = 'index.php';
    public const string POSTS = 'edit.php';
    public const string MEDIA = 'upload.php';
    public const string PAGES = self::POSTS . '?post_type=page';
    public const string COMMENTS = 'edit-comments.php';
    public const string CUSTOM_POST_TYPE_S = self::POSTS . '?post_type=%s';
    public const string APPEARANCE = 'themes.php';
    public const string PLUGINS = 'plugins.php';
    public const string USERS = 'users.php';
    public const string TOOLS = 'tools.php';
    public const string SETTINGS = 'options-general.php';
    public const string NETWORK_SETTINGS = 'settings.php';
}
