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

namespace kingdomscraft\economy\provider\mysql;

use kingdomscraft\economy\AccountInfo;
use kingdomscraft\economy\provider\mysql\task\CheckDatabaseTask;
use kingdomscraft\economy\provider\mysql\task\LoadTask;
use kingdomscraft\economy\provider\mysql\task\RegisterTask;
use kingdomscraft\economy\provider\mysql\task\UnregisterTask;
use kingdomscraft\economy\provider\mysql\task\UpdateTask;
use kingdomscraft\provider\mysql\MySQLProvider;

class MySQLEconomyProvider extends MySQLProvider {

	public function init() {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new CheckDatabaseTask($this));
	}

	public function register($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new RegisterTask($this, $name, $info->serialize()));
	}

	public function load($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new LoadTask($this, $name));
	}

	public function update($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new UpdateTask($this, $name, $info->serialize()));
	}

	public function delete($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new UnregisterTask($this, $name));
	}

}