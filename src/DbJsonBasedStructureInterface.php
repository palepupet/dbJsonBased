<?php

namespace Palepupet\DbJsonBased;

interface DbJsonBasedStructureInterface
{

    /**
     * setTableName
     * 
     * Set the name of the future table
     *
     * @param string $tableName
     * @return void
     */
    public function setTableName(string $tableName): void;

    /**
     * getTableName
     * 
     * Return the tableName
     *
     * @return string
     */
    public function getTableName(): string;

    /**
     * setColumns
     * 
     * Set the columns of the future table
     *
     * @param array $columns
     * @return void
     */
    public function setColumns(array $columns): void;

    /**
     * getColumns
     * 
     * Return the columns
     *
     * @return array
     */
    public function getColumns(): array;
}
