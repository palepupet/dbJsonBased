<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidTypeException;

class DbJsonBasedStructure implements DbJsonBasedStructureInterface
{
    public const TYPE_BOOLEAN = "boolean";
    public const TYPE_FLOAT = "float";
    public const TYPE_INT = "int";
    public const TYPE_STRING = "string";

    private const ID_ARRAY = ["ID" => "int"];

    private array $_structure = [
        "TABLE" => "",
        "COLUMNS" => []
    ];

    /**
     * __construct
     *
     * @param string $tableName The name of the future table
     * @param array $columns The names/types of the future columns
     * 
     * ["COLUMN_1" => "TYPE_1", "COLUMN_2" => "TYPE_2"]
     * 
     * exemple :
     * 
     * ["NAME" => "string", "PRICE" => "float"]
     * 
     * Valid types are **string**, **int**, **float** and **boolean** only. You can use the class constants provided: **TYPE_BOOLEAN | TYPE_FLOAT | TYPE_INT | TYPE_STRING**
     * 
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedInvalidTypeException
     */
    public function __construct(private string $tableName, private array $columns)
    {
        $this->setTableName($tableName);
        $this->setColumns($columns);
    }

    /**
     * setTableName
     * 
     * Set the name of the future table
     *
     * @param string $tableName Name of the future table
     * @throws DbJsonBasedInvalidArgumentException
     * @return void
     */
    public function setTableName(string $tableName): void
    {
        if (!isset($tableName) || empty($tableName)) {
            throw new DbJsonBasedInvalidArgumentException("TABLE cannot be empty");
        }

        $this->_structure["TABLE"] = strtoupper($this->tableName);
    }

    /**
     * getTableName
     * 
     * Return the tableName
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->_structure["TABLE"];
    }

    /**
     * setColumns
     * 
     * Set the names/types of the future columns
     *
     * @param array $columns Names/Types of the future columns
     * @throws DbJsonBasedInvalidArgumentException
     * @return void
     */
    public function setColumns(array $columns): void
    {
        // Format datas
        $resultArray = Utils::harmonizeKeyCase($columns, Utils::STRTOUPPER);

        // Adding ID if it is not provided
        if (!isset($resultArray["ID"])) {
            $resultArray += self::ID_ARRAY;
        }

        // Modifying ID if it is not well typed
        if (isset($resultArray["ID"]) && $resultArray["ID"] !== DbJsonBasedStructure::TYPE_INT) {
            $resultArray["ID"] = DbJsonBasedStructure::TYPE_INT;
        }

        // Validation and aggregation of datas
        $this->validateDatas($resultArray);
        $this->_structure["COLUMNS"] = $resultArray;
    }

    /**
     * getColumns
     * 
     * Return the columns
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->_structure["COLUMNS"];
    }

    /**
     * validateDatas
     * 
     * Do some checks to validate the structure datas
     *
     * @param array $columns
     * @throws DbJsonBasedInvalidArgumentException
     * @return void
     */
    private function validateDatas(array $columns): void
    {
        $allowedTypes = [
            self::TYPE_BOOLEAN,
            "bool",
            self::TYPE_FLOAT,
            self::TYPE_INT,
            self::TYPE_STRING
        ];

        foreach ($columns as $key => $type) {
            // Check valid types
            if (!in_array($type, $allowedTypes)) {
                throw new DbJsonBasedInvalidTypeException("Only '" . implode(", ", $allowedTypes) . "' types are accepted. '$type' is not a valid type");
            }

            // Check integrity of columns
            if (
                !isset($key) || empty($key)
                || !isset($type) || empty($type)
            ) {
                throw new DbJsonBasedInvalidArgumentException("COLUMNS and VALUES cannot be empty");
            }
        }
    }
}
