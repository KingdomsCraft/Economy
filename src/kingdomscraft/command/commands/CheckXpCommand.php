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
use kingdomscraft\Main;
use pocketmine\command\CommandSender;

class CheckXpCommand extends EconomyCommand {

	public function __construct(Main $plugin) {
//		$this->setPermission("economy.command.setgold");
		parent::__construct($plugin, "checkxp", "Check a players XP", "/checkxp {player}", []);
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 *
	 * @return bool
	 */
	public function run(CommandSender $sender, array $args) {
		// TODO: Implement run() method.
	}

}