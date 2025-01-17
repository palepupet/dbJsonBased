# README

Lightweight PHP library to use JSON file as a database. Very practical when you don't want to bring out the heavy artillery or when you want to set up data persistence quickly.

## Installation

```sh
composer require palepupet/dbjsonbased
```

## Use

### Init

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

### Insert datas

> **_NOTICE :_**
> You have to create the structure of your database, before inserting datas.

**insert(DbJsonBasedDataInterface $datas)** Insert the datas passed as arguments, use **DbJsonBasedDataInterface** to do this. You can use the **DbJsonBasedData** class which implements it with all needed methods to check them.

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

### Update datas

**update(DbJsonBasedDataInterface \$datas)** Update the datas only put in arguments. The other datas will remain unchanged. The update uses **DbJsonBasedDataInterface** as for creating the database structure. This allows to use the same checks on the table, the fields... For updating datas you must therefore also use the **DbJsonBasedData** class.

> **_NOTICE :_**
> You absolutely must provide the entity ID (with the datas) to make the modification. Without it, it will be impossible to find the entity to modify.

```php
// 'Entity' datas
["id" => 0, "first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["id" => 1, "first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],
["id" => 2, "first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["id" => 3, "first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]

// Update the entity with the ID 1
$datasToUpdate = new DbJsonBasedData($jsonDb, "customers", [
    [
        "last_name" => "Turingstone",
        "age" => 55,
        "id" => 1
    ]
]);
$jsonDb->update($datasToUpdate);

// result
["id" => 0, "first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["id" => 1, "first_name" => "Neo", "last_name" => "Turingstone", "size" => 184.20, "age" => 55], // modified datas
["id" => 2, "first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["id" => 3, "first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]
```

If you have several entities to modify, you can add as many array as necessary. Each one containing the modifications to be made.

```php
// Update multiple entities
$datasToUpdate = new DbJsonBasedData($jsonDb, "customers", [
    [
        "first_name" => "Neo",
        "age" => 45,
        "id" => 0
    ],
    [
        "last_name" => "Doe",
        "size" => 32,
        "id" => 1
    ],
    [
        "age" => 25,
        "id" => 2
    ]
]);
$jsonDb->update($datasToUpdate);
```

### Remove datas

**remove(string \$tableName, ?int \$idToRemove, bool \$removeAllTableNameValues = false)**
DbJsonBased offers several data deletion options, depending on your needs. One function brings this together. Only the arguments will configure the deletion you need

* You can delete only one entity in a table **remove('Customers', 1)**. In this case you will delete the entity from the 'Customer' table which has id 1. If we base ourselves on the datas inserted in the insert function above, you will therefore have, after deletion, the following data :

```php
// 'Entity' datas
["id" => 0, "first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["id" => 1, "first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],

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

### Add columns

**addColumn(DbJsonBasedStructureInterface $structure)** Adds columns to your structure and datas. Adding columns uses **DbJsonBasedStructureInterface** to take advantage of structure checks. You can use the **DbJsonBasedStructure** class which implements this interface.

> **_NOTICE :_**
> Keep in mind that adding column, insert missing columns in your datas. However your datas will be added with a NULL value. You will need to do an update to update them with the desired values.

```php
// 'Entity' columns
["ID" => "int", "FIRST_NAME" => "string", "LAST_NAME" => "string"]

// Adding new columns
$addColumns = new DbJsonBasedStructure(
    "customers",
    [
        "actif" => DbJsonBasedStructure::TYPE_BOOLEAN,
        "address" => DbJsonBasedStructure::TYPE_STRING,
    ]
);
$jsonDb->addColumn($addColumns);

// result columns
["ID" => "int", "FIRST_NAME" => "string", "LAST_NAME" => "string", "ACTIF" => "bool", "ADDRESS" => "string"]

// result datas with NULL value
["id" => 0, "first_name" => "John", "last_name" => "Doe", "actif" => NULL, "address" => NULL],
["id" => 1, "first_name" => "Neo", "last_name" => "Turingstone", "actif" => NULL, "address" => NULL]
```

### Remove columns

**removeColumn(string \$tableName, array \$removedColumns)** If you realize that a column has become unnecessary or you want to remove a column added by mistake, you can use this method. It allows you to delete columns in each datas, but also in the structure part of the table.

> **_NOTICE :_**
> If you delete columns, keep in mind that any deleted data cannot be recovered. It will be permanently deleted.

```php
// 'Entity' columns
["ID" => "int", "FIRST_NAME" => "string", "LAST_NAME" => "string", "SIZE" => "float", "AGE" => "int"]

// 'Entity' datas
["id" => 0, "first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["id" => 1, "first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],
["id" => 2, "first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["id" => 3, "first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]

// Deletion of the columns SIZE and AGE
$jsonDb->removeColumn("customers", [
    "size",
    "age"
]);

// result 'Entity' columns
["ID" => "int", "FIRST_NAME" => "string", "LAST_NAME" => "string"]

// result 'Entity' datas
["id" => 0, "first_name" => "John", "last_name" => "Doe"],
["id" => 1, "first_name" => "Neo", "last_name" => "Trinitron"],
["id" => 2, "first_name" => "Alan", "last_name" => "Turingstone"],
["id" => 3, "first_name" => "Luke", "last_name" => "Skylogger"]
```

### Update columns

**updateColumn(DbJsonBasedStructureInterface $structure)** Allows you to modify the type of a column. Update column uses **DbJsonBasedStructureInterface** to take advantage of structure checks. You can use the **DbJsonBasedStructure** class which implements this interface.

> **_NOTICE :_**
> Modifying a type allows you to modify the type inside the database structure. However, your datas may no longer agree with the new type. You will therefore have to modify your datas according to your new type.

```php
// 'Entity' columns
["ID" => "int", "FIRST_NAME" => "string", "LAST_NAME" => "string", "SIZE" => "float", "AGE" => "int"]

// Update columns
$newColumnType = new DbJsonBasedStructure("customers", [
        "size" => DbJsonBasedStructure::TYPE_INT,
        "age" => DbJsonBasedStructure::TYPE_STRING,
    ]
);
$jsonDb->updateColumn($newColumnType);

// result 'Entity' columns
["ID" => "int", "FIRST_NAME" => "string", "LAST_NAME" => "string", "SIZE" => "int", "AGE" => "string"]
```

### Rename columns

**renameColumn(string \$tableName, array \$renamedColumns)** If you need to modify the name of a column in the database structure and also in the datas, you can use this method.

```php
// 'Entity' columns
["ID" => "int", "FIRST_NAME" => "string", "LAST_NAME" => "string", "SIZE" => "float", "AGE" => "int"]

// 'Entity' datas
["id" => 0, "first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["id" => 1, "first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],
["id" => 2, "first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["id" => 3, "first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]

// Rename columns
$jsonDb->renameColumn("customers", [
    "first_name" => "name",
    "age" => "is_adult"
]);

// result 'Entity' columns
["ID" => "int", "NAME" => "string", "LAST_NAME" => "string", "SIZE" => "int", "IS_ADULT" => "string"]

// result 'Entity' datas
["id" => 0, "name" => "John", "last_name" => "Doe", "size" => 175.50, "is_adult" => 21],
["id" => 1, "name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "is_adult" => 33],
["id" => 2, "name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "is_adult" => 45],
["id" => 3, "name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "is_adult" => 18]
```

## Handling

Several methods are available to manipulate databases. To find and fetch datas :

### findOne

**findOne(string \$tableName, int $id)** Returns the first data wich is equal to the provided id in the database's table

```php
// datas
["id" => 0, "first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["id" => 1, "first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],
["id" => 2, "first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["id" => 3, "first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]

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

### findOneBy

**findOneBy(string \$tableName, array \$criteria, bool \$caseSensitive = true)** Returns all datas that meets the filters in the database's table. By default, if no filter is provided, the search is performed in "EXACT".
All valid Filters are **START_BY**, **END_BY**, **CONTAINS** and **EXACT** only.
You can use the class constants provided **DbJsonBased::START_BY|END_BY|CONTAINS|EXACT**

```php
// datas
["id" => 0, "first_name" => "Andrea", "last_name" => "Baker"],
["id" => 1, "first_name" => "Angela", "last_name" => "Carr"],
["id" => 2, "first_name" => "Stephanie", "last_name" => "Dowd"],
["id" => 3, "first_name" => "stephanie", "last_name" => "Hardacre"]

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
["id" => 0, "first_name" => "Andrea", "last_name" => "Baker"],
["id" => 1, "first_name" => "Angela", "last_name" => "Carr"],
["id" => 2, "first_name" => "Stephanie", "last_name" => "Dowd"]

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
["id" => 0, "first_name" => "Andrea", "last_name" => "Baker"],
["id" => 1, "first_name" => "Angela", "last_name" => "Carr"],
["id" => 2, "first_name" => "Stephanie", "last_name" => "Dowd"],
["id" => 3, "first_name" => "stephanie", "last_name" => "Hardacre"]

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

### findAll

**findAll(string $tableName)** Returns all datas contained in the database's table

```php
// datas
["id" => 0, "first_name" => "John", "last_name" => "Doe", "size" => 175.50, "age" => 21],
["id" => 1, "first_name" => "Neo", "last_name" => "Trinitron", "size" => 184.20, "age" => 33],
["id" => 2, "first_name" => "Alan", "last_name" => "Turingstone", "size" => 170.30, "age" => 45],
["id" => 3, "first_name" => "Luke", "last_name" => "Skylogger", "size" => 173.80, "age" => 18]

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

## GET methods

### getFullName

**getFullName()** Return the name of the database's file, with its extension.

### getName

**getName()** Return the name of the database's file, without its extension.

### getPath

**getPath()** Return the name of the database's file, with its entire path and extension.

> **_NOTICE :_**
> Even if the path is returned, it does not confirm that the database exists. At least, not until you used the createDb() method.
If your goal is to find out if the file physically exists, please use the static method **isFileExist(string $path)**

### getLastId

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

### getColumns

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

### updateKeysArray

**Utils::updateKeysArray(array &\$arrayToModified, array \$renamedKey)** Renames the keys of the array passed as an argument

### harmonizeKeyCase

**Utils::harmonizeKeyCase(array \$datas, string \$caseFunction, bool \$simpleArray = false)** Returns the array of datas passed as arguments. You can change the case to Lower or Upper. If $simpleArray is true, each element of the array (standard) is modified according to the given case. Otherwise if it is false, only the keys of this array (associative) are modified according to the case indicated. You can use class constants to do that : **STRTOUPPER | STRTOLOWER**

### isFileExist

**Utils::isFileExist(string $path)** Returns a boolean if the database exists and the .json file is created.

### getContentAndDecode

**Utils::getContentAndDecode(string \$path, bool \$associative = true)** Returns the content of the file and decode it. It returns an associative array if the $associative argument is true, or an object if it is false.

### encodeAndWriteFile

**Utils::encodeAndWriteFile(string \$path, array $toEncode)** Encodes datas and writes these into the specified file.

> **_NOTICE :_**
> Datas written replaces the entire file. So if the file already exists with datas. The previous datas will be erased and replaced with the new ones.
>
> These last 2 static methods (Utils::getContentAndDecode(), Utils::encodeAndWriteFile() ) are not intended to be used outside of the methods concerned. They are used to recover and save databases.
