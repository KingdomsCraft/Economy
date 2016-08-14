<?php


namespace kingdomscraft\command\tasks;


use kingdomscraft\economy\provider\mysql\MySQLEconomyProvider;
use kingdomscraft\economy\provider\mysql\task\LoadTask;
use kingdomscraft\Main;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\PluginException;

class CheckLevelCommandTask extends LoadTask {

	/** @var string */
	protected $sender;

	/**
	 * ViewVaultCommandTask constructor
	 *
	 * @param MySQLEconomyProvider $provider
	 * @param $name
	 * @param $sender
	 */
	public function __construct(MySQLEconomyProvider $provider, $name, $sender) {
		parent::__construct($provider, $name);
		$this->sender = $sender;
	}

	/**
	 * @param Server $server
	 */
	public function onCompletion(Server $server) {
		$plugin = $server->getPluginManager()->getPlugin("Economy");
		if($plugin instanceof Main and $plugin->isEnabled()) {
			$result = $this->getResult();
			$notify = false;
			$sender = $server->getPlayerExact($this->sender);
			if($sender instanceof Player) {
				$notify = true;
			}
			switch((is_array($result) ? $result[0] : $result)) {
				case self::SUCCESS:
					if($notify) {
						if(strtolower($this->sender) === $this->name) {
							$sender->sendMessage($plugin->getMessage("command.vault-self", [$result[1]["gold"], $result[1]["rubies"]]));
						} else {
							$sender->sendMessage($plugin->getMessage("command.vault-other", [$this->name, $result[1]["gold"], $result[1]["rubies"]]));
						}
					}
					$plugin->getLogger()->debug("Successfully completed CheckGold on kingdomscraft_economy database for {$this->name}");
					return;
				case self::CONNECTION_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("command.db-connection-error"));
					$plugin->getLogger()->critical("Couldn't connect to kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("Connection error while executing CheckGold on kingdomscraft_economy database for {$this->name}");
					return;
				case self::MYSQLI_ERROR:
					if($notify) $sender->sendMessage($plugin->getMessage("command.error"));
					$plugin->getLogger()->error("MySQL error while querying kingdomscraft_database! Error: {$result[1]}");
					$plugin->getLogger()->debug("MySQL error while executing CheckGold on kingdomscraft_economy database for {$this->name}");
					return;
				case self::NO_DATA:
					if($notify) $sender->sendMessage($plugin->getMessage("command.no-data", [$this->name]));
					$plugin->getLogger()->debug("Couldn't find economy data on kingdomscraft_database for {$this->name}");
					return;
			}
		} else {
			throw new PluginException("Attempted to execute CheckGoldCommandTask while Economy plugin isn't loaded!");
		}
	}

}