<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use WP_Error;
use function __;
use function apply_filters;
use function current_user_can;
use function function_exists;
use function is_object;
use function is_string;
use function is_wp_error;
use function method_exists;
use function sprintf;
use function str_starts_with;
use function wp_delete_secret;
use function wp_get_secret;
use function wp_set_secret;

/**
 * Class SecretsApi
 * Thin, feature-detected wrapper around the WordPress Secrets API (WordPress 7.2+).
 * Every core call is gated on `isAvailable()`, so this class is safe to load on older WordPress versions.
 * @link https://make.wordpress.org/core/2026/08/25/proposal-a-secrets-api-for-wordpress-7-2/
 * @package Dwnload\WpSettingsApi\Api
 */
class SecretsApi
{

    /**
     * Prefix of the placeholder stored in the option in place of the secret value.
     * The secret itself only lives in the Secrets API store.
     */
    public const string MARKER_PREFIX = 'wp-secret:';

    /**
     * Capability core requires to manage site secrets.
     */
    public const string CAPABILITY = 'manage_secrets';

    /**
     * Whether the WordPress Secrets API is available (and not disabled via filter).
     * @return bool
     */
    public static function isAvailable(): bool
    {
        $available = function_exists('wp_set_secret') &&
            function_exists('wp_get_secret') &&
            function_exists('wp_delete_secret');

        /**
         * Filter whether "secret" fields use the WordPress Secrets API.
         * When false (or on WordPress < 7.2), secret fields fall back to obfuscated option storage.
         * @param bool $available Whether the Secrets API functions exist.
         */
        return (bool)apply_filters(WpSettingsApi::FILTER_PREFIX . 'use_secrets_api', $available) && $available;
    }

    /**
     * Whether a section's secret fields use the Secrets API.
     * Sections on a network settings page don't: core stores network secrets with separate functions and the
     * `manage_network_secrets` capability, so they fall back to obfuscated network option storage for now.
     * @param string $section_id
     * @return bool
     */
    public static function isAvailableFor(string $section_id): bool
    {
        return self::isAvailable() && !SectionManager::isNetworkSection($section_id);
    }

    /**
     * Get the Secrets API name for a field, `{section_id}/{field_id}` by default.
     * @param string $section_id
     * @param string $field_id
     * @return string
     */
    public static function getSecretName(string $section_id, string $field_id): string
    {
        /**
         * Filter the secret name (core convention is `plugin-slug/secret-name`).
         * @param string $name Default `{section_id}/{field_id}`.
         * @param string $section_id
         * @param string $field_id
         */
        return (string)apply_filters(
            WpSettingsApi::FILTER_PREFIX . 'secret_name',
            sprintf('%s/%s', $section_id, $field_id),
            $section_id,
            $field_id
        );
    }

    /**
     * Get the placeholder value stored in the option for a secret.
     * @param string $name
     * @return string
     */
    public static function getMarker(string $name): string
    {
        return self::MARKER_PREFIX . $name;
    }

    /**
     * Is the value a secret placeholder (rather than a real/plaintext value)?
     * @param mixed $value
     * @return bool
     */
    public static function isMarker(mixed $value): bool
    {
        return is_string($value) && str_starts_with($value, self::MARKER_PREFIX);
    }

    /**
     * Can the current user manage secrets?
     * @return bool
     */
    public static function canManage(): bool
    {
        return current_user_can(self::CAPABILITY);
    }

    /**
     * Store a secret.
     * @param string $name
     * @param string $value
     * @return true|WP_Error
     */
    public static function set(string $name, string $value): true|WP_Error
    {
        if (!self::isAvailable()) {
            return self::unavailableError();
        }

        $result = wp_set_secret($name, $value);
        if (is_wp_error($result)) {
            return $result;
        }

        return $result === true ?
            true :
            new WP_Error('secret_not_saved', __('The secret could not be saved.', 'wp-settings-api'));
    }

    /**
     * Delete a secret.
     * @param string $name
     * @return true|WP_Error
     */
    public static function delete(string $name): true|WP_Error
    {
        if (!self::isAvailable()) {
            return self::unavailableError();
        }

        $result = wp_delete_secret($name);
        if (is_wp_error($result)) {
            return $result;
        }

        return $result === true ?
            true :
            new WP_Error('secret_not_deleted', __('The secret could not be deleted.', 'wp-settings-api'));
    }

    /**
     * Get a secret's plaintext value.
     * @param string $name
     * @return string|null Null when the secret doesn't exist, can't be decrypted, or the API is unavailable.
     */
    public static function reveal(string $name): ?string
    {
        $secret = self::getSecret($name);

        return $secret === null || is_wp_error($secret) ? null : $secret->reveal();
    }

    /**
     * Get a secret's fingerprint, safe to display in the UI.
     * @param string $name
     * @return string|WP_Error|null Null when the secret doesn't exist (or the API is unavailable).
     */
    public static function getFingerprint(string $name): string|WP_Error|null
    {
        $secret = self::getSecret($name);
        if ($secret === null || is_wp_error($secret)) {
            return $secret;
        }

        $fingerprint = $secret->fingerprint();

        return is_string($fingerprint) ? $fingerprint : '';
    }

    /**
     * Get the WP_Secret object.
     * @param string $name
     * @return object|WP_Error|null A `WP_Secret` instance, WP_Error when it can't be read, or null.
     */
    private static function getSecret(string $name): object|null
    {
        if (!self::isAvailable()) {
            return null;
        }

        $secret = wp_get_secret($name);
        if ($secret === null || is_wp_error($secret)) {
            return $secret;
        }

        return is_object($secret) && method_exists($secret, 'reveal') ?
            $secret :
            new WP_Error('secret_invalid', __('The Secrets API returned an unexpected value.', 'wp-settings-api'));
    }

    /**
     * Error returned when the Secrets API isn't available.
     * @return WP_Error
     */
    private static function unavailableError(): WP_Error
    {
        return new WP_Error(
            'secrets_api_unavailable',
            __('The WordPress Secrets API is not available.', 'wp-settings-api')
        );
    }
}
