<?php

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\PluginInfo;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\WP\Utils\WpHooksInterface;

/**
 * Class Bootstrap
 *
 * @package Dwnload\WpSettingsApi
 */
class App extends PluginInfo implements WpHooksInterface {

	const FILTER_PREFIX = 'dwnload/wp_settings_api/';
	const ACTION_PREFIX = self::FILTER_PREFIX;
	const HOOK_PRIORITY = 999;

	/**
	 * Add class hooks.
	 */
	public function addHooks() {
		/**
		 * "Out of the frying pan, and into the fire!"
		 */
		add_action( 'init', function() {
			/**
			 * Fires when this plugin is loaded!
			 *
			 * @var SectionManager Instance of the SectionManager object.
			 * @var FieldManager Instance of the FieldManager object.
			 */
			do_action( self::ACTION_PREFIX . 'init', ( new SectionManager() ), ( new FieldManager() ) );
		}, self::HOOK_PRIORITY );
	}

	/**
	 * Returns the allowed admin capability to modify or view settings.
	 *
	 * @link https://codex.wordpress.org/Roles_and_Capabilities
	 * @return string
	 */
	public function getAppCap(): string {
		return apply_filters( self::FILTER_PREFIX . 'capability', 'manage_options' );
	}

	/**
	 * Return the URL of from our plugin root directory.
	 *
	 * @param string $src The file path relative to the root of the plugin directory.
	 *
	 * @return string
	 */
	public function getPluginsUrl( string $src ): string {
		return plugins_url( $src, __DIR__ );
	}
}
