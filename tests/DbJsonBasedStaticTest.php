<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\Utils;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;

class DbJsonBasedStaticTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedStaticTest::isFileExist
     */
    public function testIsFileExist()
    {
        // File does exists
        $fileExists = Utils::isFileExist($this->db->getPath());
        $this->assertTrue($fileExists);

        // File does not exists
        $fileDoesNotExists = Utils::isFileExist("file_does_not_exists.json");
        $this->assertFalse($fileDoesNotExists);
    }

    /**
     * @covers DbJsonBasedStaticTest::getContentAndDecode
     */
    public function testGetContentAndDecode()
    {
        $fileAssoc = Utils::getContentAndDecode($this->db->getPath());
        $this->assertIsArray($fileAssoc);

        $fileNotAssoc = Utils::getContentAndDecode($this->db->getPath(), false);
        $this->assertIsObject($fileNotAssoc);

        $this->expectException(DbJsonBasedRuntimeException::class);
        $fileNotAssoc = Utils::getContentAndDecode($this->fakeDb->getPath());
    }

    /**
     * @covers DbJsonBasedStaticTest::encodeAndWriteFile
     */
    public function testEncodeAndWriteFile()
    {
        $structure = [
            "first_name" => DbJsonBasedStructure::TYPE_STRING,
            "last_name" => DbJsonBasedStructure::TYPE_STRING,
            "size" => DbJsonBasedStructure::TYPE_FLOAT,
            "age" => DbJsonBasedStructure::TYPE_INT
        ];

        Utils::encodeAndWriteFile($this->createDb->getPath(), $structure);

        $fetchDatas = Utils::getContentAndDecode($this->createDb->getPath());

        $this->assertCount(4, $fetchDatas);
        $this->assertEquals($fetchDatas["first_name"], DbJsonBasedStructure::TYPE_STRING);
        $this->assertEquals($fetchDatas["last_name"], DbJsonBasedStructure::TYPE_STRING);
        $this->assertEquals($fetchDatas["size"], DbJsonBasedStructure::TYPE_FLOAT);
        $this->assertEquals($fetchDatas["age"], DbJsonBasedStructure::TYPE_INT);
    }
}
