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

use kingdomscraft\command\EconomyCommand;
use kingdomscraft\command\tasks\AddGoldCommandTask;
use kingdomscraft\Main;
use pocketmine\command\CommandSender;

class AddGoldCommand extends EconomyCommand {

	public function __construct(Main $plugin) {
//		$this->setPermission("economy.command.setgold");
		parent::__construct($plugin, "addgold", "Give gold to a player", "/addgold {player} {amount}", []);
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 *
	 * @return bool
	 */
	public function run(CommandSender $sender, array $args) {
		if(isset($args[1])) {
			$name = $args[0];
			$amount = (int) $args[1];
			if(is_int($amount) and $amount >= 0) {
				$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new AddGoldCommandTask($this->getPlugin()->getEconomy()->getProvider(), $name, $amount, $sender->getName()));
				return true;
			} else {
				$sender->sendMessage($this->getPlugin()->getMessage("command.cannot-be-negative"));
				return true;
			}
		} else {
			$sender->sendMessage($this->getPlugin()->getMessage("command.usage", [$this->getUsage()]));
			return true;
		}
	}

}