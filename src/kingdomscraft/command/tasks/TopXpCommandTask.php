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
use pocketmine\utils\PluginException;

class TopXpCommandTask extends MySQLTask{

	/** @var string */
	protected $sender;

	/** @var int */
	protected $page;

	/** @var int */
	protected $lowestRank = 1;

	/** @var int */
	protected $highestRank = 1;

	/**
	 * TopXpCommandTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 * @param $sender
	 * @param $page
	 */
	public function __construct(MySQLEconomyProvider $provider, $sender, $page) {
		parent::__construct($provider->getCredentials());
		$this->sender = $sender;
		$this->page = $page;
		$this->highestRank = $page * 5;
		$this->lowestRank = $this->highestRank - 5;
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		if($this->checkConnection($mysqli)) return;
		$result = $mysqli->query("SELECT username, xp FROM kingdomscraft_economy ORDER BY rubies DESC LIMIT {$this->lowestRank}, {$this->highestRank}");
		if($this->checkError($mysqli)) return;
		if($result instanceof \mysqli_result) {
			$data = $result->fetch_all();
			$result->free();
			$mysqli->close();
			$this->setResult([self::SUCCESS, $data]);
			return;
		}
		$mysqli->close();
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
			$sender = $server->getPlayerExact($this->sender);
			if($sender instanceof Player) {
				$notify = true;
			}
			switch((is_array($result) ? $result[0] : $result)) {
				case self::SUCCESS:
					$message = "";
					$rank = $this->lowestRank + 1;
					foreach($result[1] as $data) {
						if($rank - 1 >= $this->highestRank) break;
						$message .= $plugin->getMessage("top-xp-format", [$rank++, $data[0], $data[1]]) . "\n";
					}
					$page = "";
					if($this->page != 1) $page = $plugin->getMessage("page-format", [(string)$this->page]);
					if($notify) $sender->sendMessage($plugin->getMessage("top-xp-success", [$page, $message]));
					$plugin->getLogger()->debug("Successfully completed TopRubiesTask on kingdomscraft_economy database");
					return;
				case self::CONNECTION_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("db-connection-error"));
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing TopRubiesTask on kingdomscraft_economy database");
					return;
				case self::MYSQLI_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("error"));
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing TopRubiesTask on kingdomscraft_economy database");
					return;
				case self::NO_DATA:
					$plugin->getLogger()->debug("Failed to execute TopRubiesTask on kingdomscraft_database as the database doesn't exist!");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute TopRubiesTask while Economy plugin isn't loaded!");
		}
	}

}