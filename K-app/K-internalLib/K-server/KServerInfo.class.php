<?php
/**
 * From Nextcloud server info
 * @author Frank Karlitschek <frank@nextcloud.com>
 * 
 * @link https://github.com/nextcloud/serverinfo/
 * 
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */
/*
 * File modified for the KProject Framework
 * 
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 */
declare(strict_types=1);
enum SizeFormat 
{
    case KiloByte;
    case MegaByte;
    case GigaByte;
    case TeraByte;
}

class KServerInfo
{
    private ?KMemoryStruct $dataMemory =null;
    function __construct()
    {
        
    }

    protected function readContent(string $filename): string
    {
        $data = @file_get_contents($filename);
        if ($data === false || $data === '')
        {
            throw new \RuntimeException('Unable to read: "'.$filename.'"');
        }
        return $data;
    }

    protected function executeCommand(string $command): string
    {
        $output = @shell_exec(escapeshellcmd($command));
        if ($output === false || $output === null || $output === '')
        {
            throw new \RuntimeException('No output for command: "'.$command.'"');
        }
        return $output;
    }

    public function getTime(): string
    {
        return (string) shell_exec('date');
    }

    public function getUptime(): int
    {
        $data = -1;
        
        if(!is_readable('/proc/uptime'))
        {
            return $data;
        }
        
        try
        {
            $uptime = $this->readContent('/proc/uptime');
        } catch (\RuntimeException $e)
        {
            return $data;
        }

        [$uptimeInSeconds,] = array_map('intval', explode(' ', $uptime));

        return $uptimeInSeconds;
    }

    public function getCpuName(): string
    {
        $data = 'Unknown Processor';
        
        if(!is_readable('/proc/cpuinfo'))
        {
            return $data;
        }        
        
        if(!is_readable('/proc/cpuinfo'))
        {
            return $data;
        }

        try
        {
            $cpuinfo = $this->readContent('/proc/cpuinfo');
        } catch (\RuntimeException $e)
        {
            return $data;
        }

        $matches = [];
        $pattern = '/model name\s:\s(.+)/';

        $result = preg_match_all($pattern, $cpuinfo, $matches);
        if ($result === 0 || $result === false)
        {
            // For Raspberry Pi 4B
            $pattern = '/Model\s+:\s(.+)/';
            $result = preg_match_all($pattern, $cpuinfo, $matches);
            if ($result === 0 || $result === false)
            {
                return $data;
            }
        }

        $model = $matches[1][0];

        $pattern = '/processor\s+:\s(.+)/';

        $result = preg_match_all($pattern, $cpuinfo, $matches);
        $cores = count($matches[1]);

        if ($cores === 1)
        {
            $data = $model.' (1 core)';
        }
        else
        {
            $data = $model.' ('.$cores.' cores)';
        }

        return $data;
    }

    private function retrieveMemory(): KMemoryStruct
    {
        $data = new KMemoryStruct();
        
        if(!is_readable('/proc/meminfo'))
        {
            return $data;
        }

        try
        {
            $meminfo = $this->readContent('/proc/meminfo');
        } catch (\RuntimeException $e)
        {
            return $data;
        }

        $matches = [];
        $pattern = '/(?<Key>(?:MemTotal|MemFree|MemAvailable|SwapTotal|SwapFree)+):\s+(?<Value>\d+)\s+(?<Unit>\w{2})/';

        $result = preg_match_all($pattern, $meminfo, $matches);
        if ($result === 0 || $result === false)
        {
            return $data;
        }

        foreach ($matches['Key'] as $i => $key)
        {
            // Value is always in KB: https://github.com/torvalds/linux/blob/c70672d8d316ebd46ea447effadfe57ab7a30a50/fs/proc/meminfo.c#L58-L60
            $value = (int) ((int) $matches['Value'][$i] / 1024);

            switch ($key)
            {
                case 'MemTotal':
                    $data->setMemTotal($value);
                    break;
                case 'MemFree':
                    $data->setMemFree($value);
                    break;
                case 'MemAvailable':
                    $data->setMemAvailable($value);
                    break;
                case 'SwapTotal':
                    $data->setSwapTotal($value);
                    break;
                case 'SwapFree':
                    $data->setSwapFree($value);
                    break;
            }
        }
        $this->dataMemory=$data;
        return $this->dataMemory;
    }
    
    public function getMemory(bool $reload=false): KMemoryStruct
    {  
        if(is_null($this->dataMemory)||$reload)
        {
            return $this->retrieveMemory();       
        }
        return $this->dataMemory;
    }
    

    
//    case KiloByte;
//    case MegaByte;
//    case GigaByte;
//    case TeraByte;    
    
    private function getLabelForSizeFormat(SizeFormat $format=SizeFormat::GigaByte) : string
    {
        return match($format)
        {
            SizeFormat::KiloByte => ' KB',
            SizeFormat::MegaByte => ' MB',
            SizeFormat::GigaByte => ' GB',
            SizeFormat::TeraByte => ' TB',
        };        
    }
    private function getFactorForSizeFormat(SizeFormat $format=SizeFormat::GigaByte) : float
    {
        return match($format)
        {
            SizeFormat::KiloByte => 1024,
            SizeFormat::MegaByte => 1,
            SizeFormat::GigaByte => (1/1024),
            SizeFormat::TeraByte => (1/(1024*1024)),
        };          
    }    
    
    public function getMemoryTotal(SizeFormat $format=SizeFormat::GigaByte): string
    {
        return ($this->getMemory()->getMemTotal()*$this->getFactorForSizeFormat($format)).$this->getLabelForSizeFormat($format);
    }
//    public function getMemoryUsed(SizeFormat $format=SizeFormat::GigaByte): string
//    {
//        return ($this->getMemory()->getMemAvailable()*$this->getFactorForSizeFormat($format)).$this->getLabelForSizeFormat($format);
//    } 
    public function getMemoryUsed(SizeFormat $format = SizeFormat::GigaByte): string
    {
        $memory = $this->getMemory();
        $usedMb = $memory->getMemTotal() - $memory->getMemAvailable();

        return $this->convertSizeToString($usedMb, $format);
    }    
    public function getMemoryFree(SizeFormat $format=SizeFormat::GigaByte): string
    {
        return ($this->getMemory()->getMemFree()*$this->getFactorForSizeFormat($format)).$this->getLabelForSizeFormat($format);
    } 
    public function getMemorySwapTotal(SizeFormat $format=SizeFormat::GigaByte): string
    {
        return ($this->getMemory()->getSwapTotal()*$this->getFactorForSizeFormat($format)).$this->getLabelForSizeFormat($format);
    } 
    public function getMemorySwapFree(SizeFormat $format=SizeFormat::GigaByte): string
    {
        return ($this->getMemory()->getSwapFree()*$this->getFactorForSizeFormat($format)).$this->getLabelForSizeFormat($format);
    }   
    
    public function convertSizeToString(int $size,SizeFormat $format=SizeFormat::GigaByte) : string
    {
        return ($size*$this->getFactorForSizeFormat($format)).$this->getLabelForSizeFormat($format);
    }

    /**
     * 
     * @return array<string,string>
     */
    public function getNetworkInfo(): array
    {
        $result = [];
        $result['hostname'] = \gethostname();
        $dns = shell_exec('cat /etc/resolv.conf |grep -i \'^nameserver\'|head -n1|cut -d \' \' -f2');
        $result['dns'] = $dns;
        $gw = shell_exec('ip route | awk \'/default/ { print $3 }\'');
        $result['gateway'] = $gw;
        return $result;
    }

    /**
     * 
     * @return array<int,mixed>
     */
    public function getNetworkInterfaces(): array
    {
        $interfaces = glob('/sys/class/net/*') ?: [];
        $result = [];

        foreach ($interfaces as $interface)
        {
            $iface = [];
            $iface['interface'] = basename($interface);
            $iface['mac'] = shell_exec('ip addr show dev '.$iface['interface'].' | grep "link/ether " | cut -d \' \' -f 6  | cut -f 1 -d \'/\'');
            $iface['ipv4'] = shell_exec('ip addr show dev '.$iface['interface'].' | grep "inet " | cut -d \' \' -f 6  | cut -f 1 -d \'/\'');
            $iface['ipv6'] = shell_exec('ip -o -6 addr show '.$iface['interface'].' | sed -e \'s/^.*inet6 \([^ ]\+\).*/\1/\'');
            if ($iface['interface'] !== 'lo')
            {
                $iface['status'] = shell_exec('cat /sys/class/net/'.$iface['interface'].'/operstate');
                $iface['speed'] = (int) shell_exec('cat /sys/class/net/'.$iface['interface'].'/speed');
                if($iface['speed'] > 0)
                {
                    if ($iface['speed'] >= 1000)
                    {
                        $iface['speed'] = ($iface['speed'] / 1000).' Gbps';
                    }
                    else
                    {
                        $iface['speed'] = $iface['speed'].' Mbps';
                    }
                }
                else
                {
                    $iface['speed'] = 'unknown';
                }
                $duplex = shell_exec('cat /sys/class/net/'.$iface['interface'].'/duplex');
                if (isset($duplex) && $duplex !== '')
                {
                    $iface['duplex'] = 'Duplex: '.$duplex;
                }
                else
                {
                    $iface['duplex'] = '';
                }
            }
            else
            {
                $iface['status'] = 'up';
                $iface['speed'] = 'unknown';
                $iface['duplex'] = '';
            }
            $result[] = $iface;
        }
        return $result;
    }

    /**
     * 
     * @return array<int,mixed>
     */
    public function getThermalZones(): array
    {
        $thermalZones = glob('/sys/class/thermal/thermal_zone*') ?: [];
        $result = [];

        foreach ($thermalZones as $thermalZone)
        {
            $tzone = [];
            try
            {
                $tzone['hash'] = md5($thermalZone);
                $tzone['type'] = $this->readContent($thermalZone.'/type');
                $tzone['temp'] = (float) ((int) ($this->readContent($thermalZone.'/temp')) / 1000);
            } catch (\RuntimeException $e)
            {
                continue;
            }
            $result[] = $tzone;
        }

        return $result;
    }

    /**
     * 
     * @return array<int,KDiskStruct>
     */
    public function getDiskInfo(): array
    {
        $data = [];

        try
        {
            $disks = $this->executeCommand('df -TPk');
        } catch (\RuntimeException $e)
        {
            return $data;
        }

        $matches = [];
        $pattern = '/^(?<Filesystem>[\S]+)\s*(?<Type>[\S]+)\s*(?<Blocks>\d+)\s*(?<Used>\d+)\s*(?<Available>\d+)\s*(?<Capacity>\d+%)\s*(?<Mounted>[\w\/-]+)$/m';

        $result = preg_match_all($pattern, $disks, $matches);
        if ($result === 0 || $result === false)
        {
            return $data;
        }

        foreach ($matches['Filesystem'] as $i => $filesystem)
        {
            if (in_array($matches['Type'][$i], ['tmpfs', 'devtmpfs', 'squashfs', 'overlay'], false))
            {
                continue;
            }
            elseif (in_array($matches['Mounted'][$i], ['/etc/hostname', '/etc/hosts'], false))
            {
                continue;
            }

            $disk = new KDiskStruct();
            $disk->setDevice($filesystem);
            $disk->setFs($matches['Type'][$i]);
            $disk->setUsed((int) ((int) $matches['Used'][$i] / 1024));
            $disk->setAvailable((int) ((int) $matches['Available'][$i] / 1024));
            $disk->setPercent($matches['Capacity'][$i]);
            $disk->setMount($matches['Mounted'][$i]);

            $data[] = $disk;
        }

        return $data;
    }

    public function getOSName(): string
    {
        return PHP_OS.' '.php_uname('r').' '.php_uname('m');
    }

    /**
     * 
     * @return array<string,string>
     */
    public function getPhpStatistics(): array
    {
        $phpStats = new KPHPStats();
        return $phpStats->getPhpStatistics();
    }
    
    public function getHostName() : string
    {
        $hostname=gethostname();
        return $hostname;
    }

    public function toString(string $delimitor = "<br \>"): string
    {
        $s = '';

        $s .= 'Host Name='.$this->getHostName().$delimitor;

        $s .= 'Os Name='.$this->getOSName().$delimitor;
        $s .= 'Time='.$this->getTime().$delimitor;
        $s .= 'Uptime='.$this->getUptime().$delimitor;
        $s .= 'CpuName='.$this->getCpuName().$delimitor;
        $s .= 'NetWorkInfo='.print_r($this->getNetworkInfo(), true).$delimitor;
        $s .= 'NetworkInterfaces='.print_r($this->getNetworkInterfaces(), true).$delimitor;

        $s .= 'ThermalZones='.print_r($this->getThermalZones(), true).$delimitor;

        $s .= 'DiskInfo='.print_r($this->getDiskInfo(), true).$delimitor;
        $s .= 'Memory='.print_r($this->getMemory(), true).$delimitor;
        $s .= 'PhpStats='.print_r($this->getPhpStatistics(), true).$delimitor;

        return $s;
    }
}