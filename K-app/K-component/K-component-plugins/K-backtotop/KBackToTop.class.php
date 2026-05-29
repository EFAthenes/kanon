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
 * Description of KBackToTop
 *
 * @author Louis Mulot
 */
class KBackToTop extends KComponent
{
    final public function __construct(string $name="")
    {
        parent::__construct();
        if(empty($name))
        {
            $name=KRandom::makeRandomString();
        }
        $this->setName($name);
        $this->setNone();
        $this->addJS($this->makeJavaScript());
        $comp=new HTMLComponent($this->makeHtml());
        $this->addComponent($comp);
    }
    private function makeJavaScript() : string
    {
        $js="
<script>
$(document).ready(function()
{
    $('#back-top-".$this->getName()."').hide();
    $(function () 
    {
        $(window).scroll(function () 
        {
            if ($(this).scrollTop() > 100) 
            {
                $('#back-top-".$this->getName()."').fadeIn();
            } 
            else 
            {
                $('#back-top-".$this->getName()."').fadeOut();
            }
        });
        $('#back-top-".$this->getName()." a').click(function () 
        {
            $('body,html').animate(
            {
                    scrollTop: 0
            }, 800);
            return false;
        });
    });
});
</script>    
";
        return $js;
    }

    function makeHtml() : string
    {
        $print='
<p id="back-top-'.$this->getName().'" class="k_back_to_top" style="display: block;">
<a href="#icon-top">
<span></span>
</a>
</p>
        ';
        return $print;
    }
   
    #[\Override]
    public static function testMe() : ?static
    {
        //KDebugger::getInstance()->dump("louis");
        $class=new static("toTop");
        return $class;
    }       
}