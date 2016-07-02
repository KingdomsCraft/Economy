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
use kingdomscraft\Main;
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\PluginException;

class MySQLEconomyDisplayTask extends MySQLTask {

	/** @var string $who */
	protected $who;

	/** @var string $to */
	protected $to;

	/**
	 * Error states
	 */
	const NO_DATA = "error.no.data";
	const DATA_WRONG_FORMAT = "error.wrong.format";

	public function __construct(Economy $economy, $who, $to) {
		parent::__construct($economy->getProvider()->getCredentials());
		$this->who = strtolower($who);
		$this->to = strtolower($to);
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
				$this->setResult(self::DATA_WRONG_FORMAT);
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
			$player = $server->getPlayer($this->to);
			if($player instanceof Player) {
				$result = $this->getResult();
				if(is_array($result)) {
					$player->sendMessage("{$this->who}'s economy info\nXP: {$result["xp"]}\nLevel: {$result["level"]}\nGold: {$result["gold"]}\nRubies: {$result["rubies"]}");
					$server->getLogger()->debug("Successfully executed MySQLEconomyDisplayTask for '{$this->to}'");
					return;
				}
				switch($result) {
					default:
						$player->sendMessage("Error displaying {$this->who}'s economy info");
						$server->getLogger()->debug("Unknown error while MySQLEconomyDisplayTask for '{$this->to}'");
						return;
					case self::DATA_WRONG_FORMAT:
						$server->getLogger()->debug("Failed to execute MySQLEconomyDisplayTask for '{$this->to}' as the data isn't in an array");
						return;
					case self::NO_DATA:
						$player->sendMessage("{$this->who} doesn't have any economy info");
						$server->getLogger()->debug("Successfully executed MySQLEconomyDisplayTask for '{$this->to}'");
						return;
				}
			} else {
				$server->getLogger()->debug("Failed to execute MySQLEconomyDisplayTask for '{$this->to}' as the player isn't online");
			}
		} else {
			$server->getLogger()->debug("Failed to execute MySQLEconomyDisplayTask for '{$this->to}' as the Economy plugin isn't loaded");
			throw new PluginException("Economy plugin isn't enabled!");
		}
	}

}