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
 * Description of BaseAjaxDataTableController
 *
 * @author Maxime Tueux
 */
abstract class BaseAjaxDataTableController extends KController
{
    public static string $JSON_ERRORS_FIELD = "errors";

    protected string $PARAM_DRAW = "draw";
    protected string $PARAM_LENGTH = "length";
    protected string $PARAM_START = "start";

    protected string $className = "";
    protected bool $initForeign = false;
    /**
     * 
     * @var array<mixed,mixed>|null
     */
    protected ?array $dbFieldNames = null;
    protected ?DbList $dbList = null;

    protected int $draw = 0;
    protected int $length = 0;
    protected int $start = 0;
    protected int $maxResult = 0;
    protected int $resultsCount = 0;

    /**
     * 
     * @var array<int,QueryField>
     */
    protected array $optionalQueryFields = [];
    /**
     * 
     * @var array<int,QueryField>
     */
    protected array $queryFields = [];
    /**
     * 
     * @var array<string,mixed>
     */
    protected array $jsonResponse = [];

    public function setRequiredProperties(string $className, bool $initForeign = false): void
    {
        $this->className = $className;
        $this->initForeign = $initForeign;
    }

    public function appendOptionalQueryField(QueryField $queryField): void
    {
        $this->optionalQueryFields[] = $queryField;
    }

    /**
     * 
     * @param QueryField|array<int,QueryField> $queryField
     * @return void
     */
    public function appendQueryField(QueryField|array $queryField): void
    {
        $this->queryFields[] = $queryField;
    }

    /**
     * Ajoute une erreur à la réponse JSON
     */
    public function appendError(string $error): void
    {
        $this->jsonResponse[self::$JSON_ERRORS_FIELD][] = $error;
    }

    /**
     * Retourne les erreurs stockées dans la réponse JSON
     */
    /**
     * 
     * @return array<int,string>
     */
    public function getErrors(): array
    {
        return $this->jsonResponse[self::$JSON_ERRORS_FIELD];
    }

    /**
     * Retourne true si la réponse JSON contient des erreurs
     */
    public function checkForErrors(): bool
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * Retourne la réponse JSON encodée 
     */
    public function getJsonResponse(): string
    {
        return json_encode($this->jsonResponse);
    }

    /**
     * Ajoute une clé et sa valeur à la réponse JSON
     */
    public function addJsonResponseValue(string $key, mixed $value): BaseAjaxDataTableController
    {
        $this->jsonResponse[$key] = $value;
        return $this;
    }

    public function init(): void
    {
        $jsonEncodedDbFieldNames = null; // Stocke les noms des champs de la table dans la base de données

        // Validation des paramètres de recherche obligatoires communs à toutes les implémentation de tableau de données
        if (
            KInput::checkInputPost($this->PARAM_DRAW, KInput::$VARIABLE_INT, $this->draw) && $this->draw > 0 &&
            KInput::checkInputPost($this->PARAM_LENGTH, KInput::$VARIABLE_INT, $this->length) &&
            KInput::checkInputPost($this->PARAM_START, KInput::$VARIABLE_INT, $this->start) &&
            KInput::checkInputGet(BaseAjaxDataTable::$PARAM_MAX_RESULT, KInput::$VARIABLE_INT, $this->maxResult) &&
            KInput::checkInputGet(BaseAjaxDataTable::$PARAM_DB_FIELDS, KInput::$VARIABLE_ARRAY, $jsonEncodedDbFieldNames)
        ) {
            // Récupération des noms de colonnes de la base données
            $this->dbFieldNames = json_decode($jsonEncodedDbFieldNames);

            $this->dbList = new DbList($this->className);
            $this->dbList->setInitForeignObject($this->initForeign);
        } else {
            $this->appendError("Paramètres de recherche requis manquants (" . $this->PARAM_DRAW . ", " . $this->PARAM_LENGTH . ", " . $this->PARAM_START . ", " . BaseAjaxDataTable::$PARAM_MAX_RESULT . ", " . BaseAjaxDataTable::$PARAM_DB_FIELDS);
        }
    }
    
    public function getDbList() : ?DbList
    {
        return $this->dbList;
    }

    public function execute(): bool
    {
        return true;
    }

    protected function makeSQLOrder(): SqlOrder
    {
        $order = SqlOrder::$ASC;
        $column = $this->dbFieldNames[0];

        if (isset($_POST["order"]) && is_array($_POST["order"]) && isset($_POST["order"][0]) && is_array($_POST["order"][0])) 
        {
            if (isset($_POST["order"][0]["dir"])) 
            {
                switch ($_POST["order"][0]["dir"]) 
                {
                    case "desc":
                        $order = SqlOrder::$DSC;
                        break;
                    default:
                        $order = SqlOrder::$ASC;
                        break;
                }
            }

            if (isset($_POST["order"][0]["column"]) && isInteger($_POST["order"][0]["column"])) 
            {
                $columnIndex = $_POST["order"][0]["column"];
                if (!is_null($this->dbFieldNames[$columnIndex])) 
                {
                    $column = $this->dbFieldNames[$columnIndex];
                }
            }
        }
        return new SqlOrder($column, $order);
    }

    protected function makeSQLLimit(): ?SqlLimit
    {
        $length = 0;
        $start = 0;

        if (
            KInput::checkInputPost($this->PARAM_LENGTH, KInput::$VARIABLE_INT, $length) && 
            KInput::checkInputPost($this->PARAM_START, KInput::$VARIABLE_INT, $start)
        ) {
            return new SqlLimit($length, $start);
        }
        return null;
    }
}
