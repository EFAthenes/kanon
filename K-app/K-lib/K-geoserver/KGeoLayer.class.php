<?php declare(strict_types=1);

class KGeoLayer
{
    protected $type = "";
    protected $name = "";
    protected $style_name = "";
    protected $featureType = "";
    protected $name_with_workspace = "";
    protected $workspace_name = "";
    protected $opaque = false;
    protected $queryable = false;
    protected $projection = "";
    protected $geometry_type = "";
    protected $bbox = array("0", "0", "0", "0");
    protected $list_kfields = null;
    protected $file_path = "";
    protected $file_type = "";
    protected $default_style = null;
    protected $capabilitiesURL="";
    protected $dataBaseStore="";
    protected $title= "";
    protected $abstract="";
    protected $keywords= null;
    protected $metadataLinks = null;    


    function __construct($workspace_name = "")
    {
        $this->workspace_name = $workspace_name;
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

    function getOpaque(): bool
    {
        return $this->opaque;
    }

    function getQueryable(): bool
    {
        return $this->queryable;
    }

    function setOpaque(bool $opaque)
    {
        $this->opaque = $opaque;
    }

    function setQueryable(bool $queryable)
    {
        $this->queryable = $queryable;
    }

    function getProjection(): string
    {
        return $this->projection;
    }

    function getGeometry_type(): string
    {
        return $this->geometry_type;
    }

    function getBbox(): array
    {
        return $this->bbox;
    }

    function setProjection(string $projection)
    {
        $this->projection = $projection;
    }

    function setGeometry_type(string $geometry_type)
    {
        $this->geometry_type = $geometry_type;
    }

    function getFile_path(): string
    {
        return $this->file_path;
    }

    function setFile_path(string $file_path)
    {
        $this->file_path = $file_path;
    }

    function getFile_type(): string
    {
        return $this->file_type;
    }

    function setFile_type(string $file_type)
    {
        $this->file_type = $file_type;
    }

    function setBbox(array $bbox): bool
    {
        if (count($bbox) == 4)
        {
            $this->bbox = $bbox;
            return true;
        }
        return false;
    }

    function getBboxToString(): string
    {
        $string = "";
        if (count($this->bbox) == 4)
        {
            $string .= $this->bbox[0] . "," . $this->bbox[1] . "," . $this->bbox[2] . "," . $this->bbox[3];
        }
        return $string;
    }

    function addKfieldList(?ArrayList $list): bool
    {
        if (!is_null($list))
        {
            $this->list_kfields = $list;
            return true;
        }
        return false;
    }

    function addDefaultGeoStyle(?KGeoStyle $kStyle)
    {
        if(!is_null($kStyle))
        {
            $this->style_name=$kStyle->getName();
            $this->default_style=$kStyle;
        }
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

    function getAbstract() : ?string
    {
        return $this->abstract;
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

    function setAbstract(?string $abstract)
    {
        $this->abstract = $abstract;
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
            if (is_array($var_value))
            {
                $string .= "" . $var_key . "=>" . print_r($var_value, true) . $delimitor;
            }
            else if ($var_value instanceof ArrayList)
            {
                $string .= "" . $var_key . "=>" . $var_value->toString($delimitor) . $delimitor;
            }
            else if ($var_value instanceof KGeoStyle)
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
    
    function getCapabilitiesURL()
    {
        return $this->capabilitiesURL;
    }

    function setCapabilitiesURL($capabilitiesURL)
    {
        $this->capabilitiesURL = $capabilitiesURL;
    }

    function getDataBaseStore()
    {
        return $this->dataBaseStore;
    }

    function setDataBaseStore($dataBaseStore)
    {
        $this->dataBaseStore = $dataBaseStore;
    }
    
}