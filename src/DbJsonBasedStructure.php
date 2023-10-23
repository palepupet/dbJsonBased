<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;

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
     * [
     * 
     * "NAME" => "string",
     * 
     * "PRICE" => "float"
     * 
     * ]
     * 
     * 
     * Valid types are **string**, **int**, **float** and **boolean** only
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
     * @return void
     */
    public function setColumns(array $columns): void
    {
        // Format datas
        $keysUpperCase = array_map("strtoupper", array_keys($columns));
        $valuesLowerCase = array_map("strtolower", $columns);
        $resultArray = array_combine($keysUpperCase, $valuesLowerCase);

        // Adding ID if it is not provided
        if (!isset($resultArray["ID"])) {
            $resultArray = $resultArray += self::ID_ARRAY;
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
                throw new DbJsonBasedInvalidArgumentException("Only '" . implode(", ", $allowedTypes) . "' types are accepted. '$type' is not a valid type");
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
