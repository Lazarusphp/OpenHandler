<?php

namespace LazarusPhp\OpenHandler\CoreFiles\Traits;

use App\System\Core\Functions;

trait FileParser
{


    protected function supportedFlags(array $flags)
    {
        $flagOptions = [];
        $supported = (array) ["no-delete","rewrite","sections"];
        if(count($flags) > 0 )
        {
            
            foreach($flags as $key)
            {
                // Convert to lower string
                if(!is_string($key))
                {
                    continue;
                } 

                $key = strtolower($key);
                
                // echo "$key : ";
                // // Echo Out value
                if(isset($key) && in_array($key,$supported))
                {
                    $flagOptions[$key] = true;
                }
                else
                {
                    $flagOptions[$key] = false;
                }
            }
            return $flagOptions;
        }
    }

    protected function bindFiles(string $filename)
    {
        $extension = $this->getExtension($filename);

        if (in_array($extension, ["php", "js"])) {
            trigger_error("Invalid File Format");
            exit();
        } else {
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


    protected function getOptions(string $key)
    {
        if(array_key_exists($key,$this->options[$this->filename])){
            return $this->options[$this->filename][$key];
        }
    }

    // Must Return true or false
    protected function hasOptions($key):bool
    {
        if(array_key_exists($key,$this->options[$this->filename]))
        {
            return ($this->options[$this->filename][$key] === true) ? true : false;
        }
        else
        {
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
            $sections = $this->hasOptions("sections") ?? false;
            $data =  (array) parse_ini_file($filename, $sections);
        }

        if ($extension === "txt") {
            if ($extension === "txt") {
                $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $data = [];

                foreach ($lines as $line) {
                    if (!str_contains($line, "::")) {
                        continue;
                    }

                    [$key, $value] = explode("::", $line, 2);
                    $data[trim($key)] = trim($value);
                }
            }
        }

        // CHeck For env
        if ($extension === "env") {
            $sections = $this->hasOptions("sections") ?? false;
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
            $this->writeJson($filename);
        }

        if ($extension === "txt") {
            $this->writeTxt($filename);
        }

        if ($extension === "env") {
            $this->writeEnv($filename);
        }


        if ($extension === "ini") {
            $this->writeIni($filename);
        }
    }


    private function parseData(array $data, string $filenane = "")
    {

        $filename = $filename ?? $this->filename;
        foreach ($data as $sections => $k) {
            if ($this->hasOptions("sections") === true) {
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
        $encode = (string) json_encode($this->data, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES);
        return $this->writeFile($filename, $encode);
    }

    private function writeTxt(string $filename)
    {
        $output = "";
        // Write Ini File Here.
        foreach ($this->data as $sec => $k) {

            if (!is_string($sec)) {
                continue;
            }

            $output .= "$sec::$k " . PHP_EOL;
        }
        $this->writeFile($filename, $output);
    }

    private function writeIni(string $filename)
    {
        $output = "";
        // Write Ini File Here.
        foreach ($this->data as $sec => $k) {

            if ($this->hasOptions("sections") === true) {
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
