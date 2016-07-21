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
use kingdomscraft\economy\Economy;
use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\Main;
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\PluginException;

class MySQLEconomyLoadTask extends MySQLTask {

	/** @var string $name */
	protected $name;

	/**
	 * Error states
	 */
	const NO_DATA = "error.no.data";
	const DATA_WRONG_FORMAT = "error.wrong.format";

	/**
	 * MySQLEconomyLoadTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 * @param $name
	 */
	public function __construct(MySQLEconomyProvider $provider, $name) {
		parent::__construct($provider->getCredentials());
		$this->name = strtolower($name);
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$result = $mysqli->query("SELECT * FROM kingdomscraft_economy WHERE username = '{$mysqli->escape_string($this->name)}'");
		if($result instanceof \mysqli_result) {
			$row = $result->fetch_assoc();
			$result->free();
			$mysqli->close();
			if(is_array($row)) {
				$this->setResult($row);
				return;
			} else {
				$this->setResult(self::NO_DATA);
				return;
			}
		} else {
			$this->setResult(self::NO_DATA);
			return;
		}
	}

	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$player = $server->getPlayer($this->name);
			if($player instanceof Player) {
				$result = $this->getResult();
				if(is_array($result)) {
					$plugin->getEconomy()->updateInfo($player->getName(), AccountInfo::fromDatabaseRow($result));
					$server->getLogger()->debug("Successfully executed MySQLEconomyLoadTask for '{$this->name}'");
					return;
				}
				switch($result) {
					default:
						$plugin->getEconomy()->updateInfo($player->getName(), AccountInfo::getInstance($player));
						$server->getLogger()->debug("Successfully executed MySQLEconomyLoadTask for '{$this->name}'");
						return;
					case self::NO_DATA:
						$plugin->getEconomy()->updateInfo($player->getName(), AccountInfo::getInstance($player));
						$server->getLogger()->debug("Failed to execute MySQLEconomyLoadTask for '{$this->name}' as they're not registered to the economy database");
						return;
				}
			} else {
				$server->getLogger()->debug("Failed to execute MySQLEconomyLoadTask for '{$this->name}' as the player isn't online");
			}
		} else {
			$server->getLogger()->debug("Failed to execute MySQLEconomyLoadTask for '{$this->name}' as the Economy plugin isn't loaded");
			throw new PluginException("Economy plugin isn't enabled!");
		}
	}

}