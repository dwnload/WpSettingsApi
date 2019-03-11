<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class PluginInfo
 * @package Dwnload\WpSettingsApi\Api
 */
class PluginSettings extends BaseModel
{

    /** @var string $nonce_s */
    protected $nonce_s = WpSettingsApi::FILTER_PREFIX . '%s';

    /** @var string $domain */
    private $domain;

    /** @var string $file */
    private $file;

    /** @var string $menu_slug */
    private $menu_slug;

    /** @var string $menu_title */
    private $menu_title;

    /** @var string $page_title */
    private $page_title;

    /** @var string $prefix */
    private $prefix;

    /** @var string $version */
    private $version;

    /**
     * @param string $domain
     */
    public function setDomain(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $file
     */
    public function setFile(string $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getNonce(): string
    {
        return \sprintf($this->nonce_s, \plugin_basename($this->getFile()));
    }

    /**
     * @param string $slug
     */
    public function setMenuSlug(string $slug)
    {
        $this->menu_slug = $slug;
    }

    /**
     * @return string
     */
    public function getMenuSlug(): string
    {
        return $this->menu_slug ?? $this->domain;
    }

    /**
     * @param string $title
     */
    public function setMenuTitle(string $title)
    {
        $this->menu_title = $title;
    }

    /**
     * @return string
     */
    public function getMenuTitle(): string
    {
        return $this->menu_title;
    }

    /**
     * @param string $title
     */
    public function setPageTitle(string $title)
    {
        $this->page_title = $title;
    }

    /**
     * @return string
     */
    public function getPageTitle(): string
    {
        return $this->page_title;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
