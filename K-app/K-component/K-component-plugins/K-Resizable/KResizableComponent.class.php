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
 * Description of KResizableComponent
 * https://jqueryui.com/resizable/
 * @author Hippolyte
 */
class KResizableComponent extends KComponent
{
    protected string $idDiv;
    private string $title;
    private ?int $height;
    private string $alsoResize="";
    private bool $animate=false;
    private int $animateDuration=100;
    private bool $aspectRatio=false;
    private bool $autoHide=false;
    private string $cancel="input,textarea,button,select,option";
    private string $containment="";
    private bool $disabled=false;
    private bool $ghost=false;
    private string $handles="e, s, se";
    private ?int $maxHeight=null;
    private ?int $maxWidth=null;
    private int $minHeight=10;
    private int $minWidth=10;

    final function __construct(string $idDiv="resizable",string $title="Resizable",?int $height=null)
    {
        parent::__construct();
        $layout=KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/jquery-ui.min.js");
        $layout->addCssFileToBuffer(__DIR__."/css/jquery-ui.min.css");
        $layout->addCssFileToBuffer(__DIR__."/css/jquery-ui.structure.min.css");
        $layout->addCssFileToBuffer(__DIR__."/css/theme/jquery-ui.theme.min.css");
        $this->idDiv=$idDiv;
        $this->title=$title;
        $this->height=$height;
    }
    
    public function draw(): string
    {
        // JAVASCRIPT
        $js='
            $( function() {$("#'.$this->idDiv.'").resizable({'
                .'"alsoResize": "'.$this->alsoResize.'",'
                .'"animate": '.convertBoolToString($this->animate).','
                .'"animateDuration": '.$this->animateDuration.','
                .'"aspectRatio": '.convertBoolToString($this->aspectRatio).','
                .'"autoHide": '.convertBoolToString($this->autoHide).','
                .'"cancel": "'.$this->cancel.'",'
                .'"containment": "'.$this->containment.'",'
                .'"disabled": '.convertBoolToString($this->disabled).','
                .'"ghost": '.convertBoolToString($this->ghost).','
                .'"handles": "'.$this->handles.'",'
                .'"maxHeight": "'.$this->maxHeight.'",'
                .'"maxWidth": "'.$this->maxWidth.'",'
                .'"minHeight": '.$this->minHeight.','
                .'"minWidth": '.$this->minWidth.''
                .'});} );
            ';
        // HTML
        //style="height:'.$this->height.'px;"
        $styleHeight="";
        if($this->height)
        {
            $styleHeight.='style="height:'.$this->height.'px; max-width:100%;"';
        }
        $html='';
        $html='<div id="'.$this->idDiv.'" class="overflow-auto ui-widget-content ui-resizable" '.$styleHeight.' >';
        if(!empty($this->title))
        {
            $titleComponent=new TitleComponent($this->title,true,4);
            $titleComponent->addClassName($this->idDiv."_title");
            $html.=$titleComponent->draw();
        }
        $html.=parent::draw();
        $html.='</div>';
        //
        $this->addJSText($js);
        return $html;
    }

    /**
     * Un ou plusieurs éléments à synchroniser avec le changement de taille
     * Valeur par défaut : ""
     * @param string $alsoResize
     * @return KComponent
     */
    public function setAlsoResize(string $alsoResize): KComponent
    {
        $this->alsoResize=$alsoResize;
        return $this;
    }

    /**
     * Définit l'activation de l'animation
     * Valeur par défaut : false
     * @param bool $animate
     * @return KComponent
     */
    public function setAnimate(bool $animate): KComponent
    {
        $this->animate=$animate;
        return $this;
    }

    /**
     * Définit le temps de l'animation en millisecondes
     * Valeur par défaut : 100
     * @param int $animateDuration
     * @return KComponent
     */
    public function setAnimateDuration(int $animateDuration): KComponent
    {
        $this->animateDuration=$animateDuration;
        return $this;
    }

    /**
     * Définit si l'objet est contraint par un ratio
     * Valeur par défaut : false
     * @param bool $aspectRatio
     * @return KComponent
     */
    public function setAspectRatio(bool $aspectRatio): KComponent
    {
        $this->aspectRatio=$aspectRatio;
        return $this;
    }

    /**
     * Définit si les controles doivent être masquées lors du non-survolement
     * Valeur par défaut : false
     * @param bool $autoHide
     * @return KComponent
     */
    public function setAutoHide(bool $autoHide): KComponent
    {
        $this->autoHide=$autoHide;
        return $this;
    }

    /**
     * Bloque le redimensionnement depuis le début sur les éléments spécifiés
     * Valeur par défaut : "input,textarea,button,select,option"
     * @param string $cancel
     * @return KComponent
     */
    public function setCancel(string $cancel): KComponent
    {
        $this->cancel=$cancel;
        return $this;
    }

    /**
     * Contraint le redimmensionnement (élément ou selector ex: "parent")
     * Valeur par défaut : ""
     * Constrains resizing to within the bounds of the specified element or region.
     * @param string $containment
     * @return KComponent
     */
    public function setContainment(string $containment): KComponent
    {
        $this->containment=$containment;
        return $this;
    }

    /**
     * Désactive le resizable
     * Valeur par défaut : false
     * @param bool $disabled
     * @return KComponent
     */
    public function setDisabled(bool $disabled): KComponent
    {
        $this->disabled=$disabled;
        return $this;
    }

    /**
     * Définit un semi-transparent pour prévisualiser lors du redimmensionnement
     * Valeur par défaut : false 
     * @param bool $ghost
     * @return KComponent
     */
    public function setGhost(bool $ghost): KComponent
    {
        $this->ghost=$ghost;
        return $this;
    }

    /**
     * Définit les côtés et les angles qui permettent le redimmensionnement
     * Valeur par défaut : "e, s, se"
     * @param string $handles
     * @return KComponent
     */
    public function setHandles(string $handles): KComponent
    {
        $this->handles=$handles;
        return $this;
    }

    /**
     * Définit une hauteur maximale
     * Valeur par défaut : null
     * @param int $maxHeight
     * @return KComponent
     */
    public function setMaxHeight(int $maxHeight): KComponent
    {
        $this->maxHeight=$maxHeight;
        return $this;
    }

    /**
     * Définit une largeur maximale 
     * Valeur par défaut : null
     * @param int $maxWidth
     * @return KComponent
     */
    public function setMaxWidth(int $maxWidth): KComponent
    {
        $this->maxWidth=$maxWidth;
        return $this;
    }

    /**
     * Définit une hauteur minimale
     * Valeur par défaut : null
     * @param int $minHeight
     * @return KComponent
     */
    public function setMinHeight(int $minHeight): KComponent
    {
        $this->minHeight=$minHeight;
        return $this;
    }

    /**
     * Définit une largeur minimale
     * Valeur par défaut : null
     * @param int $minWidth
     * @return KComponent
     */
    public function setMinWidth(int $minWidth): KComponent
    {
        $this->minWidth=$minWidth;
        return $this;
    }

    /**
     * Rédéfinit le titre
     * Valeur par défaut : constructeur
     * @param string $title
     * @return KComponent
     */
    public function setTitle(string $title): KComponent
    {
        $this->title=$title;
        return $this;
    }
    
    #[\Override]
    public static function testMe(): ?static
    {
        //string $idDiv="resizable",string $title="Resizable",?int $height=null
        $class=new static("resizable_id","",180);
        $class->addHtmlComponent('<div style="background-color:#000000; width:150px; height:150px;"></div>');
        return $class;
    }      
}