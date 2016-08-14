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

namespace kingdomscraft\economy\provider\mysql\task;

use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\Main;
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Server;
use pocketmine\utils\PluginException;

class CheckDatabaseTask extends MySQLTask {

	/**
	 * LoadTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 */
	public function __construct(MySQLEconomyProvider $provider) {
		parent::__construct($provider->getCredentials());
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		// Check for connection errors
		if($this->checkConnection($mysqli)) return;
		// Do the query
		$mysqli->query("CREATE TABLE IF NOT EXISTS kingdomscraft_economy (
				username VARCHAR(64) PRIMARY KEY,
				xp INT DEFAULT 0,
				prestige INT DEFAULT 0,
				gold INT DEFAULT 0,
				rubies INT DEFAULT 10)");
		// Check for any random errors
		if($this->checkError($mysqli)) return;
		// Handle the query data
		$this->setResult(self::SUCCESS);
		return;
	}

	/**
	 * @param Server $server
	 */
	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$result = $this->getResult();
			switch((is_array($result) ? $result[0] : $result)) {
				case self::SUCCESS:
					$plugin->getLogger()->debug("Successfully completed CheckDatabaseTask on kingdomscraft_economy database");
					return;
				case self::CONNECTION_ERROR:
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing CheckDatabaseTask on kingdomscraft_economy database");
					return;
				case self::MYSQLI_ERROR:
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing CheckDatabaseTask on kingdomscraft_economy database");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute CheckDatabaseTask while Economy plugin isn't loaded!");
		}
	}

}