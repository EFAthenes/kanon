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
class ModalChangeLanguageCell extends KComponent
{
    private string $function_name_to_update_content='';
    private string  $function_name_to_get_content='';
    private string $form_id="";
    
    public function __construct(string $modal_id,string $form_id)
    {
        parent::__construct();
        $this->setNone();
        $this->setName($modal_id);
        $this->form_id=$form_id;
        
        $comp =new JoditEditorComponent($this->getName()."_editor", "");
        $this->function_name_to_update_content=$comp->updateByJsStringFunctionName();
        $this->function_name_to_get_content=$comp->getValueByJsStringFunctionName();
        $this->addComponent($comp);
    }
      
    public function draw() : string
    {
        $html='
<div class="modal fade" id="'.$this->getName().'" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Veuillez saisir la traduction :</h5>
        <div class="" style="-webkit-box-pack: end; -ms-flex-pack: end; justify-content: flex-end;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            <button type="button" class="btn btn-primary" onclick="saveModal(\'\',\'\');">Sauvegarder</button>
        </div>
      </div>
      <div class="modal-body">
        <div id="modal-body_'.$this->getName().'">
';
        
        $html.=parent::draw();
        
        $html.='
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary" onclick="saveModal(\'\',\'\');">Sauvegarder</button>
      </div>
    </div>
  </div>
</div> 

<script>
let value_id_'.$this->getName().'="";
function openModal(id_argument,data)
{ 
    if($("#"+id_argument))
    {
        value_id_'.$this->getName().'=id_argument;
        //$("#'.$this->getName().'").modal(); 
        var myModal = new bootstrap.Modal(document.getElementById("'.$this->getName().'"));
        myModal.show();             
        '.$this->function_name_to_update_content.'(data);       
    }
}
function saveModal()
{ 
    let name=value_id_'.$this->getName().';
//    console.log("saveModal");
//    console.log("#div_"+name);

    if($("#div_"+name))
    {
        console.log("exists");
        console.log($("#"+name));

        let new_content=$.trim('.$this->function_name_to_get_content.'());       
        let old_content=$.trim($("#div_"+name).html());
        

        
        if(new_content!=old_content)
        {
            $("#div_"+name).css({"color":"green"});
            $("#div_"+name).html(new_content);  

            if($("#"+name).length==0)
            {
                let input = $(\'<input id="\'+name+\'" name="\'+name+\'" type="hidden" value="">\');
                $("#'.$this->form_id.'").append(input);
                console.log("adding");
            }
            $("#"+name).val(new_content);
        }

        $("#'.$this->getName().'").modal("hide");    
    }
}
</script>
';
        return $html;
    }
    
    
    public function getIdOfBody() : string
    {
        return "modal-body_".$this->getName();
    }
    public function getOpenModalAction(string $value) : string
    {
        return 'openModal_'.$this->getName().'(\''.$value.'\');';
    }
    public function getSaveModalAction(string $id_result) : string
    {
        return 'saveModal_'.$this->getName().'(\''.$id_result.'\');';
    }    
}