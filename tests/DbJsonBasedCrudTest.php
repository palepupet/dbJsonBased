<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBasedData;
use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\DbJsonBasedDataInterface;

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
        }

        $this->assertEquals($insertedDatas[0]["ID"], 0);
        $this->assertEquals($insertedDatas[0]["FIRST_NAME"], "John");
        $this->assertEquals($insertedDatas[0]["LAST_NAME"], "Doe");
        $this->assertEquals($insertedDatas[0]["SIZE"], 175.50);
        $this->assertEquals($insertedDatas[0]["AGE"], 21);

        $this->assertEquals($insertedDatas[1]["ID"], 1);
        $this->assertEquals($insertedDatas[1]["FIRST_NAME"], "Neo");
        $this->assertEquals($insertedDatas[1]["LAST_NAME"], "Trinitron");
        $this->assertEquals($insertedDatas[1]["SIZE"], 184.20);
        $this->assertEquals($insertedDatas[1]["AGE"], 33);
    }
}
