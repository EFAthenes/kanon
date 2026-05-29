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
 * Description of UtilsEditTable
 *
 * @author Hippolyte
 */
abstract class UtilsEditTable extends PrivilegesUtilsKController
{
    protected int $CREATION_ID=-1;
    protected int $MODIFICATION_ID=-1;
    /**
     * Variable qui sert à défnir lors du traitement ajax si la
     * suppression a réussie ou non.
     * @var string
     */
    public static string $ACTION_DELETE="action_delete";

    /**
     * 
     * @var string
     */
    public static string $GET_NEW="new";
    
    protected string $routeShow="";
    protected string $idForm="";

    /**
     * Objet type {Groupe d'accès, Accès, Droit ou Groupe}.
     * @var KObject|null
     */
    private ?KObject $object=null;
    
    protected ?KTitleLayoutAdmin $title=null;

    /**
     * 
     * @var bool
     */
    protected bool $new=false;

    /**
     * Met en place la barre de titre
     */
    abstract protected function makeTitle(): void;

    /**
     * Premier traitement de EditTable.
     * Si nouveau, un nouvel objet est crée,
     * Sinon, l'objet est chargé.
     */
    abstract protected function initObject(): bool;

    /**
     * Vérifie si les données rentrées dans le formulaire sont corrects.
     */
    abstract protected function checkIfIsCorrect(): bool;

    public function getObject(): ?KObject
    {
        return $this->object;
    }

    public function setObject(?KObject $object): void
    {
        $this->object = $object;
    }

    /**
     * Met à jour l'enregistrement dans la base de données.
     * @return void
     */
    protected function updateInDb(): void
    {
        if($this->object->updateInBd())
        {
//            $modif=new Modifications();
//            if(!$modif->modifPrivileges($this->object->getId(),$this->MODIFICATION_ID,$this->object->getLabel()))
//            {
//                $this->addComponent(new KAlertComponent("Erreur","Erreur update Modifications :<br />".$modif->getKerror(),KAlertComponent::$TYPE_ERROR));
//            }
            $this->displaySuccessMessage();
        }
        else
        {
            $this->displayErrorMessage("KAPP_GROUP_MODIFICATIONS_ERROR_MSG");
        }
    }

    /**
     * Insert l'enregistrement dans la base de données.
     * @return void
     */
    protected function insertInDb(): void
    {
        if($this->object->insert())
        {
//            $modif=new Modifications();
//            if(!$modif->modifPrivileges($this->object->getId(),$this->CREATION_ID,$this->object->getLabel()))
//            {
//                $this->addComponent(new KAlertComponent("Erreur","Erreur update Modifications :<br />".$modif->getKerror(),KAlertComponent::$TYPE_ERROR));
//            }
            $this->displaySuccessMessage();
        }
        else
        {
            $this->displayErrorMessage("KAPP_GROUP_MODIFICATIONS_ERROR_MSG");
        }
        $this->new=false;
    }
    
//            protected string $routeShow="";
//    protected string $idForm="";    
    protected function setRouteAndIdForm(mixed $routeShow="",mixed $idForm="") : void
    {
        if(!empty($routeShow))
        {
            $this->setRouteShow("".$routeShow);
        }
        if(!empty($idForm))
        {
             $this->setIdForm("".$idForm);
        }        
    }
    
    protected function setRouteShow(string $routeShow): void
    {
        $this->routeShow=$routeShow;
    } 
    protected function setIdForm(string $idForm) : void
    {
        $this->idForm=$idForm;
    }
    
    public function getRouteShow(): string
    {
        return $this->routeShow;
    }

    public function getIdForm(): string
    {
        return $this->idForm;
    }

    
    /**
     * Ajoute la barre de titre pour les nouveaux éléments.
     * @param string $routeShow RouteItems du Show
     * @param string $idForm Nom du formulaire à exposer
     * @return void
     */
    protected function makeTitleNew(string $routeShow="",string $idForm=""): void
    {
        $this->setRouteAndIdForm($routeShow, $idForm);
        $this->title=new KTitleLayoutAdmin(Ki18::_("SHOW_EDIT_TABLE_ELEMENT_NEW_TITLE"),"fa fa-plus-square");
        $backButton=new KTitleButton(Ki18::_("GDA_BACK_TO_LIST"),KTitleButton::$TYPE_INFO,"fa fa-backward");
        $backButton->setActionURL(KRoute::makeURL($this->getRouteShow()));
        $this->title->addKTitleButton($backButton);
        $insertButton=new KTitleButton(Ki18::_("SHOW_EDIT_TABLE_BUTTON_SAVE"),KTitleButton::$TYPE_SUCCESS,"fa fa-check");
        $insertButton->setSubmitForm($this->getIdForm());
        $this->title->addKTitleButton($insertButton);
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$this->title);
        KApp::getInstance()->getLayout()->setTitle($this->title->getTitle());
    }
    
    /**
     * Ajoute la barre de titre pour les nouveaux éléments.
     * @param string $routeShow RouteItems du Show
     * @param string $idForm Nom du formulaire à exposer
     * @return void
     */
    protected function makeTitleEdit(string $routeShow="",string $idForm="") : void
    {
         $this->setRouteAndIdForm($routeShow, $idForm);
        $this->title=new KTitleLayoutAdmin(Ki18::_("SHOW_EDIT_TABLE_ELEMENT_TITLE "),"fas fa-edit");
        $backButton=new KTitleButton(Ki18::_("GDA_BACK_TO_LIST"),KTitleButton::$TYPE_INFO,"fa fa-backward");
        $backButton->setActionURL(KRoute::makeURL($this->getRouteShow()));
        $this->title->addKTitleButton($backButton);
        $insertButton=new KTitleButton(Ki18::_("SHOW_EDIT_TABLE_BUTTON_SAVE"),KTitleButton::$TYPE_SUCCESS,"fa fa-check");
        $insertButton->setSubmitForm($this->getIdForm());
        $this->title->addKTitleButton($insertButton);
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$this->title);
        KApp::getInstance()->getLayout()->setTitle($this->title->getTitle());
    }    

    /**
     * Traitements supplémentaires après l'insertion ou la mise à jour
     * qui suit l'envoie du formulaire.
     * @return void
     */
    protected function afterDbProccesing(): void
    {
        
    }

    /**
     * Premier traitement lors de la fonction execute d'un EditTable.
     * @return bool
     */
    protected function firstProcessingExecute(): bool
    {
        if(!$this->initObject())
        {
            return true;
        }
        if(FormComponent::isAlreadyPost())
        {
            $this->object->getAllInputPost();
            if($this->checkIfIsCorrect())
            {
                $this->new ? $this->insertInDb() : $this->updateInDb();
                $this->afterDbProccesing();
            }
        }
        $this->makeTitle();
        return false;
    }

    /**
     * Renvoie vrai si la variable $string est null, vide ou égale à 'NULL'.
     * @param mixed $string : chaîne à tester
     * @return bool : résultat du test
     */
    protected function isNullOrEmpty(mixed $string): bool
    {
        return is_null($string)||empty($string)||$string=='NULL';
    }

    /**
     * Affiche un message d'erreur en pop-up.
     * @param string $error_language_manager_comment Description du pop-up
     * @param string $error_language_manager_title Titre du pop-up
     */
    protected function displayErrorMessage(string $error_language_manager_comment,
            string $error_language_manager_title="KAPP_GROUP_MODIFICATIONS_ERROR_TITLE"): void
    {
        $this->addComponent(new KNotify(
                        Ki18::_($error_language_manager_title),
                        Ki18::_($error_language_manager_comment),
                        KNotify::$TYPE_DANGER
        ));
    }

    /**
     * Affiche un message de succès en pop-up.
     * @param string $success_language_manager_comment Description du pop-up
     * @param string $success_language_manager_title Titre du pop-up
     */
    protected function displaySuccessMessage(
            string $success_language_manager_comment="KAPP_GROUP_MODIFICATIONS_OK_MSG",
            string $success_language_manager_title="KAPP_GROUP_MODIFICATIONS_OK_TITLE"): void
    {
        $this->addComponent(new KNotify(
                        Ki18::_($success_language_manager_title),
                        Ki18::_($success_language_manager_comment),
                        KNotify::$TYPE_SUCCESS
        ));
    }

}