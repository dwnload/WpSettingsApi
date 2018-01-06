<?php

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
interface MenuPageSlug {

    /**
     * Slugs to use as parents for the built-in WordPress menu pages.
     */

    const DASHBOARD = 'index.php';
    const POSTS = 'edit.php';
    const MEDIA = 'upload.php';
    const PAGES = self::POSTS . '?post_type=page';
    const COMMENTS = 'edit-comments.php';
    const CUSTOM_POST_TYPE_S = self::POSTS . '?post_type=%s';
    const APPEARANCE = 'themes.php';
    const PLUGINS = 'plugins.php';
    const USERS = 'users.php';
    const TOOLS = 'tools.php';
    const SETTINGS = 'options-general.php';
    const NETWORK_SETTINGS = 'settings.php';
}
