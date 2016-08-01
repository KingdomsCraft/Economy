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

	/** @var MySQLEconomyProvider */
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
	 * @param Player|string $player
	 *
	 * @return AccountInfo
	 */
	public function getInfo($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		return $this->infoPool[strtolower($player)];
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
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addXp($player, $amount = 1) {
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->addXp($player, $amount);
	}

	/**
	 * Give a player levels
	 * 
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function addLevels($player, $amount = 1) {
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->addLevel($player, $amount);
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
		if($player instanceof Player)
			$player = $player->getName();
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
		if($player instanceof Player)
			$player = $player->getName();
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
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->setXp($player, $amount);
	}

	/**
	 * Set a players level
	 * 
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function setLevel($player, $amount = 1) {
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->setLevel($player, $amount);
	}

	/**
	 * Set a players Gold
	 * 
	 * @param Player|string $player
	 * @param int $amount
	 */
	public function setGold($player, $amount = 1) {
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->setGold($player, $amount);
	}

	/**
	 * Set a players rubies
	 * 
	 * @param Player|string $player
	 * @param int $amount
	 */
	public function setRubies($player, $amount = 1) {
		if($player instanceof Player)
			$player = $player->getName();
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
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->takeXp($player, $amount);
	}

	/**
	 * Subtract levels from a player
	 *
	 * @param Player|string $player
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function removeLevels($player, $amount = 1) {
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->takeLevel($player, $amount);
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
		if($player instanceof Player)
			$player = $player->getName();
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
		if($player instanceof Player)
			$player = $player->getName();
		$this->provider->takeRubies($player, $amount);
	}

}