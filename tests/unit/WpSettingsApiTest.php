<?php declare(strict_types=1);

namespace Dwnload\Tests\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\PluginSettings;
use Dwnload\WpSettingsApi\WpSettingsApi;
use PHPUnit\Framework\TestCase;

/**
 * Class WpSettingsApiTest
 * @package TheFrosty\Tests\WpLoginLocker
 */
class WpSettingsApiTest extends TestCase
{

    private WpSettingsApi $wp_settings_api;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        $this->wp_settings_api = new WpSettingsApi(new PluginSettings([]));
    }

    /**
     * Teardown.
     */
    public function tearDown(): void
    {
        unset($this->wp_settings_api);
    }

    /**
     * Test class has constants.
     */
    public function testWpSettingsApi(): void
    {
        $this->assertInstanceOf(WpSettingsApi::class, $this->wp_settings_api);
    }
}
