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

namespace kingdomscraft\economy;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EconomyListener implements Listener {

	/** @var Economy */
	private $economy;

	public function __construct(Economy $economy) {
		$this->economy = $economy;
		$plugin = $economy->getPlugin();
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @return Economy
	 */
	public function getEconomy() {
		return $this->economy;
	}

	public function onJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$this->economy->getProvider()->load($player->getName());
	}
	
	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		if($this->economy->getInfo($player->getName()) instanceof AccountInfo) {
			$this->economy->clearInfo($player->getName());
		}
	}

}