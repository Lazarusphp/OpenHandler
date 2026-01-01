<?php
namespace LazarusPhp\OpenHandler;

use LazarusPhp\OpenHandler\Handler;
use LazarusPhp\OpenHandler\Interfaces\HandlerInterface;
use LazarusPhp\OpenHandler\CoreFiles\Traits\Structure;

class OpenHandler
{
    use Structure;

    private static HandlerInterface $handlerInterface;


    

    public static function create(string $directory,array $handler =[Handler::class])
    {

        if(count($handler) === 1)
        {
            $handler = implode("",$handler);
        }
        else
        {
            trigger_error("Error Handler Must contain no more than one Class");
            return false;
        }

        $reflection = self::reflection($handler);
        if (class_exists($handler)) {
            if($reflection->isInstantiable()){
                self::$handlerInterface = new $handler($directory);
                
                if(self::$handlerInterface->setDirectory($directory)){
                return self::$handlerInterface;
                }
            }
            else
            {
                echo "Failed to instantiate new class";
            }
        } 
    }

    
}
