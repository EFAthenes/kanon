<?php
declare(strict_types=1);
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
/**
 * Description of KTitleLayoutAdmin
 *
 * @author Mulot Louis
 */
class KTitleLayoutAdmin extends KComponent
{
    private string $title="";
    private string $icon="";
    private string $subtitle="";
    /**
     * 
     * @var array<int,KTitleButton|mixed>
     */
    private array$arrayOfButton=[];
    private ?KURL $kurl=null;
    
    private ?KComponent $mobileSubMenuComponent=null;
    private bool $unactivateURL=false;

    /**
     * 
     * @param string $title
     * @param string $icon
     * @param string $subtitle
     * @param array<int,KTitleButton|mixed>|null $arrayOfButton
     */
    public function __construct(string $title="",string $icon="",string $subtitle="",?array $arrayOfButton=[])
    {
        parent::__construct();
        $this->kurl=new KURL();
        $this->kurl->removeArg("check");
        KApp::getInstance()->getLayout()->setTitle($title);

        if($title=="")
        {
            $this->title="Title";
        }
        else
        {
            $this->title=$title;
        }

        if($icon=="")
        {
            $this->icon="fa fa-dashboard";
        }
        else
        {
            $this->icon=$icon;
        }

        if($subtitle!="")
        {
            $this->subtitle="<p>".$subtitle."</p>";
        }

        if(is_array($arrayOfButton)&&count($arrayOfButton)>0)
        {
            foreach($arrayOfButton as $button)
            {
                if(!is_null($button)&&$button instanceof KTitleButton)
                {
                    $this->addKTitleButton($button);
                }
            }
        }
    }

    function getTitle() : string
    {
        return $this->title;
    }

    function getIcon() : string
    {
        return $this->icon;
    }

    function getSubtitle() : string
    {
        return $this->subtitle;
    }

    function setTitle(string $title) : void
    {
        $this->title=$title;
    }
    
    function addToTitle(string $title) : void
    {
        $this->title.=$title;
    }    

    function setIcon(string $icon) : void
    {
        $this->icon=$icon;
    }

    function setSubtitle(string $subtitle) : void
    {
        $this->subtitle=$subtitle;
    }

    function getKurl() : ?KURL
    {
        return $this->kurl;
    }

    function setKurl(KURL $kurl) : void
    {
        $this->kurl=$kurl;
    }
    
    function removeArgFromKurl(string $arg) : void
    {
        $this->kurl->removeArg($arg);
    }

    public function addKTitleButton(KTitleButton $button) : void
    {
        $this->arrayOfButton[]=$button;
    }
    
    public function addMobileSubMenuComponent(KComponent $component) : void
    {
        $this->mobileSubMenuComponent=$component;
    }

    public function draw() : string
    {
        $there_is_button=false;
        $col_title="";
        if(count($this->arrayOfButton))
        {
            $there_is_button=true;
            //$col_title="-5";
        }
        
        $mobile_sub_menu_component='';
        if(!is_null($this->mobileSubMenuComponent))
        {
           $mobile_sub_menu_component=$this->mobileSubMenuComponent->draw();          
        }

        $html='
<div class="ktitle-box">
    <div class="inner-ktitle-box d-none d-md-block d-lg-block d-xl-block">
        <div class="page-ktitle ">
            <div class="row">
                <div class="col-auto'.$col_title.' text-truncate">
                    <div class="page-ktitle-label">
';
        if(!$this->unactivateURL)
        {
            $html.='    <a class="" href="'.filter_var($this->kurl->printURL(),FILTER_SANITIZE_URL).'"><i class="'.htmlentities($this->icon).'"></i> '.htmlentities($this->title).'</a>';
        }
        else
        {
            $html.='    <i class="'.htmlentities($this->icon).'"></i> '.htmlentities($this->title).'';
        }
        $html.='                
                        '.kPurify($this->subtitle).'
                    </div>
                </div>
';

        if($there_is_button)
        {
            $html.='
                <div class="col ">
                    <div class="d-flex flex-row-reverse">
';
            foreach($this->arrayOfButton as $button)
            {
                $html.='<div class="btn-wrapper">'.$button->draw().'</div>';
            }

            $html.=' 
                    </div> 
                </div>  
';
        }
        $html.=parent::draw();
        $html.='                
            </div>
        </div>
    </div>
    <div class="mobile-ktitle-box d-block d-md-none d-lg-none d-xl-none ">
        <div class="inner-ktitle-box">
            <div class="page-ktitle ">
                <div class="row">
                    <div class="col text-truncate">
                        <div class="page-ktitle-label">
        ';
        if($mobile_sub_menu_component)
        {
            $html.='
                            <button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#collapseSubMenu" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-bars fa-lg" aria-hidden="true" ></i>
                            </button>
            ';
        }
        $html.='
                            <span>
                                <!-- Title of the site -->
                                <a class="" href="'.filter_var($this->kurl->printURL(),FILTER_SANITIZE_URL).'">'.htmlentities(ParamManager::getInstance()->app_name).'</a>
                            </span>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
        <div class="collapse sidebar-ktitle-box" id="collapseSubMenu" >
            
            <div class="page-ktitle-label-mobile">
                <a class="" href="'.filter_var($this->kurl->printURL(),FILTER_SANITIZE_URL).'"><i class="'.htmlentities($this->icon).'"></i> '.htmlentities($this->title).'</a>
                '.htmlentities($this->subtitle).'
            </div>
';
        if($there_is_button)
        {
            $html.='
                
                <div class="col">
                    <div class="d-flex">
';
            foreach($this->arrayOfButton as $button)
            {
                $html.='<div class="btn-wrapper">'.$button->draw().'</div>';
            }

            $html.=' 
                    </div> 
                </div> 
               
';
        }
        $html.='
        '.$mobile_sub_menu_component.'
        </div>    
    </div>
</div>
';
        return $html;
    }
    
    public function removeFormGetArg() : void
    {
        $kurl=new KURL();
        $kurl->removeArg(HistoryPage::$STRING_SUBMITED_POST);
        $this->setKurl($kurl);
    }
    
    public function unactivateURL() : void
    {
        $this->unactivateURL=true;
    }
}