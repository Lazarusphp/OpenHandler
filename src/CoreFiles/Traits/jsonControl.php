<?php

namespace LazarusPhp\OpenHandler\CoreFiles\Traits;

use App\System\Core\Functions;
use Exception;
use LazarusPhp\OpenHandler\Traits\Structure;

trait jsonControl
{

   use Structure;

   protected  $jsonPath = "";
   protected bool $hasSections = false;
   private $jsonFlags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;


   public function setJsonPath(string $path)
   {
      $this->jsonPath = $path;
   }

   public function parseFile(string $filename = "")
   {
      $filename  = $this->filename ?? $filename;
      $filename = (string) $this->getFullpath($filename);
      if ($this->hasFile($filename)) {
         $fetch = file_get_contents($filename, true);
         $decoded = json_decode($fetch, true);
         if($decoded !== false)
         {
            return $decoded;
         }
      }
   }

   public function getFullpath(string $filename = "")
   {
      $filename  = $this->filename ?? $filename;
      $filename = $this->tojsonFile($filename);
      $filename = $this->jsonPath . $filename;
      $filename = $this->filePath($filename);
      return $filename;
   }

  

   private function tojsonFile($filename)
   {

      return str_replace($this->getExtension($filename), "json", $filename);
   }

   protected function writeJsonFile($filename)
   {
      if($this->getExtension($filename) === "env" && $this->hasSections === true)
      {
         trigger_error("Cannot use Sections with env files.");
      }
      
      $fullpath = (string) $this->getFullpath($filename);
      // if (!$this->hasFile($fullpath)) {
      //    $encoded = json_encode("{}", $this->jsonFlags);
      //    file_put_contents($fullpath, $encoded);
      // }
      

      
      // if ($this->hasSections === true) {
         foreach($this->fileData as $section => $k)
         {
            if(!is_array($k))
            {
               continue;
            }

            foreach($k as $key => $value)
            {
               if($this->hasSections === true)
               {
                  if (isset($this->data[$section])) {
                     if (!isset($this->data[$section][$key])) {
                        $this->data[$section][$key] = $value;
                     }
                  }
               }
               else
               {
                  if (isset($this->data[$section])) {
                  $this->data[$section] = $value;
                  }
               }
            }
         }

      //    foreach ($data as $section => $k) {
      //          if (!is_array($k)) {
      //             continue; // skip non-array sections
      //          }
      //       foreach ($k as $key => $value) {
      //          if (isset($this->data[$section])) {
      //             if (!isset($this->data[$section][$key])) {
      //                $this->data[$section][$key] = $value;
      //             }
      //          }
      //       }
      //    }
      // } else {
      //    foreach ($data as $section => $k) {
      //        if (!is_array($k)) {
      //          continue; // skip non-array sections
      //       }

      //       foreach ($k as $key => $value) {
      //          if (isset($this->data[$section])) {
      //             $this->data[$section] = $value;
      //          }
      //       }
      //    }
      // }

      
      // Merge Data From the Json FIle
      // var_dump($data);
      $data = array_merge($this->fileData, $this->data);
      // Encode and write new Data back to the file.
      $encoded = json_encode($data,$this->jsonFlags,512);
      file_put_contents($fullpath, $encoded);
   }
}
