<?php

namespace Dwnload\WPSettingsApi\Api;

use Dwnload\WPSettingsApi\App;

/**
 * Class PluginInfo
 *
 * @package Dwnload\WPSettingsApi\Api
 */
abstract class PluginInfo extends BaseModel {

	/** @var string $nonce_s */
	protected $nonce_s = App::FILTER_PREFIX . '%s';

	/** @var string $_domain */
	private $_domain;

	/** @var string $_file */
	private $_file;

	/** @var string $_prefix */
	private $_prefix;

	/** @var string $_version */
	private $_version;

	/**
	 * @param string $domain
	 */
	public function setDomain( string $domain ) {
		$this->_domain = $domain;
	}

	/**
	 * @return string
	 */
	public function getDomain(): string {
		return $this->_domain;
	}

	/**
	 * @param string $file
	 */
	public function setFile( string $file ) {
		$this->_file = $file;
	}

	/**
	 * @return string
	 */
	public function getFile(): string {
		return $this->_file;
	}

	/**
	 * @return string
	 */
	public function getNonce(): string {
		return sprintf( $this->nonce_s, plugin_basename( $this->getFile() ) );
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix( string $prefix ) {
		$this->_prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public function getPrefix(): string {
		return $this->_prefix;
	}

	/**
	 * @param string $version
	 */
	public function setVersion( string $version ) {
		$this->_version = $version;
	}

	/**
	 * @return string
	 */
	public function getVersion(): string {
		return $this->_version;
	}
}
