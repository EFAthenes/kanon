<?php
/**
 * Description of DoiService
 *
 * @author bruno.morandiere
 */
namespace src\Services;

use src\Data\Doi;
use Exception;
use src\Data\DoiEvent;
use src\Data\DoiState;
use src\Helper\DoiContext;

require_once __DIR__ . '/../Data/Doi.php';

class DoiService extends Exception
{
    private ?DoiContext $doiContext=null;
    /**
     * Constructs the Doi Service
     */
    public function __construct(?DoiContext $doiContext)
    {
        $this->doiContext = $doiContext;
    }

    /**
     * 
     * @param Doi $doi
     * @param string $doiEvent
     * @return Doi|null
     * @throws Exception
     */
    public function createDoi(Doi $doi, string $doiEvent = DoiEvent::draft) : ?Doi
    {

        if ($this->doiContext == null)
        {
            //throw new \Exception('Doi context unset');
            return null;
        }
        $dataciteUrl = $this->doiContext->getDataciteUrl();
        $dataciteUser = $this->doiContext->getDataciteUser();
        $datacitePasswd = $this->doiContext->getDatacitePasswd();

        $doi->setPrefix($this->doiContext->getDatacitePrefix());

        $curlQuery = $dataciteUrl . '/dois/';

        $curl = curl_init();

        $headers = array();

        $headers[] = "Content-Type: application/vnd.api+json";

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $dataciteUser . ':' . $datacitePasswd);
        curl_setopt($curl, CURLOPT_URL, $curlQuery);
        curl_setopt($curl, CURLINFO_HEADER_OUT, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $doi->getJson());
        $response = curl_exec($curl);
        $httpReturn = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        unset($curl);

        if ($response === false)
        {
            throw new \Exception('DOI creation error CURL(false) (' . $httpReturn . ') : ' . $response."//".$doi->getJson(), $httpReturn);
        }
        else
        {
            if ($httpReturn == 201)
            {
                $returndJsonData = json_decode($response);
                $doiReturn = new Doi();//$this->doiContext->getDatacitePrefix());
                $doiReturn->setJson($returndJsonData->data);
                if ($doiEvent != DoiEvent::draft)
                {
                    $this->setDoiState($doiReturn, $doiEvent);
                }
                return $doiReturn;
            }
            else
            {
                throw new \Exception('DOI creation error (' . $httpReturn . ') : ' . $response."//".$doi->getJson(), $httpReturn);
            }
        }
    }

    /**
     * 
     * @param Doi $doi
     * @param String $doiEvent
     * @return Doi
     * @throws Exception
     */
    public function setDoiState(Doi $doi, String $doiEvent = DoiEvent::draft): ?Doi
    {

        if ($doi->getState() == DoiState::findable)
        {
            throw new \Exception("We can't change state for a published Doi");
        }
        if (($doi->getState() == DoiState::registered) && ($doiEvent == DoiEvent::draft))
        {
            throw new \Exception("It is not possible to switch to the draft status for a Doi already registered.");
        }
        if ($doiEvent == DoiEvent::publish || $doiEvent == DoiEvent::register)
        {

            $doi->setEvent($doiEvent);
            $doi = $this->updateDoi($doi);
        }
        return $doi;
    }

    /**
     * 
     * @param Doi $doi
     * @return Doi|null
     * @throws Exception
     */
    public function updateDoi(Doi $doi): ?Doi
    {

        if ($this->doiContext == null)
        {
            //throw new \Exception('Doi context unset');
            return null;
        }


        $dataciteUrl = $this->doiContext->getDataciteUrl();
        $dataciteUser = $this->doiContext->getDataciteUser();
        $datacitePasswd = $this->doiContext->getDatacitePasswd();

        $curlQuery = $dataciteUrl . '/dois/' . urlencode($doi->getDoi());



        $curl = curl_init();

        $headers = array();

        $headers[] = "Content-Type: application/vnd.api+json";

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $dataciteUser . ':' . $datacitePasswd);
        curl_setopt($curl, CURLOPT_URL, $curlQuery);
        curl_setopt($curl, CURLINFO_HEADER_OUT, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $doi->getJson());
        $response = curl_exec($curl);
        $httpReturn = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        unset($curl);

        if ($response === false)
        {
            throw new \Exception('DOI Update error (' . $httpReturn . ') : ' . $response, $httpReturn);
        }
        else
        {

            if ($httpReturn == 200)
            {
                $returndJsonData = json_decode($response);
                $returnedDoi = new Doi();//$this->doiContext->getDatacitePrefix());
                $returnedDoi->setJson($returndJsonData->data);
                return $returnedDoi;
            }
            else
            {
                throw new \Exception('DOI Update error (' . $httpReturn . ') : ' . $response, $httpReturn);
            }
        }
    }

    public function deleteDoi(string $doi) : bool
    {

        if ($this->doiContext == null)
        {
            //throw new \Exception('Doi context unset');
            return false;
        }
        $dataciteUrl = $this->doiContext->getDataciteUrl();
        $dataciteUser = $this->doiContext->getDataciteUser();
        $datacitePasswd = $this->doiContext->getDatacitePasswd();


        $query = "";
        if (!empty($doi))
        {
            $query = $this->doiContext->getDatacitePrefix() . '/' . $doi;
            //check doi format : prefix/suffix or suffix only
            if ((strpos($doi, '/') !== false))
            {
                $part = explode("/", $doi);
                if ($part[0] != $this->doiContext->getDatacitePrefix())
                {
                    throw new \Exception("Your prefix does not match the one expected for this datacite repository");
                }
                else
                {
                    $query = $doi;
                }
            }
        }
        else
        {
            throw new \Exception("You have to enter a Doi");
        }

        $curlQuery = $dataciteUrl . '/dois/' . $query;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $dataciteUser . ':' . $datacitePasswd);
        curl_setopt($curl, CURLOPT_URL, $curlQuery);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);

        $response = curl_exec($curl);
        $httpReturn = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        unset($curl);


        if ($response === false)
        {
            throw new \Exception('error when deleting the DOI (' . $httpReturn . ') : ' . $response, $httpReturn);
        }
        else
        {

            if ($httpReturn == 204)
            {
                return TRUE;
            }
            else
            {
                throw new \Exception('error when deleting the DOI (' . $httpReturn . ') : ' . $response, $httpReturn);
            }
        }
    }

    public function getDoi(?string $doi): ?Doi
    {

        if ($this->doiContext == null)
        {
            return null;
//            throw new \Exception('Doi context unset');
        }


        $dataciteUrl = $this->doiContext->getDataciteUrl();
        $dataciteUser = $this->doiContext->getDataciteUser();
        $datacitePasswd = $this->doiContext->getDatacitePasswd();
        $query = "";
        if(!empty($doi))
        {
            $query = $this->doiContext->getDatacitePrefix() . '/' . $doi;
            //check doi format : prefix/suffix or suffix only
            if ((strpos($doi, '/') !== false))
            {
                $part = explode("/", $doi);
                if ($part[0] != $this->doiContext->getDatacitePrefix())
                {
                    throw new \Exception("Your prefix does not match the one expected for this datacite repository");
                }
                else
                {
                    $query = $doi;
                }
            }
        }
        else
        {
            throw new \Exception("You have to enter a Doi");
        }
        $curlQuery = $dataciteUrl . '/dois/' . $query;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $dataciteUser . ':' . $datacitePasswd);
        curl_setopt($curl, CURLOPT_URL, $curlQuery);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);

        $response = curl_exec($curl);
        $httpReturn = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        unset($curl);

        if ($response === false)
        {
            throw new \Exception('error while retrieving the DOI (' . $httpReturn . ') : ' . $response, $httpReturn);
        }
        else
        {
            if ($httpReturn == 200)
            {
                $jsonReturn = json_decode($response);

                $doi = new Doi();
                $doi->setJson($jsonReturn->data);
                return $doi;
            }
            else
            {
                return null;
                //throw new \Exception('error while retrieving the DOI (' . $httpReturn . ') : '.$curlQuery.' ' . $response, $httpReturn);
            }
        }
    }

    /**
     * 
     * @return DoiContext|null
     */
    public function getDoiContext() : ?DoiContext
    {
        return $this->doiContext;
    }

    /**
     * 
     * @param DoiContext|null $doiContext
     * @return void
     */
    public function setDoiContext(?DoiContext $doiContext) : void
    {
        $this->doiContext = $doiContext;
    }
}