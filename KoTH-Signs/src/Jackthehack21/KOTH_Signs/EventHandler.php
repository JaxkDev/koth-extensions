<?php
/*
 *   KOTH-Signs
 *   Copyright (C) 2019 Jackthehack21 (Jack Honour/Jackthehaxk21/JaxkDev)
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 *   Twitter :: @JaxkDev
 *   Discord :: Jackthehaxk21#8860
 *   Email   :: gangnam253@gmail.com
 */

declare(strict_types=1);
namespace Jackthehack21\KOTH_Signs;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign as SignTile;
use pocketmine\utils\TextFormat as C;

class EventHandler implements Listener
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onBlockBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if($block->getName() === "Wall Sign" or $block->getName() === "Sign Post" && $this->plugin->getSign($block->getLevel()->getName(), $block->asVector3()) !== null){
            $sign = $this->plugin->getSign($block->getLevel()->getName(), $block->asVector3());
            if($player->hasPermission("kothsigns.rem")){
                $this->plugin->remSign($sign);
                $player->sendMessage(C::GREEN."[KoTH-Signs] Sign removed.");
            } else {
                $event->setCancelled();
                $player->sendMessage(C::RED."[KoTH-Signs] You do not have permission to remove this sign.");
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event){
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if($block->getName() === "Wall Sign" or $block->getName() === "Sign Post"){
            $sign = $this->plugin->getSign($block->getLevel()->getName(), $block->asVector3());
            if($sign !== null){
                if($player->hasPermission("kothsigns.use")){
                    $arena = $this->plugin->getArena($sign);
                    if($arena === null){
                        $this->plugin->remSign($sign);
                        $player->sendMessage(C::RED."[KoTH-Signs] The arena does not exist, sign removed."); //shouldn't reach here as UpdateTask should auto remove it.
                        return;
                    }
                    if($sign["type"] === $this->plugin::SIGN_TYPE_JOIN){
                        $arena->addPlayer($player);
                        return;
                    }
                    if($sign["type"] === $this->plugin::SIGN_TYPE_LEAVE){
                        if($this->plugin->koth->getArenaByPlayer($player->getName()) === null or $this->plugin->koth->getArenaByPlayer($player->getName())->getName() !== $arena->getName()){
                            return;
                        }
                        $arena->removePlayer($player, "Left the game, via KoTH-Signs"); //todo config.
                        return;
                    }
                    //todo form for more info if type is stats.
                }
            }
            $tile = $block->getLevel()->getTile($block->asVector3());
            if($tile instanceof SignTile){
                $text = $tile->getText();
                if($text[0] === "[KOTH]" && strlen($text[1]) > 1 && ($text[2] === "join" or $text[2] === "leave" or $text[2] === "")){
                    $sign = [
                        "world" => $block->getLevel()->getName(),
                        "position" => $block->getX().".".$block->getY().".".$block->getZ()
                    ];
                    switch ($text[2]){
                        case 'join':
                            $type = $this->plugin::SIGN_TYPE_JOIN;
                            break;
                        case 'leave':
                            $type = $this->plugin::SIGN_TYPE_LEAVE;
                            break;
                        default:
                            $type = $this->plugin::SIGN_TYPE_STATS;
                    }
                    $sign["type"] = $type;
                    $arena = $this->plugin->koth->getArenaByName($text[1]);
                    if($arena === null){
                        $player->sendMessage(C::RED."[KoTH-Signs] The arena '".$text[1]."' does not exist.");
                        return;
                    }
                    $sign["arena"] = $arena->getName();
                    if($player->hasPermission("kothsigns.add")){
                        $this->plugin->addSign($sign);
                        $player->sendMessage(C::GREEN."[KoTH-Signs] Sign added.");
                    }
                }
            }
        }
    }

}