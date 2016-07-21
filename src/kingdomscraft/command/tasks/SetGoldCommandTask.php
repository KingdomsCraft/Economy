<?php

/**
 * CrazedCraft Network Economy
 *
 * Copyright (C) 2016 CrazedCraft Network
 *
 * This is private software, you cannot redistribute it and/or modify any way
 * unless otherwise given permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author JackNoordhuis
 *
 * Created on 20/07/2016 at 7:33 PM
 *
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

	public function __construct(MySQLEconomyProvider $provider, $name, $amount, $sender = "") {
		parent::__construct($provider->getCredentials());
		$this->name = strtolower($name);
		$this->amount = $amount;
		$this->sender = $sender;
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$mysqli->query("UPDATE kingdomscraft_economy SET gold = {$this->amount} WHERE username = '{$mysqli->escape_string($this->name)}'");
		if($mysqli->affected_rows > 0) {
			$this->setResult(true);
			return;
		}
		$this->setResult(false);
	}

	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$sender = $server->getPlayer($this->sender);
			if($sender instanceof Player) {
				$result = $this->getResult();
				if($result) {
					$sender->sendMessage(Main::translateColors("&aSet &b{$this->name}'s &abalance to &6{$this->amount}&a!"));
				} else {
					$sender->sendMessage(Main::translateColors("&cCouldn't find any economy data for {$this->name}!"));
				}
			}
		}
	}

}