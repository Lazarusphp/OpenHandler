<?php
namespace LazarusPhp\OpenHandler\CoreFiles\Traits;

use App\System\Core\Functions;
use Exception;
use LazarusPhp\OpenHandler\Traits\Structure;

trait EnvWriter
{

    protected function WriteEnv()
    {

        $output = "";
        foreach($this->writeData as $section => $value)
        {
            $section = strtoupper($section);
            $output .= "$section='".$value."'" . PHP_EOL;
        }

        return $this->write($this->filename,$output);
    }
}