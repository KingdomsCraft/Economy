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

use pocketmine\scheduler\AsyncTask;

abstract class MySQLTask extends AsyncTask {

	/** @var MySQLCredentials */
	private $credentials;

	/** States */
	const CONNECTION_ERROR = "state.connection.error";
	const MYSQLI_ERROR = "state.mysqli.error";
	const NO_DATA = "state.no.data";
	const WRONG_FORMAT = "state.wrong.format";
	const SUCCESS = "state.success";

	/**
	 * MySQLTask constructor
	 * 
	 * @param MySQLCredentials $credentials
	 */
	public function __construct(MySQLCredentials $credentials) {
		$this->credentials = $credentials;
	}

	/**
	 * @return \mysqli
	 */
	public function getMysqli() {
		return $this->credentials->getMysqli();
	}

	/**
	 * @param \mysqli $mysqli
	 *
	 * @return bool
	 */
	public function checkConnection(\mysqli $mysqli) {
		if($mysqli->connect_error) {
			$this->setResult([self::CONNECTION_ERROR, $mysqli->connect_error]);
			return true;
		}
		return false;
	}

	/**
	 * @param \mysqli $mysqli
	 *
	 * @return bool
	 */
	public function checkError(\mysqli $mysqli) {
		if($mysqli->error) {
			$this->setResult([self::MYSQLI_ERROR, $mysqli->error]);
			return true;
		}
		return false;
	}

}