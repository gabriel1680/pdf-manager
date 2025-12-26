<?php

namespace Gabriellopes\Pdfmanager;

use Gabriellopes\Pdfmanager\Application\PDFManagerImpl;
use Gabriellopes\Pdfmanager\Application\UseCase\MergePDF;
use Gabriellopes\Pdfmanager\Out\File\FileSystemFileProvider;

class PDFManagerFactory
{
    public static function create(string $resourcesPath): PDFManager
    {
        $fileProvider = new FileSystemFileProvider($resourcesPath);
        $mergePDF = new MergePDF($fileProvider);
        return new PDFManagerImpl($mergePDF);
    }
}
