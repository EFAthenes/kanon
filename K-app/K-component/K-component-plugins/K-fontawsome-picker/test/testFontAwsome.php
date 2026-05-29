<?php
//function fa_icons () {
//    $content = file_get_contents('https://github.com/FortAwesome/Font-Awesome/raw/refs/heads/6.x/metadata/icons.json');
//    $json = json_decode($content);
//    $icons = [];
//
//    foreach ($json as $icon => $value) 
//    {
//        $newIcon=[];
//        foreach ($value->styles as $style) {
//            $newIcon[0] = 'fa'.substr($style, 0 ,1).' fa-'.$icon;
//            $newIcon[1] =[];
//        }
//        foreach ($value->search as $search) 
//        {
//            //if($search["terms"])
//            if(is_array($search))
//            {
//                $newIcon[1]=$search;
//            }
//        }
//        $icons[]=$newIcon;
//    }
//
//    return $icons;
//}
//
//echo '<link
//        rel="stylesheet"
//        href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css"
//      >
//';
//
//
//$array=fa_icons();
//$nb=0;
//foreach ($array as $value)
//{
//    $nb++;
//    $terms='';
//    foreach($value[1] as $term)
//    {
//        if(!empty($terms))
//        {
//             $terms.=',';
//        }
//        $terms.='"'.$term.'"';
//    }
//    echo '
//            {
//                title: "'.$value[0].'",
//                searchTerms: ['.$terms.']
//            },        
//';
//}