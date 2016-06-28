<?php

/**
 * Economy.php Class
 *
 * Created on 12/06/2016 at 3:13 PM
 *
 * @author Jack
 */

namespace kingdomscraft\economy;

use kingdomscraft\Main;

class Economy {

	/** @var Economy */
	private static $instance;

	/** @var Main */
	private $plugin;

	/**
	 * @param Main $plugin
	 */
	public static function enable(Main $plugin) {
		assert(self::$instance instanceof Economy, "Economy is already enabled!");
		self::$instance = new self($plugin);
	}

	/**
	 * @return Economy
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * Economy constructor
	 *
	 * @param Main $plugin
	 */
	private function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

}