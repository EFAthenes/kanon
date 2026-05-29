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
 * Description of OAI_PMH
 *
 * -> Abstract class managing OAI protocol
 * 
 * ressource : http://www.openarchives.org/OAI/openarchivesprotocol.html
 * 
 * @author louis.mulot
 */
abstract class OAI_PMH
{
    protected const int OAI_NB_ITEMS=100;
    private const string VERB="verb";
    private const string RESUMPTION_TOKEN="resumptionToken";
    private const string IDENTIFIER="identifier";
    private const string FROM="from";
    private const string UNTIL="until";
    private const string METADATA_PREFIX="metadataPrefix";
    private const string SET="set";
    private const string OFFSET="offset";
    protected const string VERB_LIST_SETS="ListSets";
    protected const string VERB_LIST_METADATA_FORMATS="ListMetadataFormats";
    protected const string VERB_IDENTIFY="Identify";
    protected const string VERB_GET_RECORD="GetRecord";
    protected const string VERB_LIST_IDENTIFIERS="ListIdentifiers";
    protected const string VERB_LIST_RECORDS="ListRecords";
    public const string META_OAI_DC="oai_dc";
    public const string META_OLAC="olac";
    public const string META_PERSEUS="perseus";
    public const string META_OAI_MARC="oai_marc";
    public const string META_OAI_EAD="oai_ead";
    public const string IDENTIFIER_DEFAULT_SEPARATOR=":";

    private int $oai_nb_items=0;
    /**
     * 
     * @var array<int,string>
     */
    public array $listOfPossibleMetaData=[self::META_OAI_DC,self::META_OLAC,self::META_PERSEUS,self::META_OAI_MARC,self::META_OAI_EAD];
    private string $identifierTag="identifier";
    private string $adminEmail="";
    private string $url="";
    private string $responseDate="";
    private string $repositoryName="";
    private string $protocolVersion="2.0";
    private string $schema='xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd" xmlns:mml="http://www.w3.org/1998/Math/MathML" xmlns:skos="http://www.w3.org/2004/02/skos/core" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" ';
    private string $granularity="YYYY-MM-DDThh:mm:ssZ";
    private string $deletedRecord="no";
    private string $xsl_url="";
    private string $verb="";
    private string $response_xml="";
    //private array $sets_name=[];
    /**
     * 
     * @var array<string,OAI_PMH_Set>
     */
    private array $sets=[];
    /**
     * 
     * @var array<string,OAI_PMH_Identifier>
     */
    private array $identifiers=[];
    private string $selectedIdentifier="";
    private string $identifierSeparator=":";
    
    private bool $wrongIdentifier=false;
    private bool $wrongIdentifierName=false;
    private bool $wrongArgumentsSetId=false;
    private bool $wrongId=false;
    private bool $wrongSet=false;
    private string $errorString="";
    private bool $has_error=false;
    private int $max_items=0;
    // ResumptionToken Varaibles
    private ?string $from=null;
    private ?string $until=null;
    private ?string $set=null;
    private ?int $offset=null;
    private ?string $metadata_prefix=null;
    /**
     * 
     * @var array<string,mixed>
     */
    private array $receiveInput=[];
    private string $debugString="";

    public function __construct()
    {
        $this->oai_nb_items=self::OAI_NB_ITEMS;
        $this->responseDate=date("Y-m-d")."T".date("H:i:s")."Z";
    }

    public function basicInitialization(string $adminEmail,string $url,string $repositoryName): OAI_PMH
    {
        $this->setAdminEmail($adminEmail);
        $this->setUrl($url);
        $this->setRepositoryName($repositoryName);
        return $this;
    }

    public function setXSL_URL(string $url) : void
    {
        $this->xsl_url='<?xml-stylesheet type="text/xsl" href="'.$url.'"?>
';
    }
    
    public function setNbItems(int $nb) : bool
    {
        if($nb>0 &&$nb<10000)
        {
            $this->oai_nb_items=$nb;
            return true;
        }
        return false;
    }
    
    public function getNbItems() : int
    {
        return $this->oai_nb_items;
    }    

    /**
     * 
     * @param string $identifier
     * @param array<int,string> $metadatas
     * @return bool
     */
    public function addIdentifierTag(string $identifier,array $metadatas): bool
    {
        if(!array_key_exists($identifier,$this->identifiers))
        {
            if(count($metadatas)==0)
            {
                $this->errorString="Metadata array empty";
                return false;
            }
            foreach($metadatas as $metadata)
            {
                if(!$this->isMetadataListed($metadata))
                {
                    $this->errorString="Unknown metadata => ".$metadata;
                    return false;
                }
            }
            $this->identifiers[$identifier]=new OAI_PMH_Identifier($identifier,$metadatas);
            return true;
        }
        $this->errorString="Identifier already present";
        return false;
    }

    /**
     * 
     * @param string $setName
     * @param string $setSpec
     * @param string $setDescription
     * @param array<int,string> $metaDataPrefixes
     * @param string|null $className
     * @return bool
     */
    public function addSet(string $setName,string $setSpec,string $setDescription="",array $metaDataPrefixes=[],?string $className=null): bool
    {
        if(!array_key_exists($setSpec,$this->sets))
        {
            $this->sets[$setSpec]=new OAI_PMH_Set($setName,$setSpec,$setDescription,$metaDataPrefixes,$className);
            return true;
        }
        return false;
    }

    protected function getVerb() : string
    {
        return $this->verb;
    }

    protected function getSetByName(?string $set): ?OAI_PMH_Set
    {
        // est ce que c'est possible que la $set passee n'existe pas ? ou bien elle est deja verifee quelque part dans le code..
        // todo: ... $this->sets n'est pas accessible..
        return $this->sets[$set] ?? null;
    }
    
    /**
     * 
     * @return array<string,OAI_PMH_Set>
     */
    protected function getArrayOfSets(): array
    {
        return $this->sets;
    }
    
    protected function getDefaultSet(): ?OAI_PMH_Set
    {
        $value=null;
        $key=array_key_first($this->sets);
        if(!is_null($key))
        {
            $value=$this->sets[$key];
        }
        return $value;
    }    

    public function getOAI_PMH_Identifier(): ?OAI_PMH_Identifier
    {
        if(count($this->identifiers)&&array_key_exists($this->selectedIdentifier,$this->identifiers))
        {
            return $this->identifiers[$this->selectedIdentifier];
        }
        return null;
    }

//    public function getIdentifierStringByDefault(): string
//    {
//        $value="";
//        if(array_key_exists($this->selectedIdentifier, $this->identifiers))
//        {
//            $value=$this->identifiers[$this->selectedIdentifier];
//            $value=strval($value);
//        }
//        return $value;
//    }

    private function checkInput(): bool
    {
        $this->verb="";
        if(isset($_GET['verb'])&&!empty($_GET['verb']))
        {
            $this->receiveInput=$_GET;
        }
        elseif(isset($_POST['verb'])&&!empty($_POST['verb']))
        {
            $this->receiveInput=$_POST;
        }
        else
        {
            return false;
        }
        $this->verb=$this->receiveInput['verb'];
        return true;
    }

    public function manageResponse(): bool
    {
        if($this->checkInput())
        {
            if($this->isVerb(self::VERB_LIST_SETS))
            {
                $this->listSets();
                return true;
            }
            if($this->isVerb(self::VERB_LIST_METADATA_FORMATS))
            {
                $this->listMetaDataFormats();
                return true;
            }
            if($this->isVerb(self::VERB_IDENTIFY))
            {
                $this->identify();
                return true;
            }
            if($this->isVerb(self::VERB_GET_RECORD))
            {
                $this->getRecord();
                return true;
            }
            if($this->isVerb(self::VERB_LIST_IDENTIFIERS))
            {
                $this->listIdentifiers();
                return true;
            }
            if($this->isVerb(self::VERB_LIST_RECORDS))
            {
                $this->listRecords();
                return true;
            }

            $this->unknownVerb();
            return true;
        }

        $this->noVerb();
        return true;
    }

    //--------------------------------------------------------------------------

    protected function unknownVerb(): void
    {
        $this->response_xml=$this->makeErrorResponse("","badVerb","Illegal OAI verb");
    }

    protected function noVerb(): void
    {
        $this->response_xml=$this->makeErrorResponse("","badVerb","No verb detected");
    }

    protected function makeErrorResponse(string $verb,string $code,string $errorDescription=""): string
    {
        $errorString="";
        if($errorDescription!="")
        {
            $errorString='<error code="'.addslashes($code).'">'.($errorDescription).'</error>';
        }
        else
        {
            $errorString='<error code="'.addslashes($code).'"/>';
        }

        $verbString="";
        if($verb!="")
        {
            $verbString='verb="'.$verb.'"';
        }

        $xml_error='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request '.$verbString.'>'.$this->url.'</request>
    '.$errorString.'
</OAI-PMH>
';
        $this->has_error=true;
        return $xml_error;
    }

    // API Methods
    private function getRecord(): void
    {
        $identifierString="";
        if(!$this->checkInputGetVars([self::VERB,self::IDENTIFIER,self::METADATA_PREFIX],true))
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"badArgument","Only '".self::VERB."', '".self::IDENTIFIER."', '".self::METADATA_PREFIX."' are allowed and mandatory");
        }
        else if(!$this->checkInputIdentifier($identifierString))
        {
            if($this->wrongIdentifier)
            {
                $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"idDoesNotExist","Wrong identifier");
            }           
            if($this->wrongIdentifierName)
            {
                $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"identifierNameDoesNotExist","Wrong identifier name");
            }
            else if($this->wrongArgumentsSetId)
            {
                $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"idOrSetNotFound","Wrong format for set and id. We need something like ':set/id'");
            }
            else if($this->wrongId)
            {
                $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"idNotRecognizedAsANumber","The id given is not recognized as a number");
            }
            else if($this->wrongSet)
            {
                $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"setDoesNotExist","Wrong set");
            }
        }
        else
        {
            if($this->checkMetaDataPrefix(self::VERB_GET_RECORD))
            {
                $oai_Identifier=$this->getOAI_PMH_Identifier();
                //Check if record exists ?
                if(!$this->checkIfGetRecordExists($identifierString,$oai_Identifier))
                {
                    if(empty($this->response_xml))
                    {
                        $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"idDoesNotExist","[B]The following identifier does not exist => ".$identifierString." '".$oai_Identifier->toString()."' ");
                    }
                }
                else
                {
                    //Print record
                    $record=$this->printGetRecord($identifierString,$oai_Identifier,$this->metadata_prefix);
                    
                    $this->response_xml='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request verb="GetRecord" identifier="'.$this->makeIdentifierRecord($identifierString).'" metadataPrefix="'.$this->metadata_prefix.'">'.$this->url.'</request>
    <GetRecord>
    '.$record.'
    </GetRecord>
</OAI-PMH>';
                }
            }
        }
    }

    private function listIdentifiers(): void
    {
        $resumptionToken=null;

        if($this->checkInputGet(self::RESUMPTION_TOKEN,$resumptionToken)&&
                !$this->checkInputGetVars([self::VERB,self::RESUMPTION_TOKEN],true))
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_IDENTIFIERS,"badArgument","When using resumptionToken no other arguments are allowed");
        }
        else if($this->checkInputGetVars([self::VERB,self::RESUMPTION_TOKEN],true)&&
                !$this->checkInputGetVarsAllowed([self::VERB,self::METADATA_PREFIX,self::FROM,self::UNTIL,self::SET,self::RESUMPTION_TOKEN])
        )
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_IDENTIFIERS,"badArgument","Only '".self::VERB."', '".self::METADATA_PREFIX."', '".self::FROM."', '".self::UNTIL."', '".self::SET."', '".self::RESUMPTION_TOKEN."' are allowed");
        }
        elseif($this->checkInputGetVars([self::VERB],true))
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_IDENTIFIERS,"badArgument","With '".self::VERB."' you need at least '".self::METADATA_PREFIX."' or '".self::RESUMPTION_TOKEN."'.");
        }
        else
        {

            $this->initRequestVariables();
            $this->setRequestVariables();
            if(!is_null($resumptionToken)&&!$this->initResumptionVariables($resumptionToken))
            {
                // bad resumption token
                $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_IDENTIFIERS,"badResumptionToken","The value of resumptionToken is incorrect.");
            }
            else if($this->checkRequestVariables(self::VERB_LIST_IDENTIFIERS))
            {
                //$this->debugString.="1____________\nFrom : ".$this->from."\nUntil : ".$this->until."\nSet : ".$this->set."\nOffest : ".$this->offset."\nMeta : ".$this->metadata_prefix."\n______________";
                if(!$this->checkListIdentifiersIsNotEmpty($this->from,$this->until,$this->set,$this->offset,$this->metadata_prefix))
                {
                    $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_IDENTIFIERS,"noRecordsMatch","The combination of the values of the from, until, and set arguments results in an empty list.");
                }
                else
                {
                    
                    $this->response_xml='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request verb="'.self::VERB_LIST_IDENTIFIERS.'" metadataPrefix="'.$this->metadata_prefix.'">'.$this->url.'</request>
    <ListIdentifiers>
'.$this->printListIdentifiers($this->from,$this->until,$this->set,$this->offset,$this->metadata_prefix).$this->printResumptionToken();

                    $this->response_xml.='               
    </ListIdentifiers>
</OAI-PMH>';
                }
            }
        }
    }

    private function printResumptionToken(): string
    {
        if(($this->offset+$this->oai_nb_items)>$this->max_items)
        {
            return "";
        }
        $datetime=new DateTime('tomorrow');
        $xml='
            <resumptionToken 
            expirationDate="'.$datetime->format('Y-m-d').'T'.$datetime->format('H:i:s').'Z'.'" 
            completeListSize="'.$this->max_items.'" 
            cursor="'.$this->offset.'">'.$this->makeResumptionToken().'</resumptionToken>     
';
        return $xml;
    }

    private function makeResumptionToken(): string
    {
        $resumptionToken="";
        if(!is_null($this->from))
        {
            $resumptionToken.="&".self::FROM."=".$this->from;
        }
        if(!is_null($this->until))
        {
            $resumptionToken.="&".self::UNTIL."=".$this->until;
        }
        if(!is_null($this->set))
        {
            $resumptionToken.="&".self::SET."=".$this->set;
        }
        if(!is_null($this->offset)&&$this->offset>=0)
        {
            $resumptionToken.="&".self::OFFSET."=".$this->offset;
        }
        if(!is_null($this->metadata_prefix))
        {
            $resumptionToken.="&".self::METADATA_PREFIX."=".$this->metadata_prefix;
        }

        return base64_encode($resumptionToken);
    }

    private function listRecords(): void
    {
        $resumptionToken=null;

        if($this->checkInputGet(self::RESUMPTION_TOKEN,$resumptionToken)&&
                !$this->checkInputGetVars([self::VERB,self::RESUMPTION_TOKEN],true))
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_RECORDS,"badArgument","When using resumptionToken no other arguments are allowed");
        }
        else if($this->checkInputGetVars([self::VERB,self::METADATA_PREFIX],false)&&!$this->checkInputGetVarsAllowed([self::VERB,self::METADATA_PREFIX,self::FROM,self::UNTIL,self::SET,self::RESUMPTION_TOKEN]))
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_RECORDS,"badArgument","Only '".self::VERB."', '".self::METADATA_PREFIX."', '".self::FROM."', '".self::UNTIL."', '".self::SET."', '".self::RESUMPTION_TOKEN."' are allowed");
        }
        elseif($this->checkInputGetVars([self::VERB],true))
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_RECORDS,"badArgument","With '".self::VERB."' you need at least '".self::METADATA_PREFIX."' or '".self::RESUMPTION_TOKEN."'.");
        }
        else
        {
            $this->initRequestVariables();
            $this->setRequestVariables();
            if(!is_null($resumptionToken)&&!$this->initResumptionVariables($resumptionToken))
            {
                // bad resumption token
                $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_RECORDS,"badResumptionToken","The value of resumptionToken is incorrect.");
            }
            else if($this->checkRequestVariables(self::VERB_LIST_RECORDS))
            {
                if(!$this->checkListRecordsIsNotEmpty($this->from,$this->until,$this->set,$this->offset,$this->metadata_prefix))
                {
                    $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_RECORDS,"noRecordsMatch","The combination of the values of the from, until, and set arguments results in an empty list.");
                }
                else
                {
                    $printListRecordsStringResult=$this->printListRecords($this->from,$this->until,$this->set,$this->offset,$this->metadata_prefix);
                    
                    if($this->has_error)
                    {
                       //Faut detecter qu'il y a eu une erreur et plutot afficher 
                       $this->response_xml.=$printListRecordsStringResult;
                    }
                    else
                    {
                        $this->response_xml='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request verb="'.self::VERB_LIST_RECORDS.'" metadataPrefix="'.$this->metadata_prefix.'">'.$this->url.'</request>
    <ListRecords>
'.$printListRecordsStringResult.$this->printResumptionToken();

                    $this->response_xml.='               
    </ListRecords>
</OAI-PMH>';
                    }
                }
            }
        }
    }

    private function listSets(): void
    {
        if(!$this->checkInputGetVars([self::VERB,self::RESUMPTION_TOKEN],true)&&
                !$this->checkInputGetVars([self::VERB],true)
        )
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_SETS,"badArgument","Only '".self::VERB."' and '".self::RESUMPTION_TOKEN."' are allowed");
        }
        else if(count($this->sets)==0)
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_SETS,"noSetHierarchy","This repository does not support sets");
        }
        else
        {
            $this->response_xml='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request verb="ListSets">'.$this->url.'</request>
    <ListSets>
';
            /* @var $set OAI_PMH_Set  */
            foreach($this->sets as $set)
            {
                $this->response_xml.='
         <set>
            <setSpec>'.$set->setSpec.'</setSpec>
            <setName>'.$set->setName.'</setName>
        </set>       
';
            }
            $this->response_xml.='
    </ListSets>
</OAI-PMH>';
        }
    }

    private function listMetaDataFormats(): void
    {
        $identifierString="";
        if(!$this->checkInputGetVars([self::VERB,self::IDENTIFIER],true)&&
                !$this->checkInputGetVars([self::VERB],true)
        )
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_METADATA_FORMATS,"badArgument","Only '".self::VERB."' and '".self::IDENTIFIER."' are allowed");
        }
        else if($this->checkInputIdentifier($identifierString))
        {
            
            $metadataString="";
            /* @var $oai_Identifier OAI_PMH_Identifier */
            $oai_Identifier=$this->getOAI_PMH_Identifier();
            foreach($oai_Identifier->metadatas as $metadata)
            {
                $metadataString.=$this->makeMetadataString($metadata);
            }
            $this->response_xml='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request verb="ListMetadataFormats" identifier="oai:'.$this->selectedIdentifier.':'.$identifierString.'">'.$this->url.'</request>
    <ListMetadataFormats>
        '.$metadataString.'
    </ListMetadataFormats>
</OAI-PMH>';
        }
        else if($this->wrongIdentifier)
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_LIST_METADATA_FORMATS,"idDoesNotExist","Unknown identifier");
        }
        else
        {
            $metadataString="";
            foreach($this->identifiers as $key=> $oai_Identifier)
            {
                /* @var $oai_Identifier OAI_PMH_Identifier */
                foreach($oai_Identifier->metadatas as $metadata)
                {
                    $metadataString.=$this->makeMetadataString($metadata);
                }
            }

            $oai_Identifier=$this->getOAI_PMH_Identifier();
            $identifier_String="";
            if(!is_null($oai_Identifier))
            {
                $identifier_String=$oai_Identifier->identifier;
            }
            $this->response_xml='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request verb="ListMetadataFormats" '.$identifier_String.'>'.$this->url.'</request>
    <ListMetadataFormats>
'.$metadataString.'
    </ListMetadataFormats>
</OAI-PMH>';
        }
    }

    private function identify(): void
    {
        if(!(isset($this->receiveInput[self::VERB])))
        {
            $this->response_xml=$this->makeErrorResponse(self::VERB_IDENTIFY,"badArgument","Only '".self::VERB."' is allowed");
        }
        else
        {

            $earliestDatestamp=$this->makeEarlierDateStamp();

            $this->response_xml='<OAI-PMH '.$this->schema.'>
    <responseDate>'.$this->responseDate.'</responseDate>
    <request verb="Identify">'.$this->url.'</request>
    <Identify>
        <repositoryName>'.$this->repositoryName.'</repositoryName>
        <baseURL>'.$this->url.'</baseURL>
        <protocolVersion>'.$this->protocolVersion.'</protocolVersion>
        <adminEmail>'.$this->adminEmail.'</adminEmail>
        <earliestDatestamp>'.$earliestDatestamp.'</earliestDatestamp>
        <deletedRecord>'.$this->deletedRecord.'</deletedRecord>
        <granularity>'.$this->granularity.'</granularity>
    </Identify>
</OAI-PMH>';
        }
    }

//    protected function makeEarlierDateStamp() : string
//    {
//        return "2006-12-20T03:49:22.950Z";
//    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    // GETTERS

    protected function getMaxItems(): int
    {
        return $this->max_items;
    }

    public function getErrorString(): string
    {
        return $this->errorString;
    }

    public function getIdentifierTag(): string
    {
        return $this->identifierTag;
    }

//    public function getPeriode_min(): int
//    {
//        return $this->periode_min;
//    }
//
//    public function getPeriode_max(): int
//    {
//        return $this->periode_max;
//    }

    public function getSchema(): string
    {
        return $this->schema;
    }

    public function getAdminEmail(): string
    {
        return $this->adminEmail;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getResponse_xml(): string
    {
        return $this->response_xml;
    }

    public function getResponse_xmlWithXSL(): string
    {
        $response=$this->xsl_url.$this->response_xml;
        if(!empty($this->debugString))
        {
            $response.="<!-- ".$this->debugString." -->";
        }
        return $response;
    }

    public function getSelectedIdentifier(): string
    {
        return $this->selectedIdentifier;
    }
    
    public function hasErrors(): bool
    {
        return $this->has_error;
    }

    // SETTERS

    protected function setMaxItems(int $max_items): OAI_PMH
    {
        $this->max_items=$max_items;
        return $this;
    }

    public function setIdentifierTag(string $identifierTag): OAI_PMH
    {
        $this->identifierTag=$identifierTag;
        return $this;
    }

//    public function setPeriode_min($periode_min): OAI_PMH
//    {
//        $this->periode_min=$periode_min;
//        return $this;
//    }
//
//    public function setPeriode_max($periode_max): OAI_PMH
//    {
//        $this->periode_max=$periode_max;
//        return $this;
//    }

    public function setSchema(string $schema): OAI_PMH
    {
        $this->schema=$schema;
        return $this;
    }

    public function setAdminEmail(string $adminEmail): OAI_PMH
    {
        $this->adminEmail=$adminEmail;
        return $this;
    }

    public function setUrl(string $url): OAI_PMH
    {
        $this->url=$url;
        return $this;
    }

    public function setRepositoryName(string $repositoryName): OAI_PMH
    {
        $this->repositoryName=$repositoryName;
        return $this;
    }

    public function setProtocolVersion(string $protocolVersion): OAI_PMH
    {
        $this->protocolVersion=$protocolVersion;
        return $this;
    }

    //--------------------------------------------------------------------------
    // Internal methods

    private function initRequestVariables(): void
    {
        $this->from=null;
        $this->until=null;
        $this->set=null;
        $this->metadata_prefix=null;
        $this->offset=0;
    }

    private function setRequestVariables(): bool
    {
        if(isset($this->receiveInput[self::FROM])&&!empty($this->receiveInput[self::FROM]))
        {
            $this->from=$this->receiveInput[self::FROM];
        }
        if(isset($this->receiveInput[self::UNTIL])&&!empty($this->receiveInput[self::UNTIL]))
        {
            $this->until=$this->receiveInput[self::UNTIL];
        }
        if(isset($this->receiveInput[self::SET])&&!empty($this->receiveInput[self::SET]))
        {
            $this->set=$this->receiveInput[self::SET];
        }
        if(isset($this->receiveInput[self::METADATA_PREFIX])&&!empty($this->receiveInput[self::METADATA_PREFIX]))
        {
            $this->metadata_prefix=$this->receiveInput[self::METADATA_PREFIX];
        }
        return true;
    }

    private function initResumptionVariables(string $resumptionToken): bool
    {
        $wrong_parameter=false;
        $wrong_parameter_offset=true;
        $wrong_parameter_metadata=true;
        $token_url=base64_decode($resumptionToken);

        $parameters=explode("&",$token_url);
        foreach($parameters as $param)
        {
            $values=explode("=",$param);
            if(count($values)==2)
            {
                if($this->stringMatch($values[0],self::FROM))
                {
                    $this->from=$values[1];
                }
                else if($this->stringMatch($values[0],self::UNTIL))
                {
                    $this->until=$values[1];
                }
                else if($this->stringMatch($values[0],self::SET))
                {
                    $this->set=$values[1];
                }
                else if($this->stringMatch($values[0],self::OFFSET))
                {
                    $val=intval($values[1]);
                    $this->offset=$val+$this->oai_nb_items;
                    $wrong_parameter_offset=false;
                }
                else if($this->stringMatch($values[0],self::METADATA_PREFIX))
                {
                    $this->metadata_prefix=$values[1];
                    $wrong_parameter_metadata=false;
                }
                else
                {
                    $wrong_parameter=true;
                    break;
                }
            }
        }

        if($wrong_parameter||$wrong_parameter_offset||$wrong_parameter_metadata)
        {
            return false;
        }
        return true;
    }

    public function debugPrintResumptionToken(string $resumptionToken): string
    {
        $string='';
        if($this->initResumptionVariables($resumptionToken))
        {
            if(!is_null($this->from))
            {
                $string.="&from=".$this->from;
            }
            if(!is_null($this->until))
            {
                $string.="&until=".$this->until;
            }
            if(!is_null($this->set))
            {
                $string.="&set=".$this->set;
            }
            if(!is_null($this->from))
            {
                $string.="&offset=".$this->offset;
            }
            if(!is_null($this->metadata_prefix))
            {
                $string.="&metadataPrefix=".$this->metadata_prefix;
            }
        }
        else
        {
            $string='error with the token';
        }
        return $string;
    }

    private function stringMatch(string $string1,string $string2): bool
    {
        return (bool)(strcasecmp($string1,$string2)==0);
    }

    private function isVerb(string $verbAction): bool
    {
        return $this->stringMatch($this->verb,$verbAction);
    }

    private function isMetadataListed(string $metadata): bool
    {
        return in_array($metadata,$this->listOfPossibleMetaData);
    }

    private function checkInputIdentifier(string &$identifierString): bool
    {
        //$this->debugString.='==>checkInputIdentifier => '.$identifierString;
        // Checker sans tag donc un nombre entier
        if($this->checkInputGet(self::IDENTIFIER,$identifierString)&&$this->isInteger($identifierString))
        {
            // si un nombre on met celui par défaut donc le premier
            $this->selectedIdentifier="oai:".reset($this->identifiers)->identifier;
            return true;
        }
        else
        {
            //$this->debugString.='==>checkInputIdentifier2 => '.$identifierString;
            return $this->checkInputIdentifierWithPrefix($identifierString);
        }
    }

    private function checkInputIdentifierWithPrefix(string &$identifierString): bool
    {
        $this->wrongIdentifier=false;
        $this->wrongArgumentsSetId=false;
        $this->wrongIdentifierName=false;
        $this->wrongSet=false;
        $this->wrongArgumentsSetId=false;
        $tempIdentifier="";

        if(isset($this->receiveInput[self::IDENTIFIER]))
        {
            if($this->receiveInput[self::IDENTIFIER]!="")
            {
                //$this->debugString.='==>checkInputIdentifierWithPrefix[] => '.$this->receiveInput[self::IDENTIFIER];
                $tempIdentifier=$this->receiveInput[self::IDENTIFIER];
                
                $parts=explode(':',$identifierString);
             
                if(count($parts)>=3)
                {
                    $idItem="";
                    $setName="";
                    $oaiString=$parts[0];
                    $identifierOAI=$parts[1];
                    if($this->identifierSeparator!=self::IDENTIFIER_DEFAULT_SEPARATOR)
                    {
                        $arrayIdentifier=explode($this->identifierSeparator,$parts[2]);
                        if(count($arrayIdentifier)!=2)
                        {
                            $this->wrongIdentifierName=true;
                            return false;                            
                        }
                        $setName=$arrayIdentifier[0];
                        $idItem=$arrayIdentifier[1];                        
                    }
                    else
                    {
                        if(count($parts)!=4)
                        {
                            $this->wrongIdentifierName=true;
                            return false;
                        }
                        $setName=$parts[2];
                        $idItem=$parts[3];
                    }
                    
                    //$this->debugString.='==>Yop[] => '.$oaiString.' | '.$identifierOAI.' | '.$setName.' | '.$idItem." | ";  
                    //$this->debugString.=''.print_r($this->identifiers,true);
                    
                    //vérifier que le set existe et que l'id est un entier et que l'identifier existe aussi
                    if(array_key_exists($identifierOAI,$this->identifiers)&&$this->isInteger($idItem)&&array_key_exists($setName,$this->sets))
                    {
                        $identifierString=$setName.$this->identifierSeparator.$idItem;
                        $this->selectedIdentifier=$identifierOAI;
                        return true;
                    }
                    else if(!array_key_exists($identifierOAI,$this->identifiers))
                    //else if(!$this->checkIdentifierMetaDataPrefix($identifierOAI))
                    {
                        $this->wrongIdentifierName=true;
                        return false;
                    }
                    else if(!array_key_exists($setName,$this->sets))
                    {
                        $this->wrongSet=true;
                        return false;
                    }
                    else if(!$this->isInteger($idItem))
                    {
                        $this->wrongId=true;
                        return false;
                    }
                    

                }
            }
            $this->wrongIdentifier=true;
        }
        return false;
    }

    private function makeMetadataString(string $metadata): string
    {
        $string="";
        if($metadata==self::META_OAI_DC)
        {
            $string.='       <metadataFormat>
            <metadataPrefix>oai_dc</metadataPrefix>
            <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
            <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>
        </metadataFormat>
';
        }
        else if($metadata==self::META_OAI_EAD)
        {
            $string.='       <metadataFormat>
            <metadataPrefix>oai_ead</metadataPrefix>
            <schema>https://www.loc.gov/ead/ead.xsd</schema>
            <metadataNamespace>http://www.w3.org/1999/xlink</metadataNamespace>
        </metadataFormat>
';
        }
        else if($metadata==self::META_OAI_MARC)
        {
            $string.='       <metadataFormat>
            <metadataPrefix>oai_marc</metadataPrefix>
            <schema>http://www.openarchives.org/OAI/1.1/oai_marc.xsd</schema>
            <metadataNamespace>http://www.openarchives.org/OAI/1.1/oai_marc</metadataNamespace>
        </metadataFormat>
';
        }
        else if($metadata==self::META_OLAC)
        {
            $string.='       <metadataFormat>
            <metadataPrefix>olac</metadataPrefix>
            <schema>http://www.language-archives.org/OLAC/1.0/olac-archive.xsd</schema>
            <metadataNamespace>http://www.language-archives.org/OLAC/1.0/</metadataNamespace>
        </metadataFormat>
';
        }
        else if($metadata==self::META_PERSEUS)
        {
            $string.='       <metadataFormat>
            <metadataPrefix>perseus</metadataPrefix>
            <schema>http://www.perseus.tufts.edu/persmeta.xsd</schema>
            <metadataNamespace>http://www.perseus.tufts.edu/persmeta.dtd</metadataNamespace>
        </metadataFormat>
';
        }
        return $string;
    }

    private function checkMetaDataPrefix(string $verb): bool
    {
        $metadata_prefix="";
        if(isset($this->receiveInput[self::METADATA_PREFIX]))
        {
            $metadata_prefix=$this->receiveInput[self::METADATA_PREFIX];
        }
        //check if identifier support metadata
        return $this->checkMetaDataPrefixVar($verb,$metadata_prefix);
    }

    private function checkMetaDataPrefixVar(string $verb,string $metadata_prefix): bool
    {
        $verif_metadata=false;
        $oai_Identifier=$this->getOAI_PMH_Identifier();
        
//        $this->debugString.="\ncheckMetaDataPrefixVar A->".$metadata_prefix." // ".$this->selectedIdentifier."//".print_r($this->identifiers,true)."\"";
        if(!is_null($oai_Identifier))
        {
//            $this->debugString.="\ncheckMetaDataPrefixVar b->".$oai_Identifier->toString()."\"";
            foreach($oai_Identifier->metadatas as $metadata)
            {
//                $this->debugString.="\ncheckMetaDataPrefixVar c->".$metadata."";
                if(strcmp($metadata_prefix,$metadata)==0)
                {
                    $verif_metadata=true;
                }
            }
        }

        if(!$verif_metadata)
        {
            $this->response_xml=$this->makeErrorResponse($verb,"cannotDisseminateFormat","[A]Metadata is not supported => ".$metadata_prefix);
            return false;
        }
        $this->metadata_prefix=$metadata_prefix;
        return true;
    }
    
    private function checkIdentifierMetaDataPrefix(string $metadata_prefix) : bool
    {
        $verif_metadata=false;
        foreach($this->identifiers as $identifier=> $oai_Identifier)
        {
            foreach($oai_Identifier->metadatas as $metadata)
            {
                if(strcmp($metadata_prefix,$metadata)==0)
                {
                    $this->selectedIdentifier=$identifier;
                    $verif_metadata=true;
                }
            }
        }
        return $verif_metadata;
    }

    private function checkMetaDataPrefixForAllVar(string $verb,?string $metadata_prefix): bool
    {
        if(empty($metadata_prefix))
        {
            $this->response_xml=$this->makeErrorResponse($verb,"cannotDisseminateFormat","Metadata is empty");
            return false;
        }

        $verif_metadata=$this->checkIdentifierMetaDataPrefix($metadata_prefix);

        if(!$verif_metadata)
        {
            $this->response_xml=$this->makeErrorResponse($verb,"cannotDisseminateFormat","[B]Metadata is not supported => ".$metadata_prefix);
            return false;
        }
        $this->metadata_prefix=$metadata_prefix;
        return true;
    }

    // OVERRIDE GetRecord
    protected abstract function checkIfGetRecordExists(string $identifierString,OAI_PMH_Identifier $oai_Identifier): bool;

    protected abstract function printGetRecord(string $identifierString,OAI_PMH_Identifier $oai_Identifier,string $metadata_prefix): string;

    // OVERRIDE ListIdentifiers
    protected abstract function checkListIdentifiersIsNotEmpty(?string $from,?string $until,?string $set,?int $offset,string $metadata_prefix): bool;

    protected abstract function printListIdentifiers(?string $from,?string $until,?string $set,?int $offset,string $metadata_prefix): string;

    protected abstract function setMaxItemsForListIdentifiers(): int;

    // OVERRIDE ListRecords
    protected abstract function checkListRecordsIsNotEmpty(?string $from,?string $until,?string $set,?int $offset,string $metadata_prefix): bool;

    protected abstract function printListRecords(?string $from,?string $until,?string $set,?int $offset,string $metadata_prefix): string;

    protected abstract function setMaxItemsForListRecords(): int;

    // OVERRIDE IDENTIFY
    // EARLIER DATESTAMP OF THE REPOSITORY
    protected abstract function makeEarlierDateStamp(): string;

    protected function makeDateStamp(string $date): string
    {
        $datestamp="";
        $arrayTemp=explode(" ",$date);
        if(count($arrayTemp)==2)
        {
            $datestamp=$arrayTemp[0]."T".$arrayTemp[1]."Z";
        }
        return $datestamp;
    }

    protected function makeIdentifierRecord(string $id,?string $set=null): string
    {
        if(!is_null($set))
        {
            return "oai:".$this->selectedIdentifier.':'.$set.$this->identifierSeparator.$id;
        }
        else
        {
            return "oai:".$this->selectedIdentifier.':'.$id;
        }
    }

    protected function checkRequestVariables(string $verb): bool
    {
        if(!is_null($this->from)&&!$this->checkFromVar())
        {
            $this->response_xml=$this->makeErrorResponse($verb,"badArgument","The argument from is not a date ".$this->from." ");
        }
        else if(!is_null($this->until)&&!$this->checkUntilVar())
        {
            $this->response_xml=$this->makeErrorResponse($verb,"badArgument","The argument until is not a date => ".$this->until." ");
        }
        else if(!is_null($this->set)&&!$this->checkSetVar())
        {
            $this->response_xml=$this->makeErrorResponse($verb,"badArgument","The set is unknow => ".$this->set." ");
        }
        else if(!is_null($this->offset)&&!$this->checkOffsetVar())
        {
            $this->response_xml=$this->makeErrorResponse($verb,"badArgument","The Offset is not a positive integer => ".$this->offset." ");
        }
        else if(!is_null($this->until)&&!is_null($this->from)&&$this->from>$this->until)
        {
            $this->response_xml=$this->makeErrorResponse($verb,"badArgument","The dates values are incorrect. The argument from (".$this->from.") have to be before until (".$this->until."). ");
        }
        else if(!is_null($this->until)&&!is_null($this->from)&&strlen($this->from)!=strlen($this->until))
        {
            $this->response_xml=$this->makeErrorResponse($verb,"badArgument","The request has different granularities for the from and until parameters.. ");
        }
        elseif($this->checkMetaDataPrefixForAllVar($verb,$this->metadata_prefix))
        {
            return true;
        }
        return false;
    }

    protected function checkOffsetVar(): bool
    {
        if($this->isInteger($this->offset)&&$this->offset>=0)
        {
            return true;
        }
        return false;
    }

    protected function checkSetVar(): bool
    {
        $set_found=false;
        /* @var $set OAI_PMH_Set  */
        foreach($this->sets as $set)
        {
            if($this->stringMatch($set->setSpec,$this->set))
            {
                $set_found=true;
                break;
            }
        }
        return $set_found;
    }

    protected function checkFromVar(): bool
    {
        return $this->checkDateFormat($this->from);
    }

    protected function checkUntilVar(): bool
    {
        return $this->checkDateFormat($this->until);
    }

    private function checkDateFormat(?string &$date): bool
    {
        if(!is_null($date))
        {
            if($this->string_contains("T",$date)&&$this->string_contains("Z",$date))
            {
                $date=$this->convertDateToSql($date);
                if($this->isDateTime($date))
                {
                    return true;
                }
            }
            else
            {
                if($this->isDateTime($date,"Y-m-d"))
                {
                    return true;
                }
            }
        }
        return false;
    }

    protected function convertDateToSql(string $date): string
    {
        if($this->string_contains("T",$date)&&$this->string_contains("Z",$date))
        {
            $date=str_replace("T"," ",$date);
            $date=str_replace("Z","",$date);
        }
        return $date;
    }

    // ##########################

    private function isInteger(mixed $number): bool
    {
        if(is_numeric($number))
        {
            return true;
        }
        return false;
    }

    private function checkInputGet(string $key_name,mixed &$dest_variable): bool
    {
        if(!empty($key_name)&&count($this->receiveInput)&& array_key_exists($key_name,$this->receiveInput))
        {
            $dest_variable=$this->receiveInput[$key_name];
            return true;
        }
        return false;
    }

    /**
     * 
     * @param array<int,string> $list
     * @param bool $search_strict
     * @return bool
     */
    private function checkInputGetVars(array $list,bool $search_strict=false): bool
    {
        $list2=[];
        foreach($list as $item)
        {
            $list2[]=strtolower($item);
        }

        foreach($this->receiveInput as $key=> $value)
        {
            $key=strtolower($key);
            //echo "Test ==>".$key."\n";
            if(($key2=array_search($key,$list2))!==false)
            {
                unset($list2[$key2]);
            }
            else if($search_strict)
            {
                return false;
            }
        }
        //echo print_r($list2,true);
        if(count($list2)==0)
        {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param array<int,string> $list
     * @return bool
     */
    private function checkInputGetVarsAllowed(array $list): bool
    {
        $list2=[];
        foreach($list as $item)
        {
            $list2[]=strtolower($item);
        }

        foreach($this->receiveInput as $key=> $value)
        {
                $key=strtolower($key);           
            if(($key2=array_search($key,$list2))!==false)
            {
                unset($list2[$key2]);
            }
            else
            {
                return false;
            }
        }
        return true;
    }

    private function isDateTime(mixed $date,string $formatDate='Y-m-d G:i:s') : bool
    {
        if(DateTime::createFromFormat($formatDate,"".$date)!==FALSE)
        {
            return true;
        }
        return false;
    }

    private function string_contains(mixed $str_search,string $content,bool $ignorecase=true) : bool
    {
        if(empty($str_search))
        {
            return false;
        }

        if($ignorecase)
        {
            $str_search=strtolower($str_search."");
            $content=strtolower($content);
        }
        if(strpos($content,$str_search)!==false)
        {
            return true;
        }
        return false;
    }

    protected function setResponse_XML(string $response): void
    {
        $this->response_xml=$response;
    }
    
    public function getIdentifierSeparator(): string
    {
        return $this->identifierSeparator;
    }

    public function setIdentifierSeparator(string $identifierSeparator): void
    {
        $this->identifierSeparator = $identifierSeparator;
    }
    protected function setDebugString(string $s) : void
    {
        $this->debugString.=$s;
    }
    protected function extractSetNameFromIdentifier(string $identifierString) : string
    {
        $results=explode($this->identifierSeparator, $identifierString);
        if(count($results)==2)
        {
            return "".$results[0];
        }
        return "";
    }
    protected function extractIdFromIdentifier(string $identifierString) : string
    {
        $results=explode($this->identifierSeparator, $identifierString);
        if(count($results)==2)
        {
            return "".$results[1];
        }
        return $results[0];        
    }    
}