<?php

/**
 * AccountManager.php Class
 *
 * Created on 13/06/2016 at 7:37 PM
 *
 * @author Jack
 */


namespace kingdomscraft\account;

use kingdomscraft\Main;
use pocketmine\Player;

class AccountManager {

	/** @var Main */
	private $plugin;
	
	/** @var AccountInfo[] */
	protected $accounts = [];
	
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return Main
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * @param Player $player
	 */
	public function loadAccount(Player $player) {
		$this->getPlugin()->getProvider()->loadAccount($player->getName());
	}

	/**
	 * @param Player $player
	 */
	public function updateAccount(Player $player) {
		$account = $this->getAccountInfo($player);
		if($account instanceof AccountInfo) {
			$this->getPlugin()->getProvider()->updateAccount($account->serialize(), $player->getName());
		}
	}

	/**
	 * @param string $player
	 */
	public function deleteAccount($player) {
		$account = $this->getAccountInfo($player);
		if($account instanceof AccountInfo) {
			$this->getPlugin()->getProvider()->updateAccount($account->serialize(), $player);
		}
	}

	/**
	 * @param $player
	 * 
	 * @return AccountInfo
	 */
	public function getAccountInfo($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}
		return $this->accounts[$player];
	}

	/**
	 * @param Player $player
	 */
	public function closeAccountInfo(Player $player) {
		$account = $this->getAccountInfo($player);
		if($account instanceof AccountInfo) {
			$account->close();
			unset($this->accounts[$player->getName()]);
		}
	}

}