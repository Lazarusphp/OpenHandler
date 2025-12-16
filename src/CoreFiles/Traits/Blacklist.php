<?php
namespace LazarusPhp\OpenHandler\CoreFiles\Traits;

use LazarusPhp\OpenHandler\ErrorHandler;
use ReflectionClass;

trait Blacklist
{

    private array $blacklist = [];
    private string $type = "";
    /**
     * BlackList Package used to restrict helpers and methods listed within a class
     */


    public function helper()
    {
        $this->type = "helper";
        return $this;
    }

    public function method()
    {
        $this->type = "method";
        return $this;
    }   
    


    private function setRestrict($method)
    {
        foreach($this->restricted as $restricted)
        {
            if($method === $restricted)
            {
                if(method_exists(static::class,$restricted) === true)
                {
                    ErrorHandler::setError($method,"method", "Method $method  is Restricted");
                    
                    if(!in_array($method,$this->blacklist)){
                        $this->blacklist[] = $method;
                    }
                }
            }
        }
    }

    protected function loadMethod($method)
    {
        $this->setRestrict($method);
        if(!in_array($method,$this->blacklist))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
        

}