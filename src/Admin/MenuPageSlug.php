<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Admin;

/**
 * Interface MenuPageSlug.
 *
 * This class defines constants to use as the parent slug for attaching submenu
 * pages to built-in WordPress admin pages.
 *
 * @since 2.2.0
 *
 * @package Dwnload\WpSettingsApi\Admi
 */
interface MenuPageSlug
{

    /**
     * Slugs to use as parents for the built-in WordPress menu pages.
     */
    public const DASHBOARD = 'index.php';
    public const POSTS = 'edit.php';
    public const MEDIA = 'upload.php';
    public const PAGES = self::POSTS . '?post_type=page';
    public const COMMENTS = 'edit-comments.php';
    public const CUSTOM_POST_TYPE_S = self::POSTS . '?post_type=%s';
    public const APPEARANCE = 'themes.php';
    public const PLUGINS = 'plugins.php';
    public const USERS = 'users.php';
    public const TOOLS = 'tools.php';
    public const SETTINGS = 'options-general.php';
    public const NETWORK_SETTINGS = 'settings.php';
}
