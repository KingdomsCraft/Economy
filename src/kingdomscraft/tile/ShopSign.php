<?php

namespace kingdomscraft\tile;

use kingdomscraft\Main;
use pocketmine\item\Item;
use pocketmine\level\format\FullChunk;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Sign;

class ShopSign extends Sign {

	/** Shop types */
	const TYPE_BUY = "Buy";
	const TYPE_SELL = "Sell";

	public function __construct(FullChunk $chunk, CompoundTag $nbt) {
		parent::__construct($chunk, $nbt);
		if(!isset($nbt->ShopData)) {
			$this->namedtag->ShopData = new CompoundTag("ShopData", [
				"Type" => new StringTag("Type", self::TYPE_BUY),
				"Price" => new IntTag("Price", 0),
				"Item" => Main::putItem(Item::get(Item::AIR), null, "Item")
			]);
		}
		if(!isset($nbt->ShopData->Type)) {
			$this->namedtag->ShopData->Type = new StringTag("Type", self::TYPE_BUY);
		}
		if(!isset($nbt->ShopData->Price)) {
			$this->namedtag->ShopData->Price = new IntTag("Price", 0);
		}
		if(!isset($nbt->ShopData->Item)) {
			$this->namedtag->ShopData->Item = Main::putItem(Item::get(Item::AIR), null, "Item");
		}
	}

	public function getShopData() {
		return $this->namedtag["ShopData"];
	}

	public function setShopData(CompoundTag $data) {
		$this->namedtag->ShopData = $data;
	}

	public function getType() {
		return $this->namedtag->ShopData["Type"];
	}

	public function setType($type) {
		$this->namedtag->ShopData->Type->setValue($type);
	}

	public function getPrice() {
		return $this->namedtag->ShopData["Price"];
	}

	public function setPrice($price) {
		$this->namedtag->ShopData->Price->setValue($price);
	}

	public function getItem() {
		return NBT::getItemHelper($this->namedtag->ShopData->Item);
	}

	public function setItem(Item $item) {
		$this->namedtag->ShopData->Item->setValue(Main::putItem($item, null, "Item"));
	}

}