<?php

namespace LazarusPhp\OpenHandler\CoreFiles\Traits;

use App\System\Core\Functions;

trait FileParser
{

    protected function bindFiles(string $filename)
    {
        $extension = $this->getExtension($filename);

        if(in_array($extension,["txt","php","js"]))
        {
            trigger_error("Invalid File Format");
            exit();
        }
        else{
            if (!$this->hasFile($filename)) {
                if ($extension === "json") {
                    $data = (string)  "{}";
                }

                if (in_array($extension, ["ini", "env", "txt"])) {
                    $data = (string) "";
                }
                $this->writeFile($filename, $data);
            }
        }
    }

    protected function GenerateSections(string $filename, bool $sections)
    {
        if (!in_array($this->filename, $this->hasSections)) {
            $this->hasSections[$filename] = $sections;
        }
    }

    protected function hasSections()
    {
        if ($this->hasSections[$this->filename] === true) {
            return true;
        } else {
            return false;
        }
    }


    protected function loadParser(string $filename)
    {

        // Detect extension
        $extension  = $this->getExtension($filename);
        // Bind the FIles if they do not exist

        


        // Cycle Extensions


        // Check for ini
        if ($extension === "ini") {
            $sections = $this->hasSections[$filename];
            $data =  (array) parse_ini_file($filename, $sections);
        }

        // CHeck For env
        if ($extension === "env") {
            $sections = $this->hasSections[$filename] = false;
            $data = (array) parse_ini_file($filename, $sections);
        }

        // Check for json
        if ($extension === "json") {
            $file = file_get_contents($filename);
            $data = (array) json_decode($file, true);
        }
        // End Cycle of Extentons

        
        // Run parseFile
        
        $this->parseData($data);
    }

    public function writeData(string $filename, $extension)
    {
        if ($extension === "json") {
            $this->writeJson($this->filename);
        }

        if ($extension === "env") {
            $this->writeEnv($this->filename);
        }


        if ($extension === "ini") {
            $this->writeIni($this->filename);
        }
    }


    private function parseData(array $data, string $filenane = "")
    {

        $filename = $filename ?? $this->filename;
        foreach ($data as $sections => $k) {
            if ($this->hasSections[$filename] === true) {
                if (!is_array($k)) {
                    continue;
                }

                foreach ($k as $pair => $value) {
                    // $this->data[$sections][] = $pair;
                    if (isset($this->data[$sections][$pair])) {
                        $this->data[$sections][$pair] = $value;
                    }
                }
            } else {
                $this->data[$sections] = $k;
            }
        }
    }

    private function writeEnv(string $filename)
    {
        $this->hasSections[$filename] = false;
        $output = "";
        foreach ($this->data as $section => $value) {

            $output .= "$section='" . $value . "'" . PHP_EOL;
        }
        return $this->writeFile($this->filename, $output);
    }

    private function writeJson(string $filename)
    {

        
        Functions::dd($this->data);

        $encode= (string) json_encode($this->data, JSON_PRETTY_PRINT);
        return $this->writeFile($filename, $encode);
    }


    private function writeIni(string $filename)
    {

        $output = "";
        // Write Ini File Here.
        foreach ($this->data as $sec => $k) {

            if ($this->hasSections() === true) {
                $output .= "[$sec]" . PHP_EOL;
                if (!is_array($k)) {
                    continue;
                }

                foreach ($k as $key => $v) {
                    $output .= "$key=" . $this->validateType($v) . PHP_EOL;
                }
            } else {
                $output .= "$sec=" . $this->validateType($k) . PHP_EOL;
            }
        }
        $this->writeFile($filename, $output);
    }

    private function validateType($value)
    {
        return (is_numeric($value) ? $value : '"' . addslashes($value) . '"');
    }
}
