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

class MySQLEconomyRegisterTask extends MySQLTask {

	/** @var string $name */
	protected $name;

	/** @var string $info */
	protected $info;

	/**
	 * Error states
	 */
	const NO_DATA = "error.no.data";

	public function __construct(MySQLEconomyProvider $provider, $name, $info) {
		parent::__construct($provider->getCredentials());
		$this->name = strtolower($name);
		$this->info = $info;
	}

	public function onRun() {
		$info = AccountInfo::createInstance();
		$info->unserialize($this->info);
		$mysqli = $this->getMysqli();
		$mysqli->query("INSERT INTO kingdomscraft_economy (username, xp_level, xp, gold, rubies) VALUES
			('{$this->name}', {$info->level}, {$info->xp}, {$info->gold}, {$info->rubies})");
		unset($info);
		if($mysqli->affected_rows > 0) {
			var_dump("YAYAYA");
			$mysqli->close();
			$this->setResult(true);
			return;
		}
		$mysqli->close();
		$this->setResult(self::NO_DATA);
		return;
	}

	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$player = $server->getPlayer($this->name);
			if($player instanceof Player) {
				$info = AccountInfo::createInstance();
				$info->unserialize($this->info);
				$plugin->getEconomy()->updateInfo($player->getName(), $info);
				unset($info);
			}
			$result = $this->getResult();
			switch($result) {
				default:
					$plugin->getEconomy()->updateInfo($player->getName(), AccountInfo::getInstance($player));
					$server->getLogger()->debug("Successfully executed MySQLEconomyRegisterTask for '{$this->name}'");
					return;
				case self::NO_DATA:
					$server->getLogger()->debug("Failed to execute MySQLEconomyRegisterTask for '{$this->name}' as the data given wasn't in the correct format");
					return;
			}
		} else {
			$server->getLogger()->debug("Failed to execute MySQLEconomyRegisterTask for '{$this->name}' as  as the Economy plugin isn't loaded");
			throw new PluginException("Economy plugin isn't enabled!");
		}
	}

}