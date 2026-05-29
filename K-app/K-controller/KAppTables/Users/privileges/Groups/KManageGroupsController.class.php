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
/**
 * Description of GestionGroupController
 *
 * @author Mateo
 */
class KManageGroupsController extends KController 
{
    public static string $ID_GROUP = "id";
    
    public function execute(): bool
    {  
        $group = new Kapp_Groups();
        
        $id = 0;
        if(KInput::checkInputPost(self::$ID_GROUP, KInput::$VARIABLE_INT, $id) && $group->initById($id))
        {
//            $idGroup = $group->getId();
//            $remarque = $group->getLabel();
            if($group->deleteAll())
            { 
//                $modif = new Modifications();
//                if(!$modif->modifPrivileges($idGroup, Modifications::DELETE_GROUP, $remarque))
//                {
//                    $this->addComponent(new KAlertComponent("Erreur", "Erreur update Modifications :<br />".$modif->getKerror(), KAlertComponent::$TYPE_ERROR));
//                }
                return true;                                    
            }
        }
        return false;
    }
}
