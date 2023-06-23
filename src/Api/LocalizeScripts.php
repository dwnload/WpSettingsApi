<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use function html_entity_decode;
use const ENT_QUOTES;

/**
 * Class LocalizeScripts
 * This class helps localize settings variables passed from PHP to JS.
 * @package Dwnload\WpSettingsApi\Api
 */
class LocalizeScripts
{

    /**
     * Localized array to pass from PHP to JS.
     * @var string[] $vars
     */
    protected static array $vars = [];

    /**
     * Add a key/value to the Array.
     * @param string $key
     * @param string $value
     */
    public function add(string $key, string $value): void
    {
        self::$vars[$key] = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get the Array.
     * @return array
     */
    public function getAllVars(): array
    {
        return self::$vars;
    }
}
