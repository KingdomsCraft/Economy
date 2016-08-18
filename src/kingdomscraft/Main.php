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
use kingdomscraft\tile\RubyShopSign;
use kingdomscraft\tile\ShopSign;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Tile;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {

	/** @var Config */
	public $settings = [];

	/** @var array */
	protected $messages = [];

	/** @var Economy */
	private $economy;

	/** @var EconomyCommandMap */
	private $commandMap;

	const SHOP_SIGN = "ShopSign";
	const RUBY_SHOP_SIGN = "RubyShopSign";

	public function onLoad() {
		Tile::registerTile(ShopSign::class);
		Tile::registerTile(RubyShopSign::class);
	}

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
		$this->saveResource("Messages.yml");
		$messages = (new Config($this->getDataFolder() . "Messages.yml", Config::YAML))->getAll();
		foreach($messages as $key => $message) {
			$this->loadMessage($message, $key);
		}
	}

	/**
	 * @param $message
	 * @param $key
	 */
	public function loadMessage($message, $key) {
		if(is_array($message)) {
			foreach($message as $key => $msg) $this->loadMessage($msg, $key);
		} elseif(is_string($message)) {
			$this->messages[strtolower($key)] = self::translateColors($message);
		}
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
	 * @return Config
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param string $key
	 * @param array $args
	 *
	 * @return string
	 */
	public function getMessage($key, array $args = []) {
		return self::translateColors(self::translateArguments($this->messages[strtolower($key)], $args));
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

	/**
	 * @param $message
	 * @param array $args
	 *
	 * @return mixed
	 */
	public static function translateArguments($message, $args = []) {
		foreach($args as $key => $data) {
			$message = str_replace("{args" . (string)((int)$key + 1) . "}", $data, $message);
		}
		return $message;
	}

	/**
	 * @param Item $item
	 * @param int  $slot
	 * @return CompoundTag
	 */
	public static function putItem(Item $item, $slot = null, $tagName = null){
		$tag = new CompoundTag($tagName, [
			"id" => new ShortTag("id", $item->getId()),
			"Count" => new ByteTag("Count", $item->getCount()),
			"Damage" => new ShortTag("Damage", $item->getDamage())
		]);

		if($slot !== null){
			$tag->Slot = new ByteTag("Slot", (int) $slot);
		}

		if($item->hasCompoundTag()){
			$tag->tag = clone $item->getNamedTag();
			$tag->tag->setName("tag");
		}

		return $tag;
	}

}