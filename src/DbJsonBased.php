<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\Utils;
use Palepupet\DbJsonBased\DbJsonBasedStructureInterface;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;
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
     * @param string $dbName Path of the json database, without extension, or only .json extension
     */
    public function __construct(private string $dbName)
    {
        // With .json extension provided correctly
        if (isset(pathinfo($dbName)["extension"]) && pathinfo($dbName)["extension"] === "json") {
            $this->dbName = $dbName;
        }

        // With extension provided incorrectly
        if (isset(pathinfo($dbName)["extension"]) && pathinfo($dbName)["extension"] !== "json") {
            throw new DbJsonBasedInvalidArgumentException("The provided extension '." . pathinfo($dbName)["extension"] . "' is invalid. Please indicate your database's name without extension or with '.json' extension.");
        }

        // Without extension provided
        if (!isset(pathinfo($dbName)["extension"])) {
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
        if (Utils::isFileExist($this->dbName)) {
            throw new DbJsonBasedRuntimeException("The file '{$this->dbName}.json' already exists");
        }

        $newStructure = [
            $structure->getTableName() => [
                "COLUMNS" => $structure->getColumns(),
                "VALUES" => [],
                "ID" => null
            ]
        ];

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
     * Return the name (without extention) of the database
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
     * Retun all datas from database's table and return these
     *
     * @param string $tableName The targeted table into the database
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
     * @param string $tableName
     * @param int $id
     * @return array
     */
    public function findOne(string $tableName, int $id): array
    {
        $datas = $this->findAll($tableName);

        $resultat = array_filter($datas, function ($item) use ($id) {
            return $item["ID"] === $id;
        });

        return array_values($resultat);
    }

    /**
     * findOneBy
     * 
     * Return all datas that meet the filters
     *
     * @param string $tableName The targeted table into the database
     * @param array $criteria The criteria on which to filter, "EXACT" by default
     * 
     * exemple with filter : [ ["first_name" => "John|filter"] ]
     * 
     * exemple with filters : [ ["first_name" => "Jo|filter"], ["last_name" => "n|filter"] ]
     * 
     * exemple without filter : [ ["first_name" => "John"] ] so "EXACT" filter implicitly
     * 
     * Valid Filters are **START_BY**, **END_BY**, **CONTAINS** and **EXACT** only.
     * You can use the class constants provided **DbJsonBased::START_BY|END_BY|CONTAINS|EXACT**
     * 
     * @param bool $caseSensitive If we have to respect the case sensitive, "true" by default
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
     * @param DbJsonBasedData $database
     * @param string $tableName Name of the targeted table
     * @param DbJsonBasedDataInterface $datas Associative array containing the informations to insert
     * 
     * exemple :
     * [
     * 
     *  ["first_name" => "John", "last_name" => "Doe", "age" => 45],
     * 
     *  ["first_name" => "Neo", "last_name" => "Trinitron", "age" => 36]
     * 
     * ]
     * @return void
     */
    public function insert(DbJsonBasedDataInterface $datas): void
    {
        // Get existing datas
        $existingDatas = $this->findAll($datas->tableName);

        // Verifying and format new datas
        $verifiedDatas = [];
        $lastUsedId = $this->getLastId($datas->tableName);

        foreach ($datas->datas as $data) {
            $keysUpperCase = array_map("strtoupper", array_keys($data));
            $valuesLowerCase = $data;
            $resultArray = array_combine($keysUpperCase, $valuesLowerCase);

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
        Utils::encodeAndWriteFile($this->dbName, $allDatabaseDatas);
    }

    /**
     * getVerifiedTable
     * 
     * Do some checks and retrun the entire table
     *
     * @param string $tableName
     * @return array
     */
    public function getVerifiedTable(string $tableName): array
    {
        if (strlen($tableName) <= 0 || empty($tableName)) {
            throw new DbJsonBasedInvalidArgumentException("The TABLENAME cannot be empty");
        }

        $datas = Utils::getContentAndDecode($this->getPath());

        if (empty($datas[strtoupper($tableName)])) {
            throw new DbJsonBasedInvalidArgumentException("The TABLENAME '$tableName' does not exists");
        }

        return $datas[strtoupper($tableName)];
    }

    public function getColumns(string $tableName): array
    {
        $datas = $this->getVerifiedTable($tableName);

        return $datas["COLUMNS"];
    }
}
