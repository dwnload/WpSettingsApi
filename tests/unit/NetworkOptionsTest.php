<?php declare(strict_types=1);

namespace Dwnload\Tests\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\Api\PluginSettings;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Class NetworkOptionsTest
 * @package Dwnload\Tests\WpSettingsApi
 */
#[CoversClass(Options::class)]
#[CoversClass(SectionManager::class)]
class NetworkOptionsTest extends TestCase
{

    private const string SECTION_ID = 'wp_settings_api_network_test';
    private const string FIELD_ID = 'license';
    private const string SITE_VALUE = 'site-value';
    private const string NETWORK_VALUE = 'network-value';

    /**
     * Setup: a site option and a (filtered) network option with different values for the same section.
     */
    public function setUp(): void
    {
        \update_option(self::SECTION_ID, [self::FIELD_ID => self::SITE_VALUE]);
        \add_filter('pre_site_option_' . self::SECTION_ID, [$this, 'networkOptions']);
    }

    /**
     * Teardown.
     */
    public function tearDown(): void
    {
        \remove_filter('pre_site_option_' . self::SECTION_ID, [$this, 'networkOptions']);
        \delete_option(self::SECTION_ID);
        $this->setNetworkSections([]);
    }

    /**
     * Filtered network option value.
     * @return array
     */
    public function networkOptions(): array
    {
        return [self::FIELD_ID => self::NETWORK_VALUE];
    }

    /**
     * A network page is only a network page on multisite.
     */
    public function testIsNetworkRequiresMultisite(): void
    {
        $settings = new PluginSettings([PluginSettings::NETWORK => true]);

        $this->assertSame(\is_multisite(), $settings->isNetwork());
        $this->assertFalse((new PluginSettings([]))->isNetwork());
    }

    /**
     * Options::getNetworkOption() always reads the network option.
     */
    public function testGetNetworkOptionReadsNetworkOption(): void
    {
        $this->assertSame(self::NETWORK_VALUE, Options::getNetworkOption(self::FIELD_ID, self::SECTION_ID));
        $this->assertSame('fallback', Options::getNetworkOption('missing', self::SECTION_ID, 'fallback'));
    }

    /**
     * Site sections keep reading site options.
     */
    public function testSiteSectionReadsSiteOption(): void
    {
        $this->assertFalse(SectionManager::isNetworkSection(self::SECTION_ID));
        $this->assertSame(self::SITE_VALUE, Options::getOption(self::FIELD_ID, self::SECTION_ID));
    }

    /**
     * Sections registered on a network page read the network option (no fallback to the site option).
     */
    public function testNetworkSectionReadsNetworkOption(): void
    {
        $this->setNetworkSections([self::SECTION_ID => true]);

        $this->assertTrue(SectionManager::isNetworkSection(self::SECTION_ID));
        $this->assertSame(self::NETWORK_VALUE, Options::getOption(self::FIELD_ID, self::SECTION_ID));
        $this->assertSame('fallback', Options::getOption('missing', self::SECTION_ID, 'fallback'));
    }

    /**
     * Set the registered network sections.
     * @param array<string, true> $sections
     */
    private function setNetworkSections(array $sections): void
    {
        (new ReflectionProperty(SectionManager::class, 'network_sections'))->setValue(null, $sections);
    }
}
