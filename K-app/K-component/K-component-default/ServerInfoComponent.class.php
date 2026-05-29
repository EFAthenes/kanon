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
class ServerInfoComponent extends KComponent
{
    function __construct(string $name="ServerInfoComponent")
    {
        parent::__construct();
        $this->setName($name);
        $this->setClass();
        
        $server= new KServerInfo();
        //$this->addHTML($server->toString());
        
        $html='
<div class="section">
    <div class="row">
        <div class="col col-12">
            <h2>
                    <i class="fa-solid fa-computer"></i>
                    '.kPurify($server->getHostName()).'				
            </h2>
        </div>
        <div class="col col-12">			
            <p>Operating System: <strong>'.kPurify($server->getOSName()).'</strong></p>
            <p>CPU:<strong>'.kPurify($server->getCpuName()).'</strong></p>
            <p>Memory:<strong>
                '.kPurify($server->getMemoryUsed(SizeFormat::MegaByte)).' / 
                '.kPurify($server->getMemoryTotal(SizeFormat::MegaByte)).'
                ('.kPurify($server->getMemoryFree(SizeFormat::MegaByte)).')
                </strong></p>
            <p>Swap:<strong>
                '.kPurify($server->getMemorySwapTotal(SizeFormat::MegaByte)).' 
                ('.kPurify($server->getMemorySwapFree(SizeFormat::MegaByte)).')
                </strong></p>                
            <p>Server time: 
                <strong>
                    <span>'.kPurify($server->getTime()).'</span>
                </strong>
            </p>
            <p>Uptime: 
            <strong>
                <span>'.kPurify("".$server->getUptime()).'</span>
            </strong>
            </p>
        </div>
    </div>
</div>  

<hr />

<div class="section">
    <div class="row">
        <div class="col col-12">
            <h2>
                <i class="fa-solid fa-hard-drive"></i>
                Storage				
            </h2>
        </div>
        <div class="col col-12">
';
        /* @var $disk KDiskStruct */
        foreach($server->getDiskInfo() as $disk)
        {
            $html.='
            <p>Mount: 
            <strong>
                <span>'.kPurify($disk->getMount()).'</span>
            </strong>
            </p>  
            <p>FileSystem: 
            <strong>
                <span>'.kPurify($disk->getFs()).'</span>
            </strong>
            </p> 
            <p>Size: 
            <strong>
                <span>'.kPurify($server->convertSizeToString($disk->getUsed()+$disk->getAvailable())).'</span>
            </strong>
            </p> 
            <p>Available: 
            <strong>
                <span>'.kPurify($server->convertSizeToString($disk->getAvailable())).'</span>
            </strong>
            </p> 
            <p>Used: 
            <strong>
                <span>'.kPurify($server->convertSizeToString($disk->getUsed()).'('.kPurify($disk->getPercent())).')</span>
            </strong>
            </p>             
';
        }
        
        
    $html.='    
        </div>
    </div>
</div>          
';
        $this->addHTML($html);
        
    }
}