<?php

declare(strict_types=1);
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
/*
 * @author Mulot Louis
 */

abstract class KAbstractTemplateController extends KController
{
    public function makeTitle(string $titleString,string $icon,?string $returnRoute=null) : void
    {
        KApp::getInstance()->getLayout()->setTitle($titleString);
        $title = new KTitleLayoutAdmin($titleString,$icon);
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER, $title);
        if(!is_null($returnRoute))
        {
            $retour = new KTitleButton(Ki18::_("KTEMPLATE_PREVIOUS_PAGE"), ButtonComponent::$TYPE_INFO, "fa fa-backward");
            $retour->setActionRouteItem($returnRoute);     
            $title->addKTitleButton($retour);            
        }
    }
}
