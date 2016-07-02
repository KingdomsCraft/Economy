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

namespace kingdomscraft\command;

use pocketmine\command\CommandSender;
use pocketmine\Player;

abstract class EconomyPlayerCommand extends EconomyCommand {

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 *
	 * @return bool
	 */
	public function run(CommandSender $sender, array $args) {
		if($sender instanceof Player) {
			return $this->onRun($sender, $args);
		} else {
			$sender->sendMessage($this->getPlugin()->getMessage("command.in-game"));
			return true;
		}
	}

	/**
	 * @param Player $player
	 * @param array $args
	 *
	 * @return mixed
	 */
	public abstract function onRun(Player $player, array $args);

}