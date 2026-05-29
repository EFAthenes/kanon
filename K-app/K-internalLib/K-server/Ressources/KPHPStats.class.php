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
class KPHPStats
{
    protected ?IniGetWrapper $phpIni=null;

    public function __construct(?IniGetWrapper $phpIni = null)
    {
        if (is_null($phpIni))
        {
            $phpIni = new IniGetWrapper();
        }
        $this->phpIni = $phpIni;
    }

    /**
     * 
     * @return array<string,mixed>
     */
    public function getPhpStatistics(): array
    {
        return [
            'version' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION,
            'memory_limit' => $this->phpIni->getBytes('memory_limit'),
            'max_execution_time' => $this->phpIni->getNumeric('max_execution_time'),
            'upload_max_filesize' => $this->phpIni->getBytes('upload_max_filesize'),
            'opcache' => $this->getOPcacheStatus(),
            'apcu' => $this->getAPCuStatus(),
            'extensions' => $this->getLoadedPhpExtensions(),
        ];
    }

    /**
     * Get status information about the cache from the OPcache extension
     *
     * @return array<string,string> with an array of state information about the cache instance
     */
    protected function getOPcacheStatus(): array
    {
        // Test if the OPcache module is installed
        if (!extension_loaded('Zend OPcache'))
        {
            // module not loaded, returning back empty array to prevent any errors on JS side.
            return [];
        }

        // get status information about the cache
        $status = (function_exists('opcache_get_status')) ? opcache_get_status(false) : false;

        if ($status === false)
        {
            // no array, returning back empty array to prevent any errors on JS side.
            $status = [];
        }

        return $status;
    }

    /**
     * Get status information about the cache from the APCu extension
     *
     * @return array<string,mixed> with an array of state information about the cache instance
     */
    protected function getAPCuStatus(): array
    {
        // Test if the APCu module is installed
        if (!extension_loaded('apcu'))
        {
            // module not loaded, returning back empty array to prevent any errors on JS side.
            return [];
        }

        // get cached information from APCu data store
        $cacheInfo = apcu_cache_info(true);

        // get APCu Shared Memory Allocation information
        $smaInfo = apcu_sma_info(true);

        if ($cacheInfo === false)
        {
            // no array, returning back N/A to prevent any errors on JS side.
            $cacheInfo = 'N/A';
        }

        if ($smaInfo === false)
        {
            // no array, returning back N/A to prevent any errors on JS side.
            $smaInfo = 'N/A';
        }

        // return the array
        return [
            'cache' => $cacheInfo,
            'sma' => $smaInfo,
        ];
    }

    /**
     * Get all loaded php extensions
     *
     * @return array<int,string>|null of strings with the names of the loaded extensions
     */
    protected function getLoadedPhpExtensions(): ?array
    {
        return (function_exists('get_loaded_extensions') ? get_loaded_extensions() : null);
    }

}