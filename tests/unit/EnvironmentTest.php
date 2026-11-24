<?php declare(strict_types=1);

namespace Dwnload\Tests\WpSettingsApi;

use PHPUnit\Framework\TestCase;

/**
 * Class EnvironmentTest
 * Guards the CI matrix: the multisite job must actually run on a multisite install.
 * @package Dwnload\Tests\WpSettingsApi
 */
class EnvironmentTest extends TestCase
{

    private const string MULTISITE_ENV = 'WP_MULTISITE';
    private const string MULTISITE_ON = '1';

    /**
     * WordPress is installed as multisite exactly when WP_MULTISITE=1 (or WP_TESTS_MULTISITE is defined).
     */
    public function testMultisiteMatchesEnvironment(): void
    {
        $expected = getenv(self::MULTISITE_ENV) === self::MULTISITE_ON ||
            (\defined('WP_TESTS_MULTISITE') && \constant('WP_TESTS_MULTISITE'));

        $this->assertSame($expected, \is_multisite());
    }
}
