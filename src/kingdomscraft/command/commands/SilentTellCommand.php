<?php


namespace kingdomscraft\command\commands;


use kingdomscraft\command\EconomyCommand;
use kingdomscraft\Main;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class SilentTellCommand extends EconomyCommand{

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "silenttell", "Send a message to a player without telling them who sent it", "/silenttell {player} {message}", ["stell"]);
		$this->setPermission("economy.command.silenttell");
	}

	public function run(CommandSender $sender, array $args) {
		if(isset($args[1])) {
			$name = strtolower(array_shift($args));
			$player = $this->getPlugin()->getServer()->getPlayer($name);
			if($player instanceof Player) {
				$player->sendMessage(Main::translateColors(implode(" ", $args)));
				return true;
			} else {
				$sender->sendMessage($this->getPlugin()->getMessage("player-offline", [$player]));
				return true;
			}
		} else {
			$sender->sendMessage($this->getPlugin()->getMessage("usage", [$this->getUsage()]));
			return true;
		}
	}

}