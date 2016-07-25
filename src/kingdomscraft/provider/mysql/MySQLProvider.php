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
 *//**
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

namespace kingdomscraft\provider\mysql;

use kingdomscraft\Main;
use kingdomscraft\provider\DummyProvider;
use pocketmine\utils\PluginException;
use pocketmine\utils\TextFormat;

/**
 * MySQLProvider class
 */
abstract class MySQLProvider extends DummyProvider {

	/** @var MySQLCredentials */
	protected $credentials;

	public function __construct(Main $plugin, MySQLCredentials $credentials) {
		parent::__construct($plugin);
		$this->credentials = $credentials;
		$this->init();
	}

	/**
	 * @return MySQLCredentials
	 */
	public function getCredentials() {
		return $this->credentials;
	}

	public function init() {
		$mysqli = $this->credentials->getMysqli();
		if($mysqli->connect_error) {
			$mysqli->close();
			throw new PluginException(TextFormat::RED . "Couldn't connect to database! Error: {$mysqli->connect_error}");
		}
		$mysqli->close();
	}

}