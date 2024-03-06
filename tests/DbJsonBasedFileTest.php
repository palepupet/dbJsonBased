<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBased;
use Palepupet\DbJsonBased\Utils;
use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;
use Palepupet\DbJsonBased\Tests\DbJsonBasedTest;


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
        $structure = new DbJsonBasedStructure("tableName", [
            "value1" => DbJsonBasedStructure::TYPE_STRING
        ]);
        $res = $this->createDb->createDb($structure);

        // Check the file exists after creation
        $this->assertFileExists($this->createDbDirectory . ".json");
        $this->assertTrue($res);
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
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT
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

    /**
     * @covers DbJsonBasedFileTest::createDb
     */
    public function testAddAnotherStructure()
    {
        // Create the 1rst structure
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT
            ]
        );
        $this->createDb->createDb($structure);

        // Create the 2nd structure
        $structure = new DbJsonBasedStructure(
            "customer",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "actif" => DbJsonBasedStructure::TYPE_BOOLEAN,
                "age" => DbJsonBasedStructure::TYPE_INT
            ]
        );
        $this->createDb->createDb($structure);

        $allStructures = Utils::getContentAndDecode($this->createDb->getPath());

        // Check
        $this->assertCount(2, $allStructures);
        $this->assertArrayHasKey("IDENTITY", $allStructures);
        $this->assertArrayHasKey("CUSTOMER", $allStructures);

        $this->assertCount(3, $allStructures["IDENTITY"]);
        $this->assertArrayHasKey("COLUMNS", $allStructures["IDENTITY"]);
        $this->assertArrayHasKey("VALUES", $allStructures["IDENTITY"]);
        $this->assertArrayHasKey("ID", $allStructures["IDENTITY"]);

        $this->assertCount(3, $allStructures["CUSTOMER"]);
        $this->assertArrayHasKey("COLUMNS", $allStructures["CUSTOMER"]);
        $this->assertArrayHasKey("VALUES", $allStructures["CUSTOMER"]);
        $this->assertArrayHasKey("ID", $allStructures["CUSTOMER"]);

        $this->assertArrayHasKey("FIRST_NAME", $allStructures["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("LAST_NAME", $allStructures["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("SIZE", $allStructures["IDENTITY"]["COLUMNS"]);
        $this->assertArrayHasKey("AGE", $allStructures["IDENTITY"]["COLUMNS"]);

        $this->assertArrayHasKey("FIRST_NAME", $allStructures["CUSTOMER"]["COLUMNS"]);
        $this->assertArrayHasKey("LAST_NAME", $allStructures["CUSTOMER"]["COLUMNS"]);
        $this->assertArrayHasKey("SIZE", $allStructures["CUSTOMER"]["COLUMNS"]);
        $this->assertArrayHasKey("AGE", $allStructures["CUSTOMER"]["COLUMNS"]);
        $this->assertArrayHasKey("ACTIF", $allStructures["CUSTOMER"]["COLUMNS"]);

        $this->assertCount(0, $allStructures["IDENTITY"]["VALUES"]);
        $this->assertCount(0, $allStructures["CUSTOMER"]["VALUES"]);

        $this->assertEquals(0, $allStructures["IDENTITY"]["ID"]);
        $this->assertEquals(0, $allStructures["CUSTOMER"]["ID"]);
    }
}
