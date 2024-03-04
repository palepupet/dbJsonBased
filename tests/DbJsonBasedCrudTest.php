<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBasedData;
use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;

class DbJsonBasedCrudTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedCrudTest::insert
     */
    public function testInsert()
    {
        // Create file
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT,
                "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
            ]
        );
        $this->createDb->createDb($structure);

        // First step, everything is empty
        $emptyDatas = $this->createDb->findAll("identity");
        $this->assertEmpty($emptyDatas);

        // Next, create datas
        $datas = new DbJsonBasedData($this->createDb, "identity", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175.50,
                "age" => 21,
                "actif" => true
            ],
            [
                "first_name" => "Neo",
                "last_name" => "Trinitron",
                "size" => 184.20,
                "age" => 33,
                "actif" => false
            ]
        ]);

        // Then, insert datas
        $this->createDb->insert($datas);

        // Finaly, verify inserted datas
        $insertedDatas = $this->createDb->findAll("identity");

        $this->assertIsArray($insertedDatas);
        $this->assertCount(2, $insertedDatas);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("ID", $insertedDatas[$i]);
            $this->assertArrayHasKey("FIRST_NAME", $insertedDatas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $insertedDatas[$i]);
            $this->assertArrayHasKey("SIZE", $insertedDatas[$i]);
            $this->assertArrayHasKey("AGE", $insertedDatas[$i]);
            $this->assertArrayHasKey("ACTIF", $insertedDatas[$i]);
        }

        $this->assertEquals($insertedDatas[0]["ID"], 0);
        $this->assertEquals($insertedDatas[0]["FIRST_NAME"], "John");
        $this->assertEquals($insertedDatas[0]["LAST_NAME"], "Doe");
        $this->assertEquals($insertedDatas[0]["SIZE"], 175.50);
        $this->assertEquals($insertedDatas[0]["AGE"], 21);
        $this->assertEquals($insertedDatas[0]["ACTIF"], true);

        $this->assertEquals($insertedDatas[1]["ID"], 1);
        $this->assertEquals($insertedDatas[1]["FIRST_NAME"], "Neo");
        $this->assertEquals($insertedDatas[1]["LAST_NAME"], "Trinitron");
        $this->assertEquals($insertedDatas[1]["SIZE"], 184.20);
        $this->assertEquals($insertedDatas[1]["AGE"], 33);
        $this->assertEquals($insertedDatas[1]["ACTIF"], false);

        $this->assertEquals($this->createDb->getLastId("identity"), 2);
    }

    /**
     * @covers DbJsonBasedCrudTest::remove
     */
    public function testRemoveOneEntity()
    {
        // Create file
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT,
                "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
            ]
        );

        $this->createDb->createDb($structure);

        // Add datas
        $datas = new DbJsonBasedData($this->createDb, "identity", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175.50,
                "age" => 21,
                "actif" => true
            ],
            [
                "first_name" => "Neo",
                "last_name" => "Trinitron",
                "size" => 184.20,
                "age" => 33,
                "actif" => false
            ]
        ]);

        $this->createDb->insert($datas);

        // Remove data 0 and check
        $result = $this->createDb->remove("identity", 0);

        $this->assertTrue($result);
        $datasKept = $this->createDb->findAll("identity");

        $this->assertIsArray($datasKept);
        $this->assertCount(1, $datasKept);

        $this->assertArrayHasKey("ID", $datasKept[0]);
        $this->assertArrayHasKey("FIRST_NAME", $datasKept[0]);
        $this->assertArrayHasKey("LAST_NAME", $datasKept[0]);
        $this->assertArrayHasKey("SIZE", $datasKept[0]);
        $this->assertArrayHasKey("AGE", $datasKept[0]);

        $this->assertEquals($datasKept[0]["ID"], 1);
        $this->assertEquals($datasKept[0]["FIRST_NAME"], "Neo");
        $this->assertEquals($datasKept[0]["LAST_NAME"], "Trinitron");
        $this->assertEquals($datasKept[0]["SIZE"], 184.20);
        $this->assertEquals($datasKept[0]["AGE"], 33);
        $this->assertEquals($datasKept[0]["ACTIF"], false);

        $this->assertEquals($this->createDb->getLastId("identity"), 2);
    }

    /**
     * @covers DbJsonBasedCrudTest::remove
     */
    public function testRemoveIdDoesNotExist()
    {
        // Create file
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT,
                "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
            ]
        );

        $this->createDb->createDb($structure);

        // Add datas
        $datas = new DbJsonBasedData($this->createDb, "identity", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175.50,
                "age" => 21,
                "actif" => true
            ],
            [
                "first_name" => "Neo",
                "last_name" => "Trinitron",
                "size" => 184.20,
                "age" => 33,
                "actif" => false
            ]
        ]);

        $this->createDb->insert($datas);

        // Remove data's ID 10 wich does not exist
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->createDb->remove("identity", 10);
    }

    /**
     * @covers DbJsonBasedCrudTest::remove
     */
    public function testRemoveTableNameDoesNotExist()
    {
        // Create file
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT,
                "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
            ]
        );

        $this->createDb->createDb($structure);

        // Add datas
        $datas = new DbJsonBasedData($this->createDb, "identity", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175.50,
                "age" => 21,
                "actif" => true
            ],
            [
                "first_name" => "Neo",
                "last_name" => "Trinitron",
                "size" => 184.20,
                "age" => 33,
                "actif" => false
            ]
        ]);

        $this->createDb->insert($datas);

        // Remove data with invalid tableName wich does not exist
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->createDb->remove("identities", 0);
    }

    /**
     * @covers DbJsonBasedCrudTest::remove
     */
    public function testRemoveEntireTableName()
    {
        // Create file
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT,
                "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
            ]
        );

        $this->createDb->createDb($structure);

        // Add datas
        $datas = new DbJsonBasedData($this->createDb, "identity", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175.50,
                "age" => 21,
                "actif" => true
            ],
            [
                "first_name" => "Neo",
                "last_name" => "Trinitron",
                "size" => 184.20,
                "age" => 33,
                "actif" => false
            ]
        ]);

        $this->createDb->insert($datas);

        // Removing all tableName's datas
        $this->createDb->remove("identity", null, true);

        // Check columns
        $columns = $this->createDb->getColumns("identity");

        $this->assertIsArray($columns);
        $this->assertCount(6, $columns);

        $this->assertArrayHasKey("ID", $columns);
        $this->assertArrayHasKey("FIRST_NAME", $columns);
        $this->assertArrayHasKey("LAST_NAME", $columns);
        $this->assertArrayHasKey("SIZE", $columns);
        $this->assertArrayHasKey("AGE", $columns);

        // Check values
        $dataKept = $this->createDb->findAll("identity");

        $this->assertIsArray($dataKept);
        $this->assertEmpty($dataKept);

        // Check last ID
        $this->assertEquals(0, $this->createDb->getLastId("identity"));
    }

    /**
     * @covers DbJsonBasedCrudTest::remove
     */
    public function testRemoveEntireTableNameWithId()
    {
        // Create file
        $structure = new DbJsonBasedStructure(
            "identity",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "size" => DbJsonBasedStructure::TYPE_FLOAT,
                "age" => DbJsonBasedStructure::TYPE_INT,
                "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
            ]
        );

        $this->createDb->createDb($structure);

        // Add datas
        $datas = new DbJsonBasedData($this->createDb, "identity", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175.50,
                "age" => 21,
                "actif" => true
            ],
            [
                "first_name" => "Neo",
                "last_name" => "Trinitron",
                "size" => 184.20,
                "age" => 33,
                "actif" => false
            ]
        ]);

        $this->createDb->insert($datas);

        // Removing all tableName's datas but with providing ID
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->createDb->remove("identity", 1, true);
    }
}
