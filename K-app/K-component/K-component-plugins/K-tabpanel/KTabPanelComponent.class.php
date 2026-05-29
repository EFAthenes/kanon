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
/**
 * Description of KTabPanelComponent
 *
 * @author Mulot Louis
 */
class KTabPanelComponent extends KComponent
{
    //private $listKTab=null;
    private string $ktab_id="ktab_";
    private string $active_title="";
    private string $buttonClassName="";
    function __construct(string $name)
    {
        parent::__construct();
        $this->setNone();
       // $this->listKTab=new ArrayList();
        $this->setName($name);
    }
    public function addKTabPanel(KTabPanelItem $panel,bool $active=false) : void
    {
        //$this->listKTab->add($panel);
        $this->addComponent($panel);
        if($active)
        {
            $this->active_title=$panel->getTitle();
        }
    }
    
    public function setButtonClass(string $class) : void
    {
        $this->buttonClassName=$class;
    }
    
    public function draw() : string
    {
        $list=$this->getListComponent();
        if($list->getSize()>0)
        {
            $html='
<div role="tabpanel">
  <!-- Nav tabs -->
';      
            $html_1="";
            $html_2="";
            /* @var $panel KTabPanelItem */
            $i=0;
            foreach($list as $panel)
            {
                $active="";
                if($i==0 && $this->active_title=="")
                {
                    $active='active';
                }
                if($this->active_title==$panel->getTitle())
                {
                    $active='active';
                }
                // <li role="presentation" class="'.$active.'">
                // <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true" aria-expanded="true">Informations générales</a>
                $html_1.='<li class="nav-item" role="presentation"> <button type="button" class="nav-link '.$active.' '.$this->buttonClassName.'" data-bs-toggle="tab" data-bs-target="#'.$this->makeLabel($i).'" aria-controls="'.$this->makeLabel($i).'" aria-selected="true" role="tab" data-toggle="tab">'.$panel->getTitle().'</button></li>';
                $html_2.='<div role="tabpanel" class="tab-pane '.$active.'" id="'.$this->makeLabel($i).'">'.$panel->draw().'</div>';
                $i++;
            }   
            
            $html.='
<ul id="'.$this->getName().'" class="nav nav-tabs" role="tablist">
'.$html_1.'
</ul> 

<div class="tab-content">
  '.$html_2.'
</div>

</div>
';
            $this->addHTML($html);
        }
        return parent::drawOnlyThisComponent(true);
    }   
    
    public function makeLabel(mixed $number) : string 
    {
        return $this->getName()."_".$this->ktab_id."_".$number;
    }
    
    public function setActiveTab(?string $title) : bool
    {
        if(is_null($title))
        {
            return false;
        }
        $list=$this->getListComponent();
        $status=false;
        /* @var $panel KTabPanelItem */
        foreach($list as $panel)
        { 
            if($panel->getTitle()==$title)
            {
                $this->active_title=$title;
                $status=true;
            }
        }
        return $status;
    }
    public function getActiveTitle(): string
    {
        return $this->active_title;
    }
}