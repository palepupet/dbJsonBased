<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\Utils;

class DbJsonBasedStaticTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedStaticTest::isFileExist
     */
    public function testIsFileExist()
    {
        // File does exists
        $fileExists = Utils::isFileExist($this->db->getName());
        $this->assertTrue($fileExists);

        // File does not exists
        $fileDoesNotExists = Utils::isFileExist("file_does_not_exists.json");
        $this->assertFalse($fileDoesNotExists);
    }
}
