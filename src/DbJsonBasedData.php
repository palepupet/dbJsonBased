<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidKeyException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidTypeException;

class DbJsonBasedData implements DbJsonBasedDataInterface
{
    public function  __construct(public DbJsonBased $database, public string $tableName, public array $datas)
    {
        $this->checkTableName();
        $this->checkColumns();
        $this->checkTypes();
    }

    /**
     * checkTableName
     * 
     * Do some checks and retrun the entire table if exists
     *
     * @return void
     */
    public function checkTableName(): void
    {
        $this->database->getVerifiedTable($this->tableName);
    }

    /**
     * checkColumns
     * 
     * Do some checks and verify if all prodvided columns exists in the database's columns
     *
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
                if ($expectedType === "float" && $providedType === "double") {
                    $providedType = "float";
                }

                // Int / Integer === same type. According to the COLUMNS type defined "int"
                if ($expectedType === "int" && $providedType === "integer") {
                    $providedType = "int";
                }

                if ($providedType !== $expectedType) {
                    throw new DbJsonBasedInvalidTypeException("The '$key' must be of type '$expectedType', '$providedType' provided");
                }
            }
        }
    }
}
