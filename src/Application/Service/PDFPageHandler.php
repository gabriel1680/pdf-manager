<?php

namespace Gabriellopes\Pdfmanager\Application\Service;

interface PDFPageHandler
{
    public function pagesCount(): int;
    public function importPage(int $pageNumber, FileShape $shape): void;
}
