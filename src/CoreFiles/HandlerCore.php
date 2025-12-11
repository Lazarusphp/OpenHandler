<?php

namespace LazarusPhp\OpenHandler\CoreFiles;

use App\System\Core\Functions;
use Exception;
use BadMethodCallException;
use LazarusPhp\OpenHandler\ErrorHandler;
use LazarusPhp\OpenHandler\Interfaces\WriterInterface;
use LazarusPhp\OpenHandler\Traits\Blacklist;
use LazarusPhp\OpenHandler\Traits\Permissions;
use LazarusPhp\OpenHandler\Traits\Structure;
use LazarusPhp\OpenHandler\CoreFiles\Writers\FileWriter;
use ReflectionClass;

/**
 * @abstract class HandlerCore
 * Cannot be called statically or as a new Instantiation.
 * is required only for use with OpenHandler Handler class.
 */

abstract class HandlerCore
{
    use Blacklist;
    use Permissions;
    use Structure;
    
    protected array $restricted = [];
    private $method = __FUNCTION__;
    private $classname;
    protected WriterInterface $writerInterface;
    private $jsonWriter;
    


    public function __construct()
    {
    }

    public function classname()
    {
        return static::class;
    }
    /**
     * Require Access Token in order to Continue Prevent Calling methods directly;
     * 
     */
    /**
     * Detect if directory exists;
     * @property string $path
     * @return bool
     */


    /**
     * @method __call
     * Detects if a dynamic method has been created and rejects it.
     */
    public function __call($name, $arguments)
    {
        if (method_exists(self::class, $name)) {
            echo "Class Exists";
        }

        trigger_error("Method : $name cannot be found or does not exist", E_USER_WARNING);
    }


    

    /**
     * Detect if directory is writable
     * @property string $path
     * @return bool
     */
    


    // Generative Methods.

    /**
     * @method generateDirectory
     * @property string $path
     * @property int $mode
     * @property boot $recursive
     */
    protected function generateDirectory(string $path, int $mode = 0755, bool $recursive = true)
    {

        if ($this->loadMethod(__FUNCTION__) === true) {
            // Detect if  directory Exists
            $path = (!empty($path)) ? $this->filePath($path) : "";
            if ($this->validMode($mode) === true) {
                // Create Folder if it doesnt exist
                if ($this->hasDirectory($path) === false) {
                    $oldUmask = umask(0);
                    if (!mkdir($path, $mode, $recursive) && !$this->hasDirectory($path)) {
                        umask($oldUmask);
                        throw new \RuntimeException("Failed to create directory: {$path}");
                    }
                    umask($oldUmask);
                    chmod($path, $mode);
                }
            } else {
                echo "Mode invalid";
            }
        }
    }

    protected function generatePrefix(string $path, callable $handler, array $middleware = [])
    {
        // $method = __FUNCTION__;
        // $this->setRestrict();
        if ($this->loadMethod(__FUNCTION__) === true) {
            if ($this->prefix === "") {
                $this->prefix = $path;
            }
            // Add MiddleWare Option here

            if (is_callable($handler)) {
                $class = new Handler();
                $handler($class, $path);
                $this->prefix = "";
            }
            // Reset Prefix to start a new oneself
            return null;
        }
    }

    protected function generateList(string $path, bool $recursive = true, $files = true)
    {
        if ($this->loadMethod(__FUNCTION__)) {
            // Avoid double prefixing
            if (!empty($this->directory) && strpos($path, $this->directory) !== 0) {
                $path = rtrim($this->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
            }


            if ($this->hasDirectory($path) === false) {
                return ["folders" => [], "files" => []];
            }

            $result = ["folders" => [], "files" => []];

            $items = @scandir($path); // use @ to safely handle unreadable dirs

            foreach ($items ?: [] as $item) {

                if ($this->withDots($item))  continue;

                $ds = DIRECTORY_SEPARATOR;
                $fullpath = (string) rtrim($path, $ds) . $ds . ltrim($item, $ds);

                if ($files === true) {
                    if ($this->hasFile($fullpath) === true) {
                        $result["files"][] = $fullpath;
                    }
                }

                if ($recursive === true) {
                    if ($this->hasDirectory($fullpath) === true) {
                        $result["folders"][] = $fullpath;

                        // Check if Request is set as recursive;

                        // Recursively list contents of subdirectories
                        $subdir = $this->generateList($fullpath, $recursive);

                        // Ensure recursion always returns an array with both keys
                        $subFolders = $subdir["folders"] ?? [];
                        $subFiles = $subdir["files"] ?? [];

                        $result["folders"] = array_merge($result["folders"], $subFolders);
                        ($files === true) ? $result["files"] = array_merge($result["files"], $subFiles) : false;
                    }
                }
            }

            // Return result;
            return $result;
        }
    }


    private function detectSections(bool $sections)
    {
        $validSections = [true,false];
        return (in_array($sections,$validSections)) ? true : false ;
       
    }



    protected function generateFile(string $filename, callable $handler,bool $sections=false)
    {

        $filename = (string) $this->filePath($filename);
        // Make sure Sections only allows true or false


        if($this->detectSections($sections) === false)
        {
            trigger_error("Error Occurred : Sections Must be true or false");
        }

        // This WIll be changes to the current Format string $filename,$handler,$classname

        
        if($this->loadMethod(__FUNCTION__)){
        
        if(empty($classname)){
        }
        // Detect Supported Data
        $class = FileWriter::class;
        $this->writerInterface = new $class($filename,$sections);
        $isClass = (class_exists($class)) ? true : false;

        if(is_callable($handler) && $isClass === true)
        {
            $handler($this->writerInterface,$this);
        }
            if($this->writerInterface)
            {
                $this->writerInterface->save();
            }
        }
    }

    protected function generateDelete(string $path)
    {

        if ($this->loadMethod(__FUNCTION__)) {
            $path = (string) $this->filePath($path);

            if(is_file($path))
            {
                unlink($path);
            }
            else
            {
                if ($this->hasFile($path)) {
                    return unlink($path);
                } elseif ($this->hasDirectory($path ?? '')) {
                    $items = $this->generateList($path, true);
                    foreach ($items['files'] as $file) {
                        @unlink($file);
                    }
                    foreach (array_reverse($items['folders']) as $folder) {
                        @rmdir($folder);
                    }
                    return @rmdir($path);
                }
                return false;
            }
        }
    }

    protected function generateUpload(string $path, string $name)
    {

        if ($this->loadMethod(__FUNCTION__)) {
            $path = (string) $this->filePath($path);

            if ($this->hasDirectory($path)) {
                $ds = DIRECTORY_SEPARATOR;
                if (isset($_FILES[$name])) {
                    $files = $_FILES[$name];
                    // Check if name is in array
                    if (is_array($files["name"])) {
                        foreach ($files["name"] as $index => $name) {
                            if (!isset($files["tmp_name"][$index])) {
                                continue;
                            }

                            $tmp_name = $files["tmp_name"][$index];
                            $safename = basename($name);
                            $destination = $path . $ds . uniqid('img_', true) . "_$safename";
                            if (move_uploaded_file($tmp_name, $destination)) {
                                echo "Uploaded files";
                            } else {
                                echo "failed to upload";
                            }
                        }
                    }
                }
            } else {
                echo "Directory Does not exist";
            }
        }
    }
}
