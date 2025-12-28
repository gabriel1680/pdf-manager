<?php

namespace Gabriellopes\Pdfmanager\Application;

use Gabriellopes\Pdfmanager\Application\UseCase\MergePDF;
use Gabriellopes\Pdfmanager\Out\File\FpdiService;
use Gabriellopes\Pdfmanager\PDFManager;

class PDFManagerFactory
{
    public static function create(string $resourcesPath): PDFManager
    {
        $pdfHandler = new FpdiService($resourcesPath);
        $mergePDF = new MergePDF($pdfHandler);
        return new PDFManagerImpl($mergePDF);
    }
}
