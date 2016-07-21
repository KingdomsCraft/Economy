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
use kingdomscraft\command\tasks\SetGoldCommandTask;
use kingdomscraft\economy\AccountInfo;
use kingdomscraft\Main;
use pocketmine\Player;

class SetGoldCommand extends EconomyPlayerCommand {

	/**
	 * SetGoldCommand constructor
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin) {
//		$this->setPermission("economy.command.setgold");
		parent::__construct($plugin, "setgold", "Set a players gold", "/setgold {amount}", []);
	}

	/**
	 * @param Player $player
	 * @param array $args
	 *
	 * @return bool
	 */
	public function onRun(Player $player, array $args) {
		if(isset($args[1])) {
			$amount = (int) $args[1];
			if(is_int((int) $args[1])) {
				$name = $args[0];
				$player->sendMessage("Attempting to set gold...");
				$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SetGoldCommandTask($this->getPlugin()->getEconomy()->getProvider(), $name, $amount, $player->getName()));
				return true;
			}
		} else {
			$player->sendMessage("Please specify a player!");
			return true;
		}
	}

}