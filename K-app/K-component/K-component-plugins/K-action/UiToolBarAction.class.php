<?php
/**
 * Description of UiToolBarAction

class UiToolBarAction extends KComponent
{
    function __construct($id,$allow_fusion=true,$allow_copy=true,$allow_create_new=true)
    {
        parent::__construct();
        $this->setNone(); 
        $html="";
        
        $html='
<div class="action_toolbar">
    <button onclick="saveEntity();" id="button1id" class="btn-small-toolbar-save"><span class="glyphicon glyphicon-ok btn-small-toolbar-save-ico" aria-hidden="true"></span> '.LanguageManager::getInstance()->get("ACTION_TOOLBAR_1").'</button>
    
';
        if($allow_create_new)
        {
        $html.='
    <button onclick="saveAndNew();" id="button2id" class="btn-small-toolbar-normal"><span class="glyphicon glyphicon-plus btn-small-toolbar-normal-ico" aria-hidden="true"></span> '.LanguageManager::getInstance()->get("ACTION_TOOLBAR_2").'</button>
';
        }
        if($allow_copy)
        {
        $html.='
    <button onclick="saveAndCopy();" id="button3id" class="btn-small-toolbar-normal"><span class="glyphicon glyphicon-edit btn-small-toolbar-normal-ico" aria-hidden="true"></span> '.LanguageManager::getInstance()->get("ACTION_TOOLBAR_3").'</button>
';
        }
        $html.='
    <button onclick="cancelToolBar();" id="button4id" class="btn-small-toolbar-normal"><span class="glyphicon glyphicon-share btn-small-toolbar-normal-ico" aria-hidden="true"></span> '.LanguageManager::getInstance()->get("ACTION_TOOLBAR_4").'</button>
';
        if($id>0)
        {
            $html.='
    <button onclick="deleteElement();" id="button5id" class="btn-small-toolbar-cancel" ><span class="glyphicon glyphicon glyphicon-remove btn-small-toolbar-cancel-ico" aria-hidden="true"></span> '.LanguageManager::getInstance()->get("ACTION_TOOLBAR_5").'</button>
';
            if($allow_fusion)
            {
            $html.='
<button onclick="mergeElement();" id="button6id" class="btn-small-toolbar-cancel" ><span class="glyphicon glyphicon-screenshot btn-small-toolbar-cancel-ico" aria-hidden="true"></span> '.LanguageManager::getInstance()->get("ACTION_TOOLBAR_6").'</button>           
';
            }
        }
        $html.='
</div>         
';

        $url=new KURL();
        $url->removeArg("id");
        $url->removeArg(KForm::$IDENTIFIER);
        $url->removeArg(KForm::$COPY);
        $url->removeArg(KForm::$NEW);
        $url->removeArg(HistoryPage::$BACK_STRING);
        $js='
function saveEntity()
{
    if(canFormBeSubmited())
    {
        var form_url = $("#form_action_entity").attr("action");
        if(form_url!=null&&form_url!="")
        {
            form_url=form_url.replace("&'.KForm::$COPY.'=1","");
            form_url=form_url.replace("&'.KForm::$NEW.'=1","");
            $("#form_action_entity").attr("action",form_url);
            $("#form_action_entity").submit();
        }        
    }
}
function cancelToolBar()
{
    window.location="'.$url->printURLWithoutAmp().'";
}
function saveAndNew()
{
    if(canFormBeSubmited())
    {
        var form_url = $("#form_action_entity").attr("action");
        if(form_url!=null&&form_url!="")
        {
            form_url=form_url.replace("&'.KForm::$COPY.'=1","");
            form_url=form_url.replace("&'.KForm::$NEW.'=1","");        
            form_url+="&'.KForm::$NEW.'=1";
            $("#form_action_entity").attr("action",form_url);
            $("#form_action_entity").submit();
        }
    }
}
function saveAndCopy()
{
    if(canFormBeSubmited())
    {
        var form_url = $("#form_action_entity").attr("action");
        if(form_url!=null&&form_url!="")
        {
            form_url=form_url.replace("&'.KForm::$COPY.'=1","");
            form_url=form_url.replace("&'.KForm::$NEW.'=1","");
            form_url+="&'.KForm::$COPY.'=1";
            $("#form_action_entity").attr("action",form_url);
            $("#form_action_entity").submit();
        }
    }
}
function deleteElement()
{
    alertify.dialog("confirm")
    .setting(
    {
        "title":"'.LanguageManager::getInstance()->get("ACTION_COLLECTION_MODAL_ERROR_TITLE").'" ,
        "labels":{ok:"'.LanguageManager::getInstance()->get("ACTION_DELETE_ENTITY_YES").'", cancel:"'.LanguageManager::getInstance()->get("ACTION_DELETE_ENTITY_NO").'"},
        "message": "'.LanguageManager::getInstance()->get("ACTION_DELETE_ENTITY").'" ,
        "onok": function()
        {
            var form_url = $("#form_action_entity").attr("action");
            if(form_url!=null&&form_url!="")
            {
                form_url=form_url.replace("&'.KForm::$COPY.'=1","");
                form_url=form_url.replace("&'.KForm::$NEW.'=1","");
                var parameters=form_url.split("?");

                if(parameters.length==2)
                {
                    var dataPost=parameters[1];
                    $.ajax({
                        type: "POST",
                        url:  "./action/site/admin_delete_data.php",
                        data: dataPost,
                        success: function(data)
                        {
                            if(data==1)
                            {
                                alertify.alert("'.LanguageManager::getInstance()->get("USER_MODIFY_MODAL_3_SUCCESS_1").'","'.LanguageManager::getInstance()->get("ACTION_DELETE_ENTITY_SUCCESS_RELOAD").'", 
                                function()
                                {
                                    window.location="'.$url->printURLWithoutAmp().'";
                                }); 
                            }
                            else
                            {
                                alertify.alert("'.LanguageManager::getInstance()->get("USER_MODIFY_MODAL_3_ERROR_TITLE").'","'.LanguageManager::getInstance()->get("USER_MODIFY_MODAL_3_ERROR_TEXT ").'",function(){});
                            }
                        }   
                    });
                }
            }
        },
        "oncancel": function()
        { 
            alertify.error("'.LanguageManager::getInstance()->get("ACTION_DELETE_ENTITY_NO_LABEL").'");
        }
    }).show();
}
function mergeElement()
{
    alertify.dialog("prompt")
    .setting(
    {
        "title":"'.LanguageManager::getInstance()->get("ACTION_MERGE_ENTITY_TITLE").'" ,
        "labels":{ok:"'.LanguageManager::getInstance()->get("ACTION_MERGE_ENTITY_YES").'", cancel:"'.LanguageManager::getInstance()->get("ACTION_MERGE_ENTITY_NO").'"},
        "message": "'.LanguageManager::getInstance()->get("ACTION_MERGE_ENTITY").'" ,
        "onok": function()
        {
            var id_fusion=$(".ajs-input").val();
            if(id_fusion>0)
            {
                var form_url = $("#form_action_entity").attr("action");
                if(form_url!=null&&form_url!="")
                {
                    form_url=form_url.replace("&'.KForm::$COPY.'=1","");
                    form_url=form_url.replace("&'.KForm::$NEW.'=1","");
                    var parameters=form_url.split("?");

                    if(parameters.length==2)
                    {
                        var dataPost=parameters[1]+"&id_fusion="+id_fusion;
                        $.ajax({
                            type: "POST",
                            url:  "./action/site/admin_fusion_data.php",
                            data: dataPost,
                            success: function(data)
                            {
                                if(data==1)
                                {
                                    alertify.alert("'.LanguageManager::getInstance()->get("USER_MODIFY_MODAL_3_SUCCESS_1").'","'.LanguageManager::getInstance()->get("ACTION_MERGE_ENTITY_SUCCESS_RELOAD").'", 
                                    function()
                                    {
                                        window.location="'.$url->printURLWithoutAmp().'";
                                    }); 
                                }
                                else if(data==2)
                                {
                                    alertify.alert("'.LanguageManager::getInstance()->get("ACTION_MERGE_ERROR_TITLE").'","'.LanguageManager::getInstance()->get("ACTION_MERGE_ERROR_TEXT_2 ").'",function(){});
                                }
                                else
                                {
                                    alertify.alert("'.LanguageManager::getInstance()->get("ACTION_MERGE_ERROR_TITLE").'","'.LanguageManager::getInstance()->get("ACTION_MERGE_ERROR_TEXT_1 ").'",function(){});
                                }
                            }   
                        });
                    }
                }
            }
            else
            {
                alertify.error("'.LanguageManager::getInstance()->get("ACTION_MERGE_ENTITY_ERROR_LABEL_1").'");
            }
        },
        "oncancel": function()
        { 
            alertify.error("'.LanguageManager::getInstance()->get("ACTION_MERGE_ENTITY_NO_LABEL").'");
        }
    }).show();
}
';
        
        $component= new HTMLComponent($html);
        $component->addJSText($js);
        $this->addComponent($component);
    }
    function __destruct()
    {
        parent::__destruct();
    }
}
 * 
 */