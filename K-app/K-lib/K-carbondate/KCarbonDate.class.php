<?php
/**
 * Description of KCarbonDate
 *
 * @author louis.mulot
 */
require_once __ROOT__.'/K-composer/vendor/autoload.php';       
use Carbon\Carbon;

class KCarbonDate
{
    //put your code here
    public function __construct()
    {

    }
    
    public function convertDateToFrenchString(string $the_date) : string
    {
        return "deprecated";
//        $frLanguage = 'fr_FR';
//
//        $dt = Carbon::create($the_date);
//        
//        $dt->locale($frLanguage);
//        
//        return $dt->day." ".$dt->monthName." ".$dt->year;//$dt->locale($frLanguage)->format('l jS \\of F Y h:i:s A'); 
    }
    
    public function convertDateToEnglishString(string $the_date): string
    {
        return "deprecated";
//        $dt = Carbon::create($the_date);        
//        return $dt->day." ".$dt->monthName." ".$dt->year;//$dt->locale($frLanguage)->format('l jS \\of F Y h:i:s A'); 
    }   
}
/*
 
 * $martinDateFactory = new Factory([
    'locale' => 'fr_FR',
    'timezone' => 'Europe/Paris',
]);
 , but we notify John in English:
$gameStart = Carbon::parse('2018-06-15 12:34:00', 'UTC');
$move = Carbon::now('UTC');
$toDisplay = $martinDateFactory->make($gameStart)->isoFormat('lll')."\n".
    $martinDateFactory->make($move)->calendar()."\n";
$notificationForJohn = $johnDateFactory->make($gameStart)->isoFormat('lll')."\n".
    $johnDateFactory->make($move)->calendar()."\n";
echo $toDisplay;

15 juin 2018 12:34
Aujourd’hui à 05:57


echo $notificationForJohn;

Jun 15, 2018 12:34 PM
Today at 5:57 AM

 */