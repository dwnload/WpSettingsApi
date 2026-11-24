<?php declare(strict_types=1);

namespace Dwnload\Tests\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\Api\PluginSettings;
use Dwnload\WpSettingsApi\Api\Sanitize;
use Dwnload\WpSettingsApi\Api\SecretsApi;
use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Class SecretFieldTest
 * @package Dwnload\Tests\WpSettingsApi
 */
#[CoversClass(SecretsApi::class)]
#[CoversClass(Sanitize::class)]
#[CoversClass(SectionManager::class)]
class SecretFieldTest extends TestCase
{

    private const string SECTION_ID = 'wp_settings_api_test';
    private const string FIELD_ID = 'api_key';

    /**
     * Setup.
     */
    public function setUp(): void
    {
        $field = new SettingField([
            SettingField::NAME => self::FIELD_ID,
            SettingField::SECTION_ID => self::SECTION_ID,
            SettingField::TYPE => FieldTypes::FIELD_TYPE_SECRET,
        ]);
        (new ReflectionProperty(FieldManager::class, 'settings_fields'))
            ->setValue(null, [self::SECTION_ID => [$field]]);
        \delete_option(self::SECTION_ID);
    }

    /**
     * Teardown.
     */
    public function tearDown(): void
    {
        (new ReflectionProperty(FieldManager::class, 'settings_fields'))->setValue(null, []);
        \remove_all_filters(WpSettingsApi::FILTER_PREFIX . 'use_secrets_api');
        \delete_option(self::SECTION_ID);
        \wp_set_current_user(0);
        if (\function_exists('wp_delete_secret')) {
            \wp_delete_secret(SecretsApi::getSecretName(self::SECTION_ID, self::FIELD_ID));
        }
    }

    /**
     * Secret fields are sanitized by Sanitize::sanitizeSecret().
     */
    public function testSecretFieldsUseSecretSanitizer(): void
    {
        $method = new ReflectionMethod(WpSettingsApi::class, 'getSanitizeCallback');
        $callback = $method->invoke(new WpSettingsApi(new PluginSettings([])), self::FIELD_ID);

        $this->assertSame(Sanitize::class . '::sanitizeSecret', $callback);
    }

    /**
     * The availability check is gated on the core functions and the filter.
     */
    public function testIsAvailableIsFeatureDetected(): void
    {
        $this->assertSame(\function_exists('wp_set_secret'), SecretsApi::isAvailable());

        \add_filter(WpSettingsApi::FILTER_PREFIX . 'use_secrets_api', '__return_false');
        $this->assertFalse(SecretsApi::isAvailable());
    }

    /**
     * Default secret name and marker helpers.
     */
    public function testSecretNameAndMarker(): void
    {
        $name = SecretsApi::getSecretName(self::SECTION_ID, self::FIELD_ID);

        $this->assertSame(self::SECTION_ID . '/' . self::FIELD_ID, $name);
        $this->assertTrue(SecretsApi::isMarker(SecretsApi::getMarker($name)));
        $this->assertFalse(SecretsApi::isMarker('sk_live_123'));
        $this->assertFalse(SecretsApi::isMarker(['value' => 'x']));
    }

    /**
     * Without the Secrets API, secret fields behave like obfuscated fields stored in the option.
     */
    public function testFallbackStoresValueInOption(): void
    {
        \add_filter(WpSettingsApi::FILTER_PREFIX . 'use_secrets_api', '__return_false');

        $value = Sanitize::sanitizeSecret('sk_live_abcdef123456', [], self::FIELD_ID);
        $this->assertSame('sk_live_abcdef123456', $value);

        \update_option(self::SECTION_ID, [self::FIELD_ID => $value]);
        // Obfuscated stars submitted back keep the stored value.
        $this->assertSame(
            'sk_live_abcdef123456',
            Sanitize::sanitizeSecret('**************123456', [], self::FIELD_ID)
        );
        $this->assertSame('sk_live_abcdef123456', Options::getSecret(self::FIELD_ID, self::SECTION_ID));
    }

    /**
     * Secret fields on a network settings page never use the (site) Secrets API.
     */
    public function testNetworkSectionsFallBackToOptionStorage(): void
    {
        $network_sections = new ReflectionProperty(SectionManager::class, 'network_sections');
        $network_sections->setValue(null, [self::SECTION_ID => true]);

        try {
            $this->assertFalse(SecretsApi::isAvailableFor(self::SECTION_ID));
            $this->assertSame(
                'sk_live_abcdef123456',
                Sanitize::sanitizeSecret(['value' => 'sk_live_abcdef123456'], [], self::FIELD_ID)
            );
        } finally {
            $network_sections->setValue(null, []);
        }
    }

    /**
     * Options::getSecret() never returns the placeholder stored in the option.
     */
    public function testGetSecretIgnoresMarker(): void
    {
        \add_filter(WpSettingsApi::FILTER_PREFIX . 'use_secrets_api', '__return_false');
        \update_option(self::SECTION_ID, [self::FIELD_ID => SecretsApi::getMarker('anything')]);

        $this->assertNull(Options::getSecret(self::FIELD_ID, self::SECTION_ID));
    }

    /**
     * With the Secrets API (WordPress 7.2+), only a placeholder is stored in the option.
     */
    public function testSecretsApiStoresPlaceholderInOption(): void
    {
        if (!\function_exists('wp_set_secret') || !\defined('WP_SECRETS_KEY')) {
            $this->markTestSkipped('Requires the WordPress 7.2 Secrets API and WP_SECRETS_KEY.');
        }

        $admins = \get_users(['role' => 'administrator', 'number' => 1, 'fields' => 'ID']);
        \wp_set_current_user((int)($admins[0] ?? 1));
        if (!SecretsApi::canManage()) {
            $this->markTestSkipped('Current user cannot manage_secrets.');
        }

        $name = SecretsApi::getSecretName(self::SECTION_ID, self::FIELD_ID);
        $value = Sanitize::sanitizeSecret(['value' => 'sk_live_abcdef123456'], [], self::FIELD_ID);
        $this->assertSame(SecretsApi::getMarker($name), $value);

        \update_option(self::SECTION_ID, [self::FIELD_ID => $value]);
        $this->assertSame('sk_live_abcdef123456', Options::getSecret(self::FIELD_ID, self::SECTION_ID));

        // Blank keeps, delete removes.
        $this->assertSame($value, Sanitize::sanitizeSecret(['value' => ''], [], self::FIELD_ID));
        $this->assertSame('', Sanitize::sanitizeSecret(['value' => '', 'delete' => '1'], [], self::FIELD_ID));
        $this->assertNull(SecretsApi::reveal($name));
    }
}
