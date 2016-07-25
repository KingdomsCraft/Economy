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
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Server;

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
				xp_level INT DEFAULT 1,
				xp INT DEFAULT 0,
				gold INT DEFAULT 0,
				rubies INT DEFAULT 0)");
		// Check for any random errors
		if($this->checkError($mysqli)) return;
		// Handle the query data
		if($mysqli->affected_rows > 0) {
			$this->setResult(self::SUCCESS);
			return;
		}
		$this->setResult(self::NO_DATA);
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
					$plugin->getLogger()->debug("Successfully completed RegisterTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::CONNECTION_ERROR:
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing RegisterTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::MYSQLI_ERROR:
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing RegisterTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::NO_DATA:
					$plugin->getLogger()->debug("Error while creating economy data on kingdomscraft_database for {$this->name}");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute RegisterTask while Economy plugin isn't loaded!");
		}
	}

}