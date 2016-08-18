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
use kingdomscraft\command\tasks\AddRubiesCommandTask;
use kingdomscraft\Main;
use pocketmine\command\CommandSender;

class AddRubiesCommand extends EconomyCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "addrubies", "Give rubies to a player", "/addrubies {player} {amount}", ["giverubies"]);
		$this->setPermission("economy.command.addrubies");
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
				$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new AddRubiesCommandTask($this->getPlugin()->getEconomy()->getProvider(), $name, $amount, $sender->getName()));
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