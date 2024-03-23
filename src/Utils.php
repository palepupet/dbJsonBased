<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;

class Utils
{
    public const STRTOUPPER = "strtoupper";
    public const STRTOLOWER = "strtolower";

    /**
     * isFileExist
     * 
     * Allows you to know if a file exists, and therefore by extension that the database is created too
     *
     * @param string $path Path of the file, with extension .json
     * @return bool
     */
    public static function isFileExist(string $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        return true;
    }

    /**
     * getContentAndDecode
     *
     * Check if the file exists and return its content.
     * 
     * @param string $path Path of the file
     * @param bool $associative=true Get an associative array or not (true by default)
     * @throws DbJsonBasedRuntimeException
     * @return array|object Content of the decoded file
     */
    public static function getContentAndDecode(string $path, bool $associative = true): array|object
    {
        if (!self::isFileExist($path)) {
            throw new DbJsonBasedRuntimeException("The Database '$path' cannot be find. Does the Database exists and the path is correct ?");
        }

        $content = json_decode(file_get_contents($path), $associative);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DbJsonBasedRuntimeException("The Database '$path' is not a valid JSON file.");
        }

        return $content;
    }

    /**
     * encodeAndWriteFile
     *
     * Encodes datas and put contents into the file
     * 
     * @param string $path Path of the file
     * @param array $toEncode Array to encode
     * @throws DbJsonBasedRuntimeException
     * @return void
     */
    public static function encodeAndWriteFile(string $path, array $toEncode): void
    {
        $encodeDb = json_encode($toEncode);

        $result = file_put_contents($path, $encodeDb);

        if ($result === false) {
            throw new DbJsonBasedRuntimeException("Error writing the file '$path'");
        }
    }

    /**
     * harmonizeKeyCase
     * 
     * Harmonize all keys case
     *
     * @param array $datas Simple datas array containing the keys to be harmonized
     * @param string $caseFunction The case function, ex: 'strtolower' | 'strtoupper' You can use class constants provided : **STRTOLOWER | STRTOUPPER**
     * @param bool $simpleArray=false Using the method on an associative array or on a simple array
     * @throws DbJsonBasedInvalidArgumentException
     * @return array
     */
    public static function harmonizeKeyCase(array $datas, string $caseFunction, bool $simpleArray = false): array
    {
        $allowedCaseFunctions = [
            self::STRTOUPPER,
            self::STRTOLOWER
        ];

        if (empty($datas)) {
            throw new DbJsonBasedInvalidArgumentException("The datas cannot be empty.");
        }

        if (strlen($caseFunction) <= 0 || empty($caseFunction)) {
            throw new DbJsonBasedInvalidArgumentException("The case function cannot be empty.");
        }

        if (!in_array($caseFunction, $allowedCaseFunctions)) {
            throw new DbJsonBasedInvalidArgumentException("The given case function is not allowed. You have to choose between : " . implode(",", $allowedCaseFunctions));
        }

        if ($simpleArray) {
            return array_map($caseFunction, $datas);
        }

        $keysUpperCase = array_map($caseFunction, array_keys($datas));
        $valuesLowerCase = $datas;
        $modifiedData = array_combine($keysUpperCase, $valuesLowerCase);

        return $modifiedData;
    }

    /**
     * updateKeysArray
     * 
     * Renames keys into an array. Updates the keys of an array recursively.
     *
     * @param array $arrayToModified The array containing the keys to update
     * @param array $renamedKey the array containing the new key which replaces the old one [old_key => new_key]
     * @return void
     */
    public static function updateKeysArray(array &$arrayToModified, array $renamedKey)
    {
        foreach ($arrayToModified as &$value) {
            // If the element is an array, recursively call the function
            if (is_array($value)) {
                self::updateKeysArray($value, $renamedKey);
            } else {
                foreach ($renamedKey as $oldKey => $newKey) {
                    // If the old key exists in the array to modify
                    if (array_key_exists($oldKey, $arrayToModified)) {
                        // Create the new key with the actual value and unset the old key
                        $arrayToModified[strtoupper($newKey)] = $arrayToModified[$oldKey];
                        unset($arrayToModified[$oldKey]);
                    }
                }
            }
        }
    }
}
