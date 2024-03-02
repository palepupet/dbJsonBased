<?php

namespace Palepupet\DbJsonBased;

interface DbJsonBasedDataInterface
{
    /**
     * checkTableName
     * 
     * Do some checks and verify if the tableName exists int the database
     *
     * @return void
     */
    public function checkTableName(): void;

    /**
     * checkColumns
     * 
     * Do some checks and verify if all prodvided columns exists in the database's columns
     *
     * @return void
     */
    public function checkColumns(): void;

    /**
     * checkTypes
     * 
     * Do some checks and verify if all provided types matches the database's types
     *
     * @return void
     */
    public function checkTypes(): void;
}
