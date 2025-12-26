<?php

namespace Gabriellopes\Pdfmanager\Out\File;

use Gabriellopes\Pdfmanager\Application\Service\FileProvider;

class FileSystemFileProvider implements FileProvider
{
    private const string READ_MODE = "r";

    public function __construct(private string $resourcesDir) {}

    public function getFileWith(string $filename)
    {
        $stream = fopen($this->resourcesDir . "/" . $filename, self::READ_MODE);
        if ($stream == false) {
            throw new \Exception("Error reading file: \"{$filename}\"");
        }
        return $stream;
    }
}
