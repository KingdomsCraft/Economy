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

class LevelInfo {

	/** @var int */
	public $level = 1;

	/** @var int */
	public $minXp = 1;

	/** @var int */
	public $maxXp = 2;

	/** @var array */
	public $commands = [];

	/**
	 * @return LevelInfo
	 */
	public static function getDefaultInstance() {
		return new self;
	}

	/**
	 * @param array $array
	 *
	 * @return LevelInfo
	 */
	public static function fromArray(array $array) {
		$instance = new self;
		try {
			$instance->level = (int)$array["level"];
			$instance->minXp = (int)$array["minXp"];
			$instance->maxXp = (int)$array["maxXp"];
			$instance->commands = $array["commands"];
			return $instance;
		} catch(\ArrayOutOfBoundsException $e) {
			// TODO Error handling
		}
	}

	/**
	 * @return string
	 */
	public function serialize() {
		return json_encode([
			"level" => $this->level,
			"minXp" => $this->minXp,
			"maxXp" => $this->maxXp,
			"commands" => $this->commands
		]);
	}

	/**
	 * @param $string
	 */
	public function unserialize($string) {
		$data = json_decode($string, JSON_OBJECT_AS_ARRAY);
		try {
			$this->level = $data["level"];
			$this->minXp = $data["minXp"];
			$this->maxXp = $data["maxXp"];
			$this->commands = $data["commands"];
		} catch(\ArrayOutOfBoundsException $e) {
			// TODO Error handling
		}
	}

}