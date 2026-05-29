<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 * 
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 * 
 */
declare(strict_types=1);
class KTimer
{
    private static ?KTimer $instance=null;
    protected float $starttime=0;
    protected float $endtime=0;
    protected float $totaltime=0;
    protected string $name="";

    function __construct(string $name="")
    {
        $this->name=$name;
    }

    function __destruct()
    {
        
    }

    public function start() : void
    {
        $mtime=microtime(false);
        $mtimeArray=explode(' ',$mtime);
        $mtimeFloat=floatval($mtimeArray[1])+floatval($mtimeArray[0]);
        $this->starttime=$mtimeFloat;
    }

    public function stop() : void
    {
        $mtime=microtime(false);
        $mtimeArray=explode(" ",$mtime);
        $mtimeFloat=floatval($mtimeArray[1])+floatval($mtimeArray[0]);
        $this->endtime=$mtimeFloat;
        $this->totaltime=($this->endtime-$this->starttime);
    }

    public function printTime() : void
    {
        echo 'Timer '.$this->name." -> ".$this->totaltime.' seconds.';
    }

    public function toString() : string
    {
        return 'Timer '.$this->name." -> ".$this->totaltime.' seconds.';
    }

    public function totalTimeToString() : string
    {
        return "".$this->totaltime;
    }

    public function totalTimeMillisecondsToString() : string
    {
        return "".intval($this->totaltime*1000);
    }
    
    public function totalTimeMilliseconds() : float
    {
        return $this->totaltime*1000;
    }    
    
    public static function getInstance() : KTimer
    {
        if(is_null(self::$instance))
        {
            self::$instance=new KTimer();
        }
        return self::$instance;
    }

}