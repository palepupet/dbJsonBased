# README

Lightweight PHP library to use JSON file as a database. Very practical when you don't want to bring out the heavy artillery or when you want to set up data persistence quickly.

## Installation

```sh
composer require palepupet/dbjsonbased
```

## Use

**Init :**

```php
$jsonDb = new DbJsonBased("path_of_the_db_without_json_extension");
```

Then you have to create the database's file, use **DbJsonBasedStructureInterface** to do this. You must provide a structure for your datas with a **DbJsonBasedStructureInterface** as argument. **DbJsonBasedStructure** class implements it with all needed methods to check them.

```php
$jsonDb->createDb(new DbJsonBasedStructure(string $tableName, array $columns));
```

To make it short, you can do that :

```php
$jsonDb = new DbJsonBased("customersDb");

$datasStructure = new DbJsonBasedStructure("Customers", [
    "first_name" => DbJsonBasedStructure::TYPE_STRING,
    "last_name" => DbJsonBasedStructure::TYPE_STRING,
    "size" => DbJsonBasedStructure::TYPE_FLOAT,
    "age" => DbJsonBasedStructure::TYPE_INT,
    ...
]);

$jsonDb->createDb($datasStructure);
```

All accepted types are **boolean**, **float**, **int**, and **string**. You can use the class constants provided if you wish

```php
DbJsonBasedStructure::TYPE_BOOLEAN
DbJsonBasedStructure::TYPE_FLOAT
DbJsonBasedStructure::TYPE_INT
DbJsonBasedStructure::TYPE_STRING
```

The created file will look like this. Every database's file will be encoded into json :

```json
{
    "CUSTOMERS":{
        "COLUMNS":{
            "FIRST_NAME":"string",
            "LAST_NAME":"string",
            "SIZE":"float",
            "AGE":"int"
        },
        "VALUES":[],
        "ID":null
    }
}
```

You can fetch the entire file and decode it with **Utils::getContentAndDecode(string $pathOfDbFile)** method.

```php
array(1) {
    ["CUSTOMERS"] => array(3) {
        ["COLUMNS"] => array(4) {
            ["FIRST_NAME"] => string(6) "string"
            ["LAST_NAME"] => string(6) "string"
            ["SIZE"] => string(4) "float"
            ["AGE"] => string(3) "int"
        }
        ["VALUES"] => array(0) {}
        ["ID"] => NULL
  }
}
```

> **_NOTICE :_**
> However if you just want to retrieve an element, or act on it, like the TABLENAME, COLUMNS, VALUES, ID some methods, explained below, are more suitables.

You can create as much structure as you want in a file. To add a new structure to an existing file, simply reuse these functions above with a different structure name.

```php
// Adding the 2nd structure
$datasStructure2 = new DbJsonBasedStructure("Address", [
    "name" => DbJsonBasedStructure::TYPE_STRING,
    "street" => DbJsonBasedStructure::TYPE_STRING,
    "zip_code" => DbJsonBasedStructure::TYPE_STRING,
    "city" => DbJsonBasedStructure::TYPE_STRING,
    ...
]);

$jsonDb->createDb($datasStructure2);

// Now we have 2 structures into the same file
array(2) {
    ["CUSTOMERS"] => array(3) {
        ["COLUMNS"] => array(4) {
            ["FIRST_NAME"] => string(6) "string"
            ["LAST_NAME"] => string(6) "string"
            ["SIZE"] => string(4) "float"
            ["AGE"] => string(3) "int"
        }
        ["VALUES"] => array(0) {}
        ["ID"] => NULL
    },
    ["ADDRESS"] => array(3) {
        ["COLUMNS"] => array(4) {
            ["NAME"] => string(6) "string"
            ["STREET"] => string(6) "string"
            ["ZIP_CODE"] => string(6) "string"
            ["CITY"] => string(6) "string"
        }
        ["VALUES"] => array(0) {}
        ["ID"] => NULL
    }
}
```

**Insert datas :**

> **_NOTICE :_**
> You have to create the structure of your database, before inserting datas.

**insert(DbJsonBasedDataInterface $datas)** Insert the datas passed as arguments, use **DbJsonBasedDataInterface** to do this. You can use the **DbJsonBasedData** class wich implements it with all needed methods to check them.

```php
$datas = new DbJsonBasedData($jsonDb, "Customers", [
    [
        "first_name" => "John",
        "last_name" => "Doe",
        "size" => 175.50,
        "age" => 21,
    ],
    [
        "first_name" => "Neo",
        "last_name" => "Trinitron",
        "size" => 184.20,
        "age" => 33,
    ]
]);

$jsonDb->insert($datas);
```

**Remove datas :**

**remove(string \$tableName, ?int \$idToRemove, bool \$removeAllTableNameValues = false)**
DbJsonBased offers several data deletion options, depending on your needs. One function brings this together. Only the arguments will configure the deletion you need

* You can delete only one entity in a table **remove('Customers', 1)**. In this case you will delete the entity from the 'Customer' table which has id 1. If we base ourselves on the datas inserted in the insert function above, you will therefore have, after deletion, the following data :

```php
// result
array(1) {
    [0] => array(5) {
        ["FIRST_NAME"] => string(4) "John"
        ["LAST_NAME"] => string(3) "Doe"
        ["SIZE"] => float(175.5)
        ["AGE"] => int(21)
        ["ID"] => int(0)
    }
}
```

* You can delete all values ​​from the table. If you do this you will reset all the values ​​already present, **remove('Customers', null, true)**. Do not provide any id if you want to use this deletion

> **_NOTICE :_**
> By doing this you will delete all the values ​​present in the table. Once the operation is done, you will not be able to recover deleted data.

**Handling :**

Several methods are available to manipulate databases. To find and fetch datas :

**findOne(string \$tableName, int $id)** Returns the first data wich is equal to the provided id in the database's table

```php
// datas
["first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],
["first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]

$jsonDb->findOne("Customers", 1);

// result
array(1) {
    [0]=> array(5) {
        ["ID"] => int(1)
        ["FIRST_NAME"] => string(3) "Neo"
        ["LAST_NAME"] => string(9) "Trinitron"
        ["SIZE"] => float(184.2)
        ["AGE"] => int(33)
    }
}
```

**findAll(string $tableName)** Returns all datas contained in the database's table

```php
// datas
["first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],
["first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]

$jsonDb->findAll("Customers");

// result
array(4) {
    [0] => array(5) {
        ["ID"] => int(0)
        ["FIRST_NAME"] => string(4) "John"
        ["LAST_NAME"] => string(3) "Doe"
        ["SIZE"] => float(175.5)
        ["AGE"] => int(21)
    }
    [1] => array(5) {
        ["ID"] => int(1)
        ["FIRST_NAME"] => string(3) "Neo"
        ["LAST_NAME"] => string(9) "Trinitron"
        ["SIZE"] => float(184.2)
        ["AGE"] => int(33)
    }
    [2] => array(5) {
        ["ID"] => int(2)
        ["FIRST_NAME"] => string(4) "Alan"
        ["LAST_NAME"] => string(11) "Turingstone"
        ["SIZE"] => float(170.3)
        ["AGE"] => int(45)
    }
    [3] => array(5) {
        ["ID"] => int(3)
        ["FIRST_NAME"] => string(4) "Luke"
        ["LAST_NAME"] => string(9) "Skylogger"
        ["SIZE"] => float(173.8)
        ["AGE"] => int(18)
    }
}
```

**findOneBy(string \$tableName, array \$criteria, bool \$caseSensitive = true)** Returns all datas that meets the filters in the database's table. By default, if no filter is provided, the search is performed in "EXACT".
All valid Filters are **START_BY**, **END_BY**, **CONTAINS** and **EXACT** only.
You can use the class constants provided **DbJsonBased::START_BY|END_BY|CONTAINS|EXACT**

```php
// datas
["first_name" => "Andrea", "last_name" => "Baker"],
["first_name" => "Angela", "last_name" => "Carr"],
["first_name" => "Stephanie", "last_name" => "Dowd"],
["first_name" => "stephanie", "last_name" => "Hardacre"]

$jsonDb->findOneBy("Customers", [
    "first_name" => "Stephanie"
]);

// result
array(1) {
    [0] => array(3) {
        ["FIRST_NAME"] => string(9) "Stephanie"
        ["LAST_NAME"] => string(4) "Dowd"
        ["ID"] => int(2)
    }
}
```

It is also possible to search on multiple filters. However, one filter === one field. It is not possible to have several filters on one field.

```php
// datas 
["first_name" => "Andrea", "last_name" => "Baker"],
["first_name" => "Angela", "last_name" => "Carr"],
["first_name" => "Stephanie", "last_name" => "Dowd"]

$jsonDb->findOneBy("Customers", [
    "first_name" => "an|" . DbJsonBased::CONTAINS // "first_name" must contains "an"
], false);

// result 1 filter
array(3) {
    [0] => array(3) {
        ["FIRST_NAME"] => string(6) "Andrea"
        ["LAST_NAME"] => string(5) "Baker"
        ["ID"] => int(0)
    }
    [1] => array(3) {
        ["FIRST_NAME"] => string(6) "Angela"
        ["LAST_NAME"] => string(4) "Carr"
        ["ID"] => int(1)
    }
    [2] => array(3) {
        ["FIRST_NAME"] => string(9) "Stephanie"
        ["LAST_NAME"] => string(4) "Dowd"
        ["ID"] => int(2)
    }
}

// ============================

$jsonDb->findOneBy("Customers", [
    "first_name" => "an|" . DbJsonBased::CONTAINS, // "first_name" must contain "an"
    "last_name" => "er|" . DbJsonBased::END_BY // AND "last_name" must end by "er"
], false);

// result 2 filters
array(1) {
    [0] => array(3) {
        ["FIRST_NAME"] => string(6) "Andrea"
        ["LAST_NAME"] => string(5) "Baker"
        ["ID"] => int(0)
    }
}
```

You can also choose whether you want to search case-sensitively or not. By default the search is case sensitive.

```php
// datas
["first_name" => "Andrea", "last_name" => "Baker"],
["first_name" => "Angela", "last_name" => "Carr"],
["first_name" => "Stephanie", "last_name" => "Dowd"],
["first_name" => "stephanie", "last_name" => "Hardacre"]

$jsonDb->findOneBy("Customers", [
    "first_name" => "Stephanie"
], false); // case sensitive = false

// result
array(2) {
    [0] => array(3) {
        ["FIRST_NAME"] => string(9) "Stephanie"
        ["LAST_NAME"] => string(4) "Dowd"
        ["ID"] => int(2)
    }
    [1] => array(3) {
        ["FIRST_NAME"] => string(9) "stephanie"
        ["LAST_NAME"] => string(8) "Hardacre"
        ["ID"] => int(3)
    }
}
```

## GET methods

**getFullName()** Return the name of the database's file, with its extension.

**getName()** Return the name of the database's file, without its extension.

**getPath()** Return the name of the database's file, with its entire path and extension.

> **_NOTICE :_**
> Even if the path is returned, it does not confirm that the database exists. At least, not until you used the createDb() method.
If your goal is to find out if the file physically exists, please use the static method **isFileExist(string $path)**

**getLastId(string $tableName)** Returns the last used ID from a database's table

```php
// datas
array(1) {
    ["CUSTOMERS"] => array(3) {
        ["COLUMNS"] => array(4) {
            ["FIRST_NAME"] => string(6) "string"
            ["LAST_NAME"] => string(6) "string"
            ["SIZE"] => string(4) "float"
            ["AGE"] => string(3) "int"
        }
        ["VALUES"] => array(3) {
            {datas},
            {datas},
            {datas}
        }
        ["ID"] => 3 // This id is not necessarily equal to the record numbers into "VALUES" because we can delete data. This is why this value keeps the last id used even if it has been deleted
  }
}

$jsonDb->getLastId("Customers")

// result
int(3)
```

**getColumns(string $tableName)** Returns all columns with their types from a database's table

```php
array(4) {
    ["FIRST_NAME"] => string(6) "string"
    ["LAST_NAME"] => string(6) "string"
    ["ACTIF"] => string(3) "int"
    ["ID"] => string(3) "int"
}
```

## STATIC methods

**Utils::isFileExist(string $path)** Return a boolean if the database exists and the .json file is created.

**Utils::getContentAndDecode(string \$path, bool \$associative = true)** Return the content of the file and decode it. It return an associative array if the $associative argument is true, or an object if it is false.

**Utils::encodeAndWriteFile(string \$path, array $toEncode)** Encodes datas and write these into the specified file.

> **_NOTICE :_**
> Datas written replaces the entire file. So if the file already exists with datas. The previous datas will be erased and replaced with the new ones.
>
> These last 2 static methods (Utils::getContentAndDecode(), Utils::encodeAndWriteFile() ) are not intended to be used outside of the methods concerned. They are used to recover and save databases.
