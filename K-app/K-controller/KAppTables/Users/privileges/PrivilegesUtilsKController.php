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
 * Description of PrivilegesUtilsKController
 *
 * @author Hippolyte
 */
abstract class PrivilegesUtilsKController extends KController
{
       
    /**
     * Renvoie le script JS du bouton delete.
     * @param string $routeGestion route de l'action à appeler
     * @param string $routeShow route de la page à charger (ShowAll-----.class.php)
     * @param $ACTION_DELETE variable static qui permet de savoir si un élément a été supprimé 
     * afin d'afficher un message de confirmation ou d'erreur (Edit-----.class.php)
     * @return string le JS à insérer dans la Page
     */
    protected function addJSActionButtonDelete(string $routeGestion,string $routeShow,string $ACTION_DELETE): string
    {
        return '
<script>            
function addJSActionButtonDelete(id)
{
    let message = document.createElement("div");
    message.innerHTML = "Vous êtes sur le point de supprimer l\'élément avec l\'id n°"+id+". Poursuivre ?";
    swal(
    {
        content: message,
        buttons: ["Annuler", "Continuer"],
    }).then((result) =>
    {
        if (result) 
        {
            $.ajax({
                url: "'.KRoute::makeActionURL($routeGestion).'",
                type: "POST",
                data: {
                    id:id,
                },
                success: function(data)
                {
                    window.location.href = "'.KRoute::makeURLNoAmp($routeShow,[$ACTION_DELETE=>1]).'";
                },    
                error: function(data)
                {
                    window.location.href = "'.KRoute::makeURLNoAmp($routeShow,[$ACTION_DELETE=>2]).'";
                }
            });
        }
    });
}   
</script>
';
    }

}