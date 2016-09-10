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
use kingdomscraft\tile\RubyShopSign;
use kingdomscraft\tile\ShopSign;
use pocketmine\block\Block;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Tile;

class EconomyListener implements Listener {

	/** @var Economy */
	private $economy;

	public function __construct(Economy $economy) {
		$this->economy = $economy;
		$plugin = $economy->getPlugin();
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @return Main
	 */
	public function getPlugin() {
		return $this->economy->getPlugin();
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
	 * @param SignChangeEvent $event
	 *
	 * @ignoreCancelled true
	 */
	public function onSignChange(SignChangeEvent $event) {
		$lines = $event->getLines();
		if(in_array(strtolower($lines[0]), $this->getPlugin()->getSettings()->getNested("shop.identifiers", ["shop"]))) {
			$event->setCancelled(true);
			$player = $event->getPlayer();
			$block = $event->getBlock();
			$level = $block->getLevel();
			$pos = new Vector3($block->x, $block->y, $block->z);
			$typeData = explode(" ", $lines[1]);
			if(in_array(strtolower($typeData[0]), ["b", "s"])) {
				$type = ($typeData[0] === "b" ? ShopSign::TYPE_BUY : ShopSign::TYPE_SELL);
				$price = (int)$typeData[1];
				if(is_int($price)) {
					$metaData = explode(":", $lines[2]);
					$countData = explode(" ", $lines[2]);
					$name = (isset($metaData[1]) ? $metaData[0] : $countData[0]);
					$meta = (isset($metaData[1]) ? $metaData[1] : 0);
					$amount = (int)(isset($countData[1]) ? $countData[1] : 1);
					$item = Item::get($name, $meta, $amount);
					if(strtolower($name) == "rubies") {
						$formats = [];
						foreach($this->getPlugin()->getSettings()->getNested("shop.format") as $key => $format) {
							$formats[$key] = Main::translateColors(str_replace(["{type}", "{price}", "{amount}", "{item}"], [$type, $price, $amount, "Rubies"], $format));
						}
						$level->setBlock($pos, $block);
						$nbt = new CompoundTag("", [
							"id" => new StringTag("id", Main::RUBY_SHOP_SIGN),
							"x" => new IntTag("x", $pos->x),
							"y" => new IntTag("y", $pos->y),
							"z" => new IntTag("z", $pos->z),
							"Text1" => new StringTag("Text1", $formats[1]),
							"Text2" => new StringTag("Text2", $formats[2]),
							"Text3" => new StringTag("Text3", $formats[3]),
							"Text4" => new StringTag("Text4", $formats[4]),
							"ShopData" => new CompoundTag("ShopData", [
								"Type" => new StringTag("Type", $type),
								"Price" => new IntTag("Price", $price),
								"Amount" => new IntTag("Amount", $amount)
							])
						]);
						Tile::createTile(Main::RUBY_SHOP_SIGN, $level->getChunk($pos->x >> 4, $pos->z >> 4), $nbt);
					} elseif($item instanceof Item) {
						$formats = [];
						foreach($this->getPlugin()->getSettings()->getNested("shop.format") as $key => $format) {
							$formats[$key] = Main::translateColors(str_replace(["{type}", "{price}", "{amount}", "{item}"], [$type, $price, $item->getCount(), "{$item->getName()}" . ($item->getDamage() == 0 ? "" : ":{$item->getDamage()}")], $format));
						}
						$level->setBlock($pos, $block);
						$nbt = new CompoundTag("", [
							"id" => new StringTag("id", Main::SHOP_SIGN),
							"x" => new IntTag("x", $pos->x),
							"y" => new IntTag("y", $pos->y),
							"z" => new IntTag("z", $pos->z),
							"Text1" => new StringTag("Text1", $formats[1]),
							"Text2" => new StringTag("Text2", $formats[2]),
							"Text3" => new StringTag("Text3", $formats[3]),
							"Text4" => new StringTag("Text4", $formats[4]),
							"ShopData" => new CompoundTag("ShopData", [
								"Type" => new StringTag("Type", $type),
								"Price" => new IntTag("Price", $price),
								"Item" => Main::putItem($item, null, "Item")
							])
						]);
						Tile::createTile(Main::SHOP_SIGN, $level->getChunk($pos->x >> 4, $pos->z >> 4), $nbt);
					} else {
						$level->setBlock($pos, Block::get(Block::AIR));
						$level->dropItem($pos, Item::get(Item::SIGN));
						$player->sendMessage($this->getPlugin()->getMessage("unknown-item", [$name]));
						return;
					}
				} else {
					$level->setBlock($pos, Block::get(Block::AIR));
					$level->dropItem($pos, Item::get(Item::SIGN));
					$player->sendMessage($this->getPlugin()->getMessage("undefined-price"));
					return;
				}
			} else {
				$level->setBlock($pos, Block::get(Block::AIR));
				$level->dropItem($pos, Item::get(Item::SIGN));
				$player->sendMessage($this->getPlugin()->getMessage("undefined-type"));
				return;
			}
		}
	}

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @ignoreCancelled true
	 */
	public function onPlayerInteract(PlayerInteractEvent $event) {
		$block = $event->getBlock();
		if($block->getId() === Block::SIGN_POST or Block::WALL_SIGN) {
			$player = $event->getPlayer();
			$level = $block->getLevel();
			$pos = new Vector3($block->x, $block->y, $block->z);
			$tile = $level->getTile($pos);
			if($tile instanceof ShopSign) {
				$price = $tile->getPrice();
				$inv = $player->getInventory();
				$item = $tile->getItem();
				if($tile->getType() === ShopSign::TYPE_BUY) {
					if($this->economy->getGold($player) >= $price) {
						if($inv->canAddItem($item)) {
							$this->economy->removeGold($player, $price);
							$inv->addItem($item);
							$player->sendMessage($this->getPlugin()->getMessage("buy-success", [$item->getName() . ($item->getDamage() == 0 ? "" : $item->getDamage()), $item->getCount(), $price]));
						} else  {
							$player->sendMessage($this->getPlugin()->getMessage("inventory-full"));
						}
					} else {
						$player->sendMessage($this->getPlugin()->getMessage("cannot-afford"));
					}
				} elseif($tile->getType() === ShopSign::TYPE_SELL) {
					if($inv->contains($item)) {
						$this->economy->addGold($player, $price);
						$inv->removeItem($item);
						$player->sendMessage($this->getPlugin()->getMessage("sell-success", [$item->getName() . ($item->getDamage() == 0 ? "" : $item->getDamage()), $item->getCount(), $price]));
					} else {
						$player->sendMessage($this->getPlugin()->getMessage("no-items"));
					}
				} else {
					$player->sendMessage($this->getPlugin()->getMessage("unknown-error"));
				}
			} elseif($tile instanceof RubyShopSign) {
				$price = $tile->getPrice();
				$amount = $tile->getAmount();
				if($tile->getType() === ShopSign::TYPE_BUY) {
					if($this->economy->getGold($player) >= $price) {
						$this->economy->removeGold($player, $price);
						$this->economy->addRubies($player, $amount);
						$player->sendMessage($this->getPlugin()->getMessage("buy-success", ["Rubies", $amount, $price]));

					} else {
						$player->sendMessage($this->getPlugin()->getMessage("cannot-afford"));
					}
				} elseif($tile->getType() === ShopSign::TYPE_SELL) {
					if($this->economy->getRubies($player) >= $amount) {
						$this->economy->addGold($player, $price);
						$this->economy->removeRubies($player, $amount);
						$player->sendMessage($this->getPlugin()->getMessage("sell-success", ["Rubies", $amount, $price]));
					} else {
						$player->sendMessage($this->getPlugin()->getMessage("no-items"));
					}
				} else {
					$player->sendMessage($this->getPlugin()->getMessage("unknown-error"));
				}
			}
		}
	}

	/**
	 * @param PlayerDeathEvent $event
	 */
	public function onPlayerDeath(PlayerDeathEvent $event) {
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

}