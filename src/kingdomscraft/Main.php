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

namespace kingdomscraft;

use kingdomscraft\command\EconomyCommandMap;
use kingdomscraft\economy\Economy;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {

	/** @var Config */
	public $settings = [];

	/** @var Economy */
	private $economy;

	/** @var EconomyCommandMap */
	private $commandMap;

	/**
	 * Enable all modules and load configs
	 */
	public function onEnable() {
		$this->loadConfigs();
		$this->enableEconomy();
		$this->setCommandMap();
	}

	/**
	 * Load all the config things \o/
	 */
	public function loadConfigs() {
		$this->saveResource("Settings.yml");
		$this->settings = new Config($this->getDataFolder() . "Settings.yml", Config::YAML);
	}

	/**
	 * @return Economy
	 */
	public function getEconomy() {
		return $this->economy;
	}

	/**
	 * @return EconomyCommandMap
	 */
	public function getCommandMap() {
		return $this->commandMap;
	}

	/**
	 * Enable the economy module
	 */
	public function enableEconomy() {
		$this->economy = Economy::enable($this);
	}

	/**
	 * Set the command map
	 */
	public function setCommandMap() {
		$this->commandMap = new EconomyCommandMap($this);
	}

	/**
	 * @param string $nested
	 *
	 * @return string
	 */
	public function getMessage($nested) {
		return self::translateColors($this->settings->getNested("messages.{$nested}", " "));
	}

	/**
	 * Apply minecraft color codes to a string from our custom ones
	 *
	 * @param string $string
	 * @param string $symbol
	 *
	 * @return string
	 */
	public static function translateColors($string, $symbol = "&") {
		$string = str_replace($symbol . "0", TF::BLACK, $string);
		$string = str_replace($symbol . "1", TF::DARK_BLUE, $string);
		$string = str_replace($symbol . "2", TF::DARK_GREEN, $string);
		$string = str_replace($symbol . "3", TF::DARK_AQUA, $string);
		$string = str_replace($symbol . "4", TF::DARK_RED, $string);
		$string = str_replace($symbol . "5", TF::DARK_PURPLE, $string);
		$string = str_replace($symbol . "6", TF::GOLD, $string);
		$string = str_replace($symbol . "7", TF::GRAY, $string);
		$string = str_replace($symbol . "8", TF::DARK_GRAY, $string);
		$string = str_replace($symbol . "9", TF::BLUE, $string);
		$string = str_replace($symbol . "a", TF::GREEN, $string);
		$string = str_replace($symbol . "b", TF::AQUA, $string);
		$string = str_replace($symbol . "c", TF::RED, $string);
		$string = str_replace($symbol . "d", TF::LIGHT_PURPLE, $string);
		$string = str_replace($symbol . "e", TF::YELLOW, $string);
		$string = str_replace($symbol . "f", TF::WHITE, $string);

		$string = str_replace($symbol . "k", TF::OBFUSCATED, $string);
		$string = str_replace($symbol . "l", TF::BOLD, $string);
		$string = str_replace($symbol . "m", TF::STRIKETHROUGH, $string);
		$string = str_replace($symbol . "n", TF::UNDERLINE, $string);
		$string = str_replace($symbol . "o", TF::ITALIC, $string);
		$string = str_replace($symbol . "r", TF::RESET, $string);

		return $string;
	}

	public static function translateArguments($message, $args = []) {
		return $message;
	}

}