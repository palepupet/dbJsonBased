<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\Utils;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;

class DbJsonBased
{
    /**
     * __construct
     *
     * @param string $dbName Path of the json database, without extension
     * @return void
     */
    public function __construct(private string $dbName)
    {
        $this->dbName = $dbName . ".json";
    }

    /**
     * createDb
     * 
     * Create the json database
     *
     * @throws DbJsonBasedRuntimeException
     * @return bool
     */
    public function createDb(): bool
    {
        if (Utils::isFileExist($this->dbName)) {
            throw new DbJsonBasedRuntimeException("The file '{$this->dbName}.json' already exists");
        }

        file_put_contents($this->dbName, "");

        return true;
    }

    /**
     * getName
     * 
     * Return the full path of the database
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->dbName;
    }
}
