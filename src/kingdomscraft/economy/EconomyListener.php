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

use kingdomscraft\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

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

	/**
	 * Load a players economy data
	 * 
	 * @param PlayerPreLoginEvent $event
	 * 
	 * @priority MONITOR
	 */
	public function onPreLogin(PlayerPreLoginEvent $event) {
		$player = $event->getPlayer();
		$this->economy->getProvider()->load($player->getName());
	}

	/**
	 * @param PlayerDeathEvent $event
	 */
	public function onPLayerDeath(PlayerDeathEvent $event) {
		$victim = $event->getEntity();
		$cause = $victim->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent) {
			$attacker = $cause->getDamager();
			if($attacker instanceof Player) {
				$factionsModeData = $this->economy->getPlugin()->getSettings()->getNested("factions-mode");
				if($factionsModeData["enabled"]) {
					$this->economy->addXp($attacker, $factionsModeData["xp"]);
					if($factionsModeData["take-xp-from-victim"]) $this->economy->removeXp($victim, $factionsModeData["xp"]);
					$this->economy->addGold($attacker, $factionsModeData["gold"]);
					if($factionsModeData["take-gold-from-victim"]) $this->economy->removeGold($victim, $factionsModeData["gold"]);
					$attacker->sendMessage(Main::translateColors(str_replace("{victim}", $victim->getName(), $factionsModeData["message"])));
				}
			}
		}
	}

	/**
	 * Update a players economy info when they quit
	 * 
	 * @param PlayerQuitEvent $event
	 * 
	 * @priority MONITOR
	 */
	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		$name = $player->getName();
		$info = $this->economy->getInfo($player->getName());
		if($info instanceof AccountInfo) {
			$this->economy->getProvider()->update($name, clone $info);
			$this->economy->clearInfo($player->getName());
		}
	}

	/**
	 * Update a players economy info when they're kicked
	 * 
	 * @param PlayerKickEvent $event
	 * 
	 * @priority MONITOR
	 */
	public function onKick(PlayerKickEvent $event) {
		$player = $event->getPlayer();
		$name = $player->getName();
		$info = $this->economy->getInfo($player->getName());
		if($info instanceof AccountInfo) {
			$this->economy->getProvider()->update($name, clone $info);
			$this->economy->clearInfo($player->getName());
		}
	}

}