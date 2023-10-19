<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\Tests\DbJsonBasedTest;

use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;


class DbJsonBasedFileAndStructureTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedTest::createDb
     */
    public function testCreateDbNewFile()
    {
        // Check the file does not exists first
        $this->assertFileDoesNotExist($this->createDbDirectory);

        // Create the file
        $res = $this->createDb->createDb();

        // Check the file exists after creation
        $this->assertFileExists($this->createDbDirectory . ".json");
        $this->assertTrue($res);
    }

    /**
     * @covers DbJsonBasedTest::createDb
     */
    public function testCreateDbIfFileAlreadyExists()
    {
        // First creation
        $this->createDb->createDb();

        // If file already exists throw exception
        $this->expectException(DbJsonBasedRunTimeException::class);
        $this->createDb->createDb();
    }
}
