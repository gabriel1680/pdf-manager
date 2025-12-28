<?php

namespace Gabriellopes\Pdfmanager\In\Cli;

use Gabriellopes\Pdfmanager\In\Cli\Command\Merge\MergePDFCommand;
use Gabriellopes\Pdfmanager\In\CLI\PDFManagerCli;
use Gabriellopes\Pdfmanager\PDFManagerFactory;

class PDFManagerCliFactory
{
    public static function create(string $resourcesPath): PDFManagerCli
    {
        $pdfManager = PDFManagerFactory::create($resourcesPath);
        $app = new PDFManagerCli();
        $app->register("merge", new MergePDFCommand($pdfManager->merge(...)));
        return $app;
    }
}
