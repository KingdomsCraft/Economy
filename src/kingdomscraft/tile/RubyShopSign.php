<?php

namespace kingdomscraft\tile;

use pocketmine\level\format\FullChunk;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Sign;

class RubyShopSign extends Sign {

	/** Shop types */
	const TYPE_BUY = "Buy";
	const TYPE_SELL = "Sell";

	public function __construct(FullChunk $chunk, CompoundTag $nbt) {
		parent::__construct($chunk, $nbt);
		if(!isset($nbt->ShopData)) {
			$nbt->ShopData = new CompoundTag("ShopData", [
				"Type" => new StringTag("Type", self::TYPE_BUY),
				"Price" => new IntTag("Price", 0),
				"Amount" => new IntTag("Amount", 1)
			]);
		}
		if(!isset($nbt->ShopData->Type)) {
			$nbt->ShopData->Type = new StringTag("Type", self::TYPE_BUY);
		}
		if(!isset($nbt->ShopData->Price)) {
			$nbt->ShopData->Price = new IntTag("Price", 0);
		}
		if(!isset($nbt->ShopData->Amount)) {
			$nbt->ShopData->Amount = new IntTag("Amount", 1);
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

	public function getAmount() {
		return $this->namedtag->ShopData["Amount"];
	}

	public function setAmount($amount) {
		$this->namedtag->ShopData->Amount->setValue($amount);
	}

}