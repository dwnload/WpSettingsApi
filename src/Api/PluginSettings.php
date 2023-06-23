<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class PluginInfo
 * @package Dwnload\WpSettingsApi\Api
 * phpcs:disable Generic.Commenting.DocComment.MissingShort
 * phpcs:disable Squiz.Commenting.FunctionComment.Missing
 */
class PluginSettings extends BaseModel
{

    /** @var string $nonce_s */
    protected string $nonce_s = WpSettingsApi::FILTER_PREFIX . '%s';

    /** @var string $domain */
    private string $domain;

    /** @var string $file */
    private string $file;

    /** @var string $menu_slug */
    private string $menu_slug;

    /** @var string $menu_title */
    private string $menu_title;

    /** @var string $page_title */
    private string $page_title;

    /** @var string $prefix */
    private string $prefix;

    /** @var string $version */
    private string $version;

    public function getDomain(): string
    {
        return $this->domain;
    }

    protected function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    protected function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function getNonce(): string
    {
        return \sprintf($this->nonce_s, \plugin_basename($this->getFile()));
    }

    public function getMenuSlug(): string
    {
        return $this->menu_slug ?? $this->domain;
    }

    protected function setMenuSlug(string $slug): void
    {
        $this->menu_slug = $slug;
    }

    public function getMenuTitle(): string
    {
        return $this->menu_title;
    }

    protected function setMenuTitle(string $title): void
    {
        $this->menu_title = $title;
    }

    public function getPageTitle(): string
    {
        return $this->page_title;
    }

    protected function setPageTitle(string $title): void
    {
        $this->page_title = $title;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    protected function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    protected function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
