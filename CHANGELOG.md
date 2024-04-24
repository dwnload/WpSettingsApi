# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

# UNRELEASED

- Require PHP >= 8.1

## 3.11.0 - 2024-04-24
- Update admin CSS with a slight design change.
- Allow int values in Script/Style models version property.
- Prepare tests for PHP 8.3, and ready code for PHP 8.0 deprecation.
- Update wp-color-picker-alpha to v3.0.3.

## 3.10.0 - 2023-11-10
- Add new Text Array field type.

## 3.9.0 - 2023-10-26
- Update roots/wordpress requirement from ~6.1.1 to ~6.2.2.
- Update wp-phpunit/wp-phpunit requirement from ~6.1.1 to ~6.2.0.
- Update slevomat/coding-standard requirement from ~7.2 to ~8.12
- A little code + dependencies + Action cleanup (#73).
- PHP 8.0 enhancements (#74)
- New Repeater Field Type (WIP) - Part I
- PHP 8.1 compat: Don't pass null values to wp_editor or wp_kses_post (#77)
- Bump word-wrap from 1.2.3 to 1.2.4 (#76)

## 3.8.2 - 2023-05-19
- Resolve issue with new instance objects and defined type (edge case) in datetimelocal field.

## 3.8.1 - 2023-05-19
- Allow dashed id's in field types, and sanitize the type on getter.

## 3.8.0 - 2023-05-19
- Add new date fields.

## 3.7.0 - 2023-02-16
- Set minimum PHP version to 8.0.

## 3.6.1 - 2022-08-09
- Add mew `WpSettingsApi::HOOK_INIT_SLUG__S` action hook, which would allow hooks only on current 
`getPluginInfo()->getMenuSlug()`. 
- Change passed param in `do_action` for `ActionHookName::SETTINGS_SETTINGS_SIDEBARS` from an array to a
`WpSettingsApi` instance. [#48](https://github.com/dwnload/WpSettingsApi/pull/48)
- Add `isCurrentMenuSlug($slug)` to allow for easy boolean condition checks.

## 3.6.0 - 2022-08-08
- Lock WP Utilities to version `^2.8`.
- Fix: Allow multiple settings instances by not removing current action hook. (Fixes 
 [#13](https://github.com/dwnload/WpSettingsApi/issues/13))

## 3.5.0 - 2022-03-28
- Add new Alpha Color Picker field type.
- Update `SettingField` and `SettingSection` to extend the `BaseModel` class.
- Add new `ActionHookName` interface for easier hook calling.
- Cleanup Options code (formatting PSR12), use import functions, and update docblock(s).
- Update views to utilize new interface constants for repeatable action hook name(s) defined in the Interface.
- 

## 3.4.1 - 2022-03-11
- In FieldTypes::getFieldDescription(); don't escape HTML, but pass it through `wp_kses_post` which 
will "Sanitizes content for allowed HTML tags".

## 3.4.0 - 2022-03-11
- Require PHP ^7.4.
- Update composer dev-dependencies.
- Build PHP 8.0 with Travis.

## 3.3.2 - 2021-11-07
- Incorrect version passed to the new JS Delivr CDN.

## 3.3.1 - 2021-11-07
- Add new filter to allow for local assets.
  - Use `WpSettingsApi::FILTER_PREFIX . 'use_local_scripts'`, to return a boolean value.
  - Assets for admin settings will now be loaded from the jsdelivr CDN (if local assets is false).

## 3.3 - 2021-04-05
- Add field type: `multiselect`, use `Dwnload\WpSettingsApi\Settings\FieldTypes::FIELD_TYPE_MULTISELECT`.
- Add missing "attributes" constant: `Dwnload\WpSettingsApi\Settings\FieldTypes\SettingField::ATTRIBUTES`.
- Update composer development dependencies.

## 3.2.3 - 2021-02-07
- Fix: JQMIGRATE: jQuery.fn.blur() event shorthand is deprecated #23.

## 2020-10-06 (README and composer.json update)
- Add suggestion for `frontpack/composer-assets-plugin`.

## 3.2.2 - 2020-09-16
- Version bump since composer didn't pick up 3.2.1.

## 3.2.1 - 2020-09-15
- Fix JS not loading for the new color picker. 

## 3.2.0 - 2020-09-15
- Add support for WordPress 5.4+
- Fixed media element uploads for WordPress 5.5.
- Add `color` as a field type.
- Remove inline JS for old WpMediaUpload element.

## 3.1.1 - 2020-09-12
- Require PHP >= 7.3
- `WpSettingsApi::addHooks()` requires `:void` return type in `wp-utilities:^2`

## 3.0.0 - 2019-03-11
- Breaking rewrite which allows the use of multiple instances in one application.
- Requires PHP >= 7.1
- See [Example.php](https://github.com/dwnload/WpSettingsApi/tree/master/examples/Example.php) for update on how to use version 3.

### Updated
* `thefrosty/wp-utilities` to version 1.4.1.
* SettingsApiFactory method `createApp` is now `create` which returns a new instance of the `PluginSettings`. Use like: 
`new WpSettingsApi(SettingsApiFactory::create([]))`.

### Removed
* `AbstractApp()`, `App()` & `PluginInfo()`.
### Added
* On the init action hook, a third parameter has been added to the context of the current WpSettingsApi instance.
It's also been moved into the base object since the App class was removed.

## 2.6 - 2018-11-21
* Update: Settings navigation sections now hook into the `App::ACTION_PREFIX . 'settings_sidebars'` action tag. The 
opening `ul` tag is hooked in to priority `0` and the closing `ul` is on `199`. Each section menu item is hooked into
a priority starting at `3` and incrementing in value by _+2_.
* Update: admin.css; fixing the removed `th` width attribute in **2.5.0**.
* Update: admin.js; target sidebar anchor elements with the `[data-tab-id]` only.
* Added: Version constant to the base file and passed to the settings page anchor title.

## 2.5.1 - 2018-11-19
* Update: Move wp_add_inline_script into private helper method.

## 2.5.0 - 2018-11-19
* Update: add missing field types defined in constants `html` & `image`.
* Update: FileTypes `file` & `image` now proper re-use the WordPress media uploader.
* Update: `Script()` with new `Script::INLINE_SCRIPT` constant to register date to pass to `wp_add_inline_script` 
(defaults to bottom).
* Added: new `wp-media-uploader.js` for the `file` & `image` field uploads.
* Removed: `color` field (missing) (to be added back later?).
* Removed: `field-types.js`.

## 2.4.4 - 2018-10-16
* Add new setAttributes to the SettingField class.
* SettingField() methods now return the object instance (allowing chaining). 
* SettingSection() methods now return the object instance (allowing chaining).
* Added `.gitattributes`. #8

## 2.4.3 - 2018-10-16
* Add missing URL field type.
* Add EMAIL field type.

## 2.4.2 - 2018-10-08
* PHP 7.2 fix. Changes the order of the fields callback condition on the class object for `add_settings_field`.

## 2.4.1 - 2018-09-25
* Force return array type.

## 2.4.0 - 2018-09-25
* Cleaned up composer.json to optimize autoloader and sort packages.
* Removed extra ruleset.xml file.
* Updated: FieldTypes with constants for the respected types.
* PHPCS code standards updates.

## 2.3.0 - 2018-09-24
- Updated the thefrosty/wp-utilites to version ~1.2.
- Refactor code to new wp-utilities standards.
- Update to PSR2 code.

## 2.2.0 - 2018-01-06
- Added: `AppFactory` class.
- Updated: Added `SectionManager` DI of the `App` instance. Allows for multiple instances to be
created by multiple plugins. Please update your instantiation using the new `AppFactory` and remove
the `$app` from `( new Init() )->add( $app )` as it's bootstrapped from `WpSettingsApi` now. See
[Example.php](https://github.com/dwnload/WpSettingsApi/tree/master/examples/Example.php#89)

## 2.1.5 - 2017-08-23
### Updated
- Set Options::getOptions `$section_id` default to `null`.

## 2.1.4 - 2017-08-23
### Updated
- Return type of Options::getOptions is now 'mixed' as opposed to a strict array.

## 2.1.3 - 2017-08-16
### Updated
- Class name follows folder structure with lowercase 'p'.
- Autoloading of files has been updated to reflect the filename and class change.
- Make sure str_repeat's second parameter is always a non-negative integer.
- PHPdocs updates to the FieldManager object.

## 2.1.2 - 2017-08-15
### Updated
- Both Api\Style & Api\Script were not using the fully qualified BaseModel class.
- call_user_func passes three params instead of an array of three params.
- Auto add sanitize for obfuscated setting in the callback stack.

## 2.1.0 - 2017-08-15
### Added
- New obfuscated setting type to the FieldTypes object and Options class.
- Add second & third parameter to sanitize callback function in WPSettingsApi.
- New Sanitize class.

## 2.0.3 - 2017-07-28
### Updated
- Removed BaseModel in favor of class existing in thefrosty/wp-utilites.
- Cleaned up view/setting-html. Use registered admin page title and add correct link to github package. 
### Fixed
- Incorrect call to method in WPSettingsApi to App class..

## 2.0.1 - 2017-07-28
### Added
- menu slug, menu title and page title to App attributes array.
### Updated
- Call to non static method when using new App injection in WPSettingsApi.

## 2.0.0 - 2017-07-23
### Changed
- Forked plugin into wrapper package.
### Updated
- Example plugin with correct functionality on how to use.

## 1.0.2 - 2016-12-08
### Updated
- View.php so unix recognizes directory structure change.  

## 1.0.1 - 2016-12-08
### Updated
- Update `views` directory to uppercase for correct PSR-4 autoload.

## 1.0.0 - 2016-11-28
### Added
- Forked base settings wrapper from Beachbody LIVE BAU.
- README explaining the purpose and use of the project
- Tests to verify adequate functionality
- This CHANGELOG file
