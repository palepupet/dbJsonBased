<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\Utils;
use Palepupet\DbJsonBased\DbJsonBasedStructureInterface;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidKeyException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;

class DbJsonBased
{
    public const START_BY = "START";
    public const END_BY = "END";
    public const CONTAINS = "CONTAINS";
    public const EXACT = "EXACT";

    /**
     * __construct
     *
     * @param string $dbName Path of the json database, without extension, or only .json extension allowed
     * @throws DbJsonBasedInvalidArgumentException
     */
    public function __construct(private string $dbName)
    {
        // With .json extension provided incorrectly
        if (isset(pathinfo($dbName)["extension"]) && pathinfo($dbName)["extension"] !== "json") {
            throw new DbJsonBasedInvalidArgumentException("The provided extension '." . pathinfo($dbName)["extension"] . "' is invalid. Please indicate your database's name without extension or with '.json' extension only.");
        }

        // With .json extension provided correctly
        if (isset(pathinfo($dbName)["extension"]) && pathinfo($dbName)["extension"] === "json") {
            $this->dbName = $dbName;
        } else {
            // Without extension
            $this->dbName = $dbName . ".json";
        }
    }

    /**
     * createDb
     * 
     * Create the json database
     *
     * @param DbJsonBasedStructureInterface $structure Structure of the future Table
     * @throws DbJsonBasedRuntimeException
     * @return bool
     */
    public function createDb(DbJsonBasedStructureInterface $structure): bool
    {
        // Create the structure
        $newStructure = [
            $structure->getTableName() => [
                "COLUMNS" => $structure->getColumns(),
                "VALUES" => [],
                "ID" => null
            ]
        ];

        if (Utils::isFileExist($this->dbName)) {
            // Adding new structures
            $previousStructure = Utils::getContentAndDecode($this->dbName);
            $newStructure = array_merge($previousStructure, $newStructure);
        }

        Utils::encodeAndWriteFile($this->dbName, $newStructure);

        return true;
    }

    /**
     * getFullName
     * 
     * Return the full name (with extension) of the database
     *
     * @return string
     */
    public function getFullName(): string
    {
        return basename($this->dbName);
    }

    /**
     * getName
     * 
     * Return the name (without extension) of the database
     *
     * @return string
     */
    public function getName(): string
    {
        return basename($this->dbName, ".json");
    }

    /**
     * getPath
     * 
     * Return the path (with extension) of the database
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->dbName;
    }

    /**
     * findAll
     * 
     * Return all datas from database's table
     *
     * @param string $tableName The targeted table into the database
     * @throws DbJsonBasedInvalidArgumentException
     * @return array
     */
    public function findAll(string $tableName): array
    {
        $datas = $this->getVerifiedTable($tableName);

        return $datas["VALUES"];
    }

    /**
     * findOne
     * 
     * Return the data equal to the provided id
     *
     * @param string $tableName The targeted table into the database
     * @param int $id The targeted ID
     * @throws DbJsonBasedInvalidArgumentException
     * @return array
     */
    public function findOne(string $tableName, int $id): array
    {
        $datas = $this->findAll($tableName);

        $result = array_filter($datas, function ($item) use ($id) {
            return $item["ID"] === $id;
        });

        // If ID does not exist
        if (empty($result)) {
            throw new DbJsonBasedInvalidArgumentException("The provided ID does not exist in the Database.");
        }

        return array_values($result);
    }

    /**
     * findOneBy
     * 
     * Return all datas that meet the filters
     *
     * @param string $tableName The targeted table into the database
     * @param array $criteria The criteria on which to filter, "EXACT" by default
     * 
     * example with filter : [ ["first_name" => "John|filter"] ]
     * 
     * example with filters : [ ["first_name" => "Jo|filter"], ["last_name" => "n|filter"] ]
     * 
     * example without filter : [ ["first_name" => "John"] ] so "EXACT" filter implicitly
     * 
     * Valid Filters are **START_BY**, **END_BY**, **CONTAINS** and **EXACT** only.
     * You can use the class constants provided **DbJsonBased::START_BY|END_BY|CONTAINS|EXACT**
     * 
     * @param bool $caseSensitive If we have to respect the case sensitive, "true" by default
     * @throws DbJsonBasedInvalidArgumentException
     * @return array
     */
    public function findOneBy(string $tableName, array $criteria, bool $caseSensitive = true): array
    {
        $datas = $this->findAll($tableName);
        $filters = [
            DbJsonBased::START_BY,
            DbJsonBased::END_BY,
            DbJsonBased::CONTAINS,
            DbJsonBased::EXACT
        ];

        foreach ($criteria as $key => $criterion) {

            if (!$caseSensitive) {
                $criterion = strtoupper($criterion);
            }

            // Check if we have a filter
            preg_match("/(\D{0,})\|(\D{0,})$/", $criterion, $matches);

            if ($matches && in_array($matches[2], $filters)) {

                switch ($matches[2]) {

                    case DbJsonBased::START_BY:
                        $filterFunction = function ($item) use ($key, $matches, $caseSensitive) {
                            $value = ($caseSensitive) ? $item[strtoupper($key)] : strtoupper($item[strtoupper($key)]);
                            return strpos($value, $matches[1]) === 0;
                        };
                        break;

                    case DbJsonBased::END_BY:
                        $filterFunction = function ($item) use ($key, $matches, $caseSensitive) {
                            $value = ($caseSensitive) ? $item[strtoupper($key)] : strtoupper($item[strtoupper($key)]);
                            return substr($value, -strlen($matches[1])) === $matches[1];
                        };
                        break;

                    case DbJsonBased::CONTAINS:
                        $filterFunction = function ($item) use ($key, $matches, $caseSensitive) {
                            $value = ($caseSensitive) ? $item[strtoupper($key)] : strtoupper($item[strtoupper($key)]);
                            return strpos($value, $matches[1]) !== false;
                        };
                        break;

                    default:
                        $filterFunction = function ($item) use ($key, $criterion, $caseSensitive) {
                            $value = ($caseSensitive) ? $item[strtoupper($key)] : strtoupper($item[strtoupper($key)]);
                            return $value === $criterion;
                        };
                }
            } else {
                // If we do not have a filter
                $filterFunction = function ($item) use ($key, $criterion, $caseSensitive) {
                    $value = ($caseSensitive) ? $item[strtoupper($key)] : strtoupper($item[strtoupper($key)]);
                    return $value === $criterion;
                };
            }

            // Apply filter
            $datas = array_filter($datas, $filterFunction);
        }

        return array_values($datas);
    }

    /**
     * getLastId
     * 
     * Return the last used ID from a database's table
     * 
     * If the table is created but without data yet, the returned id will be NULL
     *
     * @param string $tableName The targeted table into the database
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return int|null
     */
    public function getLastId(string $tableName): int|null
    {
        $datas = $this->getVerifiedTable($tableName);

        return $datas["ID"];
    }

    /**
     * insert
     * 
     * Insert datas into the targeted table
     *
     * @param DbJsonBasedDataInterface $datas Associative array containing the informations to insert
     * 
     * example :
     * ($dataBase, "TableName", [
     * 
     *  ["first_name" => "John", "last_name" => "Doe", "age" => 45],
     * 
     *  ["first_name" => "Neo", "last_name" => "Trinitron", "age" => 36]
     * 
     * ])
     * 
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return bool
     */
    public function insert(DbJsonBasedDataInterface $datas): bool
    {
        // Get existing datas
        $existingDatas = $this->findAll($datas->tableName);

        // Verifying and format new datas
        $verifiedDatas = [];
        $lastUsedId = $this->getLastId($datas->tableName);

        foreach ($datas->datas as $data) {
            // Harmonize key case to Upper
            $resultArray = Utils::harmonizeKeyCase($data, "strtoupper");

            // Adding ID
            if (is_null($lastUsedId)) {
                $resultArray["ID"] = 0;
            } else {
                $resultArray["ID"] = $lastUsedId;
            }
            $lastUsedId = $lastUsedId += 1;

            array_push($verifiedDatas, $resultArray);
        }

        // Merging datas
        $mergingDatas = array_merge($existingDatas, $verifiedDatas);

        // Updating datas
        $allDatabaseDatas = Utils::getContentAndDecode($this->dbName);
        $allDatabaseDatas[strtoupper($datas->tableName)]["VALUES"] = $mergingDatas;
        $allDatabaseDatas[strtoupper($datas->tableName)]["ID"] = $lastUsedId;
        Utils::encodeAndWriteFile($this->dbName, $allDatabaseDatas);

        return true;
    }

    /**
     * remove
     * 
     * Remove one entity in tableName or remove the entire tableName's values
     *
     * @param string $tableName The targeted table into the database
     * @param int|null $idToRemove The entity's ID to remove
     * @param bool $removeAllTableNameValues=false Will remove the entire tableName's values if true (and without ID)
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return bool
     */
    public function remove(string $tableName, ?int $idToRemove, bool $removeAllTableNameValues = false): bool
    {
        $datasKept = [];
        $removingAll = false;

        if (!is_null($idToRemove) && $removeAllTableNameValues) {
            // Remove entire tableName with an ID
            throw new DbJsonBasedInvalidArgumentException("Cannot remove entire value's table if an ID is provided.");
        } else if (is_null($idToRemove) && $removeAllTableNameValues) {
            // Remove entire tableName without ID
            $removingAll = true;
            $this->getVerifiedTable($tableName);
        } else {
            // Remove one entity
            $removingAll = false;
            $this->findOne($tableName, $idToRemove);
            $allDatas = $this->findAll($tableName);

            $datasKept = array_filter($allDatas, function ($item) use ($idToRemove) {
                return $item["ID"] !== $idToRemove;
            });

            $datasKept = array_values($datasKept);
        }

        $allDatabaseDatas = Utils::getContentAndDecode($this->dbName);
        $allDatabaseDatas[strtoupper($tableName)]["VALUES"] = $datasKept;

        // If removing all tableName's values, resetting ID
        if ($removingAll) {
            $allDatabaseDatas[strtoupper($tableName)]["ID"] = 0;
        }

        Utils::encodeAndWriteFile($this->dbName, $allDatabaseDatas);

        return true;
    }

    /**
     * update
     * 
     * Update values by replacing them into the BDD
     *
     * @param DbJsonBasedDataInterface $datas Associative array containing the informations to update
     * 
     * example :
     * ($dataBase, "TableName", [
     * 
     *  ["first_name" => "John", "id" => 1],
     * 
     *  ["last_name" => "Trinitron", "age" => 36, "id" => 5]
     * 
     * ])
     * 
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return bool
     */
    public function update(DbJsonBasedDataInterface $datas): bool
    {
        $modifiedDatas = $datas->datas;

        // If datas are empty
        if (empty($modifiedDatas)) {
            throw new DbJsonBasedInvalidArgumentException("Datas cannot be empty.");
        }

        // Harmonizing and ID check
        foreach ($modifiedDatas as &$modifiedData) {
            // Harmonize given keys into uppercase
            $modifiedData = Utils::harmonizeKeyCase($modifiedData, "strtoupper");

            if (!array_key_exists("ID", $modifiedData)) {
                throw new DbJsonBasedInvalidArgumentException("You must provide the entity ID to modify.");
            }
        }

        // Get all current datas
        $allDatas = $this->findAll($datas->tableName);

        foreach ($modifiedDatas as &$modifiedData) {
            // Update new values if ID match
            $id = $modifiedData["ID"];

            foreach ($allDatas as &$data) {
                if ($data["ID"] === $id) {
                    // If we match the ID, we modify the datas
                    foreach ($modifiedData as $key => $value) {
                        if ($key !== "ID") {
                            $data[$key] = $value;
                        }
                    }
                }
            }
        }

        // Get actual values and update them into bdd's file
        $allDatabaseDatas = Utils::getContentAndDecode($this->dbName);
        $allDatabaseDatas[strtoupper($datas->tableName)]["VALUES"] = $allDatas;
        Utils::encodeAndWriteFile($this->dbName, $allDatabaseDatas);

        return true;
    }

    /**
     * addColumn
     * 
     * Adds additional columns to an existing structure. Also adds fields in values ​​with NULL value by default
     *
     * @param DbJsonBasedStructureInterface $structure Structure of the existing Table
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return bool
     */
    public function addColumn(DbJsonBasedStructureInterface $structure): bool
    {
        // Modifying columns
        $actualColumns = $this->getColumns($structure->getTableName());
        $modifiedColumns = $structure->getColumns();
        $mergedColumns = array_merge($actualColumns, $modifiedColumns);

        // Adding columns in all datas except for ID
        $allDatas = $this->findAll($structure->getTableName());

        foreach ($allDatas as &$data) {
            foreach ($modifiedColumns as $key => $value) {
                if ($key === "ID") {
                    continue;
                }
                $data += [$key => null];
            }
        }

        // Updating datas
        $allDatabase = Utils::getContentAndDecode($this->dbName);
        $allDatabase[$structure->getTableName()]["COLUMNS"] = $mergedColumns;
        $allDatabase[$structure->getTableName()]["VALUES"] = $allDatas;
        Utils::encodeAndWriteFile($this->dbName, $allDatabase);

        return true;
    }
    
    /**
     * removeColumn
     * 
     * Remove columns in the table's structure and also in the datas
     *
     * @param string $tableName The targeted table into the database
     * @param array $removedColumns Table containing the different keys to remove
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @throws DbJsonBasedInvalidKeyException
     * @return bool
     */
    public function removeColumn(string $tableName, array $removedColumns): bool 
    {
        // If removed columns is empty
        if (count($removedColumns) <= 0) {
            throw new DbJsonBasedInvalidArgumentException("You must add at least one Key that you want to remove.");
        }

        // Harmonize given keys into uppercase
        $removedColumns = $removedColumns = Utils::harmonizeKeyCase($removedColumns, "strtoupper", true);

        // Remove ID key from the removed array if exists, we cannot remove it
        if (in_array("ID", $removedColumns)) {
            unset($removedColumns[array_search("ID", $removedColumns)]);
        }
        
        // Remove columns into columns
        $allColumns = $this->getColumns($tableName);

        $columnsToRemove = [];
        foreach($removedColumns as $removed) {
            // If the given key does not exist
            if (!array_key_exists($removed, $allColumns)) {
                throw new DbJsonBasedInvalidKeyException("the given key '{$removed}' does not exist in the columns of the table '" . strtoupper($tableName) . "'.");
            }
            
            array_push($columnsToRemove, $removed);
        }

        foreach($columnsToRemove as $column) {
            unset($allColumns[$column]);
        }

        // Remove all columns into datas
        $allDatas = $this->findAll($tableName);
        
        foreach($allDatas as &$datas) {
            foreach($removedColumns as $column) {
                if (array_key_exists($column, $datas)) {
                    unset($datas[$column]);
                }
            }
        }

        // Updating datas
        $allDatabase = Utils::getContentAndDecode($this->dbName);
        $allDatabase[strtoupper($tableName)]["COLUMNS"] = $allColumns;
        $allDatabase[strtoupper($tableName)]["VALUES"] = $allDatas;
        Utils::encodeAndWriteFile($this->dbName, $allDatabase);

        return true;
    }

    /**
     * getVerifiedTable
     * 
     * Checks the Table name and return the entire table
     *
     * @param string $tableName The targeted table into the database
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return array
     */
    public function getVerifiedTable(string $tableName): array
    {
        if (strlen($tableName) <= 0 || empty($tableName)) {
            throw new DbJsonBasedInvalidArgumentException("The TABLENAME cannot be empty.");
        }

        $datas = Utils::getContentAndDecode($this->getPath());

        if (empty($datas[strtoupper($tableName)])) {
            throw new DbJsonBasedInvalidArgumentException("The TABLENAME '$tableName' does not exist.");
        }

        return $datas[strtoupper($tableName)];
    }

    /**
     * getColumns
     * 
     * Return all columns belonging to the tageted Database
     *
     * @param string $tableName The targeted table into the database
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return array
     */
    public function getColumns(string $tableName): array
    {
        $datas = $this->getVerifiedTable($tableName);

        return $datas["COLUMNS"];
    }
}
