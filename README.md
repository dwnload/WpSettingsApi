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
        "dwnload/wp-settings-api": "^3.8"
    }
}
```

Now run:

`$ composer install dwnload/wp-settings-api`

Usage Example
---------------

@see [examples/Example.php](https://github.com/dwnload/WpSettingsApi/tree/master/examples/Example.php)

#### Suggested package

⭐️ `frontpack/composer-assets-plugin`

**As of v3.2.1**, this shouldn't be needed any more (unless you are filtering local assets only). Assets will now
be loaded via the jsdelivr CDN. 

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
