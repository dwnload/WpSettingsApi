<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class Style
 *
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @package Dwnload\WpSettingsApi\Api
 */
class Style extends BaseModel
{

    const HANDLE = 'handle';
    const SRC = 'src';
    const DEPENDENCIES = 'dependencies';
    const VERSION = 'version';
    const MEDIA = 'media';

    /**
     * Style handle (name).
     *
     * @var string $handle
     */
    protected $handle;

    /**
     * Set Style handle (name).
     *
     * @param string $handle
     */
    public function setHandle(string $handle)
    {
        $this->handle = $handle;
    }

    /**
     * Get Style handle (name).
     *
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * Style src URL.
     *
     * @var string $src
     */
    protected $src;

    /**
     * Set Style src URL.
     *
     * @param string $src
     */
    public function setSrc(string $src)
    {
        $this->src = $src;
    }

    /**
     * Get Style src URL.
     *
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * Style dependencies array.
     *
     * @var array $dependencies
     */
    protected $dependencies = [];

    /**
     * Set Style dependencies array.
     *
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get Style dependencies array.
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * Style version.
     *
     * @var string $version
     */
    protected $version;

    /**
     * Set Style version.
     *
     * @param string $version
     */
    public function setVersion(string $version = '0.0.1')
    {
        $this->version = $version;
    }

    /**
     * Get Style version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version ?? '0.0.1';
    }

    /**
     * Style media type.
     *
     * @var string $media
     */
    protected $media = 'screen';

    /**
     * Set Style media type.
     * The media for which this stylesheet has been defined.
     *
     * @param string $media
     */
    public function setMedia(string $media)
    {
        $this->media = $media;
    }

    /**
     * Get Style media type.
     *
     * @return string
     */
    public function getMedia(): string
    {
        return $this->media;
    }
}
