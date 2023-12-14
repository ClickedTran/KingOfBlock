<?php
namespace NoobMCBG\KingOfBlock\commands;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;

use jojoe77777\FormAPI\{CustomForm, SimpleForm};

use NoobMCBG\KingOfBlock\KingOfBlock;

class KingOfBlockShopCommands extends Command implements PluginOwned
{
   private KingOfBlock $plugin;
   private $playerList = [];
   public function __construct(KingOfBlock $plugin)
   {
     $this->plugin = $plugin;
     parent::__construct("kingofblockshop", "Open KingOfBlock Shop Menu", null, ["kobs"]);
       $this->setPermission("kingofblock.command");
       
   }
    
    public function execute(CommandSender $sender, String $label, Array $args)
    {
       if(!$sender instanceof Player){
          $sender->sendMessage("§cPlease use command in-game!");
          return;
       }
       $this->menuShop($sender);
        
    }
    
    public function getOwningPlugin() : KingOfBlock
    {
        return $this->plugin;
    }
    
    public function menuShop(Player $player)
    {
        $form = new SimpleForm(function(Player $player, $data) : void{
           if($data === null){return;}
           switch($data){
              case 0:
              break;
              case 1:
                 if(!$this->plugin->getTime()->exists($player->getName())){
                     $this->buyTime($player);
                 }else{
                     $this->extendTime($player);
                 }
              break;
              case 2:
                   $this->giveTime($player);
              break;
           }
        });
        $form->setTitle("§l§0( §r§eKINGOFBLOCKSHOP §r§f| §l§8MENU§0 )");
        if(!$this->plugin->getTime()->exists($player->getName())){
            $form->setContent("§eHello §b".$player->getName().",\n§eWelcome to KINGOFBLOCK SHOP\n\n§eChoose the button you want!");
        }else{
            $form->setContent("§eHello §b".$player->getName().",\n§eYour remaining time to use KingOfBlock is: §9".$this->plugin->getTime()->get($player->getName())."§e minute\n\n§eChoose the button you want!");
        }
        $form->addButton("§l§8EXIT\n§r§7»»§l§7 Click To Exit");
        if(!$this->plugin->getTime()->exists($player->getName())){
            $form->addButton("§l§8Buy Time Use\n§r§7»» §l§7Click To Open");
        }else{
            $form->addButton("§l§8Extend Usage Time\n§r§7»» §l§7Click To Open");
        }
        $form->addButton("§l§8Buy Time For Player\n§r§7»» §l§7Click To Open");
        $player->sendForm($form);
    }
    
    public function buyTime(Player $player) : void{
        $form = new CustomForm(function(Player $player, $data) : void{
            if($data == null){return;}
            $this->plugin->getEconomyProvider()->getMoney($player, function(int|float $money) use ($player, $data){
            $price = $this->plugin->getConfig()->get("price-buy");
            if($money < $price*$data[0]){
               $player->sendMessage("§9[§b KINGOFBLOCKSHOP§9 ]§r§c You don't have money to buy time use!");
               return;
            }else{
              $this->plugin->getTime()->set($player->getName(), (int)$data[0]);
              $this->plugin->getTime()->save();
              $this->plugin->getRankProvider()->setPlayerPermission($player, "kingofblock.command.use");
              $player->sendMessage("§9[ §bKINGOFBLOCKSHOP §9]§r§e You have purchased §b".$data[0]."§e extra minute of usage time");
             $this->plugin->getEconomyProvider()->takeMoney($player, $price*$data[0]);
            }
         });    
               
        });
        $form->setTitle("§l§0( §r§eKINGOFBLOCKSHOP §r§f| §l§8BUY §0)");
        $form->addSlider("§aScroll to the number of minutes you want to buy", 1, 60, 1);
        $player->sendForm($form);
    }
    
    public function extendTime(Player $player) : void
    {
        $form = new CustomForm(function(Player $player, $data) : void{
          if($data == null){return;}
          $this->plugin->getEconomyProvider()->getMoney($player, function(int|float $money) use ($player, $data){
              $price = $this->plugin->getConfig()->get("price-buy");
              if($money < $price*$data[0]){
                  $player->sendMessage("§9[§b KINGOFBLOCKSHOP §9]§r§c You don't have money to buy more time!");
                  return;
              }else{
                  $this->plugin->getEconomyProvider()->takeMoney($player, $price*$data[0]);
                  $player->sendMessage("§9[§b KINGOFBLOCKSHOP§9 ]§r§e You have purchased additional §b".$data[0]."§e minutes to use KingOfBlock!");
                  $this->plugin->getTime()->set($player->getName(), $this->plugin->getTime()->get($player->getName()) + (int)$data[0]);
                  $this->plugin->getTime()->save();
              }
          });
        });
        $form->setTitle("§l§0( §r§eKINGOFBLOCKSHOP §r§f| §l§8EXTEND TIME§0 )");
        $form->addSlider("§aScroll to the number of minutes you want to extend",1,60,1);
        $player->sendForm($form);
    }
    
    public function giveTime(Player $player) : void
    {
        $list = [];
        foreach($this->plugin->getServer()->getOnlinePlayers() as $players){
            $list[] = $players->getName();
        }
        $this->playerList[$player->getName()] = $list;
        
        $form = new CustomForm(function(Player $player, $data) : void{
            if($data === null){return;}
             $playerName = $this->playerList[$player->getName()][$data[0]];
            $this->plugin->getEconomyProvider()->getMoney($player, function(int|float $money) use ($playerName, $data, $player){
            $price = $this->plugin->getConfig()->get("price-give");
            if($money < $price*$data[1]){
               $player->sendMessage("§9[ §bKINGOFBLOCKSHOP §9]§r§cYou don't have money to buy §b".$data[1]."§c minute!");
                return;
             }else{
                if(!$this->plugin->getTime()->exists($playerName)){
                    $this->plugin->getTime()->set($playerName, (int)$data[1]);
                    $this->plugin->getTime()->save();      
                    $this->plugin->getRankProvider()->setPlayerPermission($this->plugin->getServer()->getPlayerByPrefix($playerName), "kingofblock.command.use");
                    $player->sendMessage("§9[§b KINGOFBLOCKSHOP §9]§r§e You gave §9".$playerName."§e with time §b".$data[1]."§e minute");
                    $this->plugin->getServer()->getPlayerByPrefix($playerName)->sendMessage("§9[§b KINGOFBLOCKSHOP §9]§e You have received §b".$data[1]."§a minutes from §9".$player->getName());
                    $this->plugin->getEconomyProvider()->takeMoney($player, $price*$data[1]);
               }else{
                    $this->plugin->getTime()->set($playerName, $this->plugin->getTime()->get($playerName) + (int)$data[1]);
                    $this->plugin->getTime()->save();
                    $player->sendMessage("§9[§b KINGOFBLOCKSHOP §9]§r§e You have extended §9".$playerName."§e for another §b".$data[1]."§e minutes of use!!");
                    $this->plugin->getServer()->getPlayerByPrefix($playerName)->sendMessage("§9[§b KINGOFBLOCKSHOP §9]§e You have been granted an extension of §b".$data[1]."§e minutes from §9".$player->getName());
                    $this->plugin->getEconomyProvider()->takeMoney($player, $price*$data[1]);
                }
             }
           });
        });
        $form->setTitle("§l§0( §r§eKINGOFBLOCKSHOP §r§f| §l§8GIVE GIFT§0 )");
        $form->addDropdown("§aChoose player", $this->playerList[$player->getName()]);
        $form->addSlider("§aScroll to the number of minutes you want to give a gift",1,60,1);
        $player->sendForm($form);
    }
