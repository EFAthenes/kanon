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
class KImagePopUp extends KComponent
{
    private string $img_url="";
    private string $img_url_big="";
    
    /**
     * 
     * @param string $id_name
     * @param string $img_url
     * @param string $legende
     * @param string|null $img_url_big
     * @param array<mixed,mixed>|null $options
     * @param int|null $width
     * @param int|null $height
     */
    final public function __construct(string $id_name,string $img_url,string $legende,?string $img_url_big=null,?array $options=null,?int $width=null,?int $height=null )
    {        
        parent::__construct();
        $this->setNone();    
                
        $this->img_url=$img_url;
        if(is_null($img_url_big))
        {
            $this->img_url_big=$img_url;
        }
        else
        {
            $this->img_url_big=$img_url_big;
        }
        
        $this->setIdName($id_name);
        
        $string_options="";
        if(!is_null($options))
        {
            foreach ($options as $key=>$value)
            {
                $the_key=strval($key);
                $the_value=strval($value);
                if(!empty($the_key)&&!empty($the_value))
                {
                    $string_options.=$the_key.':'.$the_value.',';
                }
            }
        }
        else
        {
            /*
//        thumbs : {
//	    autoStart   : true,
//	    hideOnClose : true
//	  },
             */
            
            
            $string_options='
        buttons: [      
            "slideShow",
            "fullScreen",
            "zoom",
            "thumbs",
            "close"
          ],        
        thumbs          : { autoStart : false},
        hash            : true,
        loop            : true,
        keyboard        : true,
        toolbar         : true,
        animationEffect : true,
        arrows          : true,
        idleTime        : 0,
        infobar         : true, 
        slideShow: {
            autoStart: false,
            speed: 3000
          },
  
';
        }
        
        $width_string="";
        if(!is_null($width))
        {
            $width_string=' width="'.$width.'px" ';
        }
        $height_string="";
        if(!is_null($width))
        {
            $height_string=' height="'.$height.'px" ';
        }        
        
        $layout=KApp::getInstance()->getLayout();
        $layout->addCSSFileToBuffer(__DIR__."/css/jquery.fancybox.min.css");
        $layout->addJsFileToBuffer(__DIR__."/js/jquery.fancybox.min.js"); 
        
        $html='

<a data-fancybox="images_'.$this->getIdName().'" href="'.$this->img_url_big.'" data-caption="'. FormComponent::inputString($legende).'" >
    <img class="img-fluid" src="'.$this->img_url.'" '.$height_string.$width_string.' />
</a>

<script>
$(document).ready(function()
{
    $("[data-fancybox=\'images_'.$this->getIdName().'\']").fancybox(
    {
        '.$string_options.'
    });
});  
</script>      
';
        $this->addHtml($html);
    }
    
    #[\Override]
    public static function testMe() : ?static
    { 
        //string $id_name,string $img_url,string $legende,?string $img_url_big=null,?array $options=null,?int $width=null,?int $height=null 
        $class=new static("KimagePopUp","https://archimage.efa.gr/image_request_iiif/795459/full/max/0/default.jpg","legend",null,null,400,400);
        //$class->addImage("https://archimage.efa.gr/image_request_iiif/795459/full/max/0/default.jpg","image 1");
        //$class->addImage("https://archimage.efa.gr/image_request_iiif/500618/full/max/0/default.jpg","image 2");
       // $class->getDivComponent()->addStyleCode("background-color:#FFFFFF;height:400px;width:400px;");
        return $class;
    }    
}