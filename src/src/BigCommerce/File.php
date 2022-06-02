<?php

namespace IntuitSolutions\BigCommerce;

class File
{
    private $path;
    private $contentType;
    private $filename;

    public function __construct($path, $contentType, $filename)
    {
        $this->path = $path;
        $this->contentType = $contentType;
        $this->filename = $filename;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getStream()
    {
        return fopen($this->path, 'r');
    }
}
