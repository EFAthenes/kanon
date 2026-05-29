<?php declare(strict_types=1);

class KGeoLayerGroupItem
{
    public const KGEO_ITEM_LAYER="KGEO_ITEM_LAYER";
    public const KGEO_ITEM_LAYER_GROUP="KGEO_ITEM_LAYER_GROUP";
    protected $item;
    protected $type;
    protected $style;

    function __construct()
    {
        
    }
    
    function init($unknown_type_layer, ?KGeoStyle $style) : bool
    {
        if($unknown_type_layer instanceof KGeoLayer)
        {
            $this->initAsLayer($unknown_type_layer, $style);
            return true;
        }
        else if($unknown_type_layer instanceof KGeoLayerGroup)
        {
            $this->initAsLayerGroup($unknown_type_layer, $style);
            return true;
        }
        return false;
    }
    
    function initAsLayer(KGeoLayer $layer, ?KGeoStyle $style)
    {
        $this->item=$layer;
        $this->style=$style;
        $this->type=self::KGEO_ITEM_LAYER;
    }
    
    function initAsLayerGroup(KGeoLayerGroup $layerGroup, ?KGeoStyle $style)
    {
        $this->item=$layerGroup;
        $this->style=$style;
        $this->type=self::KGEO_ITEM_LAYER_GROUP;
    } 
    
    function getItem()
    {
        return $this->item;
    }
    
    function getLayer() :?KGeoLayer
    {
        if($this->type==self::KGEO_ITEM_LAYER)
        {
            return $this->item;
        }
        return null;
    }
    
    function getLayerGroup() : ?KGeoLayerGroup
    {
        if($this->type==self::KGEO_ITEM_LAYER_GROUP)
        {
            return $this->item;
        }  
        return null;
    }
    
    function getStyle() : ?KGeoStyle
    {
        return $this->style;
    }
    
    function getName() : ?string
    {
        if($this->type==self::KGEO_ITEM_LAYER)
        {
            return $this->item->getName();
        }
        else if($this->type==self::KGEO_ITEM_LAYER_GROUP)
        {
            return $this->item->getName();
        }
        return null;
    }
    
    function toString(string $delimitor = "\n"): string
    {
        $string = "KGeoLayerGroupItem =>" . $this->getName() . $delimitor;
        if ($this->type == self::KGEO_ITEM_LAYER)
        {
            $string.="LAYER =".$this->item->toString($delimitor);
        }
        else if ($this->type == self::KGEO_ITEM_LAYER_GROUP)
        {
            $string.="LAYERGROUP =".$this->item->toString($delimitor);
        }
        
        if(!is_null($this->style))
        {
            $string.="STYLE =".$this->style->toString($delimitor);
        }
        return $string;
    }             
   
}