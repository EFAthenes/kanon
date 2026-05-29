<?php

/**
 * Description of Doi
 *
 * @author bruno.morandiere
 */

namespace src\Data;

use Exception;

abstract class DoiEvent
{
    const string draft = 'draft';
    const string register = 'register';
    const string publish = 'publish';
}

abstract class DoiState
{
    const string draft = 'draft';
    const string registered = 'registered';
    const string findable = 'findable';
}

abstract class DoiType
{
    const string Audiovisual = 'Audiovisual';
    const string Collection = 'Collection';
    const string Dataset = 'Dataset';
    const string Event = 'Event';
    const string Image = 'Image';
    const string InteractiveResource = 'InteractiveResource';
    const string Model = 'Model';
    const string PhysicalObject = 'PhysicalObject';
    const string Software = 'Software';
    const string Sound = 'Sound';
    const string Text = 'Text';
    const string Workflow = 'Workflow';
    const string Other = 'Other';
}

abstract class Institution
{
    const string casa = "Casa de Velazquez";
    const string efr = "École française de Rome";
    const string efa = "École française d'Athènes";
    const string ifao = "Institut français d'archéologie orientale";
    const string efeo = "École française d'Extrême-Orient";
}

class Doi extends Exception
{
    private string $efe = "";
    private string $prefix = ""; // ex : 10.80081
    private string $suffix = ""; // ex : casa.c66f-4ee5
    private string $doi = ""; // ex 10.80081/casa.c66f-4ee5
    private string $url = ""; // required to publish
    private string $title = ""; // required to publish
    private string $publisher = ""; // required to publish
    private string $publicationYear = ""; // required to publish
    private string $state = "";
    private string $description = "";
    private string $type = ""; // required to publish

    /**
     * 
     * @var array<int,Author>
     */
    private array $authors = array(); // required to publish
    private string $email = ""; // doi manager'email
    private string $institution = ""; // doi manager's institution : EFA, EFEO, CASA, IFAO, EFR
    private string $event = ""; //used only to change a doi state
    private string $json = ""; //internal json generated

    /**
     * @var array<string,array<string,array<mixed,mixed>>>|null
     */
    private ?array $jsonData = null; //internal array for Data

    /**
     * Constructs the Doi Item
     */
    public function __construct()
    {
        $this->type = DoiType::Dataset; // default value;
    }

    public function __toString(): string
    {
        return "doi : ".$this->doi.'\r\n'.
                "url : ".$this->url.'\n'.
                "titre : ".$this->title.'\n'.
                "date : ".$this->publicationYear.'\n'.
                "publisher : ".$this->publisher.'\n'.
                "type : ".$this->type.'\n'.
                "description : ".$this->description.'\n'.
                "état : ".$this->state.'\n';
    }

    public function toWebString(string $delimiter = "<br />"): string
    {
        return "doi : ".$this->doi.'\r'.$delimiter.
                "url : ".$this->url.$delimiter.
                "titre : ".$this->title.$delimiter.
                "date : ".$this->publicationYear.$delimiter.
                "publisher : ".$this->publisher.$delimiter.
                "type : ".$this->type.$delimiter.
                "description : ".$this->description.$delimiter.
                "suffix : ".$this->suffix.$delimiter.
                "prefix : ".$this->prefix.$delimiter.
                "état : ".$this->state.$delimiter;
    }

    /**
     * 
     * @param mixed $jsonData
     * @return void
     */
    public function setJson(mixed $jsonData): void
    {
        //prefix
        $this->setPrefix($jsonData->attributes->prefix);
        //suffix
        $this->setSuffix($jsonData->attributes->suffix);
        //doi
        $this->setDoi($jsonData->attributes->prefix."/".$jsonData->attributes->suffix);
        //titre
        if (isset($jsonData->attributes->titles[0]->title))
        {
            $this->setTitle($jsonData->attributes->titles[0]->title);
        }
        //date
        $this->setPublicationYear($jsonData->attributes->publicationYear);
        //url
        $this->setUrl($jsonData->attributes->url);
        //publisher
        $this->setPublisher($jsonData->attributes->publisher);
        //type
        $this->setType($jsonData->attributes->types->resourceTypeGeneral);
        //description
        if (isset($jsonData->attributes->descriptions[0]->description))
        {
            $this->setDescription($jsonData->attributes->descriptions[0]->description);
        }
        //Etat
        $this->setState($jsonData->attributes->state);
        //Institution
        if (isset($jsonData->attributes->alternateIdentifiers[1]->alternateIdentifier))
        {
            $this->setInstitution($jsonData->attributes->alternateIdentifiers[1]->alternateIdentifier);
        }
        //Email
        if (isset($jsonData->attributes->alternateIdentifiers[0]->alternateIdentifier))
        {
            $this->setEmail($jsonData->attributes->alternateIdentifiers[0]->alternateIdentifier);
        }
    }

    public function addAuthor(Author $author): void
    {
        $this->authors[] = $author;
    }

    public function getJson(): string
    {
        $this->jsonData = array(
            "data" => array(
                "attributes" => array()
            )
        );

        if (!empty($this->doi))
        {
            $this->jsonData["data"]["attributes"]["doi"] = $this->prefix."/".$this->suffix;
        }

        if (!empty($this->email))
        {

            $identifier = array("identifier" => $this->email, "identifierType" => "ContactPerson");
            $this->jsonData["data"]["attributes"]["identifiers"][] = $identifier;
            $alternateIdentifier = array("identifier" => $this->email, "identifierType" => "ContactPerson");
            $this->jsonData["data"]["attributes"]["alternateIdentifiers"][] = $alternateIdentifier;
            $contributor = array("name" => $this->email, "contributorType" => "ContactPerson");
            $this->jsonData["data"]["attributes"]["contributors"][] = $contributor;
        }

        if (!empty($this->institution))
        {

            $identifier = array("identifier" => $this->institution, "identifierType" => "HostingInstitution");
            $this->jsonData["data"]["attributes"]["identifiers"][] = $identifier;
            $alternateIdentifier = array("identifier" => $this->institution, "identifierType" => "HostingInstitution");
            $this->jsonData["data"]["attributes"]["alternateIdentifiers"][] = $alternateIdentifier;
            $contributor = array("name" => $this->institution, "contributorType" => "HostingInstitution");
            $this->jsonData["data"]["attributes"]["contributors"][] = $contributor;
        }
        if (!empty($this->description))
        {
            $this->jsonData["data"]["attributes"]["descriptions"][] = array("description" => $this->description);
        }

        //$this->jsonData["data"]["attributes"]["rights"] = "Creative Commons Attribution 3.0 Germany License";
        $right = [];
        $right["rights"] = "Attribution-NonCommercial-ShareAlike 4.0 International";
        $right["rightsUri"] = "http://creativecommons.org/licenses/by-nc-sa/4.0/";
        $right["schemeUri"] = "https://spdx.org/licenses/";
        $right["rightsIdentifier"] = "CC BY-NC-SA 4.0 ";
        $right["rightsIdentifierScheme"] = "SPDX";
        $this->jsonData["data"]["attributes"]["rightsList"] = [$right];

        if (!empty($this->type))
        {
            $this->jsonData["data"]["attributes"]["types"]["resourceTypeGeneral"] = $this->type;
        }

        if (!empty($this->publisher))
        {
            $this->jsonData["data"]["attributes"]["publisher"] = $this->publisher;
        }

        if (!empty($this->publicationYear))
        {
            $this->jsonData["data"]["attributes"]["publicationYear"] = $this->publicationYear;
        }

        if (!empty($this->url))
        {
            $this->jsonData["data"]["attributes"]["url"] = $this->url;
        }
        if (!empty($this->event))
        {
            $this->jsonData["data"]["attributes"]["event"] = $this->event;
        }
        if (!empty($this->title))
        {
            $this->jsonData["data"]["attributes"]["titles"][] = array("title" => $this->title);
        }

        foreach ($this->authors as $author)
        {
            if (!$author->isOrganization())
            {
                $this->jsonData["data"]["attributes"]["creators"][] = array("givenName" => $author->getGivenName(), "familyName" => $author->getFamilyName(), "nameType" => "Personal");
            }
            else
            {
                $this->jsonData["data"]["attributes"]["creators"][] = array("name" => $author->getOrgName(), "nameType" => "Organizational");
            }           
        }

        $this->json = json_encode($this->jsonData, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);
        return $this->json;
    }

    /**
     * 
     * @return array<string,array<string,array<mixed,mixed>>>|null
     */
    public function getTemporaryJsonData(): ?array
    {
        return $this->jsonData;
    }

    public function getJsonGenerated(): string
    {
        return $this->json."";
    }

    public function getDoi(): string
    {
        return $this->doi;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function getPublicationYear(): string
    {
        return $this->publicationYear;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getInstitution(): string
    {
        return $this->institution;
    }

    public function setDoi(string $doi): void
    {
        $this->doi = $doi;
    }

    public function setUrl(string $url): void
    {
        if ((strpos($url, 'https://') == 0) || (strpos($url, 'http://') == 0) || (strpos($url, 'ftp://') == 0))
        {
            $this->url = $url;
        }
        else
        {
            throw new \Exception("Url must start with http, https or ");
        }
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setPublisher(string $publisher): void
    {
        $this->publisher = $publisher;
    }

    public function setPublicationYear(string $publicationYear): void
    {
        $this->publicationYear = $publicationYear;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setInstitution(string $institution): void
    {
        $this->institution = $institution;
    }

    public function getEfe(): string
    {
        return $this->efe;
    }

    public function setEfe(string $efe): void
    {
        $this->efe = $efe;
        $this->suffix = $efe.".".substr(md5("".rand()), 0, 4).'-'.substr(md5("".rand()), 0, 4);
        switch ($efe)
        {
            case 'casa' :
                $this->institution = "Casa de Velazquez";
                $this->email = "doi@casadevelazquez.org";
                $this->publisher = "Casa de Velazquez";
                break;
            case 'efr' :
                $this->institution = "École française de Rome";
                $this->email = "doi@efrome.it";
                $this->publisher = "École française de Rome";
                break;
            case 'efa' :
                $this->institution = "École française d'Athènes";
                $this->email = "doi@efa.gr";
                $this->publisher = "École française d'Athènes";
                break;
            case 'ifao' :
                $this->institution = "Institut français d'archéologie orientale";
                $this->email = "doi@ifao.egnet.net";
                $this->publisher = "Institut français d'archéologie orientale";
                break;
            case 'efeo' :
                $this->institution = "École française d'Extrême-Orient";
                $this->email = "doi@efeo.net";
                $this->publisher = "École française d'Extrême-Orient";
                break;
        }
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
        //$this->doi = $this->prefix."/".$this->suffix;
    }

    public function setSuffix(string $suffix): void
    {
        $this->suffix = $suffix;
        //$this->doi = $this->prefix."/".$this->suffix;
    }

    public function makeDoiString(): void
    {
        $this->doi = $this->prefix."/".$this->suffix;
    }
}