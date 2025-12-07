<?php
namespace LazarusPhp\OpenHandler\Traits;

trait Permissions
{
    protected function writable(string $path)
    {
        if ($this->loadMethod(__FUNCTION__) === true) {
            return is_writable($path) ? true : false;
        }
    }

    protected  function readable(string $path)
    {
        if ($this->loadMethod(__FUNCTION__)) {
            return is_readable($path) ? true : false;
        }
    }

    protected function setPermissions(string $path,int $permissions=0755)
    {
        echo "Setting permissions for $path";
        $path = dirname($path);
        // echo $path
        if(is_dir($path))
        {
            if(!chmod($path,$permissions))
            {
                echo "failed";
            }
        }

        if(is_file($path) && file_exists($path))
        {
            if(!chmod($path,$permissions))
            {
                echo "failed";
            }
        }
    }

    public function getFilePerms($filename)
    {
        $perms =stat(fileperms($filename));
        return $perms;
    }
    
    // protected function apacheUid($path)
    // {
    //     // Build full path using configured directory if provided
    //     $fullPath = ($this->$directory === "") ? $path : rtrim(self::$directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);

    //     // Ensure file exists and is readable
    //     if (!file_exists($fullPath)) {
    //         return null;
    //     }

    //     // Suppress warnings from fileowner for unexpected permission issues
    //     $owner = @fileowner($fullPath);
    //     if ($owner === false || $owner === null) {
    //         return null;
    //     }

    //     // Prefer posix_getpwuid when available, otherwise return owner id object
    //     if (function_exists('posix_getpwuid')) {
    //         $stats = posix_getpwuid($owner);
    //         if ($stats === false || $stats === null) {
    //             return null;
    //         }
    //         return (object) $stats;
    //     }

    //     return (object) ['uid' => $owner];
    // }
}