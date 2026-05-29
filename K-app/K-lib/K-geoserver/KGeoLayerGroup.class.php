<?php declare(strict_types=1);

class KGeoLayerGroup
{
    protected $type = "";
    protected $name = "";
    protected $style_name = "";
    protected $mode="";
    protected $projection= "";
    protected $bbox=array("0","0","0","0");
    protected $default_style = null;
    protected $map_item=null;
    protected $title= "";
    protected $abstractTxt="";
    protected $keywords= null;
    protected $metadataLinks = null;
    protected string $workspace_name="";
    protected string $featureType="";
    protected string $name_with_workspace="";
    
    function __construct($workspace_name = "")
    {
        $this->workspace_name = $workspace_name;
    }

    function getMode()
    {
        return $this->mode;
    }

    function setMode($mode)
    {
        $this->mode = $mode;
    }

        function getType()
    {
        return $this->type;
    }

    function getName()
    {
        return $this->name;
    }

    function getStyle_name()
    {
        return $this->style_name;
    }

    function getFeatureType()
    {
        return $this->featureType;
    }

    function getName_with_workspace()
    {
        return $this->name_with_workspace;
    }

    function setType(string $type)
    {
        $this->type = $type;
    }

    function setName(string $name)
    {
        $this->name = $name;
        if ($this->workspace_name != "")
        {
            $this->name_with_workspace = $this->workspace_name . ":" . $this->name;
        }
    }

    function setStyle_name(string $style_name)
    {
        $this->style_name = $style_name;
    }

    function setFeatureType(string $featureType)
    {
        $this->featureType = $featureType;
    }

    function setName_with_workspace(string $name_with_workspace)
    {
        $this->name_with_workspace = $name_with_workspace;
    }

    function getWorkspace_name()
    {
        return $this->workspace_name;
    }

    function setWorkspace_name(string $workspace_name)
    {
        $this->workspace_name = $workspace_name;
    }
    
    function getProjection() : string
    {
        return $this->projection;
    }

    function getBbox() : array
    {
        return $this->bbox;
    }
//
    function setProjection(string $projection)
    {
        $this->projection = $projection;
    }

    function setBbox(array $bbox) : bool
    {
        if(count($bbox)==4)
        {
            $this->bbox = $bbox;
            return true;
        }
        return false;
    }
    
    function getBboxToString() : string
    {
        $string= "";
        if(count($this->bbox)==4)
        {
            $string.=$this->bbox[0].",".$this->bbox[1].",".$this->bbox[2].",".$this->bbox[3];
        }
        return $string;        
    }
    
    function addGroupItem(KGeoLayerGroupItem $item)
    {
        if(is_null($this->map_item))
        {
            $this->map_item=new HashMap();
        }        
        $this->map_item->put($item->getName(), $item);
    }
    
    function getGroupItemByName(string $name) : ?KGeoLayerGroupItem
    {
        if(!is_null($this->map_item))
        {
            return $this->map_item->get($name);                    
        }
        return null;
    }
    
    function getAllGroupItems() : ?HashMap
    {
        return $this->map_item;
    }
    
    
    function initKeyWordsFromArray(?array $keywords) : bool
    {
        $status=false;
        if(is_array($keywords) && count($keywords)>0)
        {
            $this->keywords=array();
            foreach ($keywords as $keyword)
            {
                $geoKeyword=new KGeoKeyWord();
                if($geoKeyword->initByString($keyword))
                {
                    $this->keywords[]=$geoKeyword;
                }
            }
            $status=true;
        }
        return $status;
    }
    
    function addMetadataLink(KGeoMetaDataLink $metadata)
    {
        if(is_null($this->metadataLinks))
        {
            $this->metadataLinks=[];
        }
        $this->metadataLinks[]=$metadata;
    }
    
    
    function getTitle() : ?string
    {
        return $this->title;
    }

    function getAbstractTxt() : ?string
    {
        return $this->abstractTxt;
    }

    function getKeywords() : ?array
    {
        return $this->keywords;
    }

    function getMetadataLinks() : ?array
    {
        return $this->metadataLinks;
    }

    function setTitle(?string $title)
    {
        $this->title = $title;
    }

    function setAbstractTxt(?string $abstractTxt)
    {
        $this->abstractTxt = $abstractTxt;
    }

    function setKeywords(?array $keywords)
    {
        $this->keywords = $keywords;
    }

    function setMetadataLinks(?array $metadataLinks)
    {
        $this->metadataLinks = $metadataLinks;
    }

        
    
    function getPreviewImageLink(string $base_url = ""): string
    {
        $name_layer = urlencode($this->workspace_name . ":" . $this->name);
        return $base_url . $this->workspace_name . "/wms/reflect?layers=" . $name_layer . "";
    }


    function toString(string $delimitor = "\n"): string
    {
        $string = "";
        $vars = get_object_vars($this);
        foreach ($vars as $var_key => $var_value)
        {
            if(is_array($var_value))
            {
                $string .= "" . $var_key . "=>" . print_r($var_value,true) . $delimitor;
            }
            else if($var_value instanceof ArrayList)
            {
                $string .= "" . $var_key . "=>" . $var_value->toString($delimitor) . $delimitor;
            }
            else if($var_value instanceof HashMap)
            {
                $string .= "" . $var_key . "=>" . $var_value->toString($delimitor) . $delimitor;
            }            
            else
            {
                $string .= "" . $var_key . "=>" . $var_value . $delimitor;
            }
        }
        return $string;
    }

}