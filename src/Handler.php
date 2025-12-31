<?php

namespace LazarusPhp\OpenHandler;

use App\System\Core\Functions;
use Exception;
use LazarusPhp\OpenHandler\Interfaces\HandlerInterface;
use LazarusPhp\OpenHandler\Interfaces\ImageInterface;
use LazarusPhp\OpenHandler\CoreFiles\Traits\Blacklist;
use LazarusPhp\OpenHandler\CoreFiles\Traits\FileParser;
use LazarusPhp\OpenHandler\CoreFiles\Traits\Permissions;
use LazarusPhp\OpenHandler\CoreFiles\Traits\Structure;
use LazarusPhp\OpenHandler\FileWriter;
use LogicException;

class Handler implements HandlerInterface
{

    use Blacklist;
    use Permissions;
    use Structure;
    use FileParser;

    private $method = __FUNCTION__;
    private $classname;
    private $fileWriter;
    /**
     * @propertyy array $allowedHelpers
     * Lists allowed Helpers
     */

    /**
     * @property array $allowedMethods
     * Sets List of allowed  values for array.
     */

    protected array $restricted = [];

    /**
     * @method __construct()
     * Used to set Directory on instantiation if a value is presented.
     * @param $directory
     * 
     * */
    public function __construct(string $directory = "")
    {
        // Empty Constructor 
        if ($directory !== "") {
            $this->setDirectory($directory);
        }

        $this->fileWriter = FileWriter::class;

    }


    /**
     * @method setDirectory
     * @param string $directory
     * can be used seperatly to override the directory set in constructor.
     * sets @property self::$directory  if directory exists.
     */
    public function setDirectory(string $directory = "./")
    {
        if ($this->hasDirectory($directory)) {
            // check the Directory is Writable
            if ($this->writable($directory)) {
                // Set the variable and Create the Directory
                $this->directory = $directory;
                return true;
            } else {
                trigger_error("Directory is Not Writable");
                return false;
            }
        } else {
            // Trigger Error
            trigger_error("$directory cannot be found");
            return false;
        }
    }

    /**
     * Add a directory at the specified path.
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function directory(string $path, int $mode = 0755, bool $recursive = true)
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



    /**
     * Delete a file or directory at the specified path.
     * @param string $path
     * @return bool
     */

    public function delete(string $path)
    {
        if ($this->loadMethod(__FUNCTION__)) {
            $path = (string) $this->filePath($path);

                if (is_file($path) && $this->hasFile($path) === true) {
                    unlink($path);
                } else {
                    if ($this->hasFile($path)) {
                        return unlink($path);
                    } elseif ($this->hasDirectory($path ?? '')) {
                        $items = $this->list($path, true);
                        foreach ($items['files'] as $file) {
                            unlink($file);
                        }
                        foreach (array_reverse($items['folders']) as $folder) {
                            rmdir($folder);
                        }
                        
                        // Unset Data Properties
                        if(class_exists($this->fileWriter))
                        {
                            $class = new $this->fileWriter($path,[],1);
                            $class->deleteData();
                        }

                        return rmdir($path);
                    }

                    return false;
                }

            }

            // Unset Data Paths

   
    }

    /**
     * Add a file with data at the specified filename.
     * @param string $filename
     * @param mixed $data
     * @return bool|int
     */

    // Note Change Sections to array and leave empty rename to $options
    public function file(string $filename, callable $handler, ?array $flags=[])
    {
    
        $options = count($flags) ? $this->supportedFlags($flags) : $flags;

        $filename = (string) $this->filePath($filename);
        // Make sure Sections only allows true or false

        // This WIll be changes to the current Format string $filename,$handler,$classname


        if ($this->loadMethod(__FUNCTION__)) {

            if (empty($classname)) {
            }
            // Detect Supported Data
            $isClass = (class_exists($this->fileWriter)) ? true : false;
            $class = new $this->fileWriter($filename, $options);

            if (is_callable($handler) && $isClass === true) {
                $handler($class, $this);
            }
            if ($class) {
                $class->save();
            }
        }
    }


    /**
     * List all directories and files
     * @property string $path
     * @property bool $recursive
     * @return array
     */


    public function list(string $path, $recursive = true, $files = true)
    {
        $path = $this->prefix !== "" ? $this->filePath($path) : $path;
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
                        $subdir = $this->list($fullpath, $recursive);

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


    public function prefix(string $path, callable $handler)
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


    /**
     * @method upload
     * @param string $path
     * @param callable $image
     */
    public function upload(string $path, string $name)
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
