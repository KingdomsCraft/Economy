<?php

/**
 * MySQLProvider.php Class
 *
 * Created on 13/06/2016 at 6:19 PM
 *
 * @author Jack
 */

namespace kingdomscraft\provider\mysql;

use kingdomscraft\provider\DummyProvider;
use pocketmine\utils\PluginException;
use pocketmine\utils\TextFormat;

/**
 * MySQLProvider class
 */
class MySQLProvider extends DummyProvider {

	/** @var MySQLCredentials */
	protected $credentials;

	public function init() {
		$this->credentials = MySQLCredentials::fromArray($this->getPlugin()->settings["database"]);
		$mysqli = $this->credentials->getMysqli();
		if($mysqli->connect_error) {
			$mysqli->close();
			throw new PluginException(TextFormat::RED . "Couldn't connect to database! Error: {$mysqli->connect_error}");
		}
		$mysqli->query("CREATE TABLE IF NOT EXISTS kingdomscraft_economy (
				username VARCHAR(64) PRIMARY KEY DEFAULT '',
				level INT DEFAULT 1,
				xp INT DEFAULT 0,
				gold INT DEFAULT 0,
				rubies INT DEFAULT 0)");
		if(isset($mysqli->error) and $mysqli->error) {
			throw new \RuntimeException($mysqli->error);
		}
		$mysqli->close();
	}

	/**
	 * @return MySQLCredentials
	 */
	public function getCredentials() {
		return $this->credentials;
	}
	
	public function loadAccount($name) {
		
	}
	
	public function updateAccount($info, $name) {
		
	}
	
	public function deleteAccount($name) {
		
	}

}