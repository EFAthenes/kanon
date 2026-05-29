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
class Test_OAI_PMH extends OAI_PMH
{  
    // override
    protected function makeEarlierDateStamp() : string
    {
        // date de la ressource modifiée la plus récente
        $date="2021-03-18T11:51:13Z";
        return $date;
    }
    
    //##########################################################################
    // For GetRecord
    //##########################################################################
    // override
    protected function checkIfGetRecordExists(string $identifierString, \OAI_PMH_Identifier $oai_Identifier): bool
    {
        
        // Chercher l'item avec $oai_Identifier et le stocker dans une variable pour être réutiliser par la suite
        // identifierString peut être l'identifiant de recherche ou l'identifiant + son tag
        return true;

        /*
        $this->response_xml=$this->makeErrorResponse(self::VERB_GET_RECORD,"idDoesNotExist","No record found for the identifier => '".$identifierString."' ");   
        return false;
         * 
         */
    }
    

    // override
    protected function printGetRecord(string $identifierString, \OAI_PMH_Identifier $oai_Identifier, string $metadata_prefix): string
    {
        // exemple DC bidon
        // la méthode peut être réutilisée lors de printListRecords()
        $xml=
'       <record>
           <header>
                <identifier>Identifiant => Tag + identifiant de la ressource unique : ('.$identifierString.') + ('.$oai_Identifier->toString().') '.$metadata_prefix.'</identifier>
                <datestamp>date de modification</datestamp>
            </header>
            <metadata>
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dct="http://purl.org/dc/terms" xmlns:dcat="http://www.w3.org/ns/dcat#" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:acdm="http://schemas.cloud.dcu.gr/ariadne-registry/" xmlns:skos="http://www.w3.org/2004/02/skos/core#" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
                    <dc:title>Titre</dc:title>
                    <dc:description>Description</dc:description>
                    <dc:publisher>publisher</dc:publisher>
                    <dc:owner>Owner</dc:owner>
                    <dct:accessRights>accessRights</dct:accessRights>
                    <dc:date>date</dc:date>
                    <dc:identifier>identifier</dc:identifier>
                    <dcat:landingPage>landingPage</dcat:landingPage>
                    <dct:issued>date issued</dct:issued>
                    <dct:language>language</dct:language>
                    <dct:modified>date modif</dct:modified>
                    <originalId>originalId</originalId>
                    <scientificResponsible>scientificResponsible</scientificResponsible>
                    <technicalResponsible>technicalResponsible</technicalResponsible>    
                    <archaeologicalResourceType>archaeologicalResourceType</archaeologicalResourceType>
                </oai_dc:dc>
            </metadata>
        </record>        
';
        return $xml;
    }
    
    //##########################################################################
    // For ListIdentifiers
    //##########################################################################
    // override
    protected function checkListIdentifiersIsNotEmpty(?string $from,?string $until,?string $set,?int $offset, string $metadata_prefix): bool
    {
        // Chercher les items avec par from // until et le set
        // le charger dans un container ou array et le stocker pour être utiliser printListRecords ou printListIdentifiers
        //return true;
        
        // si pas de réponse
        return false;
    }    
   
    // override
    protected function printListIdentifiers(?string $from,?string $until,?string $set,?int $offset, string $metadata_prefix): string
    {
        $arrayResult=["test"];
        $xml="";
        foreach($arrayResult as $item)
        {
            $xml.='
            <header>
                <identifier>Identifiant : '.$this->getIdentifierTag().' idenfiant de la ressource unique => '.$item.'</identifier>
                <datestamp>date de modification</datestamp>
            </header>';
        }
        return $xml;
    }

    //##########################################################################
    // For ListRecords
    //##########################################################################
    // override
    protected function checkListRecordsIsNotEmpty(?string $from,?string $until,?string $set,?int $offset, string $metadata_prefix): bool
    {
        //créer la liste de reponse et la stocker qqpart
        //si réponse pas vide ==> true
        return true;
        
        //si vide
        //return false;
    }   
    
    protected function printListRecords(?string $from,?string $until,?string $set,?int $offset, string $metadata_prefix): string
    {
        // prendre la liste de réponse et renvoyer sous forme xml de record 
        $xml='';
        // exemple 
        $i=2;
        for ($i=0;$i<2;$i++)
        {
            //string $identifierString, \OAI_PMH_Identifier $oai_Identifier, string $metadata_prefix
            $xml.=$this->printGetRecord($this->getIdentifierTag(),$this->getOAI_PMH_Identifier(), $metadata_prefix);
        }
        return $xml;
    }  
    
    
    // override facultatif
    
    protected function setMaxItemsForListRecords() : int
    {
        // nombre maximum d'item par réponse à modifier selon ses envies sinon on laisse tel quel
        return $this->getMaxItems();;
    }
    
    
    protected function setMaxItemsForListIdentifiers() : int
    {
        // nombre maximum d'item par réponse  à modifier selon ses envies sinon on laisse tel quel
        return $this->getMaxItems();
    }
    
    //##########################################################################
}