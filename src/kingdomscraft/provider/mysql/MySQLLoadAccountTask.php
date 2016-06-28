<?php

/**
 * MySQLLoadAccountTask.php Class
 *
 * Created on 13/06/2016 at 7:51 PM
 *
 * @author Jack
 */


namespace kingdomscraft\provider\mysql;

use pocketmine\Server;

class MySQLLoadAccountTask extends MySQLTask {

	/** @var string */
	protected $username;

	const NO_DATA = "error.no.data";
	const DATA_WRONG_FORMAT = "error.wrong.format";

	/**
	 * MySQLLoadAccountTask constructor
	 *
	 * @param MySQLProvider $provider
	 * @param $username
	 */
	public function __construct(MySQLProvider $provider, $username) {
		parent::__construct($provider->getCredentials());
		$this->username = strtolower($username);
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$result = $mysqli->query("SELECT * FROM kingdomscraft_economy WHERE username = {$mysqli->escape_string($this->username)}");
		if($result instanceof \mysqli_result) {
			$row = $result->fetch_assoc();
			$result->free();
			$mysqli->close();
			if(is_array($row)) {
				$this->setResult($row);
				return;
			} else {
				$this->setResult(self::DATA_WRONG_FORMAT);
				return;
			}
		} else {
			$this->setResult(self::NO_DATA);
			return;
		}
	}

	public function onCompletion(Server $server) {
		$result = $this->getResult();
		if($result === self::NO_DATA) {
			// need to register
		} elseif($result === self::DATA_WRONG_FORMAT) {
			// mysql error?
		} else {
			// load account
		}
	}

}