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
class KChangeLanguageItems extends KController
{
    private const string FORM_ID='form_edit_language';
    public function execute(): bool
    {
        $defaultLang = LanguageManager::getInstance()->getLanguage();
        $kurl =new KURL();
        
        $form=new FormComponent($kurl->printURLWithoutAmp(),self::FORM_ID);  
        $div_id=new DivIdComponent("form_data");
        $div_id->setStyleCode("display:none;");
        $form->addComponent($div_id);        
        $this->addComponent($form);
        
        $this->managePost($form);
        
        $this->makeTitleBar();

        $tile=new TileComponent();
        $dataTables = new DataTableSimpleTableComponent("lang_list", $this->makeArrayOfColumns(), $this->makeArrayOfLanguagesItems());
        $dataTables->setButton_bar(true);
        $tile->addComponent($dataTables);
        $this->addComponent($tile);
        $modal=new ModalChangeLanguageCell("lang_modal",self::FORM_ID);
        $this->addComponent($modal);       
        //$this->addComponent(new PostGetComponent());


        $js='
<script>            
function changeItemLanguage(id)
{
    console.log("#div_"+id);
//    console.log($("#div_"+id).html());
    openModal(id,$("#div_"+id).html());
}

function checkData()
{
    $("#form_edit_language").submit();
}
</script>
';
      
        
        $this->addString($js);           

        LanguageManager::getInstance()->initLanguage($defaultLang);
        return true;
    }
    
    private function managePost(FormComponent $form) : void
    {  
        if($form->isAlreadyPost())
        {
            //LanguageManager::getInstance()->initTemplateLanguageDir();
            $defaultLang=LanguageManager::getInstance()->getLanguage();         
            $arrayLang = LanguageManager::getInstance()->getArrayOfLanguages();
            
            $modified_lang_items=[];
            $count=0;
            foreach ($arrayLang as $lang)
            {
                if (LanguageManager::getInstance()->initLanguage($lang))
                {
                    $modified_lang_items[$lang]=[];
                    $lang_key=$lang.LanguageDatatableCell::SEPARATOR;

                    //$map = LanguageManager::getInstance()->getMapOfItems();                

                    foreach ($_POST as $key=>$value)
                    {
                        if(str_starts_with($key, $lang_key))
                        {
                            $newKey=str_replace($lang_key,"",$key);
                            $modified_lang_items[$lang][$newKey]=str_replace(["\n","\r","\t"],"",$value);
                            $count++;
                        }
                    }
                }
            }
            
            if($count)
            {
                //KDebugger::getInstance()->dump($modified_lang_items,"Modifications");    

                if(LanguageManager::getInstance()->updateTemplateLang($modified_lang_items))
                {
                    $this->addComponent(new KAlertComponent("Modification reussie","Traductions modifiées = >".$count, KAlertComponent::$TYPE_SUCCESS));
                }   
                else
                {
                    $this->addComponent(new KAlertComponent("Erreur","Une erreur s'est produite!", KAlertComponent::$TYPE_ERROR));
                }
            }

            LanguageManager::getInstance()->initLanguage($defaultLang);     
        }
        
    }
    
    /**
     * 
     * @return array<int,string>
     */
    private function makeArrayOfColumns() : array
    {    
        $arrayLang = LanguageManager::getInstance()->getArrayOfLanguages();
        array_unshift($arrayLang, "Item");
        return $arrayLang;
    }
    
    /**
     * 
     * @return array<int,mixed>
     */
    private function makeArrayOfLanguagesItems(): array
    {
        
        $arrayLang = LanguageManager::getInstance()->getArrayOfLanguages();
              

        $newMAp = new HashMap();
        foreach ($arrayLang as $lang)
        {
            //$this->addString($lang."<br />");
            if (LanguageManager::getInstance()->initLanguage($lang))
            {
                //$this->addString($lang."<br />");
                $map = LanguageManager::getInstance()->getMapOfItems();
                
                foreach ($map as $key => $value)
                {
                    $array = $newMAp->get($key);
                    if (is_null($array))
                    {
                        $array = [];
                        $array[$lang] = $value;
                        $newMAp->put($key, $array);
                    }
                    else
                    {
                        $array[$lang] = $value;
                        $newMAp->putOrReplace($key, $array);
                    }
                }
            }
        }
        $arrayLines=[];
        $count = 0;
        if ($newMAp->getSize())
        {
            foreach ($newMAp as $key => $item)
            {
                $count++;
                //$this->addComponent(new LanguagePrintItem($key, $item));
                //$arrayLines[]=[]
                $array=[$key];
                
                
                foreach ($arrayLang as $lang)
                {
                    if(array_key_exists($lang, $item))
                    {
                        $array[]=new LanguageDatatableCell($item[$lang],$lang,$key);
                    }
                    else
                    {
                        $array[]=new LanguageDatatableCell("",$lang,$key);
                    }
                }
                $arrayLines[]=$array;
            }
        }
        else
        {
            // File empty or not presents
        }       
        return $arrayLines;
    }
    
    private string $component_id="";
    public function setComponentId() : void
    {
        $this->component_id=KAdminLayout::$HEADER;
    }
    
    public function init(): void
    {
        parent::init();
        $this->setComponentId();
    }
    
    private function makeTitleBar() : void
    {
        KApp::getInstance()->getLayout()->setTitle(LanguageManager::_("ONGLET_TITLE_ACCADM"));
        $title = new KTitleLayoutAdmin(LanguageManager::_("LA_TITLE"), "fa fa-language");
        KApp::getInstance()->getLayout()->addComponent($this->component_id, $title);
        $saveButton= new KTitleButton(LanguageManager::_("GA_OPE_SAVE"),KTitleButton::$TYPE_PRIMARY,"fa fa-times");     
        $saveButton->setClickAction("checkData();");
        $title->addKTitleButton($saveButton);
    }
}