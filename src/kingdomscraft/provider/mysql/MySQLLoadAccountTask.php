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