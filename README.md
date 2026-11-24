# Dwnload WordPress Settings Api

[![PHP from Packagist](https://img.shields.io/packagist/php-v/dwnload/wp-settings-api.svg)]()
[![Latest Stable Version](https://img.shields.io/packagist/v/dwnload/wp-settings-api.svg)](https://packagist.org/packages/dwnload/wp-settings-api)
[![Total Downloads](https://img.shields.io/packagist/dt/dwnload/wp-settings-api.svg)](https://packagist.org/packages/dwnload/wp-settings-api)
[![License](https://img.shields.io/packagist/l/dwnload/wp-settings-api.svg)](https://packagist.org/packages/dwnload/wp-settings-api)
![Build Status](https://github.com/dwnload/WpSettingsApi/actions/workflows/main.yml/badge.svg)
[![codecov](https://codecov.io/gh/dwnload/WpSettingsApi//branch/develop/graph/badge.svg)](https://codecov.io/gh/dwnload/WpSettingsApi/)

It's a PHP class wrapper for handling WordPress [Settings API](http://codex.wordpress.org/Settings_API).

## Package Installation (via Composer)

To install this package, edit your `composer.json` file:

```json
{
    "require": {
        "dwnload/wp-settings-api": "^4.0"
    }
}
```

Now run:

`$ composer install dwnload/wp-settings-api`

Usage Example
---------------

@see [examples/Example.php](https://github.com/dwnload/WpSettingsApi/tree/master/examples/Example.php)

#### Network settings pages (multisite)

Pass `PluginSettings::NETWORK => true` to register the page under **Network Admin → Settings** instead of
**Settings**. All of its sections are stored as network options (`get_site_option()`), and they never mix with
per-site options. A plugin that needs both registers two instances: one site page and one network page.

```php
$network = new WpSettingsApi(SettingsApiFactory::create([
    'domain' => 'my-plugin',
    'file' => __FILE__,
    'menu-slug' => 'my-plugin-network',
    'menu-title' => 'My Plugin',
    'page-title' => 'My Plugin Network Settings',
    'prefix' => 'my_plugin_',
    'version' => '1.0.0',
    PluginSettings::NETWORK => true,
]));

// Read a network value from any site (including the front end):
$license = Options::getNetworkOption('license', 'my_plugin_network');
```

- Saving goes through `wp-admin/network/edit.php?action=dwnload_wp_settings_api_{menu-slug}` with the same nonce,
  sanitizing (`register_setting()` callbacks), and "Settings saved." notice as `options.php`.
- The default capability is `manage_network_options` (`dwnload/wp_settings_api/capability` filter, which now also
  receives the `PluginSettings`).
- On a single site, a network page behaves like a regular site page (network options *are* options there).

#### Secret fields (WordPress 7.2+ Secrets API)

Use `FieldTypes::FIELD_TYPE_SECRET` (`'secret'`) for API keys and other credentials:

```php
$field_manager->addField(new SettingField([
    SettingField::NAME => 'api_key',
    SettingField::LABEL => 'API key',
    SettingField::TYPE => FieldTypes::FIELD_TYPE_SECRET,
    SettingField::SECTION_ID => $section_id,
]));

$api_key = Options::getSecret('api_key', $section_id); // ?string
```

- **WordPress 7.2+** (`wp_set_secret()` exists): the value is encrypted with the
  [Secrets API](https://make.wordpress.org/core/2026/08/25/proposal-a-secrets-api-for-wordpress-7-2/) under the name
  `{section_id}/{field_id}`, and the option only stores a `wp-secret:` placeholder. The value is never sent back to
  the browser; a blank submission keeps it, and a "Delete" checkbox removes it. Saving requires the `manage_secrets`
  capability and a `WP_SECRETS_KEY` in `wp-config.php`. If core returns an error, nothing is saved and the error is
  shown. A plaintext value saved before the upgrade is moved into the Secrets API on the next save.
- **Older WordPress**: it behaves like an obfuscated password field stored in the option.
- Filters: `dwnload/wp_settings_api/use_secrets_api` (bool) and `dwnload/wp_settings_api/secret_name` (string).

#### Suggested package

⭐️ `frontpack/composer-assets-plugin`

**As of v3.2.1**, this shouldn't be needed any more (unless you are filtering local assets only). Assets will now
be loaded via the jsdelivr CDN. 

**As of v4.0**, CDN assets get a [Subresource Integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity)
`integrity` attribute when the installed package version matches `WpSettingsApi::VERSION`. The hash is taken from the
installed copy of each file, so the browser refuses a CDN file that doesn't match it. In that case the unminified file
is served, because jsDelivr generates the `.min` files itself. To turn it off:
`add_filter('dwnload/wp_settings_api/asset_integrity', '__return_false');`

**Otherwise**, since this is a PHP package and not a WordPress plugin the assets included can't be loaded properly.
In order to have the settings page inherit the styles and use the proper JS, you've got to copy the
`/assets` directory to your plugin or theme. Then add the following to filter the asset src to your
directory:

```php
<?php declare(strict_types=1);

namespace Vendor\Package;

use Dwnload\WpSettingsApi\Api\Script;
use Dwnload\WpSettingsApi\Api\Style;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;
use TheFrosty\WpUtilities\Plugin\HooksTrait;

/**
 * Class WpSettingsApi
 *
 * @package Dwnload\WpSettingsApi
 */
class WpSettingsApiScripts extends AbstractHookProvider
{


    public function addHooks(array $scripts): void
    {
        \add_filter(WpSettingsApi::FILTER_PREFIX . 'admin_scripts', [$this, 'adminScripts']);
        \add_filter(WpSettingsApi::FILTER_PREFIX . 'admin_styles', [$this, 'adminStyles']);
    }
    
    /**
     * The default script needs to be moved from the vendor directory somewhere into our app since the
     * vendor directory is outside of the doc root.
     * @param Script[] $scripts
     * @return array
     */
    public function adminScripts(array $scripts): array
    {
        \array_walk($scripts, function (Script $script, int $key) use (&$scripts) {
            switch ($script->getHandle()) {
                case WpSettingsApi::ADMIN_SCRIPT_HANDLE:
                /**
                 * If you're not using the `TheFrosty\WpUtilities\Plugin\AbstractHookProvider`
                 * use `plugins_url()` in place of the `$this->getPlugin()->getUrl` or any other WP
                 * function that will point to the asset.
                 * (Should match `frontpack/composer-assets-plugin configs`)
                 */
                $scripts[$key]->setSrc($this->getPlugin()->getUrl('assets/js/admin.js'));
                break;
            case WpSettingsApi::ADMIN_MEDIA_HANDLE:
                /**
                 * If you're not using the `TheFrosty\WpUtilities\Plugin\AbstractHookProvider`
                 * use `plugins_url()` in place of the `$this->getPlugin()->getUrl` or any other WP
                 * function that will point to the asset.
                 * (Should match `frontpack/composer-assets-plugin configs`)
                 */
                $scripts[$key]->setSrc($this->getPlugin()->getUrl('assets/js/wp-media-uploader.js'));
                break;
            }
            $this->registerScript($script);
        });
    
        return $scripts;
    }
    
    /**
     * The default style needs to be moved from the vendor directory somewhere into our app since the
     * vendor directory is outside of the doc root.
     * @param Style[] $styles
     * @return array
     */
    public function adminStyles(array $styles): array
    {
        \array_walk($styles, function (Style $style, int $key) use (&$styles) {
            if ($style->getHandle() === WpSettingsApi::ADMIN_STYLE_HANDLE) {
                /**
                 * If you're not using the `TheFrosty\WpUtilities\Plugin\AbstractHookProvider`
                 * use `plugins_url()` in place of the `$this->getPlugin()->getUrl` or any other WP
                 * function that will point to the asset.
                 */
                $styles[$key]->setSrc($this->getPlugin()->getUrl('assets/css/admin.css'));
                $this->registerStyle($style);
            }
        });
    
        return $styles;
    }
    
    /**
     * If the script is not registered before being returned back to the filter the src still uses
     * the vendor directory file path.
     * @param Script $script
     */
    private function registerScript(Script $script): void
    {
        \wp_register_script(
            $script->getHandle(),
            $script->getSrc(),
            $script->getDependencies(),
            $script->getVersion(),
            $script->getInFooter()
        );
    }
    
    /**
     * If the style is not registered before being returned back to the filter the src still uses
     * the vendor directory file path.
     * @param Style $style
     */
    private function registerStyle(Style $style): void
    {
        \wp_register_style(
            $style->getHandle(),
            $style->getSrc(),
            $style->getDependencies(),
            $style->getVersion(),
            $style->getMedia()
        );
    }
}
```
