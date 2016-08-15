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

use kingdomscraft\economy\AccountInfo;
use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\Main;
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Server;
use pocketmine\utils\PluginException;

class UpdateTask extends MySQLTask {

	/** @var string */
	protected $name;

	/** @var string */
	protected $info;

	public function __construct(MySQLEconomyProvider $provider, $name, $info) {
		parent::__construct($provider->getCredentials());
		$this->name = strtolower($name);
		$this->info = $info;
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		// Check for connection errors
		if($this->checkConnection($mysqli)) return;
		// Load the info
		$info = AccountInfo::createInstance();
		$info->unserialize($this->info);
		// Do the query
		$mysqli->query("UPDATE kingdomscraft_economy SET xp = {$info->xp}, gold = {$info->gold}, rubies = {$info->rubies} WHERE username = '{$mysqli->escape_string($this->name)}'");
		unset($info);
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
					$info = AccountInfo::createInstance();
					$plugin->getEconomy()->updateInfo($this->name, $info->unserialize(AccountInfo::createInstance()));
					$plugin->getLogger()->debug("Successfully completed UpdateTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::CONNECTION_ERROR:
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing UpdateTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::MYSQLI_ERROR:
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing UpdateTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::NO_DATA:
					$plugin->getLogger()->debug("Error while creating economy data on kingdomscraft_database for {$this->name}");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute UpdateTask while Economy plugin isn't loaded!");
		}
	}

}