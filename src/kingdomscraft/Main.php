<?php

/**
 * Kingdoms Craft Economy
 *
 * Copyright (C) 2016 Kingdoms Craft
 *
 * This is private software, you cannot redistribute it and/or modify any way
 * unless otherwise given permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author JackNoordhuis
 */

namespace kingdomscraft;

use kingdomscraft\economy\Economy;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

	/** @var Config */
	public $settings = [];

	/** @var Economy */
	private $economy;

	/**
	 * Enable all modules and load configs
	 */
	public function onEnable() {
		$this->loadConfigs();
		$this->enableEconomy();
	}

	/**
	 * Load all the config things \o/
	 */
	public function loadConfigs() {
		$this->saveResource("Settings.yml");
		$this->settings = new Config($this->getDataFolder() . "Settings.yml", Config::YAML);
	}

	/**
	 * @return Economy
	 */
	public function getEconomy() {
		return $this->economy;
	}

	/**
	 * Enable the economy module
	 */
	public function enableEconomy() {
		$this->economy = Economy::enable($this);
	}

}