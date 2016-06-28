<?php

/**
 * DummyProvider.php Class
 *
 * Created on 13/06/2016 at 6:14 PM
 *
 * @author Jack
 */

namespace kingdomscraft\provider;

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
	 * Loads a players save from the database
	 * 
	 * @param string $name
	 */
	public abstract function loadAccount($name);

	/**
	 * Updates a players save in the database
	 * 
	 * @param string $info
	 * @param string $name
	 */
	public abstract function updateAccount($info, $name);

	/**
	 * Deletes a players save from the database
	 * 
	 * @param string $name
	 */
	public abstract function deleteAccount($name);

}