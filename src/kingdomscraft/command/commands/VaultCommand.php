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

namespace kingdomscraft\command\commands;

use kingdomscraft\command\EconomyPlayerCommand;
use kingdomscraft\command\tasks\ViewVaultCommandTask;
use kingdomscraft\Main;
use pocketmine\Player;

class VaultCommand extends EconomyPlayerCommand {

	/**
	 * VaultCommand constructor
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin) {
//		$this->setPermission(economy.command.vault);
		parent::__construct($plugin, "vault", "View your Level XP, Gold and Rubies", "/vault", ["v"]);
	}

	/**
	 * @param Player $player
	 * @param array $args
	 *
	 * @return bool
	 */
	public function onRun(Player $player, array $args) {
		if(isset($args[0])) {
			$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new ViewVaultCommandTask($this->getPlugin()->getEconomy()->getProvider(), $args[0], $player->getName()));
			return true;
		}
		$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new ViewVaultCommandTask($this->getPlugin()->getEconomy()->getProvider(), $player->getName(), $player->getName()));
		return true;
	}

}