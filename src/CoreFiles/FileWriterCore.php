<?php
namespace LazarusPhp\OpenHandler\CoreFiles;

use App\System\Core\Functions;
use Exception;
use LazarusPhp\OpenHandler\CoreFiles\Traits\EnvWriter;
use LazarusPhp\OpenHandler\CoreFiles\Traits\jsonControl;
use LazarusPhp\OpenHandler\CoreFiles\Writers\JsonWriter;
use LazarusPhp\OpenHandler\Traits\Permissions;
use LazarusPhp\OpenHandler\Traits\Structure;

class FileWriterCore
{

    use Structure;
    use Permissions;
    use jsonControl;
    protected bool $hasSections = false;
    protected string $filename = "";
    protected bool $serialize = false;
    protected array $readData = [];
    protected array $writeData = [];
    protected array $filePath = [];
    protected bool $rewriteSection = false;
    protected bool $rewriteFile = false;

    public function __construct($filename)
    {
        $this->preCreateFile($filename);
    }

    

    /**
     * @method openFile()
     * @param string $filename
     * @return @property $this->filename
     * @description Open file is an additional file used to open the file
     * @return void
     */
    protected function openFile($filename):void
    {
        $this->filename = (string) $this->filePath($filename);
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

    

    // // Export to files.

    // // Env Createor

    // /**
    //  * @method toEnv()
    //  * @param string $filenane
    //  * @return void
    //  */
    protected function WriteEnv()
    {
        $output = "";
        foreach($this->writeData as $section => $value)
        {
            $output .= "$section='".$value."'" . PHP_EOL;
        }
        return $this->writeFile($this->filename,$output);
    }


    private function preCreateFile($filename)
    {
        $supported = ["ini","txt","env"];
        $extention = $this->getExtension($filename);

        if(!in_array($extention,$supported))
        {
            trigger_error("Unsupported FIle Type");
            exit();
        }

        if(!$this->hasFile($filename))
        {
            $this->writeFile($filename,"");
        }
    }

    private function writeJson($filename)
    {
        $encoded = json_encode($this->writeData,$this->jsonFlags);
        return file_put_contents($filename,$encoded);
    }



    // /**
    //  * @method toTxt()
    //  * @param string $filename
    //  * @return void
    //  */
    protected function toTxt(string $filename):void
    {
        // create new Emoty $output
        $output = "";
            foreach($this->writeData as $section => $k)
            {
                if($this->hasSections === true)
                {
                
                    // Loop here 
                    foreach($k as $key => $value)
                    {
                          $output .= "[$section] : $key = $value" . PHP_EOL;   
                    }
                }
                else
                {
                  $output .= "[$section] = $value" . PHP_EOL;  
                }
            }
            $this->writeFile($filename,$output);
    }
    

    // Validate type

    private function validateType($value)
    {
        return (is_numeric($value) ? $value : '"' . addslashes($value) . '"');
    }


    /**
     * Ini Generator
     * @method toIni()
     * @param string $filename;
     * @return void;
     */
    


    // Write Files

    // To Ini/Env

    protected function toIni(string $filename):void
    {
        $output = "";

    foreach($this->writeData as $section => $k)
    {
        
        if($this->hasSections === true)
        {
            $output .= "[$section]" . PHP_EOL;
            foreach($k as $key => $value)
            {
                $output .=  "$key =". $this->validateType($value)   . PHP_EOL; 
            }
        }
        else
        {
            
            $output .=  "$section =" . $k . PHP_EOL; 
            
        }
    }

        $this->writeFile($filename,$output);
    }

    // to text

    //to json


    // Read Files


    protected function loadFile($filename)
    {
        if(!$this->hasFile($filename))
        {
            trigger_error("File $filename cannot be found");
            // return false;
        }

        $extention = $this->getExtension($filename);

        if(in_array($extention,["ini","env"]))
        {
            return $this->parseFile($filename);
        }

        if(!in_array($extention,["json"]))
        {
            return $this->parseFile($filename);
        }

        if(!in_array($extention,["txt"]))
        {
            return $this->readFile($filename);
        }

        
    }


    private function parseFile($filename)
    {
        $section = ($this->hasSections === true) ? true : false;
        return parse_ini_file($filename,$section);
    }


    private function readFile($filename)
    {
        return file_get_contents($filename,true);
    }

    private function parseJson($filename)
    {
        $filename = file_get_contents($filename);
        return json_decode($filename,true);
    }


}