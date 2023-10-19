<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBased;
use PHPUnit\Framework\TestCase;


class DbJsonBasedTest extends TestCase
{
    protected $toRemoveFolder;

    protected $db;
    protected $dbDirectory;
    protected $createDb;
    protected $createDbDirectory;
    protected $writeDb;
    protected $writeDbDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'db';
        $this->createDbDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . "to_remove" . DIRECTORY_SEPARATOR . "dbCreate";
        $this->writeDbDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . "to_remove" . DIRECTORY_SEPARATOR . "dbWrite";

        $this->db = new DbJsonBased($this->dbDirectory);
        $this->createDb = new DbJsonBased($this->createDbDirectory);
        $this->writeDb = new DbJsonBased($this->writeDbDirectory);

        // Create a test folder if it does not exist
        $this->toRemoveFolder = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . "to_remove";
        if (!file_exists($this->toRemoveFolder)) {
            mkdir($this->toRemoveFolder, 0777, true);
        }

        // Delete the test file
        $files = glob($this->toRemoveFolder . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            if (
                is_file($file) &&
                ($file === $this->createDb->getName() || $file === $this->writeDb->getName())
            ) {
                unlink($file);
            }
        }
    }
}
