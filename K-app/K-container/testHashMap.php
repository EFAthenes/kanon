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

ini_set("memory_limit","300M");

$delimitor="\n";
$delimitor="<br />";
//require_once('../../K-app/include.php');
//require_once('../../K-app/include.php');
require_once('../K-utils/KTimer.class.php');
require_once('../K-container/includeContainer.php');
require_once('../K-abstract-object/KObject.class.php');
require_once('../K-abstract-object/KField.class.php');
require_once('../K-abstract-object/K-field/KFieldInteger.class.php');
//exit();
$timer=new KTimer("hash ");
$map=new HashMap();

$map->put("salut", "1");
$map->put("salut2", "2");
$map->put("a", "3");
$map->put("ab", "4");
$map->put("aa", "5");
$map->put("z", "6");
$map->put("b", "7");
$map->put("123", "8");
$map->put("yo", "9");
$map->put("yo1", "10");
$map->put("yo2", "11");
$map->put("yo3", "12");
$map->put("yo4", "13");

echo $map->toString("\n");

echo "\n\n";

$list= $map->toArrayList();

echo $list->toString($delimitor);

echo $map->toString($delimitor);

//exit();




$timer->start();
for($i=0 ; $i < 100000 ; $i++)
{
//     echo "\n Count = ".$map->getCount()."";
    if($map->put("count_".$i, new KFieldInteger()))
    {
        //echo "\n OKI count_".$i;
    }
}
$timer->stop();
echo "TIMER 1".$timer->toString();


//$list=$map->toArrayList();
//
//for($i=0; $i< $list->getSize() ; $i++)
//{
//    echo "\n List[$i] = ".$list->get($i)->get()."";
//}
//
//echo "\n Size List = ".$list->getSize()." \n\n";


//$timer->start();
//$search=$map->get("louis");
//$timer->stop();
//
//echo "\n result 007 = ".$search."\n";

//$timer->printTime();

//$search=$map->get("123");
//
//echo "\n result 1 = ".$search."\n";
//if($search != null)
//{
//    echo "\n result = ".$search."\n";
//}
//else
//{
//    echo "\n result = NOT FOUND !!! \n";
//}

//$search=$map->get("999");
//
//echo "\n result = ".$search."\n";
//if($search != null)
//{
//    echo "\n result = ".$search."\n";
//}
//else
//{
//
//}

// echo "\n NB = ".$map->getNB()."\n";
//
//
//
//$map->replace("louis", " YOYOYOY");
//
//echo "\n NB = ".$map->getNB()."\n";
//
// $search=$map->get("louis");
//
//echo "\n result 1 = ".$search."\n";
//if($search != null)
//{
//    echo "\n result = ".$search."\n";
//}
//else
//{
//    echo "\n result = NOT FOUND !!! \n";
//}




echo "\n Count Before Remove = ".$map->getCount()."";
$map->remove("salut");
echo "\n Count After Remove = ".$map->getCount()."\n";

//$search=$map->get("louis");
//
//echo "\n result 1 = ".$search."\n";
//if($search != null)
//{
//    echo "\n result = ".$search."\n";
//}
//else
//{
//    echo "\n result = NOT FOUND !!! \n";
//}



//echo "\n Count Before Remove ROOT = ".$map->getCount()."";
//$map->remove("salut");
//echo "\n Count After Remove ROOT = ".$map->getCount()."";





for($i=0 ; $i < 1000 ; $i++)
{
    //echo "\nTRY TO REMOVE count_".$i;
    if(!$map->remove("count_".$i))
    {
        echo "\n NOT REMOVED count_".$i."\n";
        exit();
    }
    else
    {
        //echo "\n OKI REMOVED count_".$i."\n";
    }
}

//$map->remove("0");

echo "\n Count AT Last = ".$map->getCount()."\n";


$list=$map->toArrayList();
echo "TO ARRAYLIST \n";

//for($i=0; $i< $list->getSize() ; $i++)
//{
//    echo "\n List[$i] = ".$list->get($i)->get()."";
//}

//$map->remove("louis");
//echo "\n Count After Remove = ".$map->getCount()."";

//
//$lep=$map->get("dfkjgvidofvdfv,pdfbv,dpfbv,dfp,");
//echo "\n\n LEP == ".$lep."\n";
//
//
//echo "\n NB = ".$map->getNB()."\n";


$map->clear();

$timer->stop();
$timer->printTime();


/*
$list = new ArrayList();

$list->get(1);

$list->add("1");
$list->add("2");
$list->add("3");
$list->add("4");
$list->add("5");
$list->add("6");
$list->add("7");
$list->add("8");
$list->add("9");
$list->add("10");



echo "\n Size =".$list->getSize();


for($i=0 ; $i < $list->getSize() ; $i++)
{

    $search="".$list->get($i);    
     echo ("\n i=".$i." -> ".$search."\n");
}



$list->remove(3);

echo "\n ####################  \n Size =".$list->getSize();


for($i=0 ; $i < $list->getSize() ; $i++)
{

    $search="".$list->get($i);
     echo ("\n i=".$i." -> ".$search."\n");
}


$list->remove(0);

echo "\n ####################  \n Size =".$list->getSize();


for($i=0 ; $i < $list->getSize() ; $i++)
{

    $search="".$list->get($i);
     echo ("\n i=".$i." -> ".$search."\n");
}


echo "\n ####################  \n clear \n ";
$list->clear();

echo "\n ####################  \n Size =".$list->getSize();


for($i=0 ; $i < $list->getSize() ; $i++)
{

    $search="".$list->get($i);
     echo ("\n i=".$i." -> ".$search."\n");
}

$list->free();
*/


/*

echo "\n  Test Remove \n";

$h=new HashMap();

$h->put("salut", "Yo!");
$h->put("a", "Yo2!");
$h->put("z", "Yo3!");

$search=$h->get("salut");


if($search != null)
{
    echo "\n result = ".$search."\n";
}
else
{
    echo "\n result = NOT FOUND !!! \n";
}

if($h->remove("salut"))
{
    echo " Fucking Removed \n" ;
}
else
{
    echo " Fucking not Removed \n" ;
}

$search=$h->get("salut");


if($search != null)
{
    echo "\n result = ".$search."\n";
}
else
{
    echo "\n result = NOT FOUND !!! \n";
}
*/
