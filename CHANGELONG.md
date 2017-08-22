# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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