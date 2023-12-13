<?php

declare(strict_types=1);

namespace NoobMCBG\KingOfBlock\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use NoobMCBG\KingOfBlock\KingOfBlock;

class TimeTask extends Task {
	public KingOfBlock $plugin;

	public function __construct(KingOfBlock $plugin){
		$this->plugin = $plugin;
	}

	public function onRun() : void {
	  $ranks = $this->plugin->getRankProvider();
	  $all_time = $this->plugin->getTime();
          $mode = $this->plugin->getMode();
	  if(count($all_time->getAll()) >= 1){
	     foreach($all_time->getAll() as $player => $time){
	         if($mode->get($player) == "on"){
		    if($all_time->get($player) == 0){
		       $all_time->remove($player);
                       $mode->set($player, "off");
                       $mode->save();                  
		       if($this->plugin->getServer()->getPlayerByPrefix($player) !== null){
		          $this->plugin->getServer()->getPlayerByPrefix($player)->sendMessage("§9[§b KINGOFBLOCK §9] §c Your usage period has ended!");
                          $ranks->unsetPlayerPermission($this->plugin->getServer()->getPlayerByPrefix($player), "kingofblock.command.use");
                          break;
                       }
		     $ranks->unsetPlayerPermission($this->plugin->getServer()->getPlayerByPrefix($player), "kingofblock.command.use");
                     continue;
		  }
                  if($this->plugin->getServer()->getPlayerByPrefix($player) !== null){
	             $this->plugin->getServer()->getPlayerByPrefix($player)->sendMessage("§9[§b KINGOFBLOCK §9]§a Your remaining usage time is §c$time §aminutes !");
		  }
		  $all_time->set($player, $time - 1);
                  $all_time->save();
	       }
	     }
	}
     }
}
