<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidKeyException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidTypeException;

class DbJsonBasedData implements DbJsonBasedDataInterface
{
    private const TYPE_DOUBLE = "double";
    private const TYPE_FLOAT = "float";
    private const TYPE_INT = "int";
    private const TYPE_INTEGER = "integer";

    /**
     * __construct
     *
     * @param DbJsonBased $database The targeted Database
     * @param string $tableName The targeted table into the database
     * @param array $datas Associative array containing the informations
     * 
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @throws DbJsonBasedInvalidKeyException
     * @throws DbJsonBasedInvalidTypeException
     * @return void
     */
    public function  __construct(public DbJsonBased $database, public string $tableName, public array $datas)
    {
        $this->checkTableName();
        $this->checkColumns();
        $this->checkTypes();
    }

    /**
     * checkTableName
     * 
     * Do some checks and verify if the tableName exists into the database
     *
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return void
     */
    public function checkTableName(): void
    {
        $this->database->getVerifiedTable($this->tableName);
    }

    /**
     * checkColumns
     * 
     * Do some checks and verify if all provided columns exists in the database's columns
     *
     * @throws DbJsonBasedInvalidKeyException
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return void
     */
    public function checkColumns(): void
    {
        $columns = $this->database->getColumns($this->tableName);

        foreach ($this->datas as $data) {
            foreach ($data as $key => $value) {
                if (!array_key_exists(strtoupper($key), $columns)) {
                    throw new DbJsonBasedInvalidKeyException("The '$key' key does not exists in the database's COLUMNS. Please update the database COLUMNS first");
                }
            }
        }
    }

    /**
     * checkTypes
     * 
     * Do some checks and verify if all provided types matches the database's types
     *
     * @throws DbJsonBasedInvalidTypeException
     * @throws DbJsonBasedInvalidArgumentException
     * @throws DbJsonBasedRuntimeException
     * @return void
     */
    public function checkTypes(): void
    {
        $columns = $this->database->getColumns($this->tableName);

        foreach ($this->datas as $data) {
            foreach ($data as $key => $value) {

                $expectedType = $columns[strtoupper($key)];
                $providedType = gettype($value);

                // Float / Double === same type. According to the COLUMNS type defined "float"
                if ($expectedType === self::TYPE_FLOAT && $providedType === self::TYPE_DOUBLE) {
                    $providedType = self::TYPE_FLOAT;
                }

                // Int / Integer === same type. According to the COLUMNS type defined "int"
                if ($expectedType === self::TYPE_INT && $providedType === self::TYPE_INTEGER) {
                    $providedType = self::TYPE_INT;
                }

                if ($providedType !== $expectedType) {
                    throw new DbJsonBasedInvalidTypeException("The '$key' must be of type '$expectedType', '$providedType' provided");
                }
            }
        }
    }
}
