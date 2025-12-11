<?php

namespace LazarusPhp\OpenHandler\CoreFiles\Writers;

use App\System\Core\Functions;
use LazarusPhp\OpenHandler\CoreFiles\FileWriterCore;
use LazarusPhp\OpenHandler\Interfaces\WriterInterface;
use Exception;


class FileWriter extends FileWriterCore  implements WriterInterface
{

   public function __construct(string $filename)
   {
      // unset($this->jsonWriter->data); 
      $this->open($filename);
      parent::__construct($filename);

   }

   /**
    * @method open()
    * @param string $filename;
    * @return @method  $this->openFile($filename)
    * @description : Function is used to open File. 
    * @optional false;
    */
   public function open(string $filename)
   {
      return $this->openFile($filename);
   }

   /**
    * @method useSections()
    * @return @method $this->jsonWriter->useSections()
    * @description : Use to allocated Sections if file requires them in ini or env file
    * @optional true;
    */
   public function useSections()
   {
      $this->hasSections = true;
   }

   public function rewrite(bool $file=false)
   {
      $this->rewriteSection = (bool) true;
      if($file !== true)
      {
         $this->rewriteSection = true;
         return $this;
      }
      else
      {
         return $this;
      }
   }

   /**
    * @method set()
    * @param string $section
    * @param string|int $key
    * @param string|int $value
    * @return @method $this->jsonWriter->set($sections,$key,$value)
    * @description : Used to set data as an array ready for exporting to a file.
    * @optional false;
    */
   public function set(string $section, string|int $key, string|int $value = "")
   {
      // Sxript Sets but never overWriters unless specificed with rewrite Method;
            $data = ($this->getExtension($this->filename) === "json") ? json_encode($this->filename,true) : $this->loadFile($this->filename);
            $this->writeData = array_merge($data,$this->writeData);
            $sections = ($this->hasSections === true )? true : false;
            if($sections === true && !empty($value))
            {
               if(!array_key_exists($key,$data[$section]) || $this->rewriteSection === true){
                  $this->writeData[$section][$key] = true;
               }
            }
            else
            {  
               if(!array_key_exists($section,$data) || $this->rewriteSection === true){
                  $this->writeData[$section] = $key;
               }
            }

            if($this->rewriteFile === true){
               $this->rewriteSection = false;
            }
   }


   /**
    * @method get()
    * @param string $section
    * @param string|int $key
    * @param string|int $value
    * @return @method $this->jsonWriter->get($sections,$key,$value)
    * @description : Used to get data as an array ready for exporting to a file.
    * @optional false;
    */


   public function get(string $section="", string|int $key = "")
   {

      $data = $this->loadFile($this->filename);

      if(empty($section) && empty($key))
      {
         return $data;
      }
      
      if($this->hasSections === true && !empty($key))
      {
         foreach($data as $sec => $k)
         {
            if(!is_array($k))
            {
               continue;
            }

            foreach($k as $keys  => $v)
            {
               return $data[$keys][$v];
            }
         }
      }
      else
      {
         foreach($data as $sec => $value)
         {
            return $data[$sec];
         }
      }
   }
   /**
    * @method write()
    * @param string $filename
    * @param string|int|array $content
    * @param array $optional
    * @return @method $this->writeFile($sections,$key,$value)
    * @description : Used to Write to a file
    */
   public function write(string $filename, string|int|array $content, int $optional = 0)
   {
      return $this->writeFile($filename, $content, $optional);
   }


   /**
    * @method serializeData()
    * @description :  method is used set Serialize to true
    * @optional true
    * @return void
    */
   public function serializeData(): void
   {
      $this->serialize = true;
   }

   /**
    * @method unserializeData()
    * @description :  method is used set serialise to false
    * @optional true
    * @return void
    */
   public function unserializeData(): void
   {
      $this->serialize == false;
   }


   /**
    * @method remove()
    * @param string $section
    * @param string|int $key=""
    * @return @method $this->jsonWriter->remove($sections,$key,$value)
    * @description :  method is used set Serialize to true
    * @optional false
    */
   public function remove(string $section, string|int $key = "")
   {
      // Check Based on the files.
         $data = $this->loadFile($this->filename);
         if(isset($data[$section][$key]) && !empty($key))
         {
            if(!array_key_exists($key,$this->writeData[$section]))
            {
               $this->writeData = array_merge($data,$this->writeData);
            }

            unset($this->writeData[$section][$key]);
         }
         else
         {
            if(!array_key_exists($section,$this->writeData))
            {
                  $this->writeData = array_merge($data,$this->writeData);
                  // echo "We Will unset then section";
            }
            unset($this->writeData[$section]);
      }

   }


   /**
    * @method save()
    * @description :  used to finalise and save the data to a file and write it.
    * @optional false;
    */
   public function save()
   {
      // Validate OverWrite Rules

      $countWriteData = count($this->writeData);  
      $data = $this->loadFile($this->filename);
      $extension = $this->getExtension($this->filename);
      

      // $this->writeData = array_merge($data,$this->writeData);
      if($countWriteData > 0)
      {
      
      if ($extension === "env" || $extension === ".env") {
         return $this->WriteEnv($this->filename);
      }


      if ($extension === "txt") {
         $this->toTxt($this->filename);
      }

      if ($extension === "ini") {
        $this->toIni($this->filename);
      }
   }

   //    // CLear all and reset to default
      $this->writeData = [];
      $this->filename = "";
      // $this->jsonPath = "";
      $this->hasSections = false;
      // $this->preventOverWrite = [];
      $this->rewriteSection = false;
      $this->rewriteFile = false;
   }
   
   
}
