<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class Script
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @package Dwnload\WpSettingsApi\Api
 */
class Script extends BaseModel
{

    public const OBJECT_NAME = 'dwnload_wp_settings_api';
    public const HANDLE = 'handle';
    public const SRC = 'src';
    public const DEPENDENCIES = 'dependencies';
    public const VERSION = 'version';
    public const IN_FOOTER = 'inFooter';
    public const INLINE_SCRIPT = 'inline_script';

    /**
     * Script handle (name).
     * @var string $handle
     */
    protected string $handle;

    /**
     * Set the Script handle.
     * @param string $handle
     */
    public function setHandle(string $handle): void
    {
        $this->handle = $handle;
    }

    /**
     * Get the Script handle.
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * Script src URL.
     * @var string $src
     */
    protected string $src;

    /**
     * Set the Script src URL.
     * @param string $src
     */
    public function setSrc(string $src): void
    {
        $this->src = $src;
    }

    /**
     * Get the Script src URL.
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * Script dependencies array.
     * @var string[] $dependencies
     */
    protected array $dependencies = [];

    /**
     * Set the Script dependencies array.
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get the Script dependencies array.
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * Script version.
     * @var int|string $version
     */
    protected int|string $version;

    /**
     * Set the Script version.
     * @param int|string $version
     */
    public function setVersion(int|string $version = ''): void
    {
        $this->version = $version;
    }

    /**
     * Get the Script version.
     * @return int|string
     */
    public function getVersion(): int|string
    {
        global $wp_version;
        return $this->version === '' ? $wp_version : $this->version;
    }

    /**
     * Script output location.
     * @var bool $in_footer True in footer, False in body.
     */
    protected bool $in_footer = true;

    /**
     * Set Script output location.
     * Defaults to true (output in footer).
     * @param bool $in_footer
     */
    public function setInFooter(bool $in_footer): void
    {
        $this->in_footer = $in_footer;
    }

    /**
     * Get Script output location.
     * @return bool
     */
    public function getInFooter(): bool
    {
        return $this->in_footer;
    }

    /**
     * Inline Script data.
     * @var false|string $inline_script
     */
    protected string|false $inline_script = false;

    /**
     * Set the Inline Script data.
     * @param string $inline_script
     */
    public function setInlineScript(string $inline_script): void
    {
        $this->inline_script = $inline_script;
    }

    /**
     * Get the Inline Script data.
     * @return bool|string
     */
    public function getInlineScript(): bool|string
    {
        return $this->inline_script;
    }
}
