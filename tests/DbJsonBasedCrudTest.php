<?php

namespace Palepupet\DbJsonBased\Tests;

class DbJsonBasedCrudTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedCrudTest::getName
     */
    public function testGetName()
    {
        $fileName = $this->db->getName();
        $this->assertIsString($fileName);
        $this->assertEquals("db.json", basename($fileName));
    }
}
