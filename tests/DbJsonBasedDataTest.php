<?php

namespace Palepupet\DbJsonBased\Tests;

use Palepupet\DbJsonBased\DbJsonBasedData;
use Palepupet\DbJsonBased\DbJsonBasedDataInterface;
use Palepupet\DbJsonBased\DbJsonBasedStructure;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidKeyException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidTypeException;

class DbJsonBasedDataTest extends DbJsonBasedTest
{
    /**
     * @covers DbJsonBasedDataTest::__construct
     */
    public function testDataHasValidInstance()
    {
        $datas = new DbJsonBasedData($this->db, "identity", [
            "key1" => DbJsonBasedStructure::TYPE_STRING
        ]);
        $this->assertInstanceOf(DbJsonBasedDataInterface::class, $datas);
    }

    /**
     * @covers DbJsonBasedDataTest::__construct
     */
    public function testDataDoesNotHaveTableName()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        new DbJsonBasedData($this->db, "", [
            "key1" => DbJsonBasedStructure::TYPE_STRING
        ]);
    }

    /**
     * @covers DbJsonBasedDataTest::__construct
     */
    public function testDataDoesNotHaveValidTableName()
    {
        $this->expectException(DbJsonBasedInvalidArgumentException::class);
        new DbJsonBasedData($this->db, "invalidTableName", [
            "key1" => DbJsonBasedStructure::TYPE_STRING
        ]);
    }

    /**
     * @covers DbJsonBasedData::__construct
     */
    public function testDataKeyDoesNotExistIntoColumnsDb()
    {
        $structure = new DbJsonBasedStructure("Customer", [
            "first_name" => DbJsonBasedStructure::TYPE_STRING,
            "last_name" => DbJsonBasedStructure::TYPE_STRING,
            "size" => DbJsonBasedStructure::TYPE_FLOAT,
            "age" => DbJsonBasedStructure::TYPE_INT,
            "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
        ]);
        $this->createDb->createDb($structure);

        $this->expectException(DbJsonBasedInvalidKeyException::class);
        new DbJsonBasedData($this->createDb, "Customer", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175.50,
                "invalidKey" => "invalidValue",
            ]
        ]);
    }

    /**
     * @covers DbJsonBasedDataTest::__construct
     */
    public function testDataHasWrongTypeString()
    {
        $structure = new DbJsonBasedStructure("Customer", [
            "first_name" => DbJsonBasedStructure::TYPE_STRING,
            "last_name" => DbJsonBasedStructure::TYPE_STRING,
            "size" => DbJsonBasedStructure::TYPE_FLOAT,
            "age" => DbJsonBasedStructure::TYPE_INT,
            "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
        ]);
        $this->createDb->createDb($structure);

        $this->expectException(DbJsonBasedInvalidTypeException::class);
        new DbJsonBasedData($this->createDb, "Customer", [
            [
                "first_name" => "John",
                "last_name" => 57,
                "size" => 175.50,
                "age" => 21,
                "actif" => true
            ]
        ]);
    }

    /**
     * @covers DbJsonBasedDataTest::__construct
     */
    public function testDataHasWrongTypeFloat()
    {
        $structure = new DbJsonBasedStructure("Customer", [
            "first_name" => DbJsonBasedStructure::TYPE_STRING,
            "last_name" => DbJsonBasedStructure::TYPE_STRING,
            "size" => DbJsonBasedStructure::TYPE_FLOAT,
            "age" => DbJsonBasedStructure::TYPE_INT,
            "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
        ]);
        $this->createDb->createDb($structure);

        $this->expectException(DbJsonBasedInvalidTypeException::class);
        new DbJsonBasedData($this->createDb, "Customer", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 175,
                "age" => 21,
                "actif" => true
            ]
        ]);
    }

    /**
     * @covers DbJsonBasedDataTest::__construct
     */
    public function testDataHasWrongTypeInt()
    {
        $structure = new DbJsonBasedStructure("Customer", [
            "first_name" => DbJsonBasedStructure::TYPE_STRING,
            "last_name" => DbJsonBasedStructure::TYPE_STRING,
            "size" => DbJsonBasedStructure::TYPE_FLOAT,
            "age" => DbJsonBasedStructure::TYPE_INT,
            "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
        ]);
        $this->createDb->createDb($structure);

        $this->expectException(DbJsonBasedInvalidTypeException::class);
        new DbJsonBasedData($this->createDb, "Customer", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 157.50,
                "age" => "21",
                "actif" => true
            ]
        ]);
    }

    /**
     * @covers DbJsonBasedDataTest::__construct
     */
    public function testDataHasWrongTypeBool()
    {
        $structure = new DbJsonBasedStructure("Customer", [
            "first_name" => DbJsonBasedStructure::TYPE_STRING,
            "last_name" => DbJsonBasedStructure::TYPE_STRING,
            "size" => DbJsonBasedStructure::TYPE_FLOAT,
            "age" => DbJsonBasedStructure::TYPE_INT,
            "actif" => DbJsonBasedStructure::TYPE_BOOLEAN
        ]);
        $this->createDb->createDb($structure);

        $this->expectException(DbJsonBasedInvalidTypeException::class);
        new DbJsonBasedData($this->createDb, "Customer", [
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "size" => 157.50,
                "age" => 21,
                "actif" => "true"
            ]
        ]);
    }
}
