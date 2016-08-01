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
use kingdomscraft\economy\provider\mysql\task\TakeRubiesTask;
use kingdomscraft\Main;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\PluginException;

class TakeRubiesCommandTask extends TakeRubiesTask {

	/** @var string */
	protected $sender;

	/**
	 * TakeRubiesCommandTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 * @param $name
	 * @param $amount
	 * @param $sender
	 */
	public function __construct(MySQLEconomyProvider $provider, $name, $amount, $sender) {
		parent::__construct($provider, $name, $amount);
		$this->sender = $sender;
	}

	/**
	 * @param Server $server
	 */
	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$result = $this->getResult();
			$notify = false;
			$sender = $server->getPlayerExact($this->sender);
			if($sender instanceof Player) {
				$notify = true;
			}
			switch((is_array($result) ? $result[0] : $result)) {
				case self::SUCCESS:
					$info = $plugin->getEconomy()->getInfo($this->name);
					if($info instanceof AccountInfo) {
						$info->rubies -= $this->amount;
					}
					if($notify) $sender->sendMessage($plugin->getMessage("command.take-rubies-success", [$this->name, $this->amount]));
					$plugin->getLogger()->debug("Successfully completed TakeRubiesTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::CONNECTION_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("command.db-connection-error"));
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing TakeRubiesTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::MYSQLI_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("command.error"));
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing TakeRubiesTask on kingdomscraft_economy database for {$this->name}");
					return;
				case self::NO_DATA:
					if($notify) $sender->sendMessage($plugin->getMessage("command.no-data", [$this->name]));
					$plugin->getLogger()->debug("Failed to execute TakeRubiesTask on kingdomscraft_database for {$this->name} as they don't have any data");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute TakeRubiesTask while Economy plugin isn't loaded!");
		}	}

}