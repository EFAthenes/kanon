<?php
/*
// À virer en prod
ini_set('display_errors',1);
//Lib pas toucher
require_once './OAI_PMH.class.php';
require_once './OAI_PMH_Identifier.class.php';
require_once './OAI_PMH_Set.class.php';
// Fin Lib
// A modifier selon ses besoins
require_once './Test_OAI_PMH.class.php';

// exemple de requete
// ?verb=Identify
// ?verb=ListSets
// ?verb=ListRecords&metadataPrefix=oai_dc
// ?verb=ListRecords&metadataPrefix=oai_dc&from=2019-06-01
// ?verb=ListRecords&resumptionToken=JmZyb209MjAxOS0wNi0wMSZvZmZzZXQ9MCZtZXRhZGF0YVByZWZpeD1vYWlfZGM=
// ?verb=ListRecords&metadataPrefix=oai_dc&from=2018-09-01&until=2019-03-10
// ?verb=GetRecord&metadataPrefix=oai_dc&identifier=6620


$oai=new Test_OAI_PMH();

// Coordonnees & info de la ressource
$oai->basicInitialization(
        "email", 
        "url de la ressource", 
        "Label" 
        );    

// Ajouter Set c'est optionnel si on en a pas plusieurs // en gros possibilité de servir des sets de données différents
//$oai->addSet("set Name","set Spec","set Description");


// l'identifier tag peut être vide et est lié à un type de metadata
// il faut en rajouter au moins un type de metadata. ici OAI_DC, si plusieurs il faut qu'ils soient différents
// cela permet d'identifier les différentes descriptions de la ressource 
// il sera de la forme tag.620    le point étant le séparateur entre le tag et l'identifiant

//    META_OAI_DC="oai_dc";
//    META_OLAC="olac";
//    META_PERSEUS="perseus";
//    META_OAI_MARC="oai_marc";
 

if($oai->addIdentifierTag("tag",[$oai::META_OAI_DC])
   && $oai->addIdentifierTag("tag2",[$oai::META_PERSEUS,$oai::META_OAI_MARC])     
        )
{
    // on gère la requête
    if($oai->manageResponse())
    {
        header('Content-type: text/xml');
        echo $oai->getResponse_xml();
        exit();
    }
}

//réponse en texte pour faciliter le debug
echo "Error initialization!! ".$oai->getErrorString();


// Test Conversion of resumption token
//echo $oai->debugPrintResumptionToken("JmZyb209MjAxOS0wNi0wMSZvZmZzZXQ9MCZtZXRhZGF0YVByZWZpeD1vYWlfZGM=");
*/