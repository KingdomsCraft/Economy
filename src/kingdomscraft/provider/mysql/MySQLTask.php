<?php

/**
 * MySQLTask.php Class
 *
 * Created on 13/06/2016 at 7:31 PM
 *
 * @author Jack
 */

namespace kingdomscraft\provider\mysql;

use pocketmine\scheduler\AsyncTask;

abstract class MySQLTask extends AsyncTask {
	
	/** @var MySQLCredentials */
	private $credentials;

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

}