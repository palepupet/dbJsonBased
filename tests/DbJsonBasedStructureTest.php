<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\Tests\DbJsonBasedTest;
use Palepupet\DbJsonBased\DbJsonBasedStructureInterface;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;


class DbJsonBasedStructureTest extends DbJsonBasedTest
{

    /**
     * @covers DbJsonBasedStructureTest::__construct
     */
    public function testStructureHasValidInstance()
    {
        $structure = new DbJsonBasedStructure("tableName", ["key1" => "string"]);
        $this->assertInstanceOf(DbJsonBasedStructureInterface::class, $structure);
    }

    /**
     * @covers DbJsonBasedStructureTest::__construct
     */
    public function testStructureDoesNotHaveTableName()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $structure = new DbJsonBasedStructure("", ["key1" => "bool"]);
    }

    /**
     * @covers DbJsonBasedStructureTest::__construct
     */
    public function testStructureDoesNotHaveFullKeys()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $structure = new DbJsonBasedStructure("tableName", ["" => "float"]);
    }

    /**
     * @covers DbJsonBasedStructureTest::__construct
     */
    public function testStructureDoesNotHaveFullValues()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $structure = new DbJsonBasedStructure("tableName", ["key1" => ""]);
    }

    /**
     * @covers DbJsonBasedStructureTest::__construct
     */
    public function testStructureDoesNotHaveValidTypes()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        $structure = new DbJsonBasedStructure("tableName", ["key1" => "wrongTypes"]);
    }

    /**
     * @covers DbJsonBasedStructureTest::getTableName
     */
    public function testStructureHasWellTableName()
    {
        $structure = new DbJsonBasedStructure("tableName", ["key1" => "string"]);
        $this->assertEquals("TABLENAME", $structure->getTableName());
        $this->assertIsString($structure->getTableName());
    }

    /**
     * @covers DbJsonBasedStructureTest::getColumns
     */
    public function testStructureHasWellColumns()
    {
        $structure = new DbJsonBasedStructure("tableName", ["key1" => "string", "key2" => "float"]);
        $columns = $structure->getColumns();
        $this->assertIsArray($columns);
        $this->assertCount(3, $columns);

        $this->assertArrayHasKey("KEY1", $columns);
        $this->assertArrayHasKey("KEY2", $columns);
        $this->assertArrayHasKey("ID", $columns);

        $this->assertEquals($columns["KEY1"], "string");
        $this->assertEquals($columns["KEY2"], "float");
        $this->assertEquals($columns["ID"], "int");
    }

    /**
     * @covers DbJsonBasedStructureTest::setColumns
     */
    public function testStructureHasWellTypedIdColumn()
    {
        // Wrong typed ID
        $structure = new DbJsonBasedStructure("tableName", ["key1" => "string", "key2" => "float", "id" => "string"]);
        $columns = $structure->getColumns();

        $this->assertArrayHasKey("ID", $columns);
        $this->assertEquals($columns["ID"], "int");

        // Correct typed ID
        $structure = new DbJsonBasedStructure("tableName", ["key1" => "string", "key2" => "float", "id" => "int"]);
        $columns = $structure->getColumns();

        $this->assertArrayHasKey("ID", $columns);
        $this->assertEquals($columns["ID"], "int");

        // Without ID
        $structure = new DbJsonBasedStructure("tableName", ["key1" => "string", "key2" => "float"]);
        $columns = $structure->getColumns();

        $this->assertArrayHasKey("ID", $columns);
        $this->assertEquals($columns["ID"], "int");
    }
}
