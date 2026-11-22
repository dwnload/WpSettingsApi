<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Composer\InstalledVersions;
use function array_key_exists;
use function base64_encode;
use function class_exists;
use function dirname;
use function hash_file;
use function is_readable;
use function ltrim;

/**
 * Class AssetIntegrity
 * Builds Subresource Integrity (SRI) hashes for the package assets loaded from the CDN.
 * The hash is taken from the installed (local) copy of the asset, so it is only valid when the
 * installed package version is the same tag the CDN URL points at.
 * @link https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity
 * @package Dwnload\WpSettingsApi\Api
 */
class AssetIntegrity
{

    public const string PACKAGE_NAME = 'dwnload/wp-settings-api';

    /**
     * Cache of computed hashes keyed by file path.
     * @var array<string, string|null> $hashes
     */
    private static array $hashes = [];

    /**
     * Whether the installed package matches the given CDN version, so local hashes match the CDN files.
     * @param string $version The version used in the CDN URL.
     * @return bool
     */
    public static function matchesInstalledVersion(string $version): bool
    {
        if (!class_exists(InstalledVersions::class) || !InstalledVersions::isInstalled(self::PACKAGE_NAME)) {
            return false;
        }

        $installed = InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);

        return $installed !== null && ltrim($installed, 'v') === ltrim($version, 'v');
    }

    /**
     * Get the SRI value (sha384) for a package asset.
     * @param string $path Asset path relative to the package root, e.g. "src/assets/js/admin.js".
     * @return string|null The integrity value, or null when the file can't be read.
     */
    public static function getHash(string $path): ?string
    {
        $file = dirname(__DIR__, 2) . '/' . ltrim($path, '/');

        if (!array_key_exists($file, self::$hashes)) {
            $raw = is_readable($file) ? hash_file('sha384', $file, true) : false;
            self::$hashes[$file] = $raw === false ? null : 'sha384-' . base64_encode($raw);
        }

        return self::$hashes[$file];
    }
}
