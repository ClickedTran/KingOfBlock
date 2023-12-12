<?php

declare(strict_types=1);

namespace NoobMCBG\KingOfBlock\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use NoobMCBG\KingOfBlock\KingOfBlock;

class TimeTask extends Task {

	public function __construct(KingOfBlock $plugin){
		$this->plugin = $plugin;
	}

	public function onRun() : void {
	  $ranks = $this->plugin->getRankProvider();
	  $all_time = $this->plugin->getTime();
          $mode = $this->plugin->getMode();
	  if(count($all_time->getAll()) >= 1){
	     foreach($all_time->getAll() as $player => $time){
		 if($time == 0){
		    $all_time->remove($player);
                    $mode->set($player, "off");
                    $mode->save();                  
		    if($this->plugin->getServer()->getPlayerByPrefix($player) !== null){
		       $this->plugin->getServer()->getPlayerByPrefix($player)->sendMessage("§9[§b KingOfBlock §9] §e Your usage period has ended!");
                       $ranks->unsetPlayerPermission($this->plugin->getServer()->getPlayerByPrefix($player), "kingofblock.command.use");
                       break;
                    }		          
                    continue;
		 }
                 if($this->plugin->getServer()->getPlayerByPrefix($player) !== null){
	            $this->plugin->getServer()->getPlayerByPrefix($player)->sendMessage("§9[§b KingOfBlock §9]§a Your remaining usage time is §8$time §aminutes !");
		 }
		 $all_time->set($player, $time - 1);
                 $all_time->save();
	     }		
	}
     }
}
