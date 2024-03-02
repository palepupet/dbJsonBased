<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBasedStructure;


class DbJsonBasedGetTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedGetTest::getFullName
     */
    public function testGetFullName()
    {
        $fileName = $this->db->getFullName();
        $this->assertIsString($fileName);
        $this->assertEquals("db.json", $fileName);
    }

    /**
     * @covers DbJsonBasedGetTest::getName
     */
    public function testGetName()
    {
        $filename = $this->db->getName();
        $this->assertIsString($filename);
        $this->assertEquals("db", $filename);
    }

    /**
     * @covers DbJsonBasedGetTest::getPath
     */
    public function testGetPath()
    {
        $path = $this->db->getPath();
        $this->assertIsString($path);
        $this->assertEquals($this->dbDirectory . ".json", $path);
    }

    /**
     * @covers DbJsonBasedGetTest::getLastId
     */
    public function testGetLastId()
    {
        $lastUsedId = $this->db->getLastId("identity");

        $this->assertIsInt($lastUsedId);
        $this->assertEquals($lastUsedId, 3);

        $lastUsedId = $this->db->getLastId("houses");
        $this->assertIsInt($lastUsedId);
        $this->assertEquals($lastUsedId, 1);
    }

    /**
     * @covers DbJsonBasedGetTest::getLastId
     */
    public function testGetLastIdIsNull()
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

        // No datas, so NULL value
        $lastUsedId = $this->createDb->getLastId("identity");
        $this->assertNull($lastUsedId);
    }

    /**
     * @covers DbJsonBasedGetTest::getColumns
     */
    public function testGetColumns()
    {
        $columns = $this->db->getColumns("identity");

        $this->assertIsArray($columns);
        $this->assertCount(5, $columns);

        $this->assertArrayHasKey("ID", $columns);
        $this->assertArrayHasKey("FIRST_NAME", $columns);
        $this->assertArrayHasKey("LAST_NAME", $columns);
        $this->assertArrayHasKey("SIZE", $columns);
        $this->assertArrayHasKey("AGE", $columns);

        $this->assertEquals("int", $columns["ID"]);
        $this->assertEquals("string", $columns["FIRST_NAME"]);
        $this->assertEquals("string", $columns["LAST_NAME"]);
        $this->assertEquals("float", $columns["SIZE"]);
        $this->assertEquals("int", $columns["AGE"]);
    }
}
