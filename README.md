# README

PHP library to use JSON file as a database. Very practical when you don't want to take out the heavy artillery or when you want to set up data persistence quickly.

## Installation

```sh
composer require palepupet/dbjsonbased
```

## Use

**Init :**

```php
$jsonDb = new DbJsonBased("path_of_the_db_without_json_extension");
```

Then you have to create the database's file.

```php
$jsonDb->createDb();
```

## GET methods

**getName()** Return the path of the database's name, if it is created.
> **_NOTICE :_**
> Even if the path is returned, it does not confirm that the database exists. To do this, please use the static method **isFileExist()**

## STATIC methods

**isFileExist($path)** Return a boolean if the database exists and the json's file is created
