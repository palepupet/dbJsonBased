<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\Utils;
use Palepupet\DbJsonBased\DbJsonBasedStructureInterface;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;

class DbJsonBased
{
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
}
