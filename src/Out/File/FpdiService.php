<?php

namespace Gabriellopes\Pdfmanager\Out\File;

use Gabriellopes\Pdfmanager\Application\Service\FileShape;
use Gabriellopes\Pdfmanager\Application\Service\PDFHandler;
use Gabriellopes\Pdfmanager\Application\Service\PDFPageHandler;
use setasign\Fpdi\Fpdi;

class FpdiService implements PDFHandler
{
    private readonly Fpdi $fpdi;

    public function __construct(private string $resourcesDir)
    {
        $this->fpdi = new Fpdi();
    }

    public function writeTo(string $filename): void
    {
        $this->fpdi->Output("F", "tmp/{$filename}", true);
    }

    public function loadFile(string $filename): PDFPageHandler
    {
        $filepath = $this->resourcesDir . "/" . $filename;
        if (!file_exists($filepath)) {
            throw new \RuntimeException("File not found: $filepath");
        }
        $pageCount = $this->fpdi->setSourceFile($filepath);
        return new FpdiPageHandler($this->fpdi, $pageCount);
    }
}

class FpdiPageHandler implements PDFPageHandler
{
    public function __construct(
        private readonly Fpdi $fpdi,
        private readonly int $pagesCount
    ) {}

    public function pagesCount(): int
    {
        return $this->pagesCount;
    }

    public function importPage(int $pageNumber, FileShape $shape): void
    {
        $templateID = $this->fpdi->importPage($pageNumber);
        $this->fpdi->getTemplateSize($templateID);
        $this->fpdi->addPage();
        $this->fpdi->useTemplate($templateID, 0, 0, 210);
    }
}
