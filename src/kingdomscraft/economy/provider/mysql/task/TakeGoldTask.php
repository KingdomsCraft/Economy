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

class TakeGoldTask extends MySQLTask {

	/** @var string */
	protected $name;

	/** @var int */
	protected $amount;

	/**
	 * TakeGoldTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 * @param $name
	 * @param $amount
	 */
	public function __construct(MySQLEconomyProvider $provider, $name, $amount) {
		parent::__construct($provider->getCredentials());
		$this->name = strtolower($name);
		$this->amount = $amount;
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		// Check for connection errors
		if($this->checkConnection($mysqli)) return;
		// Do the query
		$mysqli->query("UPDATE kingdomscraft_economy SET gold = GREATEST(gold - {$this->amount}, 0) WHERE username = '{$mysqli->escape_string($this->name)}'");
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
					$info = $plugin->getEconomy()->getInfo($this->name);
					if($info instanceof AccountInfo) {
						$info->gold -= $this->amount;
					}
					$plugin->getLogger()->debug("Successfully completed TakeGoldTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::CONNECTION_ERROR:
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing TakeGoldTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::MYSQLI_ERROR:
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing TakeGoldTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::NO_DATA:
					$plugin->getLogger()->debug("Failed to execute TakeGoldTask on kingdomscraft_database for {$this->name} as they don't have any data");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute TakeGoldTask while Economy plugin isn't loaded!");
		}
	}

}