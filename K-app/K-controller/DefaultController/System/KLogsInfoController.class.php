<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2024 EFA, Ecole française d'athènes, EFAthenes.
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
class KLogsInfoController extends KController
{
    public function execute(): bool
    {
        KApp::getInstance()->getLayout()->setTitle("Logs Info");
        $title = new KTitleLayoutAdmin("Logs Info", "fa-solid fa-clipboard-list");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER, $title);   
        $button= new KTitleButton("Vider les logs", KTitleButton::$TYPE_WARNING,"fa-solid fa-trash");
        $title->addKTitleButton($button);
        $button->setClickAction("cleanLogs();");  
        $this->cleanLogJs();
        $tile1=new TileComponent();
        $tile1->addComponent(new KLogsComponent());
        $this->addComponent($tile1);        
        return true;
    }
    protected function cleanLogJs() : void
    {
        $js='
<script>            
function cleanLogs()
{
    let message = document.createElement("div");
    message.innerHTML = "Videz les logs ?";
    swal(
    {
        content: message,
        buttons: ["Annuler", "Continuer"],
    }).then((result) =>
    {
        if (result) 
        {
            $.ajax({
                url: "'.KRoute::makeActionURL(KRoutesItems::$LOGS_CLEAN).'",
                type: "POST",
                success: function(data)
                {
                    location.reload();           
                },    
                error: function(data)
                {
                    let message2 = document.createElement("div");
                    message2.innerHTML = "Error";                    
                    swal(
                    {
                        content: message2
                    });
                }
            });
        }
    });
}   
</script>
';
        $this->addString($js);
    }    
}