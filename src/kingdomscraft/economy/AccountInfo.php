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

use pocketmine\Player;

class AccountInfo {

	/** @var string */
	public $username = "";

	/** @var int */
	public $xp = 0;

	/** @var int */
	public $gold = 0;

	/** @var int */
	public $rubies = 10;

	/** @var int */
	public $cachedLevel = 1;

	public static function createInstance() {
		return new self;
	}

	/**
	 * @param string|Player $player
	 *
	 * @return AccountInfo
	 */
	public static function getInstance($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		$instance = new self;
		$instance->username = strtolower($player);
		return $instance;
	}

	/**
	 * Returns a json encoded array of the AccountInfo instance
	 * 
	 * @return string
	 */
	public function serialize() {
		$data = json_encode([
			"username" => $this->username,
			"xp" => $this->xp,
			"gold" => $this->gold,
			"rubies" => $this->rubies,
			"cachedLevel" => $this->cachedLevel
		]);
		return $data;
	}

	/**
	 * Loads an AccountInfo instance from a json encoded array
	 * 
	 * @param $string
	 *
	 * @return AccountInfo
	 */
	public function unserialize($string) {
		$data = json_decode($string, JSON_OBJECT_AS_ARRAY);
		try {
			$this->username = $data["username"];
			$this->xp = $data["xp"];
			$this->gold = $data["gold"];
			$this->rubies = $data["rubies"];
			$this->cachedLevel = $data["cachedLevel"];
		} catch(\ArrayOutOfBoundsException $e) {
		}
		return $this;
	}

	/**
	 * Loads an AccountInfo instance from a database row
	 * 
	 * @param array $row
	 * 
	 * @return AccountInfo
	 */
	public static function fromDatabaseRow($row) {
		$instance = new self;
		$instance->username = $row["username"];
		$instance->xp = $row["xp"];
		$instance->gold = $row["gold"];
		$instance->rubies = $row["rubies"];
		return $instance;
	}

	/**
	 * Dump the info
	 */
	public function close() {
		unset($this->username, $this->xp, $this->gold, $this->rubies, $this->cachedLevel);
	}

	public function __destruct() {
		$this->close();
	}

}