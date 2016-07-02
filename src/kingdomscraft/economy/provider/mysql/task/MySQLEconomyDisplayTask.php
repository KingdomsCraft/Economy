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

	/** @var string */
	protected $who;

	/** @var string */
	protected $to;

	/** @var string */
	protected $type;

	/*
	 * Display types
	 */
	const TYPE_VAULT = "display.type.vault";
	const TYPE_LEVEL_PROGRESS = "display.type.level_progress";
	const TYPE_TOP_PRESTIGE = "display.type.top_prestige";
	const TYPE_TOP_GOLD = "display.type.top_gold";
	const TYPE_TOP_RUBIES = "display.type.top_rubies";

	/*
	 * Error states
	 */
	const NO_DATA = "error.no.data";
	const DATA_WRONG_FORMAT = "error.wrong.format";

	public function __construct(Economy $economy, $who, $to, $type = self::TYPE_VAULT) {
		parent::__construct($economy->getProvider()->getCredentials());
		$this->who = strtolower($who);
		$this->to = strtolower($to);
		$this->type = $type;
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
			$player = $server->getPlayer($this->to);
			if($player instanceof Player) {
				$result = $this->getResult();
				if(is_array($result)) {
					switch($this->type) {
						default:
						case self::TYPE_VAULT:
							$player->sendMessage("{$this->who}'s vault\nXP: {$result["xp"]}\nLevel: {$result["level"]}\nGold: {$result["gold"]}\nRubies: {$result["rubies"]}");
							break;
						case self::TYPE_LEVEL_PROGRESS:
							$player->sendMessage("{$this->who}'s level progress\nXP required for next level: ");
							break;
						case self::TYPE_TOP_PRESTIGE:
							
							break;
						case self::TYPE_TOP_GOLD:
							
							break;
						case self::TYPE_TOP_RUBIES:
							
							break;
					}
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