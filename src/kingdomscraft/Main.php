<?php

/**
 * Main.php Class
 *
 * Created on 12/06/2016 at 3:14 PM
 *
 * @author Jack
 */

namespace kingdomscraft;

use kingdomscraft\account\AccountManager;
use kingdomscraft\economy\Economy;
use kingdomscraft\provider\DummyProvider;
use kingdomscraft\provider\mysql\MySQLProvider;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {
	
	/** @var array */
	public $settings = [];
	
	/** @var DummyProvider */
	private $provider;
	
	/** @var AccountManager */
	private $accountManager;

	/**
	 * Enable all modules and load configs
	 */
	public function onEnable() {
		$this->loadConfigs();
		$this->setProvider();
		$this->setAccountManager();
		Economy::enable($this);
	}

	/**
	 * Load all the config things \o/
	 */
	public function loadConfigs() {
		$this->saveResource("Settings.yml");
		$this->settings = (new Config($this->getDataFolder() . "Settings.yml", Config::YAML));
	}

	/**
	 * Set the provider
	 */
	public function setProvider() {
		$this->provider = new MySQLProvider($this);
	}

	/**
	 * Set the account manager
	 */
	public function setAccountManager() {
		$this->accountManager = new AccountManager($this);
	}

	/**
	 * @return DummyProvider
	 */
	public function getProvider() {
		return $this->provider;
	}

	/**
	 * @return AccountManager
	 */
	public function getAccountManager() {
		return $this->accountManager;
	}

}