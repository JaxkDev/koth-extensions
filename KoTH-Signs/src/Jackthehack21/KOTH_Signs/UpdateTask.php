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

use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\tile\Sign;

class UpdateTask extends Task{

    /** @var Main */
    private $plugin;

    /**
     * @param Main $plugin
     */
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @param int $tick
     */
    public function onRun(int $tick){
        foreach($this->plugin->getSigns() as $sign){
            $pos = explode(".",$sign["position"]);
            $tile = $this->plugin->getServer()->getLevelByName($sign["world"])->getTile(new Vector3($pos[0], $pos[1], $pos[2]));
            /*if(!$tile instanceof Sign){
                $this->plugin->remSign($sign);
                $this->plugin->getLogger()->info("Sign at '".$sign["position"]."' in world '".$sign["world"]."' has been removed as the tile no longer exists.");
                continue;
            }*/
            if(!$tile instanceof Sign) continue;
            if($this->plugin->koth->getArenaByName($sign["arena"]) === null){
                $tile->setText("[KoTH-Signs]","Sign removed as:","Arena no longer","exists :(");
                $this->plugin->remSign($sign);
                $this->plugin->getLogger()->info("Sign at '".$sign["position"]."' in world '".$sign["world"]."' has been removed as the arena assigned no longer exists.");
                continue;
            }
            for($i = 0; $i <= 3; $i++){
                $tile->setLine($i, $this->plugin->format($this->plugin->getFormat($sign["type"])[$i], $this->plugin->getArena($sign)));
            }
        }
    }
}