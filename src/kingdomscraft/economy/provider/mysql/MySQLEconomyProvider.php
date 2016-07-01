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
 * @author Jack
 *
 */

namespace kingdomscraft\economy\provider\mysql;

use kingdomscraft\provider\mysql\MySQLCredentials;
use kingdomscraft\provider\mysql\MySQLProvider;
use pocketmine\utils\PluginException;
use pocketmine\utils\TextFormat;

class MySQLEconomyProvider extends MySQLProvider {

	public function init() {
		$this->credentials = MySQLCredentials::fromArray($this->getPlugin()->settings["database"]);
		$mysqli = $this->credentials->getMysqli();
		if($mysqli->connect_error) {
			$mysqli->close();
			throw new PluginException(TextFormat::RED . "Couldn't connect to economy database! Error: {$mysqli->connect_error}");
		}
		$mysqli->query("CREATE TABLE IF NOT EXISTS kingdomscraft_economy (
				username VARCHAR(64) PRIMARY KEY,
				level INT DEFAULT 1,
				xp INT DEFAULT 0,
				gold INT DEFAULT 0,
				rubies INT DEFAULT 0)");
		if(isset($mysqli->error) and $mysqli->error) {
			throw new \RuntimeException($mysqli->error);
		}
		$mysqli->close();
	}

	public function load($name) {
		
	}

	public function display($who, $to) {
		
	}

	public function update($name, array $data) {
		
	}

	public function delete($name) {
		
	}

}