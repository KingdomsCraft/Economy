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

use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\Main;
use kingdomscraft\provider\DummyProvider;
use kingdomscraft\provider\mysql\MySQLCredentials;
use pocketmine\Player;

class Economy {

	/** @var Economy */
	private static $instance;

	/** @var Main */
	private $plugin;

	/** @var DummyProvider */
	private $provider;
	
	/** @var EconomyListener */
	private $listener;

	/** @var AccountInfo[] */
	private $infoPool = [];

	/**
	 * @param Main $plugin
	 * 
	 * @return Economy
	 */
	public static function enable(Main $plugin) {
		assert(!self::$instance instanceof Economy, "Economy is already enabled!");
		return new self($plugin);
	}

	/**
	 * @return Economy
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * Economy constructor
	 *
	 * @param Main $plugin
	 */
	private function __construct(Main $plugin) {
		$this->plugin = $plugin;
		self::$instance = $this;
		$this->setProvider();
		$this->setListener();
	}

	/**
	 * @return Main
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * @return DummyProvider
	 */
	public function getProvider() {
		return $this->provider;
	}

	/**
	 * @return EconomyListener
	 */
	public function getListener() {
		return $this->listener;
	}

	/**
	 * Set the economy provider
	 */
	public function setProvider() {
		$this->provider = new MySQLEconomyProvider($this->plugin, MySQLCredentials::fromArray($this->plugin->settings->getNested("database")));
	}

	/**
	 * Set the economy listener
	 */
	public function setListener() {
		$this->listener = new EconomyListener($this);
	}

	/**
	 * @param $player
	 *
	 * @return AccountInfo
	 */
	public function getInfo($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		return $this->infoPool[$player];
	}

	/**
	 * @param $player
	 * @param AccountInfo $info
	 */
	public function updateInfo($player, AccountInfo $info) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		$this->infoPool[$player] = $info;
	}

	/**
	 * @param $player
	 */
	public function clearInfo($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		unset($this->infoPool[$player]);
	}
	
	/*
	 * Economy API stuff
	 */

	/**
	 * Get a players current XP
	 * 
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getXp(Player $player) {
		$info = $this->getInfo($player);
		return $info instanceof AccountInfo ? $info->xp : 0;
	}

	/**
	 * Get a players current Level
	 * 
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getLevel(Player $player) {
		$info = $this->getInfo($player);
		return $info instanceof AccountInfo ? $info->level : 1;
	}

	/**
	 * Get a players current Gold
	 * 
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getGold(Player $player) {
		$info = $this->getInfo($player);
		return $info instanceof AccountInfo ? $info->gold : 0;
	}

	/**
	 * Get a players current Rubies
	 * 
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getRubies(Player $player) {
		$info = $this->getInfo($player);
		return $info instanceof AccountInfo ? $info->rubies : 0;
	}

	/**
	 * Give a player xp
	 *
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addXp(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->xp += $amount;
			return true;
		}
		return false;
	}

	/**
	 * Give a player levels
	 * 
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addLevels(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->level += $amount;
			return true;
		}
		return false;
	}

	/**
	 * Give a player gold
	 *
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addGold(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->gold += $amount;
			return true;
		}
		return false;
	}

	/**
	 * Give a player rubies
	 *
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addRubies(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->rubies += $amount;
			return true;
		}
		return false;
	}

	/**
	 * Set a players XP
	 *
	 * @param Player $player
	 * @param int $xp
	 *
	 * @return bool
	 */
	public function setXp(Player $player, $xp) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->xp = $xp;
			return true;
		}
		return false;
	}

	/**
	 * Set a players level
	 * 
	 * @param Player $player
	 * @param int $level
	 *
	 * @return bool
	 */
	public function setLevel(Player $player, $level) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->level = $level;
			return true;
		}
		return false;
	}

	/**
	 * Set a players Gold
	 * 
	 * @param Player $player
	 * @param int $gold
	 *
	 * @return bool
	 */
	public function setGold(Player $player, $gold) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->gold = $gold;
			return true;
		}
		return false;
	}

	/**
	 * Set a players rubies
	 * 
	 * @param Player $player
	 * @param int $rubies
	 *
	 * @return bool
	 */
	public function setRubies(Player $player, $rubies) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->rubies = $rubies;
			return true;
		}
		return false;
	}

	/**
	 * Subtract xp from a player
	 *
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeXp(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->xp -= $amount;
			return true;
		}
		return false;
	}

	/**
	 * Subtract levels from a player
	 *
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeLevels(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->level -= $amount;
			return true;
		}
		return false;
	}

	/**
	 * Subtract gold from a players balance
	 *
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeGold(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->gold -= $amount;
			return true;
		}
		return false;
	}

	/**
	 * Subtract rubies from a players balance
	 *
	 * @param Player $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeRubies(Player $player, $amount = 1) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$info->rubies -= $amount;
			return true;
		}
		return false;
	}

}