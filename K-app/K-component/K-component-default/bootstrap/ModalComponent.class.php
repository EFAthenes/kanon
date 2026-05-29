<?php
/**
 * Description of ModalComponent.class
 *
 * @author louis.mulot
 */
class ModalComponent extends KComponent
{
    private KURL $kurlActionLoading;
    private KURL $kurlActionSave;
    private string $result_modal_id="";
    private string $scriptAfterActionSave="";
    private string $scriptAfterOpendModal="";
    private string $label="";
    public function __construct(
            string $modal_id, 
            KURL $kurlActionLoading,
            KURL $kurlActionSave,
            string $result_modal_id
            )
    {
        parent::__construct();
        $this->setNone();
        $this->setName($modal_id);
        $this->kurlActionLoading=$kurlActionLoading;
        $this->kurlActionSave=$kurlActionSave;
        $this->result_modal_id=$result_modal_id;
    }
    
    public function setLabel(string $label) : void
    {
        $this->label=$label;
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }

    
    
    public function addScriptAfterActionSave(string $scriptAfterActionSave) : void
    {
        $this->scriptAfterActionSave=$scriptAfterActionSave;
    }
    
    public function addScriptAfterOpenedModal(string $scriptAfterOpendModal) : void
    {
        $this->scriptAfterOpendModal=$scriptAfterOpendModal;
    }    
    
    public function draw() : string
    {
        $html='
<div class="modal fade" id="'.$this->getName().'" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">'.$this->getLabel().'</h5>
        <div class="" style="-webkit-box-pack: end; -ms-flex-pack: end; justify-content: flex-end;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            <button type="button" class="btn btn-primary" onclick="saveModal_'.$this->getName().'(\''.$this->result_modal_id.'\');">Sauvegarder</button>
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary" onclick="saveModal_'.$this->getName().'(\''.$this->result_modal_id.'\');">Sauvegarder</button>
      </div>
    </div>
  </div>
</div>    

<script>
function openModal_'.$this->getName().'(id_argument)
{ 
    if($("#"+id_argument))
    {
        $.ajax({
            url: "'.$this->kurlActionLoading->printURLWithoutAmp().'",
            type: "POST",
            data: {
                value:$("#"+id_argument).val()
            },
            success: function(data)
            {
                $("#modal-body_'.$this->getName().'").html(data);
                $("#'.$this->getName().'").modal();  
                '.$this->scriptAfterOpendModal.'    
            }
        });
    }
}
function saveModal_'.$this->getName().'(id_result)
{ 
    let inputs = $("#modal-body_'.$this->getName().'").find(":input");
    let value={};
    inputs.each(function(){   
        if($(this).attr("id")!="undefined")
        {
            value+="&"+$(this).attr("id")+"="+$(this).val();       
        }
    });
    //console.log(value);
    
    $.ajax({
        url: "'.$this->kurlActionSave->printURLWithoutAmp().'",
        type: "POST",
        data: value,
        success: function(data)
        {
            //console.log("#"+id_result+"//"+data);
            if($("#"+id_result))
            {
                $("#"+id_result).val(data);
                $("#'.$this->getName().'").modal("hide");
                '.$this->scriptAfterActionSave.'
            }
        }
    });
    
}
</script>
';      
        return $html;
    }
    
    
    public function getIdOfBody() : string
    {
        return "modal-body_".$this->getName();
    }
    public function getOpenModalAction(string $value): string
    {
        return 'openModal_'.$this->getName().'(\''.$value.'\');';
    }
    public function getSaveModalAction(string $id_result): string
    {
        return 'saveModal_'.$this->getName().'(\''.$id_result.'\');';
    }    
}