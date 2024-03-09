<?php

namespace Palepupet\DbJsonBased;

use Palepupet\DbJsonBased\exceptions\DbJsonBasedInvalidArgumentException;
use Palepupet\DbJsonBased\exceptions\DbJsonBasedRuntimeException;

class Utils
{

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
     * @param string $caseFunction The case function, ex: 'strtolower' | 'strtoupper'
     * @throws DbJsonBasedInvalidArgumentException
     * @return array
     */
    public static function harmonizeKeyCase(array $datas, string $caseFunction): array
    {
        if (empty($datas)) {
            throw new DbJsonBasedInvalidArgumentException("The datas cannot be empty.");
        }

        if (strlen($caseFunction) <= 0 || empty($caseFunction)) {
            throw new DbJsonBasedInvalidArgumentException("The case function cannot be empty.");
        }

        $keysUpperCase = array_map($caseFunction, array_keys($datas));
        $valuesLowerCase = $datas;
        $modifiedData = array_combine($keysUpperCase, $valuesLowerCase);

        return $modifiedData;
    }
}
