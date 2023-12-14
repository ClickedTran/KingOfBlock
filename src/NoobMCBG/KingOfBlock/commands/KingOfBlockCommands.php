<?php

declare(strict_types=1);

namespace NoobMCBG\KingOfBlock\commands;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use NoobMCBG\KingOfBlock\KingOfBlock;

class KingOfBlockCommands extends Command implements PluginOwned {

	private KingOfBlock $plugin;

	public function __construct(KingOfBlock $plugin){
		$this->plugin = $plugin;
		parent::__construct("kingofblock", "§7KingOfBlock Commands", null, ["kob"]);
		$this->setPermission("kingofblock.command.use");
	}

	public function execute(CommandSender $sender, string $label, array $args){
	  $time = $this->plugin->getTime();
	  $mode = $this->plugin->getMode();
	  $ranks = $this->plugin->getRankProvider();
		 if(!isset($args[0])){
           $sender->sendMessage("§6======§bKINGOFBLOCK HELP§6======");
		   if(Server::getInstance()->isOp($sender->getName())){    
		    $sender->sendMessage("§b/kingofblock <give | take> <player> <time: minute>");
		   }
           if($time->exists($sender->getName())){
              $sender->sendMessage("§b/kingofblock <on | off>");
		   }
           $sender->sendMessage("§6====================");
		 }else{
		   switch($args[0]){
		    case "on":
		   	 if(!$sender instanceof Player){
		        $sender->sendMessage("Please use in-game, please!");
		       return;
	      	}else{
		       if(!$sender->hasPermission("kingofblock.command.use")){
		         $sender->sendMessage("§9[§4 ! §9] §cYou don't have permission to use command!");
		       }else{
		         if(empty($time->get($sender->getName()))){
		           $sender->sendMessage("§9[ §bKINGOFBLOCK §9]§r§c Your data does not exist!");
		         }else{
		          $mode->set($sender->getName(), "on");
		          $mode->save();
		          $sender->sendMessage("§9[§b KINGOFBLOCK §9] §aMode has been enabled!");
		         }
		       }
	      	}
		    break;
		    case "off":
		     if(!$sender instanceof Player){
	           $sender->sendMessage("Please use in-game, please!");
		        return;
	     	}else{
	 	     if(!$sender->hasPermission("kingofblock.command.use")){
		         $sender->sendMessage("§9[§4 ! §9] §cYou don't have permission to use command!");
		     }else{
		        $mode->set($sender->getName(), "off");
		        $mode->save();
		        $sender->sendMessage("§9[§b KINGOFBLOCK §9] §aMode has been disabled!");
		     }
	     	}
		    break;
		    case "give":
		     if(!$sender->hasPermission("kingofblock.command.give")){
		        $sender->sendMessage("§l§cYou don't have permission to use command!");
		     }else{
		      $player = Server::getInstance()->getPlayerByPrefix($args[1]);
		       if(!isset($player)){
		         $sender->sendMessage("§9[§4 ! §9] §cPlayer not found!");
		        return;
		     }else{
		       if(!ctype_digit($args[2]) == 0.1){
		           return;
		       }
		       if(!is_numeric($args[2])){
		          $sender->sendMessage("§9[§4 ! §9] §cTime not number!");
		          return;
		       }
		       if($args[2] <= 0){
		          $sender->sendMessage("§9[§4 ! §9] §cThe number of minutes cannot be less than or equal to 0!");
		          return;
		       }else{
		            $time->set($player->getName(), $time->get($player->getName()) + $args[2]);
		            $time->save();
		            $sender->sendMessage("§9[§b KINGOFBLOCK §9] §a You have give §b".$player->getName()."§c ".$args[2]."§a minutes of use");
		            $player->sendMessage("§9[§b KINGOFBLOCK §9] §a You have received §c".$args[2]."§a minutes of usage from §b".$sender->getName());
		            $ranks->setPlayerPermission($player, "kingofblock.command.use");
		       }
		     }
		   }
	 	   break;
	 	   case "take":
	 	    if(!$sender->hasPermission("kingofblock.command.take")){
	 	        $sender->sendMessage("§l§cYou don't have permission to use command!");
	 	    }else{
		     $player = Server::getInstance()->getPlayerByPrefix($args[1]);
		     if(!isset($player) && !is_null($player)){
		        $sender->sendMessage("§9[§4 ! §9] §cPlayer not found!");
		        return;
		     }else{
		       if(!ctype_digit($args[2]) == 0.1){
		           return;
		       }
		       if(!is_numeric($args[2])){
		          $sender->sendMessage("§9[§4 ! §9] §cTime not number!");
		          return;
		       }
		       if($args[2] < 0){
		          $sender->sendMessage("§9[§4 ! §9] §cThe number of minutes cannot be less than 0!");
		       }else{
		         if($time->get($player->getName()) >= $args[2]){
		            $time->set($player->getName(), $time->get($player->getName()) - $args[2]);
		            $time->save();
		            $sender->sendMessage("§9[§b KINGOFBLOCK §9] §aYou took §c" . $args[2] . " §aminute usage of §b" . $player->getName());
		            $player->sendMessage("§9[§b KINGOFBLOCK §9] §aYou have been deducted §c" . $args[2] . " §aminutes of use by staff §b".$sender->getName()."§a!");
		         }else{
		           $sender->sendMessage("§9[ §bKINGOFBLOCK§9]§r§c The usage time of §b" . $player->getName() . " §c is not enough to take!");
		         }
		       }
		     }
	 	   }
	 	   break;
	   }
	 }
	}

	public function getOwningPlugin() : KingOfBlock {
		return $this->plugin;
	}
}
