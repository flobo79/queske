<?php

class Files {

    /**
     * Function used to delete a folder.
     *
     * @param $path - path to folder
     * @return bool result of deletion
     */
    static public function deleteFolder($path) {
        if (is_dir($path)) {
            if (version_compare(PHP_VERSION, '5.0.0') < 0) {
                $entries = array();
                if ($handle = opendir($path)) {
                    while (false !== ($file = readdir($handle))) $entries[] = $file;
                    closedir($handle);
                }
            }else{
                $entries = scandir($path);
                if ($entries === false) $entries = array();
            }

            foreach ($entries as $entry) {
                if ($entry != '.' && $entry != '..') {
                    self::deleteFolder($path.'/'.$entry);
                }
            }

            return @rmdir($path) ? true : false;
        }else{
            return @unlink($path) ? true : false;
        }
    }

    static function copyr($source, $dest) {
        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            if ($dest !== "$source/$entry") {
                self::copyr("$source/$entry", "$dest/$entry");
            }
        }

        // Clean up
        $dir->close();
        return true;
    }
}