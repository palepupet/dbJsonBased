<?php

namespace Palepupet\DbJsonBased;

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
}
