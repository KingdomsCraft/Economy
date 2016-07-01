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
	public $level = 1;

	/** @var int */
	public $xp = 0;

	/** @var int */
	public $gold = 0;

	/** @var int */
	public $rubies = 0;

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
		return json_encode([
			"username" => $this->username,
			"level" => $this->level,
			"xp" => $this->xp,
			"gold" => $this->gold,
			"rubies" => $this->rubies
		]);
	}

	/**
	 * Loads an AccountInfo instance from a json encoded array
	 * 
	 * @param $string
	 */
	public function unserialize($string) {
		$data = json_decode($string);
		try {
			$this->username = $data["username"];
			$this->level = $data["level"];
			$this->xp = $data["xp"];
			$this->gold = $data["gold"];
			$this->rubies = $data["rubies"];
		} catch(\ArrayOutOfBoundsException $e) {
		}
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
		$instance->level = $row["level"];
		$instance->xp = $row["xp"];
		$instance->gold = $row["gold"];
		$instance->rubies = $row["rubies"];
		return $instance;
	}

	/**
	 * Dump the info
	 */
	public function close() {
		unset($this->username, $this->level, $this->xp, $this->gold, $this->rubies);
	}

	public function __destruct() {
		$this->close();
	}

}