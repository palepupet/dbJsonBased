<?php

namespace Palepupet\DbJsonBased;

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
     * @param bool $associative Get an associative array or not (true by default)
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
}
