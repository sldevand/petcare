<?php

namespace Framework\Service;

/**
 * Class FileManager
 * @package Framework\Service
 */
class FileManager
{
    const PERMISSIONS_DIRECTORY = 0755;
    const PERMISSIONS_FILE = 0644;
    const PERMISSIONS_FULL = 0777;

    /**
     * @param $filename
     * @param bool $force
     * @return bool
     * @throws \Exception
     */
    public function changeMode(string $filename, bool $force = false): bool
    {
        if (!is_dir($filename) && !is_file($filename)) {
            throw new \Exception("$filename does not exist !");
        }

        $mode = self::PERMISSIONS_FILE;

        if (is_dir($filename)) {
            $mode = self::PERMISSIONS_DIRECTORY;
        }

        if ($force) {
            $mode = self::PERMISSIONS_FULL;
        }

        if (!chmod($filename, $mode)) {
            throw new \Exception("chmod on $filename failed !");
        }

        return true;
    }


    /**
     * @param string $dir
     * @param bool $force
     * @throws \Exception
     */
    public function makeDirectory(string $dir, bool $force = false)
    {
        if (file_exists($dir)) {
            return;
        }

        $mode = self::PERMISSIONS_DIRECTORY;
        if ($force) {
            $mode = self::PERMISSIONS_FULL;
        }

        if (!mkdir($dir, $mode, true)) {
            throw new \Exception('Could not mkdir : ' . $dir);
        }

        chmod($dir, self::PERMISSIONS_DIRECTORY);
    }

    /**
     * @param string $filename
     * @param string $data
     * @return bool
     * @throws \Exception
     */
    public function save(string $filename, string $data): bool
    {
        if (file_put_contents($filename, $data) === false) {
            throw new \Exception('Could not put contents in file : ' . $filename);
        }

        return true;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getDirName(string $path): string
    {
        return pathinfo($path)['dirname'];
    }
}
