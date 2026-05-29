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
class MatomoComponent extends KComponent
{
    public function __construct(string $url_analytics,string $site_id,string $url_script="")
    {        
        parent::__construct();
        $this->setNone();
        if(empty($url_analytics)||empty($site_id))
        {
            return;
        }
        
        $script='matomo';
        if(!empty($url_script)&& ctype_alpha($script))
        {
            $script=$url_script;
        }
        
        $js='
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);
  (function() {
    var u="'.$url_analytics.'/";
    _paq.push(["setTrackerUrl", u+"'.$script.'.php"]);
    _paq.push(["setSiteId", "'.$site_id.'"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];
    g.async=true; g.src=u+"'.$script.'.js"; s.parentNode.insertBefore(g,s);
  })();
';
        $this->addJSText($js); 
    }
}
