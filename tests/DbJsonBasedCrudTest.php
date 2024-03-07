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
     * @covers DbJsonBasedCrudTest::insert
     */
    public function testInsertWithTwoStructures()
    {
        // Create 1rst structure
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

        // Create 2nd structure
        $structure2 = new DbJsonBasedStructure(
            "customer",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "age" => DbJsonBasedStructure::TYPE_INT
            ]
        );
        $this->createDb->createDb($structure2);

        $datas2 = new DbJsonBasedData($this->createDb, "customer", [
            [
                "first_name" => "Alan",
                "last_name" => "Turingstone",
                "age" => 43,
            ]
        ]);
        $this->createDb->insert($datas2);

        // Check
        $insertedIdentityDatas = $this->createDb->findAll("identity");
        $insertedCustomerDatas = $this->createDb->findAll("customer");

        $this->assertIsArray($insertedIdentityDatas);
        $this->assertCount(2, $insertedIdentityDatas);

        $this->assertCount(6, $this->createDb->getColumns("identity"));
        $this->assertCount(4, $this->createDb->getColumns("customer"));

        $this->assertIsArray($insertedCustomerDatas);
        $this->assertCount(1, $insertedCustomerDatas);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("ID", $insertedIdentityDatas[$i]);
            $this->assertArrayHasKey("FIRST_NAME", $insertedIdentityDatas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $insertedIdentityDatas[$i]);
            $this->assertArrayHasKey("SIZE", $insertedIdentityDatas[$i]);
            $this->assertArrayHasKey("AGE", $insertedIdentityDatas[$i]);
            $this->assertArrayHasKey("ACTIF", $insertedIdentityDatas[$i]);
        }

        for ($i = 0; $i < 1; $i++) {
            $this->assertArrayHasKey("ID", $insertedCustomerDatas[$i]);
            $this->assertArrayHasKey("FIRST_NAME", $insertedCustomerDatas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $insertedCustomerDatas[$i]);
            $this->assertArrayHasKey("AGE", $insertedCustomerDatas[$i]);
        }

        $this->assertEquals($insertedIdentityDatas[0]["ID"], 0);
        $this->assertEquals($insertedIdentityDatas[0]["FIRST_NAME"], "John");
        $this->assertEquals($insertedIdentityDatas[0]["LAST_NAME"], "Doe");
        $this->assertEquals($insertedIdentityDatas[0]["SIZE"], 175.50);
        $this->assertEquals($insertedIdentityDatas[0]["AGE"], 21);
        $this->assertEquals($insertedIdentityDatas[0]["ACTIF"], true);

        $this->assertEquals($insertedIdentityDatas[1]["ID"], 1);
        $this->assertEquals($insertedIdentityDatas[1]["FIRST_NAME"], "Neo");
        $this->assertEquals($insertedIdentityDatas[1]["LAST_NAME"], "Trinitron");
        $this->assertEquals($insertedIdentityDatas[1]["SIZE"], 184.20);
        $this->assertEquals($insertedIdentityDatas[1]["AGE"], 33);
        $this->assertEquals($insertedIdentityDatas[1]["ACTIF"], false);

        $this->assertEquals($insertedCustomerDatas[0]["ID"], 0);
        $this->assertEquals($insertedCustomerDatas[0]["FIRST_NAME"], "Alan");
        $this->assertEquals($insertedCustomerDatas[0]["LAST_NAME"], "Turingstone");
        $this->assertEquals($insertedCustomerDatas[0]["AGE"], 43);
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
    public function testRemoveOneEntityTwoStructure()
    {
        // Create 1rst structure
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

        // Create 2nd structure
        $structure2 = new DbJsonBasedStructure(
            "customer",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "age" => DbJsonBasedStructure::TYPE_INT
            ]
        );
        $this->createDb->createDb($structure2);

        $datas2 = new DbJsonBasedData($this->createDb, "customer", [
            [
                "first_name" => "Alan",
                "last_name" => "Turingstone",
                "age" => 43,
            ],
            [
                "first_name" => "Arrakis",
                "last_name" => "Pandora",
                "age" => 27,
            ],
            [
                "first_name" => "Arid",
                "last_name" => "Tropical",
                "age" => 57,
            ]
        ]);
        $this->createDb->insert($datas2);

        // Remove one of each
        $result = $this->createDb->remove("identity", 1);
        $result2 = $this->createDb->remove("customer", 0);

        // Result 1rst
        $this->assertTrue($result);
        $datasKept = $this->createDb->findAll("identity");

        $this->assertIsArray($datasKept);
        $this->assertCount(1, $datasKept);

        $this->assertArrayHasKey("ID", $datasKept[0]);
        $this->assertArrayHasKey("FIRST_NAME", $datasKept[0]);
        $this->assertArrayHasKey("LAST_NAME", $datasKept[0]);
        $this->assertArrayHasKey("SIZE", $datasKept[0]);
        $this->assertArrayHasKey("AGE", $datasKept[0]);

        $this->assertEquals($datasKept[0]["ID"], 0);
        $this->assertEquals($datasKept[0]["FIRST_NAME"], "John");
        $this->assertEquals($datasKept[0]["LAST_NAME"], "Doe");
        $this->assertEquals($datasKept[0]["SIZE"], 175.50);
        $this->assertEquals($datasKept[0]["AGE"], 21);
        $this->assertEquals($datasKept[0]["ACTIF"], true);

        $this->assertEquals($this->createDb->getLastId("identity"), 2);

        // Result 2nd
        $this->assertTrue($result2);
        $datasKept = $this->createDb->findAll("customer");

        $this->assertIsArray($datasKept);
        $this->assertCount(2, $datasKept);

        $this->assertArrayHasKey("ID", $datasKept[0]);
        $this->assertArrayHasKey("FIRST_NAME", $datasKept[0]);
        $this->assertArrayHasKey("LAST_NAME", $datasKept[0]);
        $this->assertArrayHasKey("AGE", $datasKept[0]);

        $this->assertEquals($datasKept[0]["ID"], 1);
        $this->assertEquals($datasKept[0]["FIRST_NAME"], "Arrakis");
        $this->assertEquals($datasKept[0]["LAST_NAME"], "Pandora");
        $this->assertEquals($datasKept[0]["AGE"], 27);

        $this->assertEquals($this->createDb->getLastId("customer"), 3);
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

    /**
     * @covers DbJsonBasedCrudTest::update
     */
    public function testUpdateEmptyData()
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
            ]
        ]);
        $this->createDb->insert($datas);

        // Update
        $datasToUpdate = new DbJsonBasedData($this->createDb, "identity", []);

        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->createDb->update($datasToUpdate);
    }

    /**
     * @covers DbJsonBasedStructureTest::update
     */
    public function testUpdateWithoutId()
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

        // Update
        $datasToUpdate = new DbJsonBasedData($this->createDb, "identity", [
            [
                "last_name" => "Turingstone",
            ]
        ]);

        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->createDb->update($datasToUpdate);
    }

    /**
     * @covers DbJsonBasedCrudTest::update
     */
    public function testUpdateOneFieldOneStructure()
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

        // Update
        $datasToUpdate = new DbJsonBasedData($this->createDb, "identity", [
            [
                "last_name" => "Turingstone",
                "id" => 1
            ]
        ]);
        $updated = $this->createDb->update($datasToUpdate);

        // Check
        $this->assertTrue($updated);

        $updatedData = $this->createDb->findAll("identity");
        $this->assertCount(6, $this->createDb->getColumns("identity"));

        // 1rst the same
        $this->assertIsArray($updatedData);
        $this->assertCount(2, $updatedData);

        $this->assertArrayHasKey("ID", $updatedData[0]);
        $this->assertArrayHasKey("FIRST_NAME", $updatedData[0]);
        $this->assertArrayHasKey("LAST_NAME", $updatedData[0]);
        $this->assertArrayHasKey("SIZE", $updatedData[0]);
        $this->assertArrayHasKey("AGE", $updatedData[0]);

        $this->assertEquals($updatedData[0]["ID"], 0);
        $this->assertEquals($updatedData[0]["FIRST_NAME"], "John");
        $this->assertEquals($updatedData[0]["LAST_NAME"], "Doe");
        $this->assertEquals($updatedData[0]["SIZE"], 175.50);
        $this->assertEquals($updatedData[0]["AGE"], 21);
        $this->assertEquals($updatedData[0]["ACTIF"], true);

        $this->assertEquals($this->createDb->getLastId("identity"), 2);

        // 2nd modified
        $this->assertArrayHasKey("ID", $updatedData[1]);
        $this->assertArrayHasKey("FIRST_NAME", $updatedData[1]);
        $this->assertArrayHasKey("LAST_NAME", $updatedData[1]);
        $this->assertArrayHasKey("SIZE", $updatedData[1]);
        $this->assertArrayHasKey("AGE", $updatedData[1]);

        $this->assertEquals($updatedData[1]["ID"], 1);
        $this->assertEquals($updatedData[1]["FIRST_NAME"], "Neo");
        $this->assertEquals($updatedData[1]["LAST_NAME"], "Turingstone");
        $this->assertEquals($updatedData[1]["SIZE"], 184.20);
        $this->assertEquals($updatedData[1]["AGE"], 33);
        $this->assertEquals($updatedData[1]["ACTIF"], false);
    }

    /**
     * @covers DbJsonBasedCrudTest::update
     */
    public function testUpdateMultipleFieldsOneStructure()
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

        // Update
        $datasToUpdate = new DbJsonBasedData($this->createDb, "identity", [
            [
                "last_name" => "Turingstone",
                "id" => 0,
                "age" => 32
            ],
            [
                "id" => 1,
                "last_name" => "Doe",
                "actif" => true
            ]
        ]);
        $updated = $this->createDb->update($datasToUpdate);

        // Check
        $this->assertTrue($updated);

        $updatedData = $this->createDb->findAll("identity");

        $this->assertCount(6, $this->createDb->getColumns("identity"));

        $this->assertIsArray($updatedData);
        $this->assertCount(2, $updatedData);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("ID", $updatedData[$i]);
            $this->assertArrayHasKey("FIRST_NAME", $updatedData[$i]);
            $this->assertArrayHasKey("LAST_NAME", $updatedData[$i]);
            $this->assertArrayHasKey("SIZE", $updatedData[$i]);
            $this->assertArrayHasKey("AGE", $updatedData[$i]);
        }

        // 1rst modified entity
        $this->assertEquals($updatedData[0]["ID"], 0);
        $this->assertEquals($updatedData[0]["FIRST_NAME"], "John");
        $this->assertEquals($updatedData[0]["LAST_NAME"], "Turingstone");
        $this->assertEquals($updatedData[0]["SIZE"], 175.50);
        $this->assertEquals($updatedData[0]["AGE"], 32);
        $this->assertEquals($updatedData[0]["ACTIF"], true);

        // 2nd modified entity
        $this->assertEquals($updatedData[1]["ID"], 1);
        $this->assertEquals($updatedData[1]["FIRST_NAME"], "Neo");
        $this->assertEquals($updatedData[1]["LAST_NAME"], "Doe");
        $this->assertEquals($updatedData[1]["SIZE"], 184.20);
        $this->assertEquals($updatedData[1]["AGE"], 33);
        $this->assertEquals($updatedData[1]["ACTIF"], true);
    }

    /**
     * @covers DbJsonBasedStructureTest::update
     */
    public function testUpdateOneFieldMultipleStructure()
    {
        // Create 1rst structure
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

        // Create 2nd structure
        $structure2 = new DbJsonBasedStructure(
            "customer",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "age" => DbJsonBasedStructure::TYPE_INT
            ]
        );
        $this->createDb->createDb($structure2);

        $datas2 = new DbJsonBasedData($this->createDb, "customer", [
            [
                "first_name" => "Alan",
                "last_name" => "Turingstone",
                "age" => 43,
            ]
        ]);
        $this->createDb->insert($datas2);

        // Update 1rst structure
        $datasToUpdate = new DbJsonBasedData($this->createDb, "identity", [
            [
                "last_name" => "Turingstone",
                "id" => 0,
                "age" => 32
            ],
        ]);
        $updated = $this->createDb->update($datasToUpdate);

        // Update 2nd structure
        $datasToUpdate2 = new DbJsonBasedData($this->createDb, "customer", [
            [
                "first_name" => "Neo",
                "id" => 0,
                "age" => 23
            ],
        ]);
        $updated2 = $this->createDb->update($datasToUpdate2);

        // Check
        $this->assertTrue($updated);
        $this->assertTrue($updated2);

        $updatedIdentityData = $this->createDb->findAll("identity");
        $updatedCustomerData = $this->createDb->findAll("customer");

        $this->assertIsArray($updatedIdentityData);
        $this->assertCount(2, $updatedIdentityData);

        $this->assertIsArray($updatedCustomerData);
        $this->assertCount(1, $updatedCustomerData);

        $this->assertCount(6, $this->createDb->getColumns("identity"));
        $this->assertCount(4, $this->createDb->getColumns("customer"));

        // 1rst modified entity
        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("ID", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("FIRST_NAME", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("LAST_NAME", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("SIZE", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("AGE", $updatedIdentityData[$i]);
        }

        $this->assertEquals($updatedIdentityData[0]["ID"], 0);
        $this->assertEquals($updatedIdentityData[0]["FIRST_NAME"], "John");
        $this->assertEquals($updatedIdentityData[0]["LAST_NAME"], "Turingstone");
        $this->assertEquals($updatedIdentityData[0]["SIZE"], 175.50);
        $this->assertEquals($updatedIdentityData[0]["AGE"], 32);
        $this->assertEquals($updatedIdentityData[0]["ACTIF"], true);

        // 2nd modified entity
        $this->assertArrayHasKey("ID", $updatedCustomerData[0]);
        $this->assertArrayHasKey("FIRST_NAME", $updatedCustomerData[0]);
        $this->assertArrayHasKey("LAST_NAME", $updatedCustomerData[0]);
        $this->assertArrayHasKey("AGE", $updatedCustomerData[0]);

        $this->assertEquals($updatedCustomerData[0]["ID"], 0);
        $this->assertEquals($updatedCustomerData[0]["FIRST_NAME"], "Neo");
        $this->assertEquals($updatedCustomerData[0]["LAST_NAME"], "Turingstone");
        $this->assertEquals($updatedCustomerData[0]["AGE"], 23);
    }

    /**
     * @covers DbJsonBasedStructureTest::update
     */
    public function testUpdateMultipleFieldsMultipleStructure()
    {
        // Create 1rst structure
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

        // Create 2nd structure
        $structure2 = new DbJsonBasedStructure(
            "customer",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING,
                "age" => DbJsonBasedStructure::TYPE_INT
            ]
        );
        $this->createDb->createDb($structure2);

        $datas2 = new DbJsonBasedData($this->createDb, "customer", [
            [
                "first_name" => "Alan",
                "last_name" => "Turingstone",
                "age" => 43,
            ],
            [
                "first_name" => "Arrakis",
                "last_name" => "Pandora",
                "age" => 27,
            ],
            [
                "first_name" => "Arid",
                "last_name" => "Tropical",
                "age" => 57,
            ]
        ]);
        $this->createDb->insert($datas2);

        // Update 1rst structure
        $datasToUpdate = new DbJsonBasedData($this->createDb, "identity", [
            [
                "last_name" => "Turingstone",
                "id" => 0,
                "age" => 32
            ],
            [
                "id" => 1,
                "first_name" => "James",
                "age" => 21,
                "actif" => true
            ]
        ]);
        $updated = $this->createDb->update($datasToUpdate);

        // Update 2nd structure
        $datasToUpdate2 = new DbJsonBasedData($this->createDb, "customer", [
            [
                "first_name" => "Neo",
                "id" => 0,
                "age" => 23
            ],
            [
                "id" => 1,
                "first_name" => "Bond",
                "age" => 25,
            ],
            [
                "age" => 12,
                "id" => 2
            ]
        ]);
        $updated2 = $this->createDb->update($datasToUpdate2);

        // Check
        $this->assertTrue($updated);
        $this->assertTrue($updated2);

        $updatedIdentityData = $this->createDb->findAll("identity");
        $updatedCustomerData = $this->createDb->findAll("customer");

        $this->assertIsArray($updatedIdentityData);
        $this->assertCount(2, $updatedIdentityData);

        $this->assertIsArray($updatedCustomerData);
        $this->assertCount(3, $updatedCustomerData);

        $this->assertCount(6, $this->createDb->getColumns("identity"));
        $this->assertCount(4, $this->createDb->getColumns("customer"));

        // 1rst modified entity
        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("ID", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("FIRST_NAME", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("LAST_NAME", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("SIZE", $updatedIdentityData[$i]);
            $this->assertArrayHasKey("AGE", $updatedIdentityData[$i]);
        }

        $this->assertEquals($updatedIdentityData[0]["ID"], 0);
        $this->assertEquals($updatedIdentityData[0]["FIRST_NAME"], "John");
        $this->assertEquals($updatedIdentityData[0]["LAST_NAME"], "Turingstone");
        $this->assertEquals($updatedIdentityData[0]["SIZE"], 175.50);
        $this->assertEquals($updatedIdentityData[0]["AGE"], 32);
        $this->assertEquals($updatedIdentityData[0]["ACTIF"], true);

        $this->assertEquals($updatedIdentityData[1]["ID"], 1);
        $this->assertEquals($updatedIdentityData[1]["FIRST_NAME"], "James");
        $this->assertEquals($updatedIdentityData[1]["LAST_NAME"], "Trinitron");
        $this->assertEquals($updatedIdentityData[1]["SIZE"], 184.20);
        $this->assertEquals($updatedIdentityData[1]["AGE"], 21);
        $this->assertEquals($updatedIdentityData[1]["ACTIF"], true);

        // 2nd modified entity
        $this->assertArrayHasKey("ID", $updatedCustomerData[0]);
        $this->assertArrayHasKey("FIRST_NAME", $updatedCustomerData[0]);
        $this->assertArrayHasKey("LAST_NAME", $updatedCustomerData[0]);
        $this->assertArrayHasKey("AGE", $updatedCustomerData[0]);

        $this->assertEquals($updatedCustomerData[0]["ID"], 0);
        $this->assertEquals($updatedCustomerData[0]["FIRST_NAME"], "Neo");
        $this->assertEquals($updatedCustomerData[0]["LAST_NAME"], "Turingstone");
        $this->assertEquals($updatedCustomerData[0]["AGE"], 23);

        $this->assertEquals($updatedCustomerData[1]["ID"], 1);
        $this->assertEquals($updatedCustomerData[1]["FIRST_NAME"], "Bond");
        $this->assertEquals($updatedCustomerData[1]["LAST_NAME"], "Pandora");
        $this->assertEquals($updatedCustomerData[1]["AGE"], 25);

        $this->assertEquals($updatedCustomerData[2]["ID"], 2);
        $this->assertEquals($updatedCustomerData[2]["FIRST_NAME"], "Arid");
        $this->assertEquals($updatedCustomerData[2]["LAST_NAME"], "Tropical");
        $this->assertEquals($updatedCustomerData[2]["AGE"], 12);
    }
}
