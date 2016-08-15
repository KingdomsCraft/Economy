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
use kingdomscraft\command\tasks\TopGoldCommandTask;
use kingdomscraft\Main;
use pocketmine\command\CommandSender;

class TopGoldCommand extends EconomyCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "topgold", "Get a list of the most rich players", "/topgold {page}", []);
		$this->setPermission("economy.command.topxp");
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
			$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new TopGoldCommandTask($this->getPlugin()->getEconomy()->getProvider(), $sender->getName(), $page));
			return true;
		} else {
			$sender->sendMessage($this->getPlugin()->getMessage("command.cannot-be-negative"));
			return true;
		}
	}

}