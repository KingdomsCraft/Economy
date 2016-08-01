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
use kingdomscraft\command\tasks\PayCommandTask;
use kingdomscraft\Main;
use pocketmine\Player;

class PayCommand extends EconomyPlayerCommand {

	public function __construct(Main $plugin) {
//		$this->setPermission("economy.command.setgold");
		parent::__construct($plugin, "pay", "Pay a player some gold", "/pay {player} {amount}", []);
	}

	public function onRun(Player $player, array $args) {
		if(isset($args[1])) {
			$name = $args[0];
			$amount = (int)$args[1];
			if($amount > 0) {
				if($this->getPlugin()->getEconomy()->hasInfo($player)) {
					$info = $this->getPlugin()->getEconomy()->getInfo($player);
					if($info->gold >= $amount) {
						$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new PayCommandTask($this->getPlugin()->getEconomy()->getProvider(), $player->getName(), $name, $amount));
					} else {
						$player->sendMessage($this->getPlugin()->getMessage("need-more-gold", [$name]));
						return true;
					}
				} else {
					$player->sendMessage($this->getPlugin()->getMessage("no-data", ["you"]));
					return true;
				}
			} else {
				$player->sendMessage($this->getPlugin()->getMessage("command.cannot-be-negative"));
				return true;
			}
		} else {
			$player->sendMessage($this->getPlugin()->getMessage("command.usage", [$this->getUsage()]));
			return true;
		}
	}

}