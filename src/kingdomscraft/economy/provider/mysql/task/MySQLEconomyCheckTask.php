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

use kingdomscraft\economy\Economy;
use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\Main;
use kingdomscraft\provider\mysql\MySQLTask;
use MongoDB\Driver\Exception\RuntimeException;
use pocketmine\Server;
use pocketmine\utils\PluginException;
use pocketmine\utils\TextFormat;

class MySQLEconomyCheckTask extends MySQLTask {

	/**
	 * MySQLEconomyCheckTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 */
	public function __construct(MySQLEconomyProvider $provider) {
		parent::__construct($provider->getCredentials());
	}

	/**
	 * Error states
	 */
	const CONNECTION_ERROR = "connection.error";
	const MYSQLI_ERROR = "mysqli.error";

	public function onRun() {
		$mysqli = $this->getMysqli();
		if($mysqli->connect_error) {
			$mysqli->close();
			$this->setResult([self::CONNECTION_ERROR, $mysqli->connect_error]);
		}
		$mysqli->query("CREATE TABLE IF NOT EXISTS kingdomscraft_economy (
				username VARCHAR(64) PRIMARY KEY,
				xp_level INT DEFAULT 1,
				xp INT DEFAULT 0,
				gold INT DEFAULT 0,
				rubies INT DEFAULT 0)");
		if(isset($mysqli->error) and $mysqli->error) {
			$this->setResult([self::MYSQLI_ERROR, $mysqli->error]);
		}
		$mysqli->close();
	}

	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$result = $this->getResult();
			switch((is_array($result) ? $result[0] : $result)) {
				default:
					$server->getLogger()->debug("Successfully completed MySQLEconomyCheckTask for economy database!");
					return;
				case self::CONNECTION_ERROR:
					$server->getLogger()->debug("Failed to complete MySQLEconomyCheckTask for economy database due to a connection error");
					throw new \RuntimeException($result[1]);
				case self::MYSQLI_ERROR:
					$server->getLogger()->debug("Failed to complete MySQLEconomyCheckTask for economy database due to a mysqli error");
					throw new \RuntimeException($result[1]);
			}
		} else {
			throw new PluginException("Economy plugin isn't enabled!");
		}
	}

}