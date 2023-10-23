<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBased;
use Palepupet\DbJsonBased\Utils;
use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;
use Palepupet\DbJsonBased\Tests\DbJsonBasedTest;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;


class DbJsonBasedFileTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedFileTest::__construct
     */
    public function testInitiateClass()
    {
        // With correct provided extension .json
        $dbExtension = new DbJsonBased("nameWithExtension.json");
        $this->assertEquals("nameWithExtension.json", $dbExtension->getFullName());

        // Without provided extension 
        $dbWithoutExtension = new DbJsonBased("nameWithoutExtension");
        $this->assertEquals("nameWithoutExtension.json", $dbWithoutExtension->getFullName());

        // With incorrect provided extension
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $dbWithWrongExtension = new DbJsonBased("nameWithWrongExtension.txt");
    }

    /**
     * @covers DbJsonBasedFileTest::createDb
     */
    public function testCreateDbNewFile()
    {
        // Check the file does not exists first
        $this->assertFileDoesNotExist($this->createDbDirectory);

        // Create the file
        $structure = new DbJsonBasedStructure("tableName", ["value1" => "string"]);
        $res = $this->createDb->createDb($structure);

        // Check the file exists after creation
        $this->assertFileExists($this->createDbDirectory . ".json");
        $this->assertTrue($res);
    }

    /**
     * @covers DbJsonBasedFileTest::createDb
     */
    public function testCreateDbIfFileAlreadyExists()
    {
        // First creation
        $structure = new DbJsonBasedStructure("tableName", ["value1" => "string"]);
        $this->createDb->createDb($structure);

        // If the file already exists throw exception
        $this->expectException(DbJsonBasedRunTimeException::class);
        $this->createDb->createDb($structure);
    }

    /**
     * @covers DbJsonBasedFileTest::createDb
     */
    public function testCreateDbHasWellStructure()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => "string",
                "last_name" => "string",
                "size" => "float",
                "age" => "int"
            ]
        );
        $this->createDb->createDb($structure);

        // Get file
        $fetchDatas = Utils::getContentAndDecode($this->createDb->getPath());

        // Check structure
        $this->assertIsArray($fetchDatas);
        $this->assertCount(1, $fetchDatas);

        $this->assertIsArray($fetchDatas["IDENTITY"]);
        $this->assertCount(3, $fetchDatas["IDENTITY"]);

        $this->assertIsArray($fetchDatas["IDENTITY"]["COLUMNS"]);
        $this->assertCount(5, $fetchDatas["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("FIRST_NAME", $fetchDatas["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("LAST_NAME", $fetchDatas["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("SIZE", $fetchDatas["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("AGE", $fetchDatas["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("ID", $fetchDatas["IDENTITY"]["COLUMNS"]);
        $this->assertEquals($fetchDatas["IDENTITY"]["COLUMNS"]["LAST_NAME"], "string");
        $this->assertEquals($fetchDatas["IDENTITY"]["COLUMNS"]["LAST_NAME"], "string");
        $this->assertEquals($fetchDatas["IDENTITY"]["COLUMNS"]["SIZE"], "float");
        $this->assertEquals($fetchDatas["IDENTITY"]["COLUMNS"]["AGE"], "int");
        $this->assertEquals($fetchDatas["IDENTITY"]["COLUMNS"]["ID"], "int");

        $this->assertIsArray($fetchDatas["IDENTITY"]["VALUES"]);
        $this->assertCount(0, $fetchDatas["IDENTITY"]["VALUES"]);

        $this->assertIsNotArray($fetchDatas["IDENTITY"]["ID"]);
        $this->assertEquals($fetchDatas["IDENTITY"]["ID"], null);
    }
}
