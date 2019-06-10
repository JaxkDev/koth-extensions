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

use Jackthehack21\KOTH\Main as KOTH;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    /** @var Config */
    private $configC;
    private $dataC; //todo remove and change to sqlite3.

    private $config;
    private $data;

    /** @var KOTH */
    private $koth;

    /** @var EventHandler */
    private $EventHandler;

    public function onEnable()
    {
        $this->koth = KOTH::getInstance();
        $this->EventHandler = new EventHandler($this);
        // VERIFY koth is a specific version so no API conflicts and api errors.
        if($this->koth->getDescription()->getVersion() !== "1.0.0-Beta3"){
            $this->getLogger()->error("KoTH v".$this->koth->getDescription()->getVersion()." is not supported, plugin is now disabled.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        $this->getServer()->getPluginManager()->registerEvents($this->EventHandler, $this);
        $this->init();
    }

    private function init(): void{
        $this->saveResource("config.yml");
        $this->configC = new Config("config.yml");
        $this->config = $this->configC->getAll();

        $this->dataC = new Config("data.yml", CONFIG::YAML, ["version"=>0, "signs"=>[]]);
        $this->data = $this->dataC->getAll();
    }
}