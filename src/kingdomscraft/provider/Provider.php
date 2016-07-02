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

namespace kingdomscraft\provider;

use kingdomscraft\economy\AccountInfo;

interface Provider {

	public function init();

	public function register($name, AccountInfo $info);

	public function load($name);

	public function display($who, $to);

	public function update($name, AccountInfo $info);
	
	public function delete($name);

}