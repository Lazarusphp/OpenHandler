<?php
namespace LazarusPhp\OpenHandler\CoreFiles\Traits;
use ReflectionClass;
use Reflection;
use Exception;

trait Structure
{
    
    protected $directory = "";
    protected $prefix = "";

    /**
     * @method hasExtention
     * @param string $filename
     * @param string $extension
     * @return bool;
     */

    /**
     * Detect if there is An invalid class called
     */
    

    
    protected static function reflection($classname)
    {
        return new ReflectionClass($classname);
    }




    protected function hasExtension(string $filename, string $extension):bool
    {
        $extention = $this->getExtension($filename);
        return ($extention === $extension) ? true : false;
    }




    // Helper methods 

    /**
     * @method hasDirectory
     * @property string $path
     * @return void
     * Helper function to detect if a directory exists.
     */
    protected function hasDirectory(string $path)
    {
        return is_dir($path) ? true : false;
    }

    //    Detect if file exists return bool

    /**
     * @method hasFile
     * Detect if is a file
     * @property string $path;
     * @return bool 
     */
    protected function hasFile(string $path)
    {
          return (string) (is_file($path)) ? true : false;
        
    }

    /**
     * Detect if file exists
     * @property string $path;
     * @return bool
     */
    protected function fileExists(string $path)
    {
           return (file_exists($path)) ? true : false;
        
    }

    /**
     * @method hasDirectory
     * @property string $path
     * @method $this->whitelist() does a check for whitelisted values set within handler.
     * @return void
     * Helper function to detect if a directory exists.
     */
    protected function filePath(string $directory)
    {
            $root = $this->directory;
            $prefix = $this->prefix ?? "";
            $directory = $directory;

            return (string) $root . $prefix . $directory;
        
    }

    /**
     * @method validMode
     * @property int $mode
     * Detrermines if the correct mode for directory creation is valid
     */
    protected function validMode(int $mode)
    {
            $modes = [0600, 0644, 0664, 0700, 0755, 0777];
            if (in_array($mode, $modes)) {
                return true;
            } else {
                return false;
            }
    }



    /**
     *  Detect if the Structure contains parent directorys
     *  @property string $path
     *  @return bool
     */
    protected function withDots($path)
    {
           return ($path === "." || $path === "..") ? true : false;
    }

    protected function getExtension(string $filename)
    {
        $extension = pathinfo($filename)["extension"];
        return $extension;
    }

    protected function whitelistExtention(string $extension,array $whitelist)
    {
        if(!in_array($extension,$whitelist))
        {
            throw new Exception("Invalid Whitelist");
        }
    }
}