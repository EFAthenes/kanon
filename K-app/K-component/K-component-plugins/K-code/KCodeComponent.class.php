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
class KCodeComponent extends KComponent
{
    private string $id_name="";
    private string $codeContent="";
    
    public const string CSS_LANG="language-css";
    public const string PHP_LANG="language-php";
    public const string HTML_LANG="language-html";
    public const string SQL_LANG="language-sql";
    
    private string $type="";
    
    final public function __construct(string $codeType="",string $codeContent="")
    {        
        parent::__construct();     
        $this->setNone();
        
        $this->type= match ($codeType) {
            self::CSS_LANG => self::CSS_LANG,
            self::PHP_LANG => self::PHP_LANG,
            self::HTML_LANG => self::HTML_LANG,
            self::SQL_LANG => self::SQL_LANG,
            default => CSS_LANG,
        };

        $this->codeContent=$codeContent;
        $this->addHtmlComponent($codeContent);
        
        $layout=KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/prism/prism.js");
        $layout->addCSSFileToBuffer(__DIR__."/prism/prism.css");
        
        
    }
    public function draw() : string
    {       
        $string="<pre><code ";
        if($this->getClassName()!="")
        {
            $string.=" class=\"".$this->type." ".$this->getClassName()."\" ";
        }
        else
        {
            $string.=" class=\"".$this->type."\" ";
        }
        if($this->id_name!="")
        {
            $string.=" id=\"".$this->id_name."\" ";
        }
        
        return $string." data-prismjs-copy=\"Copy\" >".parent::draw()."</code></pre>";
    }
    
    #[\Override]
    public static function testMe() : ?static
    {
        $code ='
declare(strict_types=1);
class KCodeComponent extends KComponent
{
    final public function __construct(string $codeType="",string $codeContent="")
    { 
        parent::__construct();  
    }
}            
';
        /* @var $class KCodeComponent */
        $class=new static(KCodeComponent::PHP_LANG,$code);
        $class->setAbstract_title("PrismJS");
        $class->setAbstract_url("https://prismjs.com");
        $class->setAbstract("Prism is a lightweight, extensible syntax highlighter, built with modern web standards in mind. It’s used in millions of websites, including some of those you visit daily.");
        //$class->addHtmlComponent("<b>ArticleComponent example</b>");
        return $class;
    }

}