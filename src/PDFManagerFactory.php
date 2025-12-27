<?php

namespace Gabriellopes\Pdfmanager;

use Gabriellopes\Pdfmanager\Application\PDFManagerImpl;
use Gabriellopes\Pdfmanager\Application\UseCase\MergePDF;
use Gabriellopes\Pdfmanager\Out\File\FpdiService;

class PDFManagerFactory
{
    public static function create(string $resourcesPath): PDFManager
    {
        $pdfHandler = new FpdiService($resourcesPath);
        $mergePDF = new MergePDF($pdfHandler);
        return new PDFManagerImpl($mergePDF);
    }
}
