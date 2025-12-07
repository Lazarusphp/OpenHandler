<?php

namespace LazarusPhp\OpenHandler\Interfaces;

interface WriterInterface
{
    public function __construct(string $filepath);
    public function open(string $filename);
    public function set(string $section,string|int $key,string|int $value="");
    public function get(string $section,string|int $key="");
    // public function setJsonPath(string $path);
    public function write(string $filename,string|int|array $content,int $optional=0);
    public function remove(string $section,string|int  $key="");
    public function save();

}