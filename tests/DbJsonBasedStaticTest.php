<?php

namespace Palepupet\DbJsonBased\Tests;

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
            "first_name" => "string",
            "last_name" => "string",
            "size" => "float",
            "age" => "int"
        ];

        Utils::encodeAndWriteFile($this->createDb->getFullName(), $structure);

        $fetchDatas = Utils::getContentAndDecode($this->createDb->getFullName());

        $this->assertCount(4, $fetchDatas);
        $this->assertEquals($fetchDatas["first_name"], "string");
        $this->assertEquals($fetchDatas["last_name"], "string");
        $this->assertEquals($fetchDatas["size"], "float");
        $this->assertEquals($fetchDatas["age"], "int");
    }
}
