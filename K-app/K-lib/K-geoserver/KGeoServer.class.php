<?php declare(strict_types=1);

require_once 'KGeoDataStore.class.php';
require_once 'KGeoListDataStores.class.php';

require_once 'KGeoLayer.class.php';
require_once 'KGeoListLayers.class.php';

require_once 'KGeoLayerGroup.class.php';
require_once 'KGeoLayerGroupItem.class.php';
require_once 'KGeoListLayerGroups.class.php';
require_once 'KGeoKeyWord.class.php';
require_once 'KGeoMetaDataLink.class.php';

require_once 'KGeoStyle.class.php';

require_once __ROOT__ . '/K-composer/vendor/autoload.php';

use EFA\GeoClient\GeoClient;
use EFA\GeoClient\Entity\DataStore;
use EFA\GeoClient\Repository\RepositoryFactory;
use EFA\GeoClient\Manager\ManagerFactory;
use EFA\GeoClient\Entity\Workspace;
use EFA\GeoClient\Manager\EntityManager;
use EFA\GeoClient\Manager\LayerManager;
use EFA\GeoClient\Manager\StyleManager;
use EFA\GeoClient\Manager\RootManager;
use EFA\GeoClient\Entity\Layer;
use EFA\GeoClient\Entity\Feature;
use EFA\GeoClient\Manager\WorkspaceManager;

class KGeoServer
{
    private $is_connected = false;
    private $errorArray = array();
    protected $client = null;
    /* @var $workspace EFA\GeoClient\Entity\Workspace */
    protected $workspace = null;
    protected $url = "";
    protected $user = "";
    protected $password = "";
    protected $workspace_name = "";

    function __construct()
    {
        
    }

    private function isConnected(): bool
    {
        return $this->is_connected;
    }

    public function connect(string $url, string $user, string $password): bool
    {
        $status = false;
        $this->client = null;
        $this->workspace = null;
        $this->errorArray = array();
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
        $client = GeoClient::create($url . "rest/", $user, $password);
        if ($client->isUp())
        {
            $this->client = $client;
            $status = true;
        }
        return $status;
    }

    public function setWorkSpace(string $workspace_name): bool
    {
        if (!is_null($this->client))
        {
            $this->workspace_name = $workspace_name;
            $workspace = RootManager::getWorkspace($workspace_name);
            if (!is_null($workspace))
            {
                /* @var $workspace EFA\GeoClient\Entity\Workspace */
                $this->workspace = $workspace;
                $this->is_connected = true;
                return true;
            }
        }
        $this->workspace = null;
        $this->is_connected = false;
        return false;
    }

    public function getWorkspacesNamesArray(): ?array
    {
        if (!is_null($this->client))
        {
            $workspaces_names = array();
            $workspaces = RootManager::getWorkspaces();
            foreach ($workspaces as $workspace_name => $workspace_data)
            {
                $workspaces_names[] = $workspace_name;
            }
            return $workspaces_names;
        }
        return null;
    }

    /* #########################################################################
     * DATA STORE PROCESSING
     */
    public function getDataStores(): ?KGeoListDataStores
    {
        if ($this->is_connected)
        {
            $datastores = null;
            if (!is_null($this->workspace))
            {
                $datastores = new KGeoListDataStores();

                /* @var $datastore EFA\GeoClient\Entity\DataStore */
                foreach ($this->workspace->getDataStores() as $datastore_name => $datastore)
                {
                    $kDatastore = $this->initKGeoDataStoreFromStore($datastore);
                    $datastores->addDataStore($datastore_name, $kDatastore);
                }
            }
            return $datastores;
        }
        return null;
    }
    
    private function initKGeoDataStoreFromStore(EFA\GeoClient\Entity\DataStore $dataStore): ?KGeoDataStore
    {
        $kDatastore = new KGeoDataStore();
        $kDatastore->setDatabase($dataStore->getConnectionParams()->getValue("database"));
        $kDatastore->setDbtype($dataStore->getConnectionParams()->getValue("dbtype"));
        $kDatastore->setHost($dataStore->getConnectionParams()->getValue("host"));
        $kDatastore->setNamespace($dataStore->getConnectionParams()->getValue("namespace"));
        $kDatastore->setPasswd($dataStore->getConnectionParams()->getValue("passwd"));
        $kDatastore->setPort($dataStore->getConnectionParams()->getValue("port"));
        $kDatastore->setSchema($dataStore->getConnectionParams()->getValue("schema"));
        $kDatastore->setUser($dataStore->getConnectionParams()->getValue("user"));
        return $kDatastore;
    }
    
    public function getDataStore(string $store_name): ?KGeoDataStore
    {
        if ($this->isConnected())
        {
            $data_store = $this->workspace->getDataStore($store_name);
            if (!is_null($data_store))
            {
                return $this->initKGeoDataStoreFromStore($data_store);
            }
        }
        return null;
    }

    public function getDataStoreByWorkspaceName(string $workspace_name, string $layer_name): ?KGeoDataStore
    {
        if ($this->setWorkSpace($workspace_name))
        {
            return $this->getDataStore($layer_name);
        }
        return null;
    }
    
    public function getDataStoreByFullName(string $full_name): ?KGeoDataStore
    {
        $two_parts = \explode(":", $full_name);
        if (count($two_parts) == 2)
        {
            return $this->getDataStoreByWorkspaceName($two_parts[0], $two_parts[1]);
        }
        return null;
    } 
    
    /* #########################################################################
     *  LAYER GROUP PROCESSING
     */

    public function getLayerGroups(): ?KGeoListLayerGroups
    {
        $groupLayers = null;
        if ($this->isConnected())
        {
            $groupLayers = new KGeoListLayerGroups();

            $wGroupLayers = $this->workspace->getLayerGroups();
            $this->logError("getGroupLayers() for " . $this->workspace_name);
            /* @var $layer EFA\GeoClient\Entity\Layer */

            if(!is_null($wGroupLayers))
            {
                foreach ($wGroupLayers as $groupLayer)
                {
                    //echo "<pre>".print_r($groupLayer,true)."</pre>";
                    $kGroupLayers = $this->initKLayerGroupFromLayer($groupLayer);
                    $groupLayers->addGroupLayer($kGroupLayers->getName(), $kGroupLayers);
                }
            }
            //exit();
        }
        return $groupLayers;
    }
    
    private function initKLayerGroupFromLayer(EFA\GeoClient\Entity\LayerGroup $groupLayer): ?KGeoLayerGroup
    {
//        var_dump($groupLayer);
//        exit;
        $kGroupLayer = new KGeoLayerGroup($this->workspace_name);
        
        $kGroupLayer->setName($groupLayer->getName());
        
        $kGroupLayer->setMode($groupLayer->getMode());
        
        /* @var $bounds EFA\GeoClient\Entity\BoundingBox */
        $bounds = $groupLayer->getBounds();
        if(!is_null($bounds))
        {
            $kGroupLayer->setBbox($bounds->bboxArray());
            $kGroupLayer->setProjection($bounds->projection);
        }  
        
        $kGroupLayer->setTitle($groupLayer->getTitle());
        $kGroupLayer->setAbstractTxt($groupLayer->getAbstractTxt());        
        
        $kGroupLayer->initKeyWordsFromArray($groupLayer->getKeyWords());
        
        $arrayMetaDataLinks=$groupLayer->getMetaDataLinks();
        if(is_array($arrayMetaDataLinks))
        {
            /* @var $metaDataLink EFA\GeoClient\Entity\MetadataLink */
            foreach ($arrayMetaDataLinks as $metaDataLink)
            {
                $kMetaDataLink= new KGeoMetaDataLink();
                $kMetaDataLink->setType($metaDataLink->getType());
                $kMetaDataLink->setMetaDataType($metaDataLink->getMetadataType());
                $kMetaDataLink->setContent($metaDataLink->getContent());
                $kGroupLayer->addMetadataLink($kMetaDataLink);
            }
        }
        
        return $kGroupLayer;     
    }    
    
    public function getLayerGroup(string $layer_name): ?KGeoLayerGroup
    {
        if ($this->isConnected())
        {
            $groupLayer = $this->workspace->getLayerGroup($layer_name);
            if (!is_null($groupLayer))
            {
                return $this->initKLayerGroupFromLayer($groupLayer);
            }
        }
        return null;
    }

    public function getLayerGroupByWorkspaceName(string $workspace_name, string $layer_name): ?KGeoLayerGroup
    {
        if ($this->setWorkSpace($workspace_name))
        {
            return $this->getLayerGroup($layer_name);
        }
        return null;
    }
    
    public function getLayerGroupByFullName(string $full_name): ?KGeoLayerGroup
    {
        $two_parts = \explode(":", $full_name);
        if (count($two_parts) == 2)
        {
            return $this->getLayerGroupByWorkspaceName($two_parts[0], $two_parts[1]);
        }
        return null;
    }  
    
    public function initLayerGroupItems(KGeoLayerGroup $layerGroup) : bool
    {
        if ($this->isConnected())
        {
            if ($this->setWorkSpace($layerGroup->getWorkspace_name()))
            {
                /* @var $groupLayer EFA\GeoClient\Entity\LayerGroup */
                $groupLayer = $this->workspace->getLayerGroup($layerGroup->getName());
                if(!is_null($groupLayer))
                {
                    $arrayLayer = $groupLayer->getLayers();
                    //var_dump($arrayLayer);
                    $arrayStyle = $groupLayer->getStyles();
//                    var_dump($arrayStyle);
                    
                    if(is_array($arrayLayer)&& is_array($arrayStyle)&&count($arrayLayer)==count($arrayStyle))
                    {
                        for ($i = 0; $i < count($arrayLayer); $i++)
                        {
                            $geoStyle=null;
                            //EFA\GeoClient\Entity\Style $style
                            if($arrayStyle[$i] instanceof EFA\GeoClient\Entity\Style)
                            {
                                $geoStyle=$this->makeKGeoStyle($arrayStyle[$i]);
                            }
                            
                            $item=new KGeoLayerGroupItem();
                            if($arrayLayer[$i] instanceof EFA\GeoClient\Entity\Layer)
                            {
                                $item->initAsLayer($this->initKLayerFromLayer($arrayLayer[$i]),$geoStyle);
                            }
                            else if($arrayLayer[$i] instanceof EFA\GeoClient\Entity\LayerGroup)
                            {
                                $item->initAsLayerGroup($this->initKLayerGroupFromLayer($arrayLayer[$i]), $geoStyle);
                            }
                            else
                            {
                                var_dump($arrayLayer[$i]);
                                exit();
                                //UNKNOWN TYPE
                            }
                            $layerGroup->addGroupItem($item);
                        }
                        return true;
                    }
                }
            }
        }   
        return false;
    }
    
    /* #########################################################################
     *  LAYER PROCESSING
     */
    
    public function getLayers(): ?KGeoListLayers
    {
        $layers = null;
        if ($this->isConnected())
        {
            $layers = new KGeoListLayers();
            $wLayers = $this->workspace->getLayers();
            $this->logError("getLayers() for " . $this->workspace_name);
            /* @var $layer EFA\GeoClient\Entity\Layer */
            if(!is_null($wLayers))
            {
                foreach ($wLayers as $layer)
                {
                    $kLayer = $this->initKLayerFromLayer($layer);
                    $layers->addLayer($kLayer->getName(), $kLayer);
                }
            }
        }
        //$this->logError("getLayers() for ".$this->workspace_name);
        return $layers;
    }

    public function getLayer(string $layer_name): ?KGeoLayer
    {
        if ($this->isConnected())
        {
            $layer = $this->workspace->getLayer($layer_name);
            if (!is_null($layer))
            {
                return $this->initKLayerFromLayer($layer);
            }
        }
        return null;
    }

    public function getLayerByWorkspaceName(string $workspace_name, string $layer_name): ?KGeoLayer
    {
        if ($this->setWorkSpace($workspace_name))
        {
            return $this->getLayer($layer_name);
        }
        return null;
    }

    public function getLayerByFullName(string $full_name): ?KGeoLayer
    {
        $two_parts = \explode(":", $full_name);
        if (count($two_parts) == 2)
        {
            return $this->getLayerByWorkspaceName($two_parts[0], $two_parts[1]);
        }
        return null;
    }

    private function initKLayerFromLayer(EFA\GeoClient\Entity\Layer $layer): ?KGeoLayer
    {
        $kLayer = new KGeoLayer($this->workspace_name);
        $kLayer->setName($layer->getName());
        $kLayer->setOpaque($layer->getOpaque());
        
        $style = $layer->getDefaultStyle();
        $kLayer->addDefaultGeoStyle($this->makeKGeoStyle($style));

        $kLayer->setType($layer->getType());
        
        /* @var $definition EFA\GeoClient\Entity\Definition */
        $definition = $layer->getDefinition();     
        
        $this->logError("getDefinition() for Layer " . $kLayer->getName_with_workspace());
        if (!is_null($definition))
        {          
            $kLayer->setTitle($definition->getTitle());
            $kLayer->setAbstract($definition->getAbstract());           
            $kLayer->initKeyWordsFromArray($definition->getKeyWords());
            
            $arrayMetaDataLinks=$definition->getMetaDataLinks();
            if(is_array($arrayMetaDataLinks))
            {
                /* @var $metaDataLink EFA\GeoClient\Entity\MetadataLink */
                foreach ($arrayMetaDataLinks as $metaDataLink)
                {
                    $kMetaDataLink= new KGeoMetaDataLink();
                    $kMetaDataLink->setType($metaDataLink->getType());
                    $kMetaDataLink->setMetaDataType($metaDataLink->getMetadataType());
                    $kMetaDataLink->setContent($metaDataLink->getContent());
                    $kLayer->addMetadataLink($kMetaDataLink);
                }
            }
            
            $box = $definition->getNativeBBox();
            $this->logError("getNativeBBox() for Layer " . $kLayer->getName_with_workspace());
            if (!is_null($box))
            {
                $kLayer->setBbox(array($box->minX, $box->minY, $box->maxX, $box->maxY));
            }

            if ($layer->getType() == EFA\GeoClient\Entity\Layer::VECTOR)
            {
                $kLayer->setGeometry_type($definition->getGeometry());
                
                /* @var $store EFA\GeoClient\Entity\DataStore */
                $store = $definition->getStore();
                $kLayer->setDataBaseStore($store->getName());
                          
                $attributes = $definition->getAttributes();
                $this->logError("getAttributes() for Layer " . $kLayer->getName_with_workspace());
                $kLayer->addKfieldList($this->makeKfieldsFromAttributes($kLayer->getName_with_workspace(),$layer, $attributes));
            }
            elseif ($layer->getType() == EFA\GeoClient\Entity\Layer::RASTER)
            {
                /* @var $store EFA\GeoClient\Entity\CoverageStore */
                $store = $definition->getStore();
                $kLayer->setFile_path($store->getFilePath());
                $kLayer->setFile_type($store->getType());
            }
            elseif ($layer->getType() == EFA\GeoClient\Entity\Layer::WMS)
            {
                /* @var $store EFA\GeoClient\Entity\WMSStore */
                $store = $definition->getStore();
                $kLayer->setCapabilitiesURL($store->getCapabilitiesURL());               
            }            
            $kLayer->setProjection($definition->getDataProjection());
        }
        return $kLayer;
    }

    private function makeKfieldsFromAttributes(string $layer_name,EFA\GeoClient\Entity\Layer $layer, ?array $attributes): ?ArrayList
    {
        if (!is_null($attributes) && is_array($attributes) && count($attributes))
        {
            $list = new ArrayList();
            /* @var $attribute EFA\GeoClient\Entity\DefinitionAttribute */
            foreach ($attributes as $attribute)
            {
                $type = null;
                if ($attribute->binding == "java.lang.Integer" || $attribute->binding == "java.math.BigDecimal" || $attribute->binding == "java.lang.Short")
                {
                    $type = KField::$INTEGER;
                }
                else if ($attribute->binding == "java.lang.String")
                {
                    $type = KField::$VARCHAR;
                }
                else if ($attribute->binding == "java.lang.Long" || $attribute->binding == "java.lang.Double" || $attribute->binding == "java.lang.Float")
                {
                    $type = KField::$DOUBLE;
                }
                else if ($attribute->binding == "java.lang.Boolean")
                {
                    $type = KField::$BOOL;
                }
                else if ($attribute->binding == "org.locationtech.jts.geom.Polygon")
                {
                    $type = KField::$POLYGON;
                }
                else if ($attribute->binding == "org.locationtech.jts.geom.LineString")
                {
                    $type = KField::$LINESTRING;
                }
                else if ($attribute->binding == "org.locationtech.jts.geom.MultiLineString")
                {
                    $type = KField::$MULTILINESTRING;
                }
                else if ($attribute->binding == "org.locationtech.jts.geom.MultiPoint")
                {
                    $type = KField::$MULTIPOINT;
                }
                else if ($attribute->binding == "org.locationtech.jts.geom.MultiPolygon")
                {
                    $type = KField::$MULTIPOLYGON;
                }
                else if ($attribute->binding == "org.locationtech.jts.geom.Point")
                {
                    $type = KField::$POINT;
                }
                else if ($attribute->binding == "org.locationtech.jts.geom.Geometry")
                {
                    $type = KField::$GEOMETRY;
                }
                else if ($attribute->binding == "java.sql.Timestamp")
                {
                    $type = KField::$TIMESTAMP;
                }
                else if ($attribute->binding == "java.sql.Date")
                {
                    $type = KField::$DATE;
                }
                else if ($attribute->binding == "java.sql.Time")
                {
                    $type = KField::$TIME;
                }
                else
                {
                    echo "<pre>" . print_r($layer, true) . "</pre>";
                    echo "<pre>" . print_r($attribute, true) . "</pre>";
                    echo "<br />" . $attribute->binding;
                    exit();
                }

                $kField = KField::factoryKField($type);

                $kField->setName($attribute->name);


                if ($attribute->nillable == 1)
                {
                    $kField->setIs_null(true);
                }
                else
                {
                    $kField->setIs_null(false);
                }

                $list->add($kField);
            }
            return $list;
        }
        else
        {
            $this->logException("makeKfieldsFromAttributes() for Layer ".$layer_name,"NO ATTRIBUTES");
        }
        return null;
    }
          
    /* #########################################################################
     *  STYLE PROCESSING
     */
    
    private function makeKGeoStyle(EFA\GeoClient\Entity\Style $style) : ?KGeoStyle
    {
        $kStyle=null;
        if(!is_null($style))
        {
            $kStyle=new KGeoStyle($this->workspace_name);
            $kStyle->setName($style->getName());
            $kStyle->setFilename($style->getFilename());
            $kStyle->setFormat($style->getFormat());
            $version=$style->getStyleVersion();
            if(!is_null($version))
            {
                $kStyle->setVersion($version->getVersion());
            }
        }
        return $kStyle;
    }
    
    public function getStyle(string $style_name): ?KGeoStyle
    {
        if ($this->isConnected())
        {
            $style = $this->workspace->getStyle($style_name);
            if (!is_null($style))
            {
                return $this->makeKGeoStyle($style);
            }
        }
        return null;
    }

    public function getStyleByWorkspaceName(string $workspace_name, string $style_name): ?KGeoStyle
    {
        if ($this->setWorkSpace($workspace_name))
        {
            return $this->getStyle($style_name);
        }
        return null;
    }

    public function getStyleByFullName(string $full_name): ?KGeoStyle
    {
        $two_parts = \explode(":", $full_name);
        if (count($two_parts) == 2)
        {
            return $this->getStyleByWorkspaceName($two_parts[0], $two_parts[1]);
        }
        return null;
    }  
    
    public function getStyleFile(string $style_name): ?string
    {
        if ($this->isConnected())
        {
            $style = $this->workspace->getStyle($style_name);
            if (!is_null($style))
            {
                return $style->getStyleData();
//                return $this->makeKGeoStyle($style);
            }
        }
        return null;
    }
    public function getStyleFileByWorkspaceName(string $workspace_name, string $style_name): ?string
    {
        if ($this->setWorkSpace($workspace_name))
        {
            return $this->getStyleFile($style_name);
        }
        return null;
    }

    public function getStyleFileByFullName(string $full_name): ?string
    {
        $two_parts = \explode(":", $full_name);
        if (count($two_parts) == 2)
        {
            return $this->getStyleFileByWorkspaceName($two_parts[0], $two_parts[1]);
        }
        return null;
    }      
    
    
    /* #########################################################################
     *  ERROR & LOGS
     */
    
    private function logException(string$label,string $content): void
    {
        $this->errorArray[$label] =[new Exception($content)];
    }

    private function logError(string $label): void
    {
        $arrayException = EFA\GeoClient\Exception\ExceptionHandler::pop();
        if (!is_null($arrayException) && is_array($arrayException) && count($arrayException) > 0)
        {
            $this->errorArray[$label] = $arrayException;
        }
    }

    public function flushErrors(): void
    {
        $this->errorArray=[];
    }

    public function errorToString($delimitor = "\n"): string
    {
        $string = "";
        $error_number=1;
        foreach ($this->errorArray as $labelError => $exceptionArray)
        {
            $string.= $error_number.") ".$labelError."=>".$delimitor;
            foreach ($exceptionArray as $exception)
            {
                if($exception instanceof JMS\Serializer\Exception\XmlErrorException)
                {
                    $string.= "<pre>".htmlentities($exception->getMessage())."</pre>".$delimitor;
                }
                else if($exception instanceof \Exception)
                {
                    $string.= $exception->getMessage()."".$delimitor;
                }
            }
            $error_number++;
        }
        return $string;
    }

}