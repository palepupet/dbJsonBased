<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBased;
use Palepupet\DbJsonBased\DbJsonBasedData;
use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\Tests\DbJsonBasedTest;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;

class DbJsonBasedFindingTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedFindingTest::findAll
     */
    public function testFindAll()
    {
        $datasIdentity = $this->db->findAll("identity");
        $this->assertCount(4, $datasIdentity);

        for ($i = 0; $i <= 3; $i++) {
            $this->assertArrayHasKey("ID", $datasIdentity[$i]);
            $this->assertArrayHasKey("FIRST_NAME", $datasIdentity[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datasIdentity[$i]);
            $this->assertArrayHasKey("SIZE", $datasIdentity[$i]);
            $this->assertArrayHasKey("AGE", $datasIdentity[$i]);
        }

        $this->assertEquals($datasIdentity[0]["ID"], 0);
        $this->assertEquals($datasIdentity[0]["FIRST_NAME"], "John");
        $this->assertEquals($datasIdentity[0]["LAST_NAME"], "Doe");
        $this->assertEquals($datasIdentity[0]["SIZE"], 175.50);
        $this->assertEquals($datasIdentity[0]["AGE"], 21);

        $this->assertEquals($datasIdentity[1]["ID"], 1);
        $this->assertEquals($datasIdentity[1]["FIRST_NAME"], "Neo");
        $this->assertEquals($datasIdentity[1]["LAST_NAME"], "Trinitron");
        $this->assertEquals($datasIdentity[1]["SIZE"], 184.20);
        $this->assertEquals($datasIdentity[1]["AGE"], 33);

        $this->assertEquals($datasIdentity[2]["ID"], 2);
        $this->assertEquals($datasIdentity[2]["FIRST_NAME"], "Alan");
        $this->assertEquals($datasIdentity[2]["LAST_NAME"], "Turingstone");
        $this->assertEquals($datasIdentity[2]["SIZE"], 170.30);
        $this->assertEquals($datasIdentity[2]["AGE"], 45);

        $this->assertEquals($datasIdentity[3]["ID"], 3);
        $this->assertEquals($datasIdentity[3]["FIRST_NAME"], "Luke");
        $this->assertEquals($datasIdentity[3]["LAST_NAME"], "Skylogger");
        $this->assertEquals($datasIdentity[3]["SIZE"], 173.80);
        $this->assertEquals($datasIdentity[3]["AGE"], 18);

        $datasHouse = $this->db->findAll("houses");
        $this->assertCount(2, $datasHouse);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("ID", $datasHouse[$i]);
            $this->assertArrayHasKey("PLANET", $datasHouse[$i]);
            $this->assertArrayHasKey("CLIMATE", $datasHouse[$i]);
            $this->assertArrayHasKey("HABITABLE", $datasHouse[$i]);
        }

        $this->assertEquals($datasHouse[0]["ID"], 0);
        $this->assertEquals($datasHouse[0]["PLANET"], "Arrakis");
        $this->assertEquals($datasHouse[0]["CLIMATE"], "Arid");
        $this->assertEquals($datasHouse[0]["HABITABLE"], false);

        $this->assertEquals($datasHouse[1]["ID"], 1);
        $this->assertEquals($datasHouse[1]["PLANET"], "Pandora");
        $this->assertEquals($datasHouse[1]["CLIMATE"], "Tropical");
        $this->assertEquals($datasHouse[1]["HABITABLE"], true);
    }

    /**
     * @covers DbJsonBasedFindingTest::findAll
     */
    public function testFindAllHasTableName()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->db->findAll("");
    }

    /**
     * @covers DbJsonBasedFindingTest::findAll
     */
    public function testFindAllHasExistingTableName()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->db->findAll("invalidTableName");
    }

    /**
     * @covers DbJsonBasedFindingTest::findOne
     */
    public function testFindOneIdDoesNotExist()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->db->findOne("identity", 100);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOne
     */
    public function testFindOne()
    {
        // First Table
        $datasIdentity1 = $this->db->findOne("identity", 1);

        $this->assertIsArray($datasIdentity1);
        $this->assertCount(1, $datasIdentity1);

        $this->assertArrayHasKey("ID", $datasIdentity1[0]);
        $this->assertArrayHasKey("FIRST_NAME", $datasIdentity1[0]);
        $this->assertArrayHasKey("LAST_NAME", $datasIdentity1[0]);
        $this->assertArrayHasKey("SIZE", $datasIdentity1[0]);
        $this->assertArrayHasKey("AGE", $datasIdentity1[0]);

        $this->assertEquals(1, $datasIdentity1[0]["ID"]);
        $this->assertEquals("Neo", $datasIdentity1[0]["FIRST_NAME"]);
        $this->assertEquals("Trinitron", $datasIdentity1[0]["LAST_NAME"]);
        $this->assertEquals(184.20, $datasIdentity1[0]["SIZE"]);
        $this->assertEquals(33, $datasIdentity1[0]["AGE"]);

        // Second Table
        $datasHouses0 = $this->db->findOne("houses", 0);

        $this->assertIsArray($datasHouses0);
        $this->assertCount(1, $datasHouses0);

        $this->assertArrayHasKey("ID", $datasHouses0[0]);
        $this->assertArrayHasKey("PLANET", $datasHouses0[0]);
        $this->assertArrayHasKey("CLIMATE", $datasHouses0[0]);
        $this->assertArrayHasKey("HABITABLE", $datasHouses0[0]);

        $this->assertEquals(0, $datasHouses0[0]["ID"]);
        $this->assertEquals("Arrakis", $datasHouses0[0]["PLANET"]);
        $this->assertEquals("Arid", $datasHouses0[0]["CLIMATE"]);
        $this->assertEquals(false, $datasHouses0[0]["HABITABLE"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOne
     */
    public function testFindOneHasTableName()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->db->findOne("", 0);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOne
     */
    public function testFindOneHasExistingTableName()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $this->db->findOne("invalidTableName", 0);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneBy()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "stephanie", "last_name" => "Hardacre"]
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "Stephanie"
        ]);

        $this->assertIsArray($datas);
        $this->assertCount(1, $datas);

        $this->assertArrayHasKey("FIRST_NAME", $datas[0]);
        $this->assertArrayHasKey("LAST_NAME", $datas[0]);
        $this->assertArrayHasKey("ID", $datas[0]);

        $this->assertEquals("Stephanie", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Dowd", $datas[0]["LAST_NAME"]);
        $this->assertEquals(2, $datas[0]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByCaseInsensitive()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "stephanie", "last_name" => "Hardacre"]
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "Stephanie"
        ], false);

        $this->assertIsArray($datas);
        $this->assertCount(2, $datas);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Stephanie", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Dowd", $datas[0]["LAST_NAME"]);
        $this->assertEquals(2, $datas[0]["ID"]);

        $this->assertEquals("stephanie", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Hardacre", $datas[1]["LAST_NAME"]);
        $this->assertEquals(3, $datas[1]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByReturnEmpty()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"]
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "Harry"
        ]);

        $this->assertEmpty($datas);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByReturnSeveral()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "baker"],
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "last_name" => "Baker"
        ]);

        $this->assertIsArray($datas);
        $this->assertCount(2, $datas);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Andrea", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[0]["LAST_NAME"]);
        $this->assertEquals(0, $datas[0]["ID"]);

        $this->assertEquals("Vanessa", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[1]["LAST_NAME"]);
        $this->assertEquals(3, $datas[1]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByReturnSeveralCaseInsensitive()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "baker"],
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "last_name" => "Baker"
        ], false);

        $this->assertIsArray($datas);
        $this->assertCount(3, $datas);

        for ($i = 0; $i <= 2; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Andrea", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[0]["LAST_NAME"]);
        $this->assertEquals(0, $datas[0]["ID"]);

        $this->assertEquals("Vanessa", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[1]["LAST_NAME"]);
        $this->assertEquals(3, $datas[1]["ID"]);

        $this->assertEquals("Adrian", $datas[2]["FIRST_NAME"]);
        $this->assertEquals("baker", $datas[2]["LAST_NAME"]);
        $this->assertEquals(4, $datas[2]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByStart()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "Lyman"],
            ["first_name" => "Julian", "last_name" => "Parsons"],
            ["first_name" => "anastasia", "last_name" => "Crowd"]
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "An|" . DbJsonBased::START_BY
        ]);

        $this->assertIsArray($datas);
        $this->assertCount(2, $datas);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Andrea", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[0]["LAST_NAME"]);
        $this->assertEquals(0, $datas[0]["ID"]);

        $this->assertEquals("Angela", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Carr", $datas[1]["LAST_NAME"]);
        $this->assertEquals(1, $datas[1]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByStartCaseInsensitive()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "Lyman"],
            ["first_name" => "Julian", "last_name" => "Parsons"],
            ["first_name" => "anastasia", "last_name" => "Crowd"]
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "An|" . DbJsonBased::START_BY
        ], false);

        $this->assertIsArray($datas);
        $this->assertCount(3, $datas);

        for ($i = 0; $i <= 2; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Andrea", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[0]["LAST_NAME"]);
        $this->assertEquals(0, $datas[0]["ID"]);

        $this->assertEquals("Angela", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Carr", $datas[1]["LAST_NAME"]);
        $this->assertEquals(1, $datas[1]["ID"]);

        $this->assertEquals("anastasia", $datas[2]["FIRST_NAME"]);
        $this->assertEquals("Crowd", $datas[2]["LAST_NAME"]);
        $this->assertEquals(6, $datas[2]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByEnd()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "Lyman"],
            ["first_name" => "Julian", "last_name" => "Parsons"],
            ["first_name" => "AndreAn", "last_name" => "Crowd"],
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "an|" . DbJsonBased::END_BY
        ]);

        $this->assertIsArray($datas);
        $this->assertCount(2, $datas);

        for ($i = 0; $i <= 1; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Adrian", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Lyman", $datas[0]["LAST_NAME"]);
        $this->assertEquals(4, $datas[0]["ID"]);

        $this->assertEquals("Julian", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Parsons", $datas[1]["LAST_NAME"]);
        $this->assertEquals(5, $datas[1]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByEndCaseInsensitive()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "Lyman"],
            ["first_name" => "Julian", "last_name" => "Parsons"],
            ["first_name" => "AndreAn", "last_name" => "Crowd"],
        ], false));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "an|" . DbJsonBased::END_BY
        ], false);

        $this->assertIsArray($datas);
        $this->assertCount(3, $datas);

        for ($i = 0; $i <= 2; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Adrian", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Lyman", $datas[0]["LAST_NAME"]);
        $this->assertEquals(4, $datas[0]["ID"]);

        $this->assertEquals("Julian", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Parsons", $datas[1]["LAST_NAME"]);
        $this->assertEquals(5, $datas[1]["ID"]);

        $this->assertEquals("AndreAn", $datas[2]["FIRST_NAME"]);
        $this->assertEquals("Crowd", $datas[2]["LAST_NAME"]);
        $this->assertEquals(6, $datas[2]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByContains()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "Lyman"],
            ["first_name" => "Julian", "last_name" => "Parsons"],
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "an|" . DbJsonBased::CONTAINS
        ]);

        $this->assertIsArray($datas);
        $this->assertCount(4, $datas);

        for ($i = 0; $i <= 3; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Stephanie", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Dowd", $datas[0]["LAST_NAME"]);
        $this->assertEquals(2, $datas[0]["ID"]);

        $this->assertEquals("Vanessa", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[1]["LAST_NAME"]);
        $this->assertEquals(3, $datas[1]["ID"]);

        $this->assertEquals("Adrian", $datas[2]["FIRST_NAME"]);
        $this->assertEquals("Lyman", $datas[2]["LAST_NAME"]);
        $this->assertEquals(4, $datas[2]["ID"]);

        $this->assertEquals("Julian", $datas[3]["FIRST_NAME"]);
        $this->assertEquals("Parsons", $datas[3]["LAST_NAME"]);
        $this->assertEquals(5, $datas[3]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByContainsCaseInsensitive()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Baker"],
            ["first_name" => "Adrian", "last_name" => "Lyman"],
            ["first_name" => "Julian", "last_name" => "Parsons"],
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "an|" . DbJsonBased::CONTAINS
        ], false);

        $this->assertIsArray($datas);
        $this->assertCount(6, $datas);

        for ($i = 0; $i <= 5; $i++) {
            $this->assertArrayHasKey("FIRST_NAME", $datas[$i]);
            $this->assertArrayHasKey("LAST_NAME", $datas[$i]);
            $this->assertArrayHasKey("ID", $datas[$i]);
        }

        $this->assertEquals("Andrea", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[0]["LAST_NAME"]);
        $this->assertEquals(0, $datas[0]["ID"]);

        $this->assertEquals("Angela", $datas[1]["FIRST_NAME"]);
        $this->assertEquals("Carr", $datas[1]["LAST_NAME"]);
        $this->assertEquals(1, $datas[1]["ID"]);

        $this->assertEquals("Stephanie", $datas[2]["FIRST_NAME"]);
        $this->assertEquals("Dowd", $datas[2]["LAST_NAME"]);
        $this->assertEquals(2, $datas[2]["ID"]);

        $this->assertEquals("Vanessa", $datas[3]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[3]["LAST_NAME"]);
        $this->assertEquals(3, $datas[3]["ID"]);

        $this->assertEquals("Adrian", $datas[4]["FIRST_NAME"]);
        $this->assertEquals("Lyman", $datas[4]["LAST_NAME"]);
        $this->assertEquals(4, $datas[4]["ID"]);

        $this->assertEquals("Julian", $datas[5]["FIRST_NAME"]);
        $this->assertEquals("Parsons", $datas[5]["LAST_NAME"]);
        $this->assertEquals(5, $datas[5]["ID"]);
    }

    /**
     * @covers DbJsonBasedFindingTest::findOneBy
     */
    public function testFindOneByMultipleFilters()
    {
        // create file
        $structure = new DbJsonBasedStructure(
            "person",
            [
                "first_name" => DbJsonBasedStructure::TYPE_STRING,
                "last_name" => DbJsonBasedStructure::TYPE_STRING
            ]
        );
        $this->createDb->createDb($structure);

        // Inserting datas
        $this->createDb->insert(new DbJsonBasedData($this->createDb, "person", [
            ["first_name" => "Andrea", "last_name" => "Baker"],
            ["first_name" => "Angela", "last_name" => "Carr"],
            ["first_name" => "Stephanie", "last_name" => "Dowd"],
            ["first_name" => "Vanessa", "last_name" => "Hardacre"],
            ["first_name" => "Adrian", "last_name" => "Lyman"],
            ["first_name" => "Julian", "last_name" => "Parsons"],
        ]));

        // Set criterion
        $datas = $this->createDb->findOneBy("person", [
            "first_name" => "an|" . DbJsonBased::CONTAINS,
            "last_name" => "er|" . DbJsonBased::END_BY
        ], false);

        $this->assertIsArray($datas);
        $this->assertCount(1, $datas);

        $this->assertArrayHasKey("FIRST_NAME", $datas[0]);
        $this->assertArrayHasKey("LAST_NAME", $datas[0]);
        $this->assertArrayHasKey("ID", $datas[0]);

        $this->assertEquals("Andrea", $datas[0]["FIRST_NAME"]);
        $this->assertEquals("Baker", $datas[0]["LAST_NAME"]);
        $this->assertEquals(0, $datas[0]["ID"]);
    }
}
