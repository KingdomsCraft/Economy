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

class ViewVaultCommandTask extends MySQLTask {

	/** @var string */
	protected $who;

	/** @var string */
	protected $to;

	public function __construct(MySQLEconomyProvider $provider, $who, $to) {
		parent::__construct($provider->getCredentials());
		$this->who = strtolower($who);
		$this->to = strtolower($to);
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$result = $mysqli->query("SELECT * FROM kingdomscraft_economy WHERE username = '{$mysqli->escape_string($this->who)}'");
		if($result instanceof \mysqli_result) {
			$data = $result->fetch_assoc();
			$result->free();
			$mysqli->close();
			if(is_array($data)) {
				$this->setResult($data);
				return;
			}
		}
		$mysqli->close();
		$this->setResult(false);
		return;
	}

	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$sender = $server->getPlayer($this->to);
			if($sender instanceof Player) {
				$result = $this->getResult();
				if(is_array($result)) {
					$info = AccountInfo::fromDatabaseRow($result);
					if($this->who === $this->to) {
						$sender->sendMessage(Main::translateColors("&aYour vault:\n&6Level: &a{$info->level}\n&6XP: &a{$info->xp}\n&6Gold: &a{$info->gold}\n&6Rubies: &a{$info->rubies}"));
						return;
					}
					$sender->sendMessage(Main::translateColors("&a{$this->who}('s) vault:\n&6Level: &a{$info->level}\n&6XP: &a{$info->xp}\n&6Gold: &a{$info->gold}\n&6Rubies: &a{$info->rubies}"));
				} else {
					if($this->who === $this->to) {
						$sender->sendMessage(Main::translateColors("&cCouldn't find any economy data for you!"));
						return;
					}
					$sender->sendMessage(Main::translateColors("&cCouldn't find any economy data for {$this->who}!"));
				}
			}
		}
	}

}