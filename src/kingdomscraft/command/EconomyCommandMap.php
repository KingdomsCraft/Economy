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

namespace kingdomscraft\command;

use kingdomscraft\command\commands\SetGoldCommand;
use kingdomscraft\command\commands\VaultCommand;
use kingdomscraft\Main;

class EconomyCommandMap {

	/** @var Main */
	private $plugin;

	/** @var EconomyCommand[] */
	protected $commands = [];

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->setDefaultCommands();
	}

	/**
	 * @return Main
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * Set the default commands
	 */
	public function setDefaultCommands() {
		$this->registerAll([
			new VaultCommand($this->plugin),
			new SetGoldCommand(($this->plugin)),
		]);
	}

	/**
	 * Register an array of commands
	 *
	 * @param array $commands
	 */
	public function registerAll(array $commands) {
		foreach($commands as $command) {
			$this->register($command);
		}
	}

	/**
	 * Register a command
	 *
	 * @param EconomyCommand $command
	 * @param string $fallbackPrefix
	 *
	 * @return bool
	 */
	public function register(EconomyCommand $command, $fallbackPrefix = "kc") {
		if($command instanceof EconomyCommand) {
			$this->plugin->getServer()->getCommandMap()->register($fallbackPrefix, $command);
			$this->commands[strtolower($command->getName())] = $command;
		}
		return false;
	}

	/**
	 * Unregisters all commands
	 */
	public function clearCommands() {
		foreach($this->commands as $command) {
			$this->unregister($command);
		}
		$this->commands = [];
		$this->setDefaultCommands();
	}

	/**
	 * Unregister a command
	 *
	 * @param EconomyCommand $command
	 */
	public function unregister(EconomyCommand $command) {
		$command->unregister($this->plugin->getServer()->getCommandMap());
		unset($this->commands[strtolower($command->getName())]);
	}

	/**
	 * Get a command
	 *
	 * @param $name
	 *
	 * @return EconomyCommand|null
	 */
	public function getCommand($name) {
		if(isset($this->commands[$name])) {
			return $this->commands[$name];
		}
		return null;
	}

	/**
	 * @return EconomyCommand[]
	 */
	public function getCommands() {
		return $this->commands;
	}

	public function __destruct() {
		$this->close();
	}

	public function close() {
		foreach($this->commands as $command) {
			$this->unregister($command);
		}
		unset($this->commands, $this->plugin);
	}

}