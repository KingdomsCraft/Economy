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

namespace kingdomscraft\command\tasks;

use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\Main;
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Player;
use pocketmine\Server;

class SetGoldCommandTask extends MySQLTask {

	/** @var string */
	protected $name;

	/** @var int */
	protected $amount;

	/** @var string */
	protected $sender;

	/** Result states */
	const CONNECTION_ERROR = "result.connection.error";
	const SUCCESS = "result.success";
	const NO_DATA = "result.no.data";

	public function __construct(MySQLEconomyProvider $provider, $name, $amount, $sender = "") {
		parent::__construct($provider->getCredentials());
		$this->name = strtolower($name);
		$this->amount = $amount;
		$this->sender = $sender;
	}

	/**
	 * Attempt to set the targets gold
	 */
	public function onRun() {
		$mysqli = $this->getMysqli();
		$mysqli->query("UPDATE kingdomscraft_economy SET gold = {$this->amount} WHERE username = '{$mysqli->escape_string($this->name)}'");
		if($mysqli->affected_rows > 0) {
			$this->setResult(true);
			return;
		}
		$this->setResult(false);
	}

	/**
	 * @param Server $server
	 */
	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$sender = $server->getPlayer($this->sender);
			$result = $this->getResult();
			if($sender instanceof Player) {
				if($result) {
					$sender->sendMessage($plugin->getMessage("gold-set-success", [$this->name, $this->amount]));
				} else {
					$sender->sendMessage($plugin->getMessage("no-data", [$this->name]));
				}
			} elseif(strtolower($this->sender) === "console") {
				if($result) {
					$server->getLogger()->info($plugin->getMessage("gold-set-success", [$this->name, $this->amount]));
				} else {
					$server->getLogger()->info($plugin->getMessage("no-data", [$this->name]));
				}
			}
		}
	}

}