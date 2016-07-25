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

namespace kingdomscraft\economy\provider\mysql\task;

use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\provider\mysql\MySQLTask;
use pocketmine\Server;

class SetRubiesTask extends MySQLTask{

	public function __construct(MySQLEconomyProvider $provider) {
		parent::__construct($provider->getCredentials());
	}

	public function onRun() {

	}

	public function onCompletion(Server $server) {

	}

}