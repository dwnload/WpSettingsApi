<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class Style
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @package Dwnload\WpSettingsApi\Api
 */
class Style extends BaseModel
{

    public const HANDLE = 'handle';
    public const SRC = 'src';
    public const DEPENDENCIES = 'dependencies';
    public const VERSION = 'version';
    public const MEDIA = 'media';

    /**
     * Style handle (name).
     * @var string $handle
     */
    protected string $handle;

    /**
     * Set Style handle (name).
     * @param string $handle
     */
    public function setHandle(string $handle): void
    {
        $this->handle = $handle;
    }

    /**
     * Get Style handle (name).
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * Style src URL.
     * @var string $src
     */
    protected string $src;

    /**
     * Set Style src URL.
     * @param string $src
     */
    public function setSrc(string $src): void
    {
        $this->src = $src;
    }

    /**
     * Get Style src URL.
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * Style dependencies array.
     * @var string[] $dependencies
     */
    protected array $dependencies = [];

    /**
     * Set Style dependencies array.
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get Style dependencies array.
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * Style version.
     * @var int|string $version
     */
    protected int|string $version;

    /**
     * Set Style version.
     * @param int|string $version
     */
    public function setVersion(int|string $version = ''): void
    {
        $this->version = $version;
    }

    /**
     * Get Style version.
     * @return int|string
     */
    public function getVersion(): int|string
    {
        global $wp_version;
        return $this->version === '' ? $wp_version : $this->version;
    }

    /**
     * Style media type.
     * @var string $media
     */
    protected string $media = 'screen';

    /**
     * Set Style media type.
     * The media for which this stylesheet has been defined.
     * @param string $media
     */
    public function setMedia(string $media): void
    {
        $this->media = $media;
    }

    /**
     * Get Style media type.
     * @return string
     */
    public function getMedia(): string
    {
        return $this->media;
    }
}
