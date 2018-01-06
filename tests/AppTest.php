<?php

namespace Dwnload\WpSettingsApi\Tests;

use Dwnload\WpSettingsApi\Api\PluginInfo;
use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\AppFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class AppTest
 *
 * @package Dwnload\WpSettingsApi\Tests
 */
class AppTest extends TestCase {

    /** @var  App $app */
    protected $app;

    /** @var array $atts */
    protected $atts = [
        'domain' => 'vendor-domain',
        'file' => __FILE__,
        'prefix' => 'vendor_',
        'version' => '0.7.8',
    ];

    /**
     * Set up with WP_Mock
     */
    public function setUp() {
        \WP_Mock::setUp();
        $this->app = AppFactory::createApp( $this->atts );
    }

    /**
     * Tear down with WP_Mock
     */
    public function tearDown() {
        \WP_Mock::tearDown();
        unset( $this->app );
    }

    /**
     * Test addHooks method
     */
    public function testAddHooks() {
        $function = function() {
        };
        \WP_Mock::expectActionAdded(
            'init',
            $function,
            App::HOOK_PRIORITY
        );

        $this->app->addHooks();
        $this->markTestSkipped(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Test getAppCap method
     */
    public function testGetAppCap() {
        \WP_Mock::onFilter( App::ACTION_PREFIX . 'capability' )
                ->with( 'manage_options' )
                ->reply( 'activate_plugins' );

        $cap = $this->app->getAppCap();

        $this->assertEquals( 'activate_plugins', $cap );
    }

    /**
     * Test getPluginsUrl method
     */
    public function testGetPluginsUrl() {
        $this->markTestSkipped(
            'This test has not been implemented yet.'
        );
        $src = 'foo/assets/css/admin.css';
        $file = __FILE__;
        \WP_Mock::userFunction( 'plugins_url', [
            'args' => [ $src, $file ],
            'times' => 1,
            'return' => 'http://example.com/foo/assets/css/admin.css',
        ] );

        $plugins_url = $this->app->getPluginsUrl( $src );
        $this->assertEquals( 'http://example.com/foo/assets/css/admin.css', $plugins_url );
    }
}
