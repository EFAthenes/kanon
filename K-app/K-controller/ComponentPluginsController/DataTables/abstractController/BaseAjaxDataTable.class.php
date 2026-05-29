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
/**
 * Description of BaseAjaxDataTable
 *
 * @author Maxime Tueux
 */
class BaseAjaxDataTable extends KController
{

    public static string $PARAM_MAX_RESULT = "max_result";
    public static string $PARAM_DB_FIELDS = "db_fields";

    protected string $title = "";
    protected string $icon = "far fa-clock";
    protected string $className;
    /**
     * 
     * @var array<string, string>
     */
    protected array $columnNames = [];
    protected bool $initForeignObject = false;
    protected bool $setButtonBar = false;

    protected DataTableAjaxComponent $dataTable;
    protected DbList $dbList;
    protected KURL $url;
    /**
     * 
     * @var array<string,mixed>
     */
    protected array $ajaxActionParameters=[];

    /**
     * Dans les classes filles, l'appel à cette fonction doit être réalisé avant l'appel à la 
     * fonction parente init() (celle de cette classe). Cette fonction initialise les propriétés
     * propre à chaque implémentation d'un tableau de données AJAX.
     */
    /**
     * 
     * @param string $title
     * @param string $icon
     * @param string $className
     * @param array<string, string> $columnNames
     * @param bool $initForeign
     * @param bool $setButtonBar
     * @return void
     */
    public function setRequiredProperties(
        string $title,
        string $icon,
        string $className,
        array $columnNames,
        bool $initForeign,
        bool $setButtonBar
    ): void {
        $this->title = $title;
        $this->icon = $icon;
        $this->className = $className;
        $this->columnNames = $columnNames;
        $this->initForeignObject = $initForeign;
        $this->setButtonBar = $setButtonBar;
    }

    /**
     * Fonction à appeler dans les classes filles pour ajouter des paramètres à l'action ajax.
     */
    protected function appendAjaxActionParameters(string $key, mixed $value): void
    {
        $this->ajaxActionParameters[$key] = $value;
    }

    public function init(): void
    {
        $tableName = $this->className::$TABLE_NAME;
        $kObject = new $this->className();

        $this->dbList = new DbList($this->className);
        $this->dbList->setInitForeignObject($this->initForeignObject);

        $maxResults = $this->dbList->getNb();

        // Noms des colonnes du tableau de données (et non ceux des colonnes de la DB)
        $fields = array_keys($this->columnNames);

        // Initialisation du tableau de données
        $this->dataTable = new DataTableAjaxComponent('Table_' . $tableName, $kObject, $fields,$maxResults);
        $this->dataTable->setButton_bar($this->setButtonBar);
                
        $this->ajaxActionParameters = [
            BaseAjaxDataTable::$PARAM_MAX_RESULT => $maxResults,
            BaseAjaxDataTable::$PARAM_DB_FIELDS => json_encode(array_values($this->columnNames))
        ];

        $this->url = new KURL();
        $layout=KApp::getInstance()->getLayout();
        $kTitle = new KTitleLayoutAdmin($this->title, $this->icon);
        $layout->addComponent(KAdminLayout::$HEADER, $kTitle);
        $layout->setTitle($this->title);

        $kTitle->setKurl($this->url);
    }
    
    public function getDataTables() : DataTableAjaxComponent
    {
        return $this->dataTable;
    }

    public function execute(): bool
    {
        return true;
    }
}
