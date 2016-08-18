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
use kingdomscraft\command\tasks\SetXpCommandTask;
use kingdomscraft\Main;
use pocketmine\command\CommandSender;

class SetXpCommand extends EconomyCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "setxp", "Set a players XP", "/setexp {player} {amount}", []);
		$this->setPermission("economy.command.setxp");
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
				$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SetXpCommandTask($this->getPlugin()->getEconomy()->getProvider(), $name, $amount, $sender->getName()));
				return true;
			} else {
				$sender->sendMessage($this->getPlugin()->getMessage("cannot-be-negative"));
				return true;
			}
		} else {
			$sender->sendMessage($this->getPlugin()->getMessage("usage", [$this->getUsage()]));
			return true;
		}
	}

}