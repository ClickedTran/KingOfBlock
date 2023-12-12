<?php

declare(strict_types=1);

namespace NoobMCBG\KingOfBlock;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use ClickedTran\libRanks\libRanks;

use NoobMCBG\KingOfBlock\task\TimeTask;
use NoobMCBG\KingOfBlock\commands\{KingOfBlockCommands, KingOfBlockShopCommands};

class KingOfBlock extends PluginBase implements Listener {

	public static $instance;
	public $rankProvider, $economyProvider, $time, $mode;

	public static function getInstance() : self {
		return self::$instance;
	}

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		libRanks::init();
		$this->rankProvider = libRanks::getProvider($this->getConfig()->get("rank"));
		
        libPiggyEconomy::init();
        $this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
		$this->time = new Config($this->getDataFolder() . "time.yml", Config::YAML);
        
        $this->mode = new Config($this->getDataFolder() . "mode.yml", Config::YAML);
		
		$this->getServer()->getCommandMap()->register("/kingofblock", new KingOfBlockCommands($this));
        $this->getServer()->getCommandMap()->register("/kingofblockshop", new KingOfBlockShopCommands($this));
		$this->getScheduler()->scheduleRepeatingTask(new TimeTask($this), 20 * 60);
		self::$instance = $this;
	}

	public function getTime()
  {
		return $this->time;
	}
    
  public function getMode()
  {
    return $this->mode;
  }

	public function getRankProvider()
  {
	  return $this->rankProvider;
	}
    
  public function getEconomyProvider()
  {
    return $this->economyProvider;
  }

	public function onDisable() : void {
        $this->getTime()->save();
     
	}
}
