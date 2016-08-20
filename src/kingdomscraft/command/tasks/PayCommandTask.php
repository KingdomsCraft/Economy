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

use kingdomscraft\economy\AccountInfo;
use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\Main;
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\PluginException;

class PayCommandTask extends MySQLTask {

	/** @var string */
	protected $from;

	/** @var string */
	protected $to;

	/** @var int */
	protected $amount;

	/**
	 * PayCommandTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 * @param $from
	 * @param $to
	 * @param $amount
	 */
	public function __construct(MySQLEconomyProvider $provider, $from, $to, $amount) {
		parent::__construct($provider->getCredentials());
		$this->from = strtolower($from);
		$this->to = strtolower($to);
		$this->amount = $amount;
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$this->checkConnection($mysqli);
		$mysqli->query("UPDATE kingdomscraft_economy SET gold = GREATEST(gold - {$this->amount}, 0) WHERE username = '{$this->from}'");
		$this->checkError($mysqli);
		$mysqli->query("UPDATE kingdomscraft_economy SET gold = gold + {$this->amount} WHERE username = '{$this->to}'");
		$this->checkError($mysqli);
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
			$notify = false;
			$sender = $server->getPlayerExact($this->from);
			if($sender instanceof Player) $notify = true;
			switch((is_array($result) ? $result[0] : $result)) {
				case self::SUCCESS:
					$info = $plugin->getEconomy()->getInfo($this->from);
					if($info instanceof AccountInfo) {
						$info->gold -= $this->amount;
					}
					$info = $plugin->getEconomy()->getInfo($this->to);
					if($info instanceof AccountInfo) {
						$info->gold += $this->amount;
					}
					if($notify) $sender->sendMessage($plugin->getMessage("pay-success", [$this->to, $this->amount]));
					$to = $server->getPlayerExact($this->to);
					if($to instanceof Player) $to->sendMessage($plugin->getMessage("pay-receive", [$this->from, $this->amount]));
					$plugin->getLogger()->debug("Successfully completed PayCommandTask on kingdomscraft_economy database for {$this->to}");
					return;
				case self::CONNECTION_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("db-connection-error"));
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing PayCommandTask on kingdomscraft_economy database for {$this->to}");
					return;
				case self::MYSQLI_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("error"));
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing PayCommandTask on kingdomscraft_economy database for {$this->to}");
					return;
				case self::NO_DATA:
					if($notify) $sender->sendMessage($plugin->getMessage("no-data", [$this->to]));
					$plugin->getLogger()->debug("Failed to execute PayCommandTask on kingdomscraft_database for {$this->to} as they don't have any data");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute PayCommandTask while Economy plugin isn't loaded!");
		}
	}

}