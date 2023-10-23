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

Then you have to create the database's file. Use **createDb(DbJsonBasedStructureInterface)** to do this. You must provide a structure for your datas with a **DbJsonBasedStructureInterface** as argument. **DbJsonBasedStructure** class implements it with all needed methods to check them.

```php
$jsonDb->createDb(new DbJsonBasedStructure(string tableName, array columns));
```

To make it short, you can do that :

```php
$jsonDb = new DbJsonBased("customersDb");

$datasStructure = new DbJsonBasedStructure("Customers", [
    "first_name" => "string",
    "last_name" => "string",
    "actif" => "boolean",
    ...
]);

$jsonDb->createDb($datasStructure);
```

All accepted types are **boolean**, **float** , **int**, and **string**. You can use the class constants provided if you wish

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
            "ACTIF":"int",
            "ID":"int"
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
            ["ACTIF"] => string(3) "int"
            ["ID"] => string(3) "int"
        }
        ["VALUES"] => array(0) {}
        ["ID"] => NULL
  }
}
```

> **_NOTICE :_**
> However if you just want to retrieve an element, or act on it, like the TABLENAME, COLUMNS, VALUES, ID some methods, explained below, are more suitables.

## GET methods

**getFullName()** Return the name of the database's file, with its extension.

**getName()** Return the name of the database's file, without its extension.

**getPath()** Return the name of the database's file, with its entire path and extension.

> **_NOTICE :_**
> Even if the path is returned, it does not confirm that the database exists. At least, not until you used the createDb() method.
If your goal is to find out if the file physically exists, please use the static method **isFileExist(string $path)**

## STATIC methods

**Utils::isFileExist(string $path)** Return a boolean if the database exists and the .json file is created.

**Utils::getContentAndDecode(string \$path, bool \$associative = true)** Return the content of the file and decode it. It return an associative array if the $associative argument is true, or an object if it is false.

**Utils::encodeAndWriteFile(string \$path, array $toEncode)** Encodes datas and write these into the specified file.

> **_NOTICE :_**
> Datas written replaces the entire file. So if the file already exists with datas. The previous datas will be erased and replaced with the new ones.
>
> These last 2 static methods (Utils::getContentAndDecode(), Utils::encodeAndWriteFile() ) are not intended to be used outside of the methods concerned. They are used to recover and save databases.
