<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\PluginInfo;
use TheFrosty\WpUtilities\Plugin\HooksTrait;

/**
 * Class AbstractApp
 *
 * @package Dwnload\WpSettingsApi
 */
class AbstractApp extends PluginInfo
{
    use HooksTrait;

    /** @var App $app */
    private $app;

    /**
     * AbstractApp constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct(\get_object_vars($app));
    }

    /**
     * @return App
     */
    public function getApp(): App
    {
        return $this->app;
    }
}
