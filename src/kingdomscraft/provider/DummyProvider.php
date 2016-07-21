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
use kingdomscraft\Main;

/**
 * DummyProvider class
 */
abstract class DummyProvider implements Provider {

	/** @var Main */
	private $plugin;

	/**
	 * DummyProvider constructor
	 * 
	 * @param Main $plugin
	 */
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
	 * Executed when the class is constructed
	 */
	public abstract function init();

	/**
	 * Registers a player to the database
	 *
	 * @param string $name
	 * @param AccountInfo $info
	 */
	public abstract function register($name, AccountInfo $info);

	/**
	 * Loads a players save from the database
	 * 
	 * @param string $name
	 */
	public abstract function load($name);

	/**
	 * Updates a players data in the database
	 * 
	 * @param string $name
	 * @param AccountInfo $info
	 */
	public abstract function update($name, AccountInfo $info);

	/**
	 * Deletes a players save from the database
	 * 
	 * @param string $name
	 */
	public abstract function delete($name);

}