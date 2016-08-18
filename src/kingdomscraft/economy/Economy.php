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
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;

class Economy {

	/** @var Economy */
	private static $instance;

	/** @var Main */
	private $plugin;

	/** @var MySQLEconomyProvider */
	private $provider;
	
	/** @var EconomyListener */
	private $listener;

	/** @var LevelInfo[] */
	private $levels = [];

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
		$this->setLevelInfo();
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
	 * @return MySQLEconomyProvider
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
	 * Set all the level info
	 */
	protected function setLevelInfo() {
		foreach($this->plugin->getSettings()->getNested("xp.levels") as $key => $data) {
			$this->levels[$key] = LevelInfo::fromArray([
				"level" => $key,
				"minXp" => $data["min-xp"],
				"maxXp" => $data["max-xp"],
				"commands" => $data["commands"]
			]);
		}
	}

	/**
	 * @param Player|string $player
	 *
	 * @return AccountInfo
	 */
	public function getInfo($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		return ($this->hasInfo($player) ? $this->infoPool[strtolower($player)] : null);
	}

	/**
	 * @param Player|string $player
	 *
	 * @return bool
	 */
	public function hasInfo($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		return isset($this->infoPool[strtolower($player)]);
	}

	/**
	 * @param Player|string $player
	 * @param AccountInfo $info
	 */
	public function updateInfo($player, AccountInfo $info) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		$this->infoPool[strtolower($player)] = $info;
	}

	/**
	 * @param Player|string $player
	 */
	public function clearInfo($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		unset($this->infoPool[strtolower($player)]);
	}

	/**
	 * Get a players next level
	 *
	 * @param Player $player
	 *
	 * @return int|LevelInfo
	 */
	public function getNextLevel(Player $player) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$level = $info->cachedLevel;
			return (isset($this->levels[$level + 1]) ? $this->levels[$level + 1]->level : $level);
		}
		return 1;
	}

	/**
	 * Get a players next level with their current XP
	 *
	 * @param int $currentXp
	 *
	 * @return int int
	 */
	public function getNextLevelWithXp($currentXp) {
		$level = $this->getLevelWithXp($currentXp);
		return (isset($this->levels[$level + 1]) ? $this->levels[$level + 1]->level : $level);
	}

	/**
	 * Check to see if a player has leveled up
	 *
	 * @param Player $player
	 */
	public function checkLevel(Player $player) {
		$info = $this->getInfo($player);
		if($info instanceof AccountInfo) {
			$cached = $info->cachedLevel;
			$current = $this->getLevel($player);
			$next = $this->getNextLevel($player);
			if($current <= $cached) return;
			if($next <= $cached) return;
			if(isset($this->levels[$next])) {
				foreach($this->levels[$next]->commands as $commands) {
					$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{player}", $player->getName(), $commands));
				}
			}
			$info->cachedLevel = $next;
			$this->checkLevel($player);
		}
	}

	/**
	 * Get a level based on it's XP value
	 *
	 * @param int $xp
	 *
	 * @return int
	 */
	public function getLevelWithXp($xp) {
		foreach($this->levels as $level) {
			if($xp >= $level->minXp and $xp <= $level->maxXp) return $level->level;
		}
		return 1;
	}

	/**
	 * Get the XP required until the next level wih XP
	 *
	 * @param int $currentXp
	 *
	 * @return int
	 */
	public function getXpTillNextLevel($currentXp) {
		$nextLevel = $this->getNextLevelWithXp($currentXp);
		return $this->levels[$nextLevel]->minXp - $currentXp;
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
		if($info instanceof AccountInfo) {
			foreach($this->levels as $level) {
				if($info->xp >= $level->minXp and $info->xp <= $level->maxXp) return $level->level;
			}
		}
		return 1;
	}

	/**
	 * Get a players current Level with their account info
	 *
	 * @param AccountInfo $info
	 *
	 * @return int
	 */
	public function getLevelWithInfo(AccountInfo $info) {
		foreach($this->levels as $level) {
			if($info->xp >= $level->minXp and $info->xp <= $level->maxXp) return $level->level;
		}
		return 1;
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
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addXp($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->addXp($player, $amount);
	}

	/**
	 * Give a player gold
	 *
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addGold($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->addGold($player, $amount);
	}

	/**
	 * Give a player rubies
	 *
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addRubies($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->addRubies($player, $amount);
	}

	/**
	 * Set a players XP
	 *
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function setXp($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->setXp($player, $amount);
	}

	/**
	 * Set a players Gold
	 *
	 * @param Player|string $player
	 * @param int $amount
	 */
	public function setGold($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->setGold($player, $amount);
	}

	/**
	 * Set a players rubies
	 * 
	 * @param Player|string $player
	 * @param int $amount
	 */
	public function setRubies($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->setRubies($player->getName(), $amount);
	}

	/**
	 * Subtract xp from a player
	 *
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeXp($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->takeXp($player, $amount);
	}

	/**
	 * Subtract gold from a players balance
	 *
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeGold($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->takeGold($player, $amount);
	}

	/**
	 * Subtract rubies from a players balance
	 *
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeRubies($player, $amount = 1) {
		if($player instanceof Player) $player = $player->getName();
		if($amount < 0) $amount = 0;
		$this->provider->takeRubies($player, $amount);
	}

}