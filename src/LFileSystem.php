<?php

namespace IconicCodes\LightUtils;

class LFileSystem {

    public static function getFileExtension($file) {
        $file = strtolower($file);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        return $ext;
    }

    public static function getFileName($file) {
        $file = strtolower($file);
        $ext = pathinfo($file, PATHINFO_FILENAME);
        return $ext;
    }

    public static function isDirectory($path) {
        return is_dir($path);
    }

    public static function isFile($path) {
        return is_file($path);
    }

    public static function isWritable($path) {
        return is_writable($path);
    }

    public static function isReadable($path) {
        return is_readable($path);
    }

    public static function makeFolders($path) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public static function makeFolderForFilePath($path) {
        $path = dirname($path);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public static function getFileSize($path) {
        return filesize($path);
    }

    public static function getMime($path) {
        return mime_content_type($path);
    }

    public static function move($source, $destination, $create_folders = true, $overwrite = false) {
        if ($create_folders) {
            self::makeFolderForFilePath($destination);
        }
        if ($overwrite) {
            if (file_exists($destination)) {
                unlink($destination);
            }
        }
        return rename($source, $destination);
    }

    public static function copy($source, $destination, $create_folders = true, $overwrite = false) {
        if ($create_folders) {
            self::makeFolderForFilePath($destination);
        }
        if ($overwrite) {
            if (file_exists($destination)) {
                unlink($destination);
            }
        }
        return copy($source, $destination);
    }

    public static function delete($path) {
        if (is_dir($path)) {
            return self::deleteDirectory($path);
        } else {
            return unlink($path);
        }
    }

    public static function deleteDirectory($path) {
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        if (!is_dir($path)) {
            return false;
        }
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        return rmdir($path);
    }

    public static function getFileList($path, $recursive = false, $filter = null) {
        $files = array();
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        if (!is_dir($path)) {
            return $files;
        }
        $dir = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        if ($recursive) {
            $dir = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        }
        foreach ($dir as $file) {
            if ($file->isDir()) {
                continue;
            }
            if ($filter !== null) {
                if (preg_match($filter, $file->getRealPath())) {
                    $files[] = $file->getRealPath();
                }
            } else {
                $files[] = $file->getRealPath();
            }
        }
        return $files;
    }

    public static function getDirectoryList($path, $recursive = false, $filter = null) {
        $files = array();
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        if (!is_dir($path)) {
            return $files;
        }
        $dir = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        if ($recursive) {
            $dir = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        }
        foreach ($dir as $file) {
            if ($file->isFile()) {
                continue;
            }
            if ($filter !== null) {
                if (preg_match($filter, $file->getRealPath())) {
                    $files[] = $file->getRealPath();
                }
            } else {
                $files[] = $file->getRealPath();
            }
        }
        return $files;
    }

    public static function listFileCreatedBetween($path, $start, $end) {
        $files = array();
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        if (!is_dir($path)) {
            return $files;
        }
        $dir = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($dir as $file) {
            if ($file->isDir()) {
                continue;
            }
            $created = $file->getCTime();
            if ($created >= $start && $created <= $end) {
                $files[] = $file->getRealPath();
            }
        }
        return $files;
    }

    public static function listFileModifiedBetween($path, $start, $end) {
        $files = array();
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        if (!is_dir($path)) {
            return $files;
        }
        $dir = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($dir as $file) {
            if ($file->isDir()) {
                continue;
            }
            $modified = $file->getMTime();
            if ($modified >= $start && $modified <= $end) {
                $files[] = $file->getRealPath();
            }
        }
        return $files;
    }

    public static function getFileDifferences($path1, $path2) {
        $files1 = self::getFileList($path1);
        $files2 = self::getFileList($path2);
        $diff = array();
        foreach ($files1 as $file1) {
            $file2 = str_replace($path1, $path2, $file1);
            if (!in_array($file2, $files2)) {
                $diff[] = $file1;
            }
        }
        return $diff;
    }

    public static  function getDifferenesInFiles($file1, $file2) {
        $diff = array();
        $lines1 = file($file1);
        $lines2 = file($file2);
        $line_count = max(count($lines1), count($lines2));
        for ($i = 0; $i < $line_count; $i++) {
            if (isset($lines1[$i]) && isset($lines2[$i])) {
                if ($lines1[$i] != $lines2[$i]) {
                    $diff[] = array($i + 1, $lines1[$i], $lines2[$i]);
                }
            } else if (isset($lines1[$i])) {
                $diff[] = array($i + 1, $lines1[$i], '');
            } else if (isset($lines2[$i])) {
                $diff[] = array($i + 1, '', $lines2[$i]);
            }
        }
        return $diff;
    }
}