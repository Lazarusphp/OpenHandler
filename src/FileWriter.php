<?php

namespace LazarusPhp\OpenHandler;

use App\System\Core\Functions;
use LazarusPhp\OpenHandler\CoreFiles\FileWriterCore;
use LazarusPhp\OpenHandler\Interfaces\WriterInterface;
use Exception;


class FileWriter extends FileWriterCore
{

   // Note
   // Rename $sections to $options change to array $options
   public function __construct(string $filename, array $options = [], int $mode = 0)
   {

      // Generate new array for key
      if (!in_array($filename, $this->options)) {
         $this->options[$filename] = [];
      }

      // Loop $options and set values;
      foreach ($options as $key => $value) {
         // Set/ Update Value.
         $this->options[$filename][$key] = $value;
      }

      // Launch FileWriteCore COnstructor
      parent::__construct($filename, $mode);
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
      if ($this->hasOptions("sections") === true) {

         if (empty($value)) {
            trigger_error("A Third Parameter mus be added when using sections");
            exit();
         }

         if (!empty($value)) {
            if (!isset($this->data[$section][$key]) || $this->options[$this->filename]["rewrite"] === true) {
               $this->data[$section][$key] = $value;
            }
         }
      } else {
         if (empty($value)) {
            if (!array_key_exists($section, $this->data) || $this->hasOptions("rewrite") === true) {
               $this->data[$section] = $key;
            }
         } else {
            trigger_error("Failed to Write value a third parameter cannot be used without sections");
            exit();
         }
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


   public function get(string $section = "", string|int $key = "")
   {
      if (!empty($section)) {
         if (array_key_exists($section, $this->data)) {
            if (!empty($key) && $this->hasOptions("sections") === true) {
               if (array_key_exists($key, $this->data[$section])) {
                  return $this->data[$section][$key];
               } else {
                  echo "Key Value cannot be found";
               }
            } else {
               return $this->data[$section];
            }
         } else {
            echo "Section cannot be found";
         }
      } else {
         return (array) $this->data;
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
   private function write(string $filename, string|int|array $content, int $optional = 0)
   {
      return $this->writeFile($filename, $content, $optional);
   }

   /**
    * @method remove()
    * @param string $section =""
    * $section can be made empty or can use Wildcard (*) to remove all values
    * @param string|int $key=""
    * @return @method $this->jsonWriter->remove($sections,$key,$value)
    * @description :  method is used set Serialize to true
    * @optional false
    */

   public function remove(string $section = "", string|int $key = "")
   {
      if ($this->hasOptions("no-delete") === false) {
         // check for empty Sections and remove all values.
         if (empty($section) || $section === (string) "*") {
            // Refresh Data Array to remove all Values before save
            $this->data = [];
         } else {
            if ($this->hasOptions("sections")() === true) {
               if (!empty($key)) {
                  if (array_key_exists($section, $this->data)) {
                     if (array_key_exists($key, $this->data[$section])) {
                        unset($this->data[$section][$key]);
                     } else {
                        echo "Key Doesnt Exist";
                     }
                  } else {
                     echo "cannot find Section";
                  }
               }
            } else {
               if (array_key_exists($section, $this->data)) {
                  unset($this->data[$section]);
               }
            }
         }
      }
   }


   /**
    * @method save()
    * @description :  used to finalise and save the data to a file and write it.
    * @optional false;
    */

   private function unsetData()
   {
      unset($this->data[$this->filename]);
      $this->filename = "";
      unset($this->options);
   }


   public function deleteData()
   {
      $this->unsetData();
   }

   public function save()
   {
      // Validate OverWrite Rules
      $extension = $this->getExtension($this->filename);
      $this->writeData($this->filename, $extension);
      //    // CLear all and reset to default
      $this->unsetData();
   }
}
