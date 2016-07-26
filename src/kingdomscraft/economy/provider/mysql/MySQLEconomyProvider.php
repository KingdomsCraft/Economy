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
use kingdomscraft\economy\provider\mysql\task\AddGoldTask;
use kingdomscraft\economy\provider\mysql\task\AddLevelTask;
use kingdomscraft\economy\provider\mysql\task\AddRubiesTask;
use kingdomscraft\economy\provider\mysql\task\AddXpTask;
use kingdomscraft\economy\provider\mysql\task\CheckDatabaseTask;
use kingdomscraft\economy\provider\mysql\task\LoadTask;
use kingdomscraft\economy\provider\mysql\task\RegisterTask;
use kingdomscraft\economy\provider\mysql\task\SetGoldTask;
use kingdomscraft\economy\provider\mysql\task\SetLevelTask;
use kingdomscraft\economy\provider\mysql\task\SetRubiesTask;
use kingdomscraft\economy\provider\mysql\task\SetXpTask;
use kingdomscraft\economy\provider\mysql\task\TakeGoldTask;
use kingdomscraft\economy\provider\mysql\task\TakeLevelTask;
use kingdomscraft\economy\provider\mysql\task\TakeRubiesTask;
use kingdomscraft\economy\provider\mysql\task\TakeXpTask;
use kingdomscraft\economy\provider\mysql\task\UnregisterTask;
use kingdomscraft\economy\provider\mysql\task\UpdateTask;
use kingdomscraft\provider\mysql\MySQLProvider;

class MySQLEconomyProvider extends MySQLProvider {

	/**
	 * Initiate the provider
	 */
	public function init() {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new CheckDatabaseTask($this));
	}

	/**
	 * Register a player to the Economy database
	 *
	 * @param string $name
	 * @param AccountInfo $info
	 */
	public function register($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new RegisterTask($this, $name, $info->serialize()));
	}

	/**
	 * Load a players data from the Economy database
	 *
	 * @param string $name
	 */
	public function load($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new LoadTask($this, $name));
	}

	/**
	 * Update a players Economy data
	 *
	 * @param string $name
	 * @param AccountInfo $info
	 */
	public function update($name, AccountInfo $info) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new UpdateTask($this, $name, $info->serialize()));
	}

	/**
	 * Delete a players Economy data
	 *
	 * @param string $name
	 */
	public function delete($name) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new UnregisterTask($this, $name));
	}

	/**
	 * Add gold to a players balance
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function addGold($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new AddGoldTask($this, $name, $amount));
	}

	/**
	 * Add levels to a players balance
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function addLevel($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new AddLevelTask($this, $name, $amount));
	}

	/**
	 * Add rubies to a players balance
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function addRubies($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new AddRubiesTask($this, $name, $amount));
	}

	/**
	 * Add XP to a players balance
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function addXp($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new AddXpTask($this, $name, $amount));
	}

	/**
	 * Set a players gold
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function setGold($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SetGoldTask($this, $name, $amount));
	}

	/**
	 * Set a players level
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function setLevel($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SetLevelTask($this, $name, $amount));
	}

	/**
	 * Set a players rubies
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function setRubies($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SetRubiesTask($this, $name, $amount));
	}

	/**
	 * Set a players XP
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function setXp($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SetXpTask($this, $name, $amount));
	}

	/**
	 * Take gold from a player
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function takeGold($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new TakeGoldTask($this, $name, $amount));
	}

	/**
	 * Take levels from a player
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function takeLevel($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new TakeLevelTask($this, $name, $amount));
	}

	/**
	 * Take rubies from a player
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function takeRubies($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new TakeRubiesTask($this, $name, $amount));
	}

	/**
	 * Take XP from a player
	 *
	 * @param string $name
	 * @param int $amount
	 */
	public function takeXp($name, $amount = 1) {
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new TakeXpTask($this, $name, $amount));
	}

}