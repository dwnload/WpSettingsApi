<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class Script
 *
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @package Dwnload\WpSettingsApi\Api
 */
class Script extends BaseModel
{

    const OBJECT_NAME = 'dwnload_wp_settings_api';

    const HANDLE = 'handle';
    const SRC = 'src';
    const DEPENDENCIES = 'dependencies';
    const VERSION = 'version';
    const IN_FOOTER = 'inFooter';
    const INLINE_SCRIPT = 'inline_script';

    /**
     * Script handle (name).
     *
     * @var string $handle
     */
    protected $handle;

    /**
     * Set the Script handle.
     *
     * @param string $handle
     */
    public function setHandle(string $handle)
    {
        $this->handle = $handle;
    }

    /**
     * Get the Script handle.
     *
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * Script src URL.
     *
     * @var string $src
     */
    protected $src;

    /**
     * Set the Script src URL.
     *
     * @param string $src
     */
    public function setSrc(string $src)
    {
        $this->src = $src;
    }

    /**
     * Get the Script src URL.
     *
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * Script dependencies array.
     *
     * @var array $dependencies
     */
    protected $dependencies = [];

    /**
     * Set the Script dependencies array.
     *
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get the Script dependencies array.
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * Script version.
     *
     * @var string $version
     */
    protected $version;

    /**
     * Set the Script version.
     *
     * @param string $version
     */
    public function setVersion(string $version = '0.0.1')
    {
        $this->version = $version;
    }

    /**
     * Get the Script version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version ?? '0.0.1';
    }

    /**
     * Script output location.
     *
     * @var bool $in_footer True in footer, False in body.
     */
    protected $in_footer = true;

    /**
     * Set Script output location.
     * Defaults to true (output in footer).
     *
     * @param bool $in_footer
     */
    public function setInFooter(bool $in_footer)
    {
        $this->in_footer = $in_footer;
    }

    /**
     * Get Script output location.
     *
     * @return bool
     */
    public function getInFooter(): bool
    {
        return $this->in_footer;
    }

    /**
     * Inline Script data.
     *
     * @var false|string $inline_script
     */
    protected $inline_script = false;

    /**
     * Set the Inline Script data.
     *
     * @param string $inline_script
     */
    public function setInlineScript(string $inline_script)
    {
        $this->inline_script = $inline_script;
    }

    /**
     * Get the Inline Script data.
     *
     * @return bool|string
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function getInlineScript()
    {
        return $this->inline_script;
    }
}
