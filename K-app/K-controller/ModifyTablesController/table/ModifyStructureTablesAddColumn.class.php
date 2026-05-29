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
class ModifyStructureTablesAddColumn extends AbstractTableController
{
    private const string COL_NAME="col_name";
    private const string COL_TYPE="col_type";
    private const string FORM_ID="ModifyStructureTables";
    private string $col_value="";
    private string $col_type_value="";
    private ?FormComponent $form=null;
    
    public function execute(): bool
    {  
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ADD_COLUMN"),"fa fa-plus-circle",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
        
        $url=new KURL();
        $this->form=new FormComponent($url->printURLWithoutAmp(), self::FORM_ID);
        
        $this->checkPostResponse();

        //DESIGN UI
        //#############
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent("Veuillez saisir le nom de votre colonne :"));
        $grid->addRowComponent();
        $grid->addColComponent(new InputStringComponent($this->col_value,self::COL_NAME,"Nom de la colonne","les espaces, majuscules, accents ne sont pas acceptés",true,false,2,10));
        $grid->addRowComponent();
        $grid->addColComponent(new KSelectComponent(self::COL_TYPE, KField::allTypesToArray(),null,false,"Type de la colonne",false,2,10));
        $grid->addRowComponent();        
        $grid->addColComponent(new ButtonComponent("Sauvegarder", ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));      
        $this->form->addComponent($grid);
        $this->addComponent($this->form);
        
        $this->addComponent(new ShowStructureDbTableInTable($this->getTablename()));

        return true;
    }
    
    private function checkPostResponse() : void
    {
        if($this->form->isAlreadyPost()
                &&KInput::checkInputPost(self::COL_NAME, KInput::$VARIABLE_STRING, $this->col_value)
                &&KInput::checkInputPost(self::COL_TYPE, KInput::$VARIABLE_STRING, $this->col_type_value))
        {
            $this->col_value=strtolower($this->col_value);
            if(!isAlphaNumericAndUndescore($this->col_value))
            {
                $notify = new KAlertComponent("Erreur lors de l'ajout", "Le nom de colonne contient des caractères non acceptés!", KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }
            else if(in_array($this->col_value,$this->getKObject()->fieldsLabelToArray(false)))
            {
                $notify = new KAlertComponent("Erreur lors de l'ajout", "Le nom de colonne est déjà utilisé!", KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }            
            else if(!in_array($this->col_type_value,KField::allTypesToArray()))
            {
                $notify = new KAlertComponent("Erreur lors de l'ajout", "Le type de colonne est inconnu!", KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);                
            }
            else
            {
                $kField=KField::make($this->col_type_value,null,["name"=>$this->col_value]);
                $list= new ArrayList();
                $list->add($kField);
                if(Sql::getInstance()->addFields($this->getTablename(),$list))
                {                
                    $notify = new KAlertComponent("Ajout de colonne", "Réussi => '".$this->col_value."' de type (".$this->col_type_value.")!!!", KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($notify);
                    $this->col_value="";
                    
                    $manage=new KManageDb(KApp::getInstance()->getAppDirectoryName());
                    
                    $cli= new KCli();
                    $cli->outputToBuffer(true);                    
                    $manage->buildFiles($cli);
                    $report=$cli->getBufferToString();
                    $area=new TextAreaComponent($report,"","Log","", false, true);
                    $this->addComponent($area);
                    
                    $this->initKobject();
                }
                else
                {
                    $notify = new KAlertComponent("Erreur lors de l'ajout", Sql::getInstance()->getError(), KAlertComponent::$TYPE_ERROR);
                    $this->addComponent($notify);                       
                }
            }
        }        
    }
}
