<?php
namespace LazarusPhp\OpenHandler\CoreFiles;

use App\System\Core\Functions;
use Exception;
use LazarusPhp\OpenHandler\CoreFiles\Traits\FileParser;
use LazarusPhp\OpenHandler\CoreFiles\Traits\Permissions;
use LazarusPhp\OpenHandler\CoreFiles\Traits\Structure;
use LazarusPhp\OpenHandler\CoreFiles\Traits\Blacklist;

class FileWriterCore
{
g
    use Structure;
    use FileParser;
    use Blacklist;
    use Permissions;
    // protected bool $hasSections = false;
    protected string $filename = "";
    protected bool $serialize = false;

    // Data From FIles.

    protected array $data = [];
    protected array $hasSections = [];
    protected array $rewritable = [];


    protected array $filePath = [];

    public function __construct($filename)
    {
        
        $this->bindFiles($filename);
        if($this->openFile($filename) === true){
            
            $this->loadParser($filename);

        }
        else
        {
            echo "filename could not be found";
        }
    }

    

    /**
     * @method openFile()
     * @param string $filename
     * @return @property $this->filename
     * @description Open file is an additional file used to open the file
     * @return void
     */
    protected function openFile($filename):bool
    {
        if($this->hasFile($filename)){
            $this->filename = (string) $this->filePath($filename);
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
     * Write file is a protected method used create execute the file
     * @method writeFile()
     * @param string $filename
     * @param  string|int|array $content
     * @param array $optional
     * @return void
     */
    protected function writeFile(string $filename,string|int|array $content,$flags=0):void
    {
            if(file_put_contents($filename,$content,$flags) === false)
            {
                throw new Exception("Cannot Create File $filename");
            }
    }



}