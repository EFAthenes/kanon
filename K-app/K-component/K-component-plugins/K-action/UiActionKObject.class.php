<?php
/**
class UiActionKObject extends KComponent
{
    private $kpage=null;
    private $object=null;
    private $the_id=0;
    private $modified=false;
    private $is_new=false;
    private $old_object=null;
    function __construct(KPage $kpage,$id,  KObject &$object,$allow_fusion=false,$allow_copy=true,$allow_create_new=true)
    {
        parent::__construct();
        $this->setNone(); 
        
        $this->kpage=$kpage;
        $this->object=&$object;
        $this->old_object=$object;
        $this->the_id=$id;
        
        $html="";       
        if($this->the_id>0)
        {
            $this->object->initAllByBd($this->the_id);
            $this->object->setIdentifierPostGet($this->object->getId());
        }
        else if($this->the_id==-1)
        {
            $this->object->setId("");
        }
        else
        {
            exit();
        }
        
        if(KForm::checkFormSubmit())
        {
            $alertComponent=null;            
            $this->object->getAllInputPost();
            $this->object->getAllInputPostList();
            if($this->object->getId()>0&&$this->object->updateInBdAll())
            {
                $message=$this->allowNewCopy($this->object,$allow_copy,$allow_create_new);
                $alertComponent=new KAlertComponent(LanguageManager::getInstance()->get("ACTION_SUCCESS_TITLE"),LanguageManager::getInstance()->get("ACTION_MODIFIED_SUCCESS").$message,KAlertComponent::$TYPE_SUCCESS,"",true);                
                $this->modified=true;                
            }
            elseif($object->getId()==""&&$object->insert())
            {
                $message=$this->allowNewCopy($this->object,$allow_copy,$allow_create_new);
                $alertComponent=new KAlertComponent(LanguageManager::getInstance()->get("ACTION_SUCCESS_TITLE"),LanguageManager::getInstance()->get("ACTION_SAVE_SUCCESS_MESSAGE").$message,KAlertComponent::$TYPE_SUCCESS,"",true);                
                $this->modified=true;                
            }
            else
            {
                $alertComponent=new KAlertComponent(LanguageManager::getInstance()->get("ACTION_ERROR_TITLE"),LanguageManager::getInstance()->get("ACTION_ERROR_MESSAGE"),KAlertComponent::$TYPE_ERROR,"",true);                
            }
            $this->addComponent($alertComponent);
        }
                
        $toolBar=new UiToolBarAction($this->object->getId(),$allow_fusion,$allow_copy,$allow_create_new);        
        $kpage->addComponent(KPage::$HEADER,$toolBar);      
    }
    
    public function addToForm($htmlText)
    {   
        $url=new KURL();
        $url->removeArg(HistoryPage::$BACK_STRING);
        $url->addOrReplace("id",$this->showIdValue($this->object->getId()));
        $url->addOrReplace("check", "1");
        
        $html=' 
<form class="form-horizontal" id="form_action_entity" action="'.$url->printURLWithoutAmp().'" method="post" onkeypress="" >
<input type="hidden" name="'.KForm::$IDENTIFIER.'" value="1">
'.$htmlText.'
</form>   
';
        $component= new HTMLComponent($html);
        $this->addComponent($component);
    }
    
    public function verification($arrayVerification=null)
    {
        $jsText='';
        if($arrayVerification!=null && count($arrayVerification)>0)
        {           
            //$verif=new UiActionVerificationItem();
            for($i =0;$i<count($arrayVerification);$i++) 
            {
                $verif=$arrayVerification[$i];
                $jsText.='
    if($("#'.$verif->getField().'").val()'.$verif->getCondition().'"'.$verif->getValue().'")
    {
        alertify.alert("'.LanguageManager::getInstance()->get("MODAL_ERROR_TITLE").'","'.$verif->getString_error().'", 
        function()
        {
                                        
        });
        return false;
    }               
';
            }
        }
        
        $component= new HTMLComponent("");
        $js='
function canFormBeSubmited()
{
'.$jsText.'
    return true;
}
';        
        $component->addJSText($js);
        $this->addComponent($component);        
    }
    
    private function allowNewCopy(KObject &$object,$allow_copy,$allow_create_new)
    {        
        $message="";
        if($allow_create_new&&isset($_GET[KForm::$NEW]) && $_GET[KForm::$NEW] == 1)
        {       
            $className=$object->getClassName();
            $object = new $className();           
            $object->setId(-1);
            $object->setIdentifierPostGet("");
            $message="<br /><br />".LanguageManager::getInstance()->get("ACTION_NEW_ITEM");
            $this->is_new=true;
        }
        else if($allow_copy&&isset($_GET[KForm::$COPY]) && $_GET[KForm::$COPY] == 1)
        {
            $object->setId(-1);
            $object->setIdentifierPostGet("");
            $message="<br /><br />".LanguageManager::getInstance()->get("ACTION_COPY_ITEM");
        }
        return $message;
    }
    protected function showIdValue($id)
    {
        $the_id="";
        if(isInteger($id))
        {
            $the_id=$id;
        }
        else
        {
            $the_id=-1;
            $this->is_new=true;
        }
        return $the_id;       
    }
    function getModified()
    {
        return $this->modified;
    }
    function setModified($modified)
    {
        $this->modified = $modified;
    }
    function getOld_object()
    {
        return $this->old_object;
    }
    function setOld_object($old_object)
    {
        $this->old_object = $old_object;
    }
    function getIs_new()
    {
        return $this->is_new;
    }
    function setIs_new($is_new)
    {
        $this->is_new = $is_new;
    }
    
    function __destruct()
    {
        parent::__destruct();
    }
}
 * 
 */