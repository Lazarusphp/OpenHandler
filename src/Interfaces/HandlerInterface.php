<?php
namespace LazarusPhp\OpenHandler\Interfaces;

interface HandlerInterface
{
    public function list(string $directory,bool $recursive=true,$files=true);
    public function directory(string $path,int $mode=0755, bool $recursive=true);
    public function delete(string $path);
    public function file(string $filename, callable $handler, bool $sections=false);
    public function prefix(string $path, callable $handler);
    public function setDirectory(string $directory="");
    public function upload(string $path,string $name);
}

