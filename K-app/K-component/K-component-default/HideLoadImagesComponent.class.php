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
/*
declare(strict_types=1);
class HideLoadImagesComponent extends KComponent
{
    private $i=0;

    function __construct()
    {
        parent::__construct();
        $this->setName("load_images_for_css");
        $this->setClass();
        $this->addCssProperties(" display:none; ");
    }
    public function headerLoad()
    {
        //echo " HEADER LOAD <br />";
        $path=ParamManager::getInstance()->image_web_path;
        //echo $path."<br />";
        if(file_exists($path)&& is_dir($path))
        {
            //echo "EXISTS AND IS PATH ==> <br />";

            if($handle = opendir($path))
            {
                //echo "HANDLE IT ==> <br />";
                while ($file = readdir($handle))
                {
                    if ($file != "." && $file != ".." && is_file($path.$file) )
                    {
                        //echo $file."<br />";
                        $this->addHideImages("../images/".$file);
                    }
                    else if($file != "." && $file != ".." && is_dir($path.$file))
                    {
                        if($handle2 = opendir($path.$file))
                        {
                            while ($file2 = readdir($handle2))
                            {
                                if ($file2 != "." && $file2 != ".." && is_file($path.$file."/".$file2) )
                                {
                                    $this->addHideImages("../images/".$file."/".$file2);
                                }
                            }
                        }
                        closedir($handle2);
                    }
                }
                closedir($handle);
            }
        }

//        $this->addHideImages("../images/tick.gif");
//        $this->addHideImages("../images/space_planning.gif");
//        $this->addHideImages("../images/ajax-loader2.gif");
//        $this->addHideImages("../images/ajax_category.gif");
//        $this->addHideImages("../images/validation2.png");
//        $this->addHideImages("../images/bag/forward.png");
//        $this->addHideImages("../images/bag/trashcan_full.png");
//        $this->addHideImages("../images/icon_clock.gif");

    }
    public function addHideImages($srcImage)
    {
        $this->i++;
        $images= new ImageComponent($this->getName()."_".$this->i,$srcImage,"");
        $this->addComponent($images);
    }
    public function clearAndReload()
    {
        $this->clearComponentList();
        $this->load();        
    }
    function  __destruct()
    {
        parent::__destruct();
    }
}
 * 
 */