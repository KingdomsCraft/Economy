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

use pocketmine\scheduler\PluginTask;

class EconomyUpdateTask extends PluginTask {

	/** @var Economy */
	private $economy;

	public function __construct(Economy $economy) {
		parent::__construct($economy->getPlugin());
		$this->economy = $economy;
		$this->setHandler($economy->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20 * $economy->getPlugin()->settings->getNested("basic.save-interval", 120)));
	}

	/**
	 * @return Economy
	 */
	public function getEconomy() {
		return $this->economy;
	}

	/**
	 * Update all the things!
	 * 
	 * @param $currentTick
	 */
	public function onRun($currentTick) {
		foreach($this->getOwner()->getServer()->getOnlinePlayers() as $p) {
			$info = $this->economy->getInfo($p);
			if($info instanceof AccountInfo) {
				$this->economy->getProvider()->update($p->getName(), clone $info);
			}
		}
	}

}