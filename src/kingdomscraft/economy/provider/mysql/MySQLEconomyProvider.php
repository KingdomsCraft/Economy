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

use kingdomscraft\economy\AccountInfo;
use kingdomscraft\economy\provider\mysql\task\MySQLEconomyDeleteTask;
use kingdomscraft\economy\provider\mysql\task\MySQLEconomyDisplayTask;
use kingdomscraft\economy\provider\mysql\task\MySQLEconomyLoadTask;
use kingdomscraft\economy\provider\mysql\task\MySQLEconomyRegisterTask;
use kingdomscraft\economy\provider\mysql\task\MySQLEconomyUpdateTask;
use kingdomscraft\provider\mysql\MySQLCredentials;
use kingdomscraft\provider\mysql\MySQLProvider;
use pocketmine\utils\PluginException;
use pocketmine\utils\TextFormat;

class MySQLEconomyProvider extends MySQLProvider {

	public function init() {
		$this->credentials = MySQLCredentials::fromArray($this->getPlugin()->settings->getNested("database"));
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

	public function register($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyRegisterTask($this->getPlugin()->getEconomy(), $name, $info->serialize()));
	}

	public function load($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyLoadTask($this->getPlugin()->getEconomy(), $name));
	}

	public function display($who, $to = "") {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyDisplayTask($this->getPlugin()->getEconomy(), $who, $to));
	}

	public function update($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyUpdateTask($this->getPlugin()->getEconomy(), $name, $info->serialize()));
	}

	public function delete($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyDeleteTask($this->getPlugin()->getEconomy(), $name));
	}

}