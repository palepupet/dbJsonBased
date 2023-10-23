<?php

namespace Palepupet\DbJsonBased\Tests;

class DbJsonBasedCrudTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedCrudTest::getFullName
     */
    public function testGetFullName()
    {
        $fileName = $this->db->getFullName();
        $this->assertIsString($fileName);
        $this->assertEquals("db.json", $fileName);
    }

    /**
     * @covers DbJsonBasedCrudTest::getName
     */
    public function testGetName()
    {
        $filename = $this->db->getName();
        $this->assertIsString($filename);
        $this->assertEquals("db", $filename);
    }

    /**
     * @covers DbJsonBasedCrudTest::getPath
     */
    public function testGetPath()
    {
        $path = $this->db->getPath();
        $this->assertIsString($path);
        $this->assertEquals($this->dbDirectory . ".json", $path);
    }
}
