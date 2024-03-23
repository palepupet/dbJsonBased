<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;
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

    /**
     * @covers DbJsonBasedStaticTest::harmonizeKeyCase
     */
    public function testHarmonizeKeyCaseWrongCaseFunction()
    {
        $mixedCase = [
            "lowercase" => "lower",
            "UPPERCASE" => "upper",
            "MIxedCAse" => "mixed"
        ];

        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        array_keys(Utils::harmonizeKeyCase($mixedCase, "invalidCaseFunction"));
    }

    /**
     * @covers DbJsonBasedStaticTest::harmonizeKeyCase 
     */
    public function testHarmonizeKeyCaseEmptyDatas()
    {
        $mixedCase = [];

        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        array_keys(Utils::harmonizeKeyCase($mixedCase, Utils::STRTOUPPER));
    }

    /**
     * @covers DbJsonBasedStaticTest::harmonizeKeyCase 
     */
    public function testHarmonizeKeyCaseEmptyCaseFunction()
    {
        $mixedCase = [
            "lowercase" => "lower",
            "UPPERCASE" => "upper",
            "MIxedCAse" => "mixed"
        ];

        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        array_keys(Utils::harmonizeKeyCase($mixedCase, ""));
    }

    /**
     * @covers DbJsonBasedStaticTest::harmonizeKeyCase
     */
    public function testHarmonizeKeyCaseUpper()
    {
        $mixedCase = [
            "lowercase" => "lower",
            "UPPERCASE" => "upper",
            "MIxedCAse" => "mixed"
        ];

        $harmonizedCase = array_keys(Utils::harmonizeKeyCase($mixedCase, Utils::STRTOUPPER));

        $this->assertEquals("LOWERCASE", $harmonizedCase[0]);
        $this->assertEquals("UPPERCASE", $harmonizedCase[1]);
        $this->assertEquals("MIXEDCASE", $harmonizedCase[2]);
    }

    /**
     * @covers DbJsonBasedStaticTest::harmonizeKeyCase
     */
    public function testHarmonizeKeyCaseLower()
    {
        $mixedCase = [
            "lowercase" => "lower",
            "UPPERCASE" => "upper",
            "MIxedCAse" => "mixed"
        ];

        $harmonizedCase = array_keys(Utils::harmonizeKeyCase($mixedCase, Utils::STRTOLOWER));

        $this->assertEquals("lowercase", $harmonizedCase[0]);
        $this->assertEquals("uppercase", $harmonizedCase[1]);
        $this->assertEquals("mixedcase", $harmonizedCase[2]);
    }

    /**
     * @covers DbJsonBasedStaticTest::harmonizeKeyCase
     */
    public function testHarmonizeKeyCaseSimpleArrayUpper()
    {
        $simpleArray = ["Upper1", "UPPER2", "upper3", "UppER4"];

        $harmonizedCase = Utils::harmonizeKeyCase($simpleArray, Utils::STRTOUPPER, true);

        $this->assertEquals("UPPER1", $harmonizedCase[0]);
        $this->assertEquals("UPPER2", $harmonizedCase[1]);
        $this->assertEquals("UPPER3", $harmonizedCase[2]);
        $this->assertEquals("UPPER4", $harmonizedCase[3]);
    }

    /**
     * @covers DbJsonBasedStaticTest::harmonizeKeyCase
     */
    public function testHarmonizeKeyCaseSimpleArrayLower()
    {
        $simpleArray = ["Lower1", "LOWER2", "lower3", "LoWEr4"];

        $harmonizedCase = Utils::harmonizeKeyCase($simpleArray, Utils::STRTOLOWER, true);

        $this->assertEquals("lower1", $harmonizedCase[0]);
        $this->assertEquals("lower2", $harmonizedCase[1]);
        $this->assertEquals("lower3", $harmonizedCase[2]);
        $this->assertEquals("lower4", $harmonizedCase[3]);
    }

    /**
     * @covers DbJsonBasedStaticTest::updateKeysArray
     */
    public function testUpdateKeys()
    {
        // Datas
        $datas = [
            [
                "FIRST_NAME" => "John",
                "LAST_NAME" => "Doe",
                "SIZE" => 175.50,
                "AGE" => 21,
                "ACTIF" => true
            ],
            [
                "FIRST_NAME" => "Neo",
                "LAST_NAME" => "Trinitron",
                "SIZE" => 184.20,
                "AGE" => 33,
                "ACTIF" => false
            ]
        ];

        // Update
        Utils::updateKeysArray($datas, [
            "FIRST_NAME" => "name",
            "ACTIF" => "is_actif"
        ]);

        // Check
        $this->assertEquals($datas[0]["NAME"], "John");
        $this->assertEquals($datas[0]["LAST_NAME"], "Doe");
        $this->assertEquals($datas[0]["SIZE"], 175.50);
        $this->assertEquals($datas[0]["AGE"], 21);
        $this->assertEquals($datas[0]["IS_ACTIF"], true);

        $this->assertEquals($datas[1]["NAME"], "Neo");
        $this->assertEquals($datas[1]["LAST_NAME"], "Trinitron");
        $this->assertEquals($datas[1]["SIZE"], 184.20);
        $this->assertEquals($datas[1]["AGE"], 33);
        $this->assertEquals($datas[1]["IS_ACTIF"], false);
    }
}
