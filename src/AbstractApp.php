<?php

namespace Dwnload\WpSettingsApi;

use Dwnload\WpSettingsApi\Api\PluginInfo;

/**
 * Class AbstractApp
 *
 * @package Dwnload\WpSettingsApi
 */
class AbstractApp extends PluginInfo {

    /** @var App $app */
    private $app;

    /**
     * AbstractApp constructor.
     *
     * @param App $app
     */
    public function __construct( App $app ) {
        $this->app = $app;
        parent::__construct( get_object_vars( $app ) );
    }

    /**
     * @return App
     */
    public function getApp(): App {
        return $this->app;
    }
}
