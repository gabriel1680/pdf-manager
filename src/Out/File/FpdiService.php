<?php

namespace Gabriellopes\Pdfmanager\Out\File;

use Gabriellopes\Pdfmanager\Application\Service\FileShape;
use Gabriellopes\Pdfmanager\Application\Service\PDFHandler;
use Gabriellopes\Pdfmanager\Application\Service\PDFPageHandler;
use setasign\Fpdi\Fpdi;

class FpdiService implements PDFHandler
{
    private readonly Fpdi $fpdi;

    public function __construct(private readonly string $resourcesDir)
    {
        $this->fpdi = new Fpdi();
    }

    public function writeTo(string $filename): void
    {
        $this->fpdi->Output("F", "tmp/{$filename}", true);
    }

    public function readFrom(string $filename): PDFPageHandler
    {
        $filepath = $this->resourcesDir . "/" . $filename;
        if (!file_exists($filepath)) {
            throw new \RuntimeException("File not found: $filepath");
        }
        $fpdi = $this->fpdi;
        $pageCount = $this->fpdi->setSourceFile($filepath);
        $onImportPage = function (int $pageNumber, FileShape $shape) use ($fpdi) {
            $templateID = $this->fpdi->importPage($pageNumber);
            $fpdi->getTemplateSize($templateID);
            $fpdi->addPage();
            $fpdi->useTemplate($templateID, 0, 0, $shape->value);
        };
        return new FpdiPageHandler($onImportPage, $pageCount);
    }
}

class FpdiPageHandler implements PDFPageHandler
{
    public function __construct(
        private $onImportPage,
        private readonly int $pagesCount
    ) {}

    public function pagesCount(): int
    {
        return $this->pagesCount;
    }

    public function usePage(int $pageNumber, FileShape $shape): void
    {
        ($this->onImportPage)($pageNumber, $shape);
    }
}
