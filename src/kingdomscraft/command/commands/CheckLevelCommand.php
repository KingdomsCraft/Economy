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
use kingdomscraft\command\tasks\CheckLevelCommandTask;
use kingdomscraft\Main;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class CheckLevelCommand extends EconomyCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "checklevel", "Check a players level", "/checklevel {player}", ["level", "checkxp", "nextlevel"]);
		$this->setPermission("economy.command.checklevel");
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 *
	 * @return bool
	 */
	public function run(CommandSender $sender, array $args) {
		if(isset($args[0])) {
			$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new CheckLevelCommandTask($this->getPlugin()->getEconomy()->getProvider(), $args[0], $sender->getName()));
			return true;
		} else {
			if($sender instanceof Player) {
				$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new CheckLevelCommandTask($this->getPlugin()->getEconomy()->getProvider(), $sender->getName(), $sender->getName()));
				return true;
			} else {
				$sender->sendMessage($this->getPlugin()->getMessage("in-game"));
				return true;
			}
		}
	}

}