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
use kingdomscraft\command\tasks\TopRubiesCommandTask;
use kingdomscraft\Main;
use pocketmine\command\CommandSender;

class TopRubiesCommand extends EconomyCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "toprubies", "Get a list of the players with the most rubies", "/toprubies {page}", []);
		$this->setPermission("economy.command.toprubies");
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 *
	 * @return bool
	 */
	public function run(CommandSender $sender, array $args) {
		if(isset($args[0])) {
			$page = (int)$args[0];
		} else {
			$page = 1;
		}
		if(is_int($page) and $page >= 0) {
			$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new TopRubiesCommandTask($this->getPlugin()->getEconomy()->getProvider(), $sender->getName(), $page));
			return true;
		} else {
			$sender->sendMessage($this->getPlugin()->getMessage("cannot-be-negative"));
			return true;
		}
	}

}