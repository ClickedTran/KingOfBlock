<?php

declare(strict_types=1);

namespace NoobMCBG\KingOfBlock;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\block\BlockTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\math\Vector3;
use NoobMCBG\KingOfBlock\KingOfBlock;

class EventListener implements Listener {

	public KingOfBlock $plugin;

	public function __construct(KingOfBlock $plugin){
		$this->plugin = $plugin;
	}

	public function onBreak(BlockBreakEvent $ev){
  	 $player = $ev->getPlayer();
	 $block = $ev->getBlock();
		
         if($ev->isCancelled()){return;}
          if($player->isCreative()){return;}else{
            if($this->plugin->getMode()->get($player->getName()) == "on"){
		    
               $rand = $player->getInventory()->getItemInHand()->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(18)) + 1;
               $rand = $rand > 1000 ? 1000 : $rand;
               $xyz = new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ());
               $blocks = [
                0 => ["block" => BlockTypeIds::DIAMOND_ORE, "random" => $rand, "ore" => "diamond_block"],
                1 => ["block" => BlockTypeIds::GOLD_ORE, "random" => $rand, "ore" => "gold_block"],
                2 => ["block" => BlockTypeIds::IRON_ORE, "random" => $rand, "ore" => "iron_block"],
                3 => ["block" => BlockTypeIds::LAPIS_LAZULI_ORE, "random" => $rand, "ore" => "lapis_lazuli_block"],
                4 => ["block" => BlockTypeIds::COAL_ORE, "random" => $rand, "ore" => "coal_block"],
                5 => ["block" => BlockTypeIds::EMERALD_ORE, "random" => $rand, "ore" => "emerald_block"],
                6 => ["block" => BlockTypeIds::REDSTONE_ORE, "random" => $rand, "ore" => "redstone_block"]
                ];
                foreach($blocks as $slot => $blockData){
                  $blockOre = $blockData["block"];
                  $random = $blockData["random"];
                  $ore = $blockData["ore"];
                  if($block->getTypeId() == $blockOre){
                     $ev->setDrops([]);
                     $item = StringToItemParser::getInstance()->parse($ore);
                     if($player->getInventory()->canAddItem($item)){
                        $player->getInventory()->addItem($item);
                     }else{
                        $player->getPosition()->getWorld()->dropItem($xyz, $item);
                     }
                  }
		}
	    }		
	  }
    }
    
    public function onJoin(PlayerJoinEvent $event) : void{
      $player = $event->getPlayer();
      $mode = $this->plugin->getMode();
      if(!$mode->exists($player->getName())){
          $mode->set($player->getName(), "off");
          $mode->save();
      }else{
         $mode->set($player->getName(), "off");
         $mode->save();
      }
   }

   public function onQuit(PlayerQuitEvent $event) : void
   {
     $player = $event->getPlayer();
     $mode = $this->plugin->getMode();
     if($mode->get($player->getName()) === "on"){
	$mode->set($player->getName(), "off");
	$mode->save();
      }
   }	
}
