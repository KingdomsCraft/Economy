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
use kingdomscraft\economy\provider\mysql\task\MySQLEconomyCheckTask;
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
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyCheckTask($this));
	}

	public function register($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyRegisterTask($this, $name, $info->serialize()));
	}

	public function load($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyLoadTask($this, $name));
	}

	public function update($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyUpdateTask($this, $name, $info->serialize()));
	}

	public function delete($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new MySQLEconomyDeleteTask($this, $name));
	}

}